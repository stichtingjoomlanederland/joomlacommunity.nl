<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussPost extends EasyDiscuss
{
    private $post = null;
    private $original = null;


    // This contains the error message.
    public $error = null;

    // This could be retrieved from the database directly
    public $noofdays = null;
    public $post_type_suffix = null;
    public $post_type_title = null;
    public $category = null; // category title
    public $lastupdate = null;
    public $total_vote_cnt = null;
    public $daydiff = null;
    public $timediff = null;
    public $polls_cnt = null;
    public $totalFavourites = null;
    public $attachments_cnt = null;
    public $isVoted = null;
    public $isNew = null;
    public $prevPostStatus = null;
    public $isModerate = null;

    public $content_raw = null;
    public $cat_parent_id = null;
    public $group_id = null;

    // This is used in reply permalink
    public $permalink = null;
    public $seq = null;

    static $_isLiked = array();
    static $ratings = null;


    public function __construct($post, $options = array())
    {
        parent::__construct();

        $resetCache = false;
        $cache = true;

        // Determines if the object cache should be cleared.
        if (isset($options['resetCache'])) {
            $resetCache = (bool) $options['resetCache'];
        }

        // Determines if the object should be cached
        if (isset($options['cache'])) {
            $cache = (bool) $options['cache'];
        }

        // we know we want to 'preload' or cache the posts.
        if ($cache && is_array($post)) {
            $this->loadBatchPosts($post);
            return;
        }

        // The $post must always be a table.
        $this->post = ED::table('Post');

        // If passed in argument is an integer, we load it
        if (is_numeric($post)) {

            $cacheExists = ED::cache()->exists($post, 'post');

            // When the post object has already been loaded before, just reuse it
            if ($cacheExists) {
                $this->post = ED::cache()->get($post, 'post');
            }

            // When cache doesn't exist, try to load the post
            if (!$cacheExists && $post != 0) {
                $this->post->load($post);
            }
        }

        // If passed in argument is already a post, table just assign it.
        if ($post instanceof DiscussPost) {
            $this->post = $post;
        }

        if (is_object($post)) {

            if (! $post instanceof DiscussPost) {
                $this->post = ED::table('Post');
                $this->post->bind($post);
            }

            // var_dump($this->post);exit;

            // need to manually assign these attributes
            if (isset($post->noofdays)) {
                $this->noofdays = $post->noofdays;
            }

            if (isset($post->post_type_suffix)) {
                $this->post_type_suffix = $post->post_type_suffix;
            }

            if (isset($post->post_type_title)) {
                $this->post_type_title = $post->post_type_title;
            }

            if (isset($post->category)) { $this->category = $post->category;}
            if (isset($post->lastupdate)) { $this->lastupdate = $post->lastupdate;}
            if (isset($post->total_vote_cnt)) { $this->total_vote_cnt = $post->total_vote_cnt;}
            if (isset($post->daydiff)) { $this->daydiff = $post->daydiff;}
            if (isset($post->timediff)) { $this->timediff = $post->timediff;}
            if (isset($post->polls_cnt)) { $this->polls_cnt = $post->polls_cnt;}
            if (isset($post->totalFavourites)) { $this->totalFavourites = $post->totalFavourites;}
            if (isset($post->attachments_cnt)) { $this->attachments_cnt = $post->attachments_cnt;}
            if (isset($post->isVoted)) { $this->isVoted = $post->isVoted;}

            if (isset($post->isNew)) {
                $this->isNew = $post->isNew;
            }
            if (isset($post->prevPostStatus)) { $this->prevPostStatus = $post->prevPostStatus;}
            if (isset($post->isModerate)) { $this->isModerate = $post->isModerate;}
            if (isset($post->cat_parent_id)) { $this->cat_parent_id = $post->cat_parent_id;}
            if (isset($post->group_id)) { $this->group_id = $post->group_id;}
        }

        // keep a copy of original data
        $this->original = clone $this->post;
    }



    private function loadBatchPosts($posts)
    {
        $ids = array();

        foreach ($posts as $item) {
            if (is_numeric($item)) {
                $ids[] = $item;
            }
        }

        if ($ids) {
            $model = ED::model('Posts');
            $posts = $model->loadBatchPosts($ids);
        }

        $cacheLib = ED::cache();
        $cacheLib->cachePosts($posts);
    }

    /**
     * Magic method to get properties which don't exist on this object but on the table
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function __get($key)
    {
        if (isset($this->post->$key)) {
            return $this->post->$key;
        }

        if (isset($this->$key)) {
            return $this->$key;
        }

        return $this->post->$key;
    }

    /**
     * Log a new hit
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function hit()
    {
        $state = $this->post->hit();

        if ($state) {
            $this->updateThread(array('hits' => $this->post->hits));
        }

        return $state;

    }

    /**
     * Determines if the current viewer can post a comment on the post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canComment()
    {
        $isAdmin = ED::isSiteAdmin();
        $isModerator = ED::isModerator($this->post->category_id);

        if ($isModerator || $isAdmin) {
            return true;
        }

        // If the user is banned, they should not be able to add comment.
        if ($this->isUserBanned()) {
            $this->setError('COM_EASYDISCUSS_SYSTEM_BANNED_YOU');
            return false;
        }

        // If the user doesn't have access, they should not be able to add comments
        if ($this->acl->allowed('add_comment')) {
            return true;
        }

        return false;
    }

    /**
     * Determines if the current viewer can vote on this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canVote()
    {
        if ($this->isPending()) {
            return false;
        }

        $isModerator = ED::isModerator($this->post->category_id);
        $isAdmin = ED::isSiteAdmin();

        if ($isModerator || $isAdmin) {
            return true;
        }

        if (!$this->my->id) {

            if ($this->config->get('main_allowguest_vote_question') && $this->isQuestion()) {
                return true;
            }

            if ($this->config->get('main_allowguest_vote_reply') && $this->isReply()) {
                return true;
            }

            return false;
        }

        if ($this->config->get('main_allowquestionvote') && $this->isQuestion()) {
            return true;
        }

        if ($this->config->get('main_allowvote') && $this->isReply()) {
            return true;
        }

        return false;
    }
    /**
     * Determines if the user can feature this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canFeature()
    {
        if (!$this->isQuestion()) {
            return false;
        }

        if ($this->acl->allowed('feature_post')) {
            return true;
        }

        if (ED::isSiteAdmin()) {
            return true;
        }

        if (ED::isModerator($this->post->category_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determines if the current viewer can delete this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canDelete()
    {
        // check is it can delete own post
        $isMine = ED::isMine($this->post->user_id);

        if (!$this->config->get('main_allowdelete')) {
            return false;
        }

        if (ED::isSiteAdmin() || ED::isModerator($this->post->category_id)) {
            return true;
        }

        if ($this->isQuestion()) {
            if ($this->acl->allowed('delete_question') || ($this->acl->allowed('delete_own_question') && $isMine)) {
                return true;
            }

            return false;
        }

        if ($this->isReply()) {
            if ($this->acl->allowed('delete_reply') || ($this->acl->allowed('delete_own_replies') && $isMine)) {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * Determines if the user can edit this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canEdit()
    {
        // check is it edit own post
        $isMine = ED::isMine($this->post->user_id);

        // Check is it site admin
        $isAdmin = ED::isSiteAdmin();

        // Check is it site admin or moderator
        if (ED::isSiteAdmin() || ED::isModerator($this->post->category_id)) {
            return true;
        }

        // If not new, means this is editing post
        if (!$this->isNew() && $this->isQuestion()) {

            //check if admin or is owner before allowing edit question.
            $isAllowEditQuestion = $this->acl->allowed('edit_question');
            $isAllowEditOwnQuestion = $this->acl->allowed('edit_own_question');

            // allow to edit all the question
            if ($isAllowEditQuestion) {
                return true;
            }

            // allow edit own question
            if ($isMine && $isAllowEditOwnQuestion) {
                return true;
            }
        }

        if ($this->isReply()) {

            // Check user's acl
            $isAllowEditReply = $this->acl->allowed('edit_reply');
            $isAllowEditOwnReply = $this->acl->allowed('edit_own_reply');

            // allow to edit all the reply
            if ($isAllowEditReply) {
                return true;
            }

            // allow edit own reply
            if ($isMine && $isAllowEditOwnReply) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determines if the current user can print this post.
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canPrint()
    {
        if ($this->isQuestion() && $this->config->get('main_enable_print')) {
            return true;
        }

        return false;
    }

    /**
     * This determines if the user can quote this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canReply()
    {
        $isAdmin = ED::isSiteAdmin();
        $isModerator = ED::isModerator($this->post->category_id);

        if ($isModerator || $isAdmin) {
            return true;
        }

        if ($this->acl->allowed('add_reply')) {
            return true;
        }

        return false;
    }

    /**
     * method to branch out a reply into discussion post.
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function branch()
    {
        $model = ED::model('Post');
        $state = $model->branch($this->id, $this->parent_id);
        return $state;
    }


    /**
     * Determines if the current viewer can branch a post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canBranch()
    {
        // If this is not a reply, they shouldn't be allowed to branch
        if (!$this->isReply()) {
            return false;
        }

        // If the user is super admin they should be allowed
        if (ED::isSiteAdmin()) {
            return true;
        }

        // If the user is a moderator, they should be allowed
        if (ED::isModerator($this->post->category_id)) {
            return true;
        }

        // Check if the user has permissions
        if ($this->canReply() && $this->acl->allowed('edit_branch')) {
            return true;
        }

        return false;
    }

    /**
     * Determines if the current viewer can mark items as resolved
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canResolve()
    {
        // Check if this feature is enabled
        if (!$this->config->get('main_qna')) {
            return false;
        }

        // Reply items should not display the resolve buttons
        if (!$this->isQuestion()) {
            return false;
        }

        // If user is site admin, they should always be able to see this
        if (ED::isSiteAdmin()) {
            return true;
        }

        // If user is a moderator, they should be able to see this
        if (ED::isModerator($this->post->category_id)) {
            return true;
        }

        // If the user owns this post, they should be able to see this
        if ($this->post->user_id && $this->my->id == $this->post->user_id) {
            return true;
        }

        return false;
    }

    /**
     * This allows caller to check if the current viewer can report this post.
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canReport()
    {
        // Ensure that this feature is enabled.
        if (!$this->config->get('main_report')) {
            return false;
        }

        // Ensure that the user is not the owner of the post
        if ($this->my->id == $this->post->user_id) {
            return false;
        }

        // Ensure that the current logged in user has access to report
        if (!$this->acl->allowed('send_report')) {
            return false;
        }

        // If the user is banned, they should not be able to send report.
        if ($this->isUserBanned()) {
            $this->setError('COM_EASYDISCUSS_SYSTEM_BANNED_YOU');
            return false;
        }

        return true;
    }

    /**
     * Determines if the user is allowed to move a post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canMove()
    {
        // Replied cannot be moved
        if (!$this->isQuestion()) {
            return false;
        }

        if (ED::isSiteAdmin() || ED::isModerator($this->post->category_id)) {
            return true;
        }

        return false;
    }



    /**
     * Determines if the current user has access to post new questions on the site
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canPostNewDiscussion()
    {
        // If guest and he is able to post a question
        if (!$this->my->id && !$this->acl->allowed('add_question', '0')) {
            $this->setError('COM_EASYDISCUSS_POST_PLEASE_LOGIN');
            return false;
        }

        // If logged in user and able to post a question
        if ($this->my->id && !$this->acl->allowed('add_question', '0')){
            $this->setError('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS');
            return false;
        }

        // If the user is banned, they should not be able to post new discussion.
        if ($this->isUserBanned()) {
            $this->setError('COM_EASYDISCUSS_SYSTEM_BANNED_YOU');
            return false;
        }

        return true;
    }


    /**
     * Allows caller to bind params to the table
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function bindParams($data)
    {
        $registry = new JRegistry();

        if (!$data) {
            return;
        }

        // Go through each of the provided data and scan for known pattern
        $pattern = '/params\_.*/i';

        foreach ($data as $key => $value) {

            // See if the key matches our wanted key
            $match = preg_match($pattern, $key);

            if (!$match) {
                continue;
            }

            // If the value is an array, we need to normalize it
            if (is_array($value)) {

                $key = str_ireplace('[]', '', $key);
                $i = 0;

                foreach ($value as $valueItem) {

                    if (!$valueItem) {
                        continue;
                    }

                    $registry->set($key . $i, $valueItem);
                    $i++;
                }
            } else {
                $registry->set($key, $value);
            }
        }

        $this->post->params = $registry->toString();
    }


    /**
     * Binds the given data to the table
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function bind($data = array(), $allowBindingId = false)
    {
        $date = ED::date();

        // Do not allow caller to bind id as this is a security issue
        if (isset($data['id']) && !$allowBindingId) {
            unset($data['id']);
        }

        // before normalize, we need to bind the data
        $state = $this->post->bind($data, true);

        // If anonymous postings is disabled, do not allow user to enforce this
        if (!$this->config->get('main_anonymous_posting') || !isset($data['anonymous'])) {
            $this->post->anonymous = false;
        }

        // Bind posted parameters such as custom tab contents.
        $this->bindParams($data);

        // // If there is mod_post_category_id, means this is from module quick question
        // if (isset($data['mod_post_topic_category_id'])) {
        //     $this->post->category_id = $data['mod_post_topic_category_id'];
        //     unset($data['mod_post_topic_category_id']);
        // }

        // Clean up the title
        $allowedTags = explode(',', $this->config->get('main_allowed_tags'));
        $allowedAttributes = explode(',', $this->config->get('main_allowed_attr'));

        // Sanitize the post title
        if (isset($data['title'])) {
            $input = JFilterInput::getInstance($allowedTags, $allowedAttributes);
            $this->title = $input->clean($data['title']);
        }

        // For replies, we need to set a title for it
        if ($this->isReply() && !isset($data['title'])) {
            $parent = $this->getParent();

            $data['title'] = 'RE: ' . $parent->getTitle();
        }

        // Need to update the module to send a content instead of quick_question_reply_content

        // If the post is edited and it doesn't have private the user might be switching from private -> non private
        if (!$this->isNew() && !isset($data['private'])) {
            $this->post->private = false;
        }

        // If post is being edited, do not change the owner of the item.
        if (!$this->isNew()) {
            $this->post->user_id = !$this->post->user_id ? $this->my->id : $this->post->user_id;
        }

        // Cleanup alias.
        if (isset($data['title'])) {
            $alias = ED::badwords()->filter($data['title']);
            $this->post->alias = ED::getAlias($alias, 'post', $this->post->id);
        }

         // Get the content type
        if ($this->isQuestion()) {
            $this->post->content_type = ED::getEditorType('question');
        }

        if($this->isReply()) {
            $this->post->content_type = ED::getEditorType('reply');

            // For replies, we need to set the category id from the parent
            $parent = $this->getParent();
            $this->post->category_id = $parent->category_id;
        }

        // Detect the poster type.
        $this->post->user_type = !$this->post->user_id ? DISCUSS_POSTER_GUEST : DISCUSS_POSTER_MEMBER;

        // some joomla editor htmlentity the content before it send to server. so we need
        // to do the god job to fix the content.
        $content = ED::string()->unhtmlentities($data['content']);

        // Ensure that the posted content is respecting the correct values.
        $this->post->content = $content;

        $this->post->preview = $content;

        // now we need to 'translate the content into preview mode so that frontend no longer need to do this heavy process'
        if ($this->post->content_type == 'bbcode') {

            $preview = ED::parser()->bbcode($content);
            $preview = nl2br($preview);
            $this->post->preview = $preview;
        }

        // Normalize other properties
        // Whenever store is called, we should always update the modified date.
        $this->post->modified = $date->toSql();

        // @since 3.0
        $this->post->legacy = '0';

        // Update the created date if no created date is provided
        if (!$this->post->id && !$this->post->created) {
            $this->post->created = $this->post->modified;
        }

        // if this is a new question, we need to set the replied date as so that the sorting will work correctly.
        if ($this->isQuestion() && $this->isNew()) {
            $this->post->replied = $this->post->modified;
        }

        // If this is a reply, we need to update the last replied date for the parent discussion
        if ($this->post->published && $this->isReply()) {
            $this->post->updateParentLastRepliedDate();
        }

        // Get previous status before binding.
        $this->prevPostStatus = $this->post->published;

        // If password is disabled, do not allow users to set password.
        if (!$this->config->get('main_password_protection')) {
            $this->post->password = '';
        }

        // Detect user's ip address.
        $ip = $this->input->server->get('REMOTE_ADDR');
        $this->post->ip = $ip;


        return $state;

    }

    /**
     * Allow the caller to publish/unpublish a discussion on the site
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function publish($publish = true)
    {
        $this->post->published = $publish;

        $state = $this->post->store();

        if ($state) {
            $this->updateThread(array('published' => $publish));
        }

        return $state;
    }

    /**
     * Allows caller to feature a discussion on the site
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function feature()
    {
        $this->post->featured = true;

        $state = $this->post->store();

        if ($state) {
            $this->updateThread(array('featured' => true));
        }

        // Send notification to the thread starter that their post is being featured.
        // Only send when the person featuring the post is not himself.
        if ($this->post->user_id != $this->my->id) {
            $notification = ED::table('Notifications');
            $notification->bind(array(
                    'title' => JText::sprintf( 'COM_EASYDISCUSS_FEATURED_DISCUSSION_NOTIFICATION_TITLE', $this->post->title),
                    'cid' => $this->post->id,
                    'type' => DISCUSS_NOTIFICATIONS_FEATURED,
                    'target' => $this->post->user_id,
                    'author' => $this->my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $this->post->id
                ));
            $notification->store();
        }

        return $state;
    }

    /**
     * Allows caller to unfeature a discussion on the site
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function unfeature()
    {
        $this->post->featured = false;

        $state = $this->post->store();

        if ($state) {
            $this->updateThread(array('featured' => false));
        }

        return $state;
    }

    /**
     * move post into new category
     * @since 4.0
     */
    public function move($newCategory)
    {
        // Switch to the new category and save it.
        $this->post->category_id = $newCategory;

        $state = $this->post->store();

        if ($state) {
            // Move the replies too
            $this->moveReplies($this->post->parent_id, $newCategory);

            $this->updateThread(array('category_id' => $newCategory));

        }
    }

    /**
     * Validates the post reply
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    private function validateReply($data)
    {
        // If the user is banned, they should not be able to add new reply.
        if ($this->isUserBanned()) {
            $this->setError('COM_EASYDISCUSS_SYSTEM_BANNED_YOU');
            return false;
        }

        // @task: User needs to be logged in, in order to submit a new reply.
        if (!$this->acl->allowed('add_reply', '0') && !$this->my->id) {
            $this->setError('COM_EASYDISCUSS_PLEASE_KINDLY_LOGIN_INORDER_TO_REPLY');
            return false;
        }

        // Check current logged in user is it have permission to reply
        if (!$this->acl->allowed('add_reply', '0')) {
            $this->setError('COM_EASYDISCUSS_ENTRY_NO_PERMISSION_TO_REPLY');
            return false;
        }

        // Check the post parent id is it exist or not
        if (!isset($data['parent_id'])) {
            $this->setError('COM_EASYDISCUSS_SYSTEM_INVALID_ID');
            return false;
        }

        // check the question id
        $question = ED::post($data['parent_id']);

        // check the question id is it exist or not
        if (!$question->id) {
            $this->setError('COM_EASYDISCUSS_SYSTEM_INVALID_ID');
            return false;
        }

        // Ensure that the user really has access to the discussion
        if ($question->private && $this->my->id != $question->user_id && !ED::isSiteAdmin() && !ED::isModerator($question->category_id, $this->my->id)) {
            $this->setError('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS');
            return false;
        }

        // Load the category for the question
        $questionCategory = ED::category($question->category_id);

        // check the category access/premission
        $questionAccess = $question->getAccess($questionCategory);

        // Check this discussion category is it allow user to reply
        if (!$questionAccess->canReply()) {
            $this->setError('COM_EASYDISCUSS_ENTRY_NO_PERMISSION_TO_REPLY');
            return false;
        }

        // Check the reply content is it got empty or not
        if (empty($data['content'])) {
            $this->setError('COM_EASYDISCUSS_ERROR_REPLY_EMPTY');
            return false;
        }

        // if this reply submitted by guest
        if (!$this->my->id) {

            // Ensure that the user is really allowed to post.
            if (!$this->acl->allowed('add_reply')) {
                $this->setError('COM_EASYDISCUSS_THIS_USERTYPE_HAD_BEEN_DISABLED');
                return false;
            }

            // Check the post name field is not empty
            if (!isset($data['poster_name']) || !$data['poster_name']) {
                $this->setError('COM_EASYDISCUSS_INVALID_NAME_IN_REPLY');
                return false;
            }

            // Check the post email field is not empty
            if (!isset($data['poster_email']) || !$data['poster_email']) {
                $this->setError('COM_EASYDISCUSS_INVALID_EMAIL_IN_REPLY');
                return false;
            }

            // Validate the user's email address
            $validEmail = ED::string()->isValidEmail($data['poster_email']);

            if (!$validEmail) {
                $this->setError('COM_EASYDISCUSS_INVALID_EMAIL_IN_REPLY');
                return false;
            }

        } else {
            $data['user_type'] = 'member';
            $data['poster_name'] = '';
            $data['poster_email'] = '';
        }

        return true;
    }

    /**
     * Validates the post question
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    private function validateQuestion($data)
    {
        $message = '';
        $valid = true;

        // Ensure that the user enters a valid post title
        if (!$this->post->title) {
            $this->setError('COM_EASYDISCUSS_POST_TITLE_CANNOT_EMPTY');
            return false;
        }

        // quick_question_reply_content is from the module quick question
        if (!isset($this->post->content) || (JString::strlen($this->post->content) == 0)){
            $this->setError('COM_EASYDISCUSS_POST_CONTENT_IS_EMPTY');
            return false;
        }

        // Ensure that the post meets the min length
        if (JString::strlen($this->post->content) < $this->config->get('main_post_min_length')) {
            $this->setError(JText::sprintf('COM_EASYDISCUSS_POST_CONTENT_LENGTH_IS_INVALID', $this->config->get('main_post_min_length')));
            return false;
        }

        if (empty($this->post->category_id)) {
            $this->setError('COM_EASYDISCUSS_POST_CATEGORY_IS_EMPTY');
            return false;
        }

        if (empty($this->my->id)) {

            if(empty($data['poster_name'])) {
                $this->setError('COM_EASYDISCUSS_POST_NAME_IS_EMPTY');
                return false;
            }

            if (empty($data['poster_email'])) {
                $this->setError('COM_EASYDISCUSS_POST_EMAIL_IS_EMPTY');
                return false;
            }

            if (!ED::string()->isValidEmail($data['poster_email'])) {
                $this->setError('COM_EASYDISCUSS_POST_EMAIL_IS_INVALID');
                return false;
            }
        }

        if (!$valid) {
            $this->setError($message);
            return false;
        }

        // // Check permission to modify assignee
        // $access = $this->post->getAccess();

        // if ($access->canAssign()) {
        //     // Add new record if assignee was changed
        //     if (array_key_exists('assignee_id', $data) && ($this->getAssignment()->assignee_id != $data['assignee_id'])) {

        //         $newAssignment = ED::table('PostAssignment');

        //         $newAssignment->post_id = $this->post->id;
        //         $newAssignment->assignee_id = (int) $data['assignee_id'];
        //         $newAssignment->assigner_id = (int) JFactory::getUser()->id;

        //         if (!$newAssignment->store()) {
        //             $this->setError(JText::_('COM_EASYDISCUSS_STORING_ASSIGNEE_FAILED'));
        //             return false;
        //         }
        //     }
        // }

        $category = $this->getCategory();
        $maxLength = $category->getParam('maxlength_size', 1000);

        // Check for maximum length of content if category has specific settings.
        // If there's a maximum content length specified per category base, then we need to check against the content.
        if ($category->getParam('maxlength')) {
            $length = JString::strlen($this->post->content);

            if ($length > $maxLength) {
                ED::storeSession($data, 'NEW_POST_TOKEN');
                $this->setError(JText::sprintf('COM_EASYDISCUSS_MAXIMUM_LENGTH_EXCEEDED', $maxLength));

                return false;
            }
        }

        // If user tries to submit in a container, throw an error.
        if ($category->container) {
            ED::storeSession($data, 'NEW_POST_TOKEN');
            $this->setError(JText::_('COM_EASYDISCUSS_NOT_ALLOWED_TO_POST_INTO_CONTAINER'));
            return false;
        }

        // Check for akismet
        if ($this->config->get('antispam_akismet') && $this->config->get('antispam_akismet_key')) {
            if (!$this->akismet()) {
                $this->setError('COM_EASYDISCUSS_AKISMET_SPAM_DETECTED');
                return false;
            }
        }

        return true;
    }

    /**
     * Validates the custom fields for this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function validateFields($operation = null)
    {
        // Get a list of custom field on the post
        $model = ED::model('CustomFields');
        $fields = $model->getFields(DISCUSS_CUSTOMFIELDS_ACL_INPUT, $operation, $this->post->id);
        $isValid = true;

        foreach ($fields as $field) {

            if ($field->required) {

                if (is_array($this->fields) && (!isset($this->fields[$field->id]) || empty($this->fields[$field->id]))) {
                    $isValid = false;
                }

                if (is_object($this->fields) && (!isset($this->fields->{$field->id}) || empty($this->fields->{$field->id}))) {
                    $isValid = false;
                }
            }
        }

        if (!$isValid) {
            $this->setError(JText::_('COM_EASYDISCUSS_FIELDS_REQUIRED_FIELDS_NOT_PROVIDED'));
            return false;
        }

        return true;
    }

    /**
     * Combine question and reply validates together
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function validate($data = array(), $operation = null)
    {
        // Perform captcha validation
        if (!$this->validateCaptcha($data)) {
            return false;
        }

        // Check the validate for the custom field which set as required
        if (!$this->validateFields($operation)){
            return false;
        }

        // check the validate for the reply and question
        if ($this->isQuestion()) {

            $askState = $this->validateQuestion($data);

            // if one of the validate is fail just return out the error message
            if (!$askState) {
                return $askState;
            }

            return true;

        }elseif($this->isReply()) {
            $replyState = $this->validateReply($data);

            return $replyState;
        }

        return true;
    }

    /**
     * Akismet Validation
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function akismet()
    {
        // load akismet lib
        ED::akismet();


        $properties = array('title', 'content');
        foreach ($properties as $property) {

            $akismet = new Akismet(DISCUSS_JURIROOT, $this->config->get('antispam_akismet_key'), array(
                                'author' => $this->my->name,
                                'email' => $this->my->email,
                                'website' => DISCUSS_JURIROOT,
                                'body' => urlencode($this->post->$property),
                                'alias' => ''
                                ));

            // Detect if there's any errors in Akismet.
            if (!$akismet->errorsExist() && $akismet->isSpam()) {
                return false;
            }
        }

        return true;
    }


    /**
     * Determines if this post can be accepted as an answer for a discussion
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canAcceptAsAnswer()
    {
        if (!$this->config->get('main_qna')) {
            return false;
        }

        // This is only applicable for replies
        if ($this->isQuestion() || $this->isResolved()) {
            return false;
        }

        // If this is a reply, ensure that the question isn't resolved yet
        if ($this->isReply()) {
            $question = $this->getParent();

            if ($question->isResolved() && !$this->post->answered) {
                return false;
            }
        }


        // Locked items cannot be further modified
        if ($this->isLocked()) {
            return false;
        }

        if (ED::isSiteAdmin()) {
            return true;
        }

        if (ED::isModerator($this->post->category_id)) {
            return true;
        }

        // Get the question
        $question = ED::post($this->post->parent_id);

        if ($this->acl->allowed('mark_answered')) {
            return true;
        }

        // Current user is the owner of question
        if ($this->my->id == $question->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determines if the provided user can assign moderators for this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canAssign($userId = null)
    {
        static $items = array();

        $user = ED::user($userId);

        if (!$user->id) {
            return false;
        }

        $key = $user->id . $this->post->id;

        if (isset($items[$key])) {
            return $items[$key];
        }

        $access = $this->getAccess();

        $items[$key] = $access->canAssign();

        return $items[$key];
    }

    /**
     * Determines if the provided user can view this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canView($viewerId = null)
    {
        $my = JFactory::getUser($viewerId);
        $isModerator = ED::isModerator($this->post->category_id, $my->id);

        // If this post doesn't have an id, it cannot be viewed.
        if (!$this->post->id) {
            return false;
        }

        // For site admins, they can always view everything
        if (ED::isSiteAdmin()) {
            return true;
        }

        // If the post is private it shouldn't be viewable by anyone else.
        if ($this->post->private && $my->id != $this->post->user_id && !$isModerator) {
            return false;
        }

        // Ensure that the user is allowed to access post from this category.
        $category = $this->getCategory();

        if (!$category->canAccess()) {
            return false;
        }

        // Check whether this is a valid discussion
        if ($this->post->parent_id != 0 || ($this->post->published == DISCUSS_ID_PENDING && ($this->post->user_id != $this->my->id))) {
            return false;
        }

        // Check if discussion is under moderation
        if ($this->isPending() && !ED::isModerator($this->post->category_id, $this->my->id)) {
            return false;
        }

        return true;
    }

    /**
     * Determines if the current viewer can like the post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canLike()
    {
        // If the user is not logged in they should not be able to
        if (!$this->my->id) {
            return false;
        }

        if ($this->isReply() && !$this->config->get('main_likes_replies')) {
            return false;
        }

        if ($this->isQuestion() && !$this->config->get('main_likes_discussions')) {
            return false;
        }

        if (ED::isSiteAdmin()) {
            return true;
        }

        if (ED::isModerator($this->post->category_id)) {
            return true;
        }

        return true;
    }

    /**
     * Determines if the current viewer can like the post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canFav()
    {
        // If the user is not logged in they should not be able to
        if (!$this->my->id) {
            return false;
        }

        if ($this->isQuestion() && !$this->config->get('main_favorite')) {
            return false;
        }

        if ($this->isReply()) {
            return false;
        }

        if (ED::isSiteAdmin()) {
            return true;
        }

        if (ED::isModerator($this->post->category_id)) {
            return true;
        }

        return true;
    }

    /**
     * Determines if the current user is allowed to lock the post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canLock()
    {
        if (!$this->isQuestion()) {
            return false;
        }

        if ($this->acl->allowed('lock_discussion')) {
            return true;
        }

        if (ED::isSiteAdmin()) {
            return true;
        }

        if (ED::isModerator($this->post->category_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determines whether this post has status or not
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function hasStatus()
    {
        return $this->isPostRejected() || $this->isPostOnhold() || $this->isPostAccepted() || $this->isPostWorkingOn();
    }
    /**
     * Determines if the user can set a status of a post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canSetStatus($status)
    {
        if (!$this->isQuestion()) {
            return false;
        }

        if (ED::isSiteAdmin()) {
            return true;
        }

        if (ED::isModerator($this->post->category_id)) {
            return true;
        }

        if ($status == 'hold' && $this->acl->allowed('mark_on_hold')) {
            return true;
        }

        if ($status == 'accepted' && $this->acl->allowed('mark_accepted')) {
            return true;
        }

        if ($status == 'working' && $this->acl->allowed('mark_working_on')) {
            return true;
        }

        if ($status == 'rejected' && $this->acl->allowed('mark_reject')) {
            return true;
        }

        if ($status == 'none' && $this->acl->allowed('mark_no_status')) {
            return true;
        }

        return false;
    }

    /**
     * Determines if the current user is allowed to lock the polls
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canLockPolls()
    {
        if (!$this->config->get('main_polls_lock')) {
            return false;
        }

        // If the post doesn't have any polls, no point showing this
        if (!$this->hasPolls()) {
            return false;
        }

        // If the user is the owner of the item they should be allowed
        if ($this->my->id == $this->post->user_id) {
            return true;
        }

        // If the user is a site admin or moderator, we should allow this
        if (ED::isSiteAdmin() || ED::isModerator($this->post->category_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determines if the user can unlock the post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canUnlock()
    {
        // if the user can lock means he also can unlock
        return $this->canLock();
    }

    /**
     * Determines if the author of this post can be banned
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function canBanAuthor()
    {
        if (!$this->my->id) {
            return;
        }

        // If the user is viewing his own post, disallow this
        if ($this->my->id == $this->post->user_id) {
            return false;
        }

        // If banning is disabled, do not allow
        if (!$this->config->get('main_ban')) {
            return false;
        }

        if (ED::isSiteAdmin()) {
            return true;
        }

        if (ED::isModerator($this->post->category_id)) {
            return true;
        }

        if ($this->acl->allowed('ban_user')) {
            return true;
        }

        return false;
    }

    /**
     * Determines if this post has any replies
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function hasReplies()
    {
        static $replies = array();

        $key = $this->post->id;

        if (!isset($replies[$key])) {
            $replies[$key] = $this->getTotalReplies() > 0;
        }

        return $replies[$key];
    }

    /**
     * Determines if this oist has any comments
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function hasComments()
    {
        static $comments = array();

        $key = $this->post->id;

        if (!isset($comments[$key])) {
            $comments[$key] = $this->getTotalComments() > 0;
        }

        return $comments[$key];
    }

    /**
     * Determines if the user voted on this post before
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function hasVoted($userId = null)
    {
        static $items = array();

        $user = ED::user($userId);

        if (!$user->id) {
            return false;
        }

        if (isset($items[$user->id])) {
            return $items[$user->id];
        }

        $model = ED::model('Votes');
        $items[$user->id] = $model->hasVoted($this->post->id, $user->id);

        return $items[$user->id];
    }

    /**
     * Determines if the post has location
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function hasLocation()
    {
        if (!$this->post->latitude || !$this->post->longitude || !$this->post->address) {
            return false;
        }

        return true;
    }

    /**
     * Get polls associated with this post
     *
     * @alternative for ->polls
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function hasPolls()
    {
        static $items = array();

        $key = $this->post->id;

        if (!isset($items[$key])) {

            if (isset($this->post->polls_cnt)) {
                $items[$key] = $this->post->polls_cnt;
            } else {
                $model = ED::model('Posts');
                $items[$key] = $model->hasPolls($this->post->id);
            }
        }

        return $items[$key];
    }

    /**
     * Retrieves a list of attachments for this post
     *
     * @alternative for ->attachments
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function hasAttachments()
    {
        static $items = array();

        $key = $this->post->id;

        if (!isset($items[$key])) {

            $model = ED::model('Posts');

            if (isset($this->post->attachments_cnt)) {
                $items[$key] = $this->post->attachments_cnt;
            } else {
                $items[$key] = $model->hasAttachments($this->post->id, DISCUSS_QUESTION_TYPE);
            }
        }

        return $items[$key];
    }

    /**
     * Determines if this post is locked
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isLocked()
    {
        return $this->islock;
    }

    /**
     * Determines if this post is resolved
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isResolved()
    {
        return $this->isresolve;
    }

    /**
     * Determines if this post is featured
     *
     * @alternative for ->isFeatured
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isFeatured()
    {
        static $items = array();

        $key = $this->post->id;

        if (!isset($items[$key])) {
            $items[$key] = $this->post->featured;
        }

        return $items[$key];
    }

    /**
     * Determines if this post is an answer
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isAnswer()
    {
        if ($this->isQuestion()) {
            return false;
        }

        if ($this->post->answered) {
            return true;
        }

        return false;
    }

    /**
     * Determines if this post is an anonymous post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isAnonymous()
    {
        if (!$this->config->get('main_anonymous_posting')) {
            return false;
        }

        return $this->anonymous;
    }

    /**
     * Determines if the current user read the forum post before
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isSeen($userId = null)
    {
        $items = array();
        $user = ED::user($userId);

        // Construct the key
        $key = $user->id . $this->post->id;

        // Default is unseen for guests.
        if (!$user->id) {
            return false;
        }

        if (!isset($items[$key])) {

            $items[$key] = ($this->post->legacy || $user->isRead($this->post->id)) ? true : false;
        }

        return $items[$key];
    }

    /**
     * Determines if the post is still within the new duration
     *
     * @alternative for previous ->isnew
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isStillNew()
    {
        static $items = array();

        $key = $this->post->id;

        if (!isset($items[$key])) {
            $items[$key] = ED::isNew($this->noofdays);
        }

        return $items[$key];
    }

    /**
     * Retrieves the post suffix
     *
     * @alternative for ->post_type_suffix
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getTypeSuffix()
    {
        return $this->post_type_suffix;
    }

    /**
     * Retrieve the post type's title
     *
     * @alternative for ->post_type_title
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getTypeTitle()
    {
        return $this->post_type_title;
    }

    /**
     * Retrieves post assignments
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getAssignment()
    {
        static $items = array();

        // TODO: Need to refactor this again for caching.

        // if (!isset($items[$this->post->id])) {

        //     $assignment = ED::table('PostAssignment');
        //     $assignment->load($this->post->id);

        //     $items[$this->post->id] = $assignment;
        // }

        // return $items[$this->post->id];
        $assignment = ED::table('PostAssignment');
        $assignment->load($this->post->id);
        $this->assignment = $assignment;

        $assignee = ED::user($assignment->assignee_id);
        $this->assignee = $assignee;

        return $this->assignment;
    }

    /**
     * Retrieves the category object for the post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getCategory()
    {
        $categories = array();

        $key = $this->post->id;

        if (!isset($categories[$key])) {
            $category = ED::category($this->post->category_id);

            $categories[$key] = $category;
        }

        return $categories[$key];
    }

    /**
     * Retrieves url references for this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getReferences()
    {
        static $items = array();

        $key = $this->post->id;

        if (isset($items[$key])) {
            return $items[$key];
        }

        $pattern = '/params_references[0-9]=(.*)/i';
        preg_match_all($pattern, $this->post->params, $matches);

        $items[$key] = array();

        if (!empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $reference = JString::str_ireplace('"', '', $match);
                $reference = ED::string()->normalizeProtocol($reference);

                $items[$key][] = $reference;
            }
        }

        return $items[$key];
    }

    /**
     * Retrieves the custom fields for this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getCustomFields()
    {
        $model = ED::model('CustomFields');
        $fields = $model->getViewableFields($this->post->id);

        return $fields;
    }

    /**
     * Retrieves the post priority
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getPriority()
    {
        static $items = array();

        if (!$this->post->priority) {
            return false;
        }

        if (!isset($items[$this->post->id])) {
            $item = ED::table('Priority');
            $item->load($this->post->priority);

            $items[$this->post->id] = $item;
        }

        return $items[$this->post->id];
    }

    /**
     * Get the post's item type
     *
     * @alternative for ->itemtype
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getType()
    {
        if (isset($this->post->itemtype)) {
            return $this->post->itemtype;
        }

        return false;
    }

    /**
     * Retrieves the duration string of the post
     *
     * @alternative for previous ->duration
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getDuration()
    {
        static $duration = array();

        $key = $this->post->id;

        if (!isset($duration[$key])) {
            $diff = new stdClass();

            $diff->daydiff = $this->daydiff;
            $diff->timediff = $this->timediff;

            $duration[$key] = ED::getDurationString($diff);
        }

        return $duration[$key];
    }

    /**
     * Generates the permalink for the post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
     public function getDiscussionContribution()
     {
        if (!$this->cluster_id && !$this->cluster_type) {
            return false;
        }

        $contribution = new stdClass();
        $contribution->id = $this->cluster_id;
        $contribution->type = $this->cluster_type;

        return $contribution;
     }    

    /**
     * Generates the permalink for the post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getPermalink($external = false, $xhtml = true, $json = false)
    {
        static $links = array();

        $key = $this->post->id . $xhtml . $external . $json;

        if (!isset($links[$key])) {

            $question = $this;

            // If this is a reply, we need to get the correct url
            if ($this->isReply()) {
                $question = $this->getParent();
            }

            if ($external) {
                $links[$key] = EDR::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $question->id, false, true);
            } else {
                $links[$key] = EDR::_('view=post&id=' . $question->id, $xhtml);
            }

            if ($this->isReply()) {
                $links[$key] .= '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $this->post->id;
            }

            if ($json) {
                $links[$key] = $links[$key] . '?format=json';
            }
        }

        return $links[$key];
    }

    /**
     * Get a list of replies for this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getReplies($checkAcl = true, $limit = null, $sort = 'oldest', $limitstart = 0)
    {
        $default = array();
        $pagination = $this->config->get('layout_replies_pagination');

        // If this is not a question, we shouldn't if any replies
        if (!$this->isQuestion()) {
            return $default;
        }

        static $items = array();

        if (isset($items[$this->post->id])) {
            return $items[$this->post->id];
        }

        // We also need to check if the viewer can view replies from this category
        $category = $this->getCategory();

        if ($checkAcl && !$category->canViewReplies()) {
            return $default;
        }

        // Get replies
        $model = ED::model('Posts');

        // Get total number of replies
        $total = $this->getTotalReplies();

        // Get the replies
        $replies = $model->getReplies($this->post->id, $sort, $limitstart, $limit, $pagination);

        if ($replies) {
            $replies = ED::formatReplies($replies, $category, $pagination);
        }

        return $replies;
    }

    /**
     * Get the last person that replies to this discussion
     *
     * @alternative for ->reply
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getLastReplier()
    {
        static $_cache = array();

        $key = $this->post->id;

        if (! isset($_cache[$key])) {

            // Get the total number of replies
            $totalReplies = $this->getTotalReplies();

            if (!$totalReplies) {
                $_cache[$key] = '0';
                return;
            }

            // Get the last reply item
            $model = ED::model('Posts');
            $reply = $model->getLastReply($this->post->id);

            if (!$reply) {
                $_cache[$key] = '0';
                return;
            }

            $post = ED::post($reply);

            // Get the user that last replied
            // $user = ED::user($reply->user_id);
            // $user->poster_name = $reply->user_id ? $user->getName() : $reply->poster_name;
            // $user->poster_email = $reply->user_id ? $user->user->email : $reply->poster_email;

            $user = $post->getOwner();

            $_cache[$key] = $user;

        }

        return $_cache[$key];
    }

    /**
     * Method to check if the last reply is anonymous reply
     *
     * @alternative for ->reply
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isLastReplyAnonymous()
    {
        static $_cache = array();

        $key = $this->post->id;

        if (!isset($_cache[$key])) {

            // Get the total number of replies
            $totalReplies = $this->getTotalReplies();

            if (!$totalReplies) {
                $_cache[$key] = '0';
                return;
            }

            // Get the last reply item
            $model = ED::model('Posts');
            $reply = $model->getLastReply($this->post->id);

            if (!$reply) {
                $_cache[$key] = '0';
                return;
            }

            $_cache[$key] = $reply->anonymous;
        }

        return $_cache[$key];
    }

    /**
     * Get the number of favorites for this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getTotalFavorites()
    {
        static $favorites = array();

        $key = $this->post->id;

        if (!isset($favorites[$key])) {

            if (isset($this->totalFavourites)) {
                $favorites[$key] = $this->totalFavourites;
            } else {
                $model = ED::model("Favourites");
                $favorites[$key] = $model->getFavouritesCount($this->post->id);
            }
        }

        return $favorites[$key];
    }

    /**
     * Get tags associated with this post
     *
     * @alternative for ->tags
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getTags()
    {
        static $items = array();

        $key = $this->post->id;

        if (!isset($items[$key])) {

            $model = ED::model('PostsTags');
            $items[$key] = $model->getPostTags($this->post->id);
        }

        return $items[$key];
    }

    /**
     * Retrieves a list of attachments for this post
     *
     * @alternative for ->attachments
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getAttachments()
    {
        static $items = array();

        $key = $this->post->id;

        if (isset($items[$key])) {
            return $items[$key];
        }

        $model = ED::model('Attachments');
        $items[$key] = $model->getPostAttachments($this->post->id, $this->getType());

        return $items[$key];
    }

    /**
     * Retrieves the html code for the like authors.
     *
     * @since   4.0
     * @access  public
     * @param   int     The user's id.
     */
    public function isLikedBy($userId)
    {
        if (!$userId) {
            return false;
        }

        // TODO: Need to refactor this again for caching.
        // $key = $this->id . $userId;

        // if( !isset( self::$_likes[ $key ] ) )
        // {
        //     $model      = ED::model( 'Likes' );
        //     self::$_likes[ $key ]       = $model->isLike( 'post' , $this->id , $userId );
        // }

        static $_data = array();

        $keys = $this->post->id . $userId;

        if (!isset($_data[$keys])) {
            $model = ED::model('Likes');
            $_data[$keys] = $model->isLike('post', $this->post->id, $userId);
        }

        return $_data[$keys];
    }

    /**
     * Retrieves a list of favourite for this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isFavBy($userId)
    {
        static $loaded = null;

        if (!isset($loaded)) {
            $model = ED::model('Favourites');

            // Check to see is it favourited?
            $loaded = $model->isFav($this->post->id, $userId);
        }

        return $loaded;
    }

    /**
     * Retrieves the total favourite count for this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getMyFavCount()
    {
        $model = ED::model('Favourites');
        $result = $model->getFavouritesCount($this->post->id);

        return $result;
    }

    /**
     * Retrieves the parent post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getParent()
    {
        static $posts = array();

        if (!isset($posts[$this->post->parent_id])) {
            $posts[$this->post->parent_id] = ED::post($this->post->parent_id);
        }

        return $posts[$this->post->parent_id];
    }

    /**
     * Retrieves a list of comments for this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getComments($limit = null, $limitstart = null)
    {
        $default = array();

        static $items = array();

        if (isset($items[$this->post->id])) {
            return $items[$this->post->id];
        }

        $model = ED::model('Posts');
        $comments = $model->getComments($this->post->id, $limit, $limitstart);
        $items[$this->post->id] = ED::formatComments($comments);

        return $items[$this->post->id];
    }

    /**
     * Retrieves the total number of comments for this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getTotalComments()
    {
        static $items = array();

        $key = $this->post->id;

        if (isset($items[$key])) {
            return $items[$key];
        }

        $model = ED::model('Posts');
        $total = $model->getTotalComments($this->post->id);

        $items[$key] = $total;

        return $items[$key];
    }

    /**
     * Retrieves the access for this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getAccess()
    {
        static $items = array();

        if (!isset($items[$this->post->id])) {

            $category = $this->getCategory();

            $options = array($this, $category);

            $items[$this->post->id] = ED::postaccess($options);
        }

        return $items[$this->post->id];
    }

    /**
     * Get the total number of replies to a discussion
     *
     * @alternative for ->totalreplies
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getTotalReplies()
    {
        static $items = array();

        if (!isset($items[$this->post->id])) {

            $items[$this->post->id] = 0;

            if (isset($this->post->num_replies)) {
                $items[$this->post->id] = $this->post->num_replies;
            } else {
                $model = ED::model('Posts');
                $items[$this->post->id] = $model->getTotalReplies($this->post->id);
            }
        }

        return $items[$this->post->id];
    }

    /**
     * Gets the total hits for this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * Get the total number of votes to a discussion
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getTotalVotes()
    {
        $votes = array();

        if (!isset($votes[$this->post->id])) {
            if (isset($this->total_vote_cnt)) {
                $votes[$this->post->id] = $this->total_vote_cnt;
            } else {
                $model = ED::model('votes');
                $votes[$this->post->id] = $model->getTotalVotes($this->post->id);
            }
        }

        return $votes[$this->post->id];
    }

    /**
     * Retrieves the total number of likes for this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getTotalLikes()
    {
        $likes = ED::model('likes')->getTotalLikes($this->post->id);

        return $likes;
    }
    /**
     * Get the voters to a discussion
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getVoters($limit = '5')
    {
        $model = ED::model('votes');
        return $model->getVoters($this->post->id);
    }

    /**
     * Gets the category title (text only)
     *
     * @alternative for ->category
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getCategoryTitle()
    {
        $categoryTitle = JText::_($this->category);

        return $categoryTitle;
    }

    /**
     * Retrieves the title of the post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getTitle()
    {
        static $titles = array();

        if (!isset($titles[$this->post->id])) {

            // Apply badword filtering
            $titles[$this->post->id] = ED::badwords()->filter($this->post->title);
        }

        return $titles[$this->post->id];
    }

    /**
     * Retrieves the password protect form to allow users to enter password
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getProtectedContent($type = 'intro')
    {
        $theme = ED::themes();

        $theme->set('post', $this->post);
        $theme->set('type', $type);

        $output = $theme->output('site/posts/password.form');

        return $output;
    }

    /**
     *
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */

    /**
     * Retrieves the intro text portion of a post
     *
     * @alternative for ->introtext
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getIntro()
    {
        static $contents = array();

        if (!isset($contents[$this->post->id])) {

            // If this post is password protected, we need to display the form to enter password
            if ($this->isProtected() && !ED::isSiteAdmin() && $this->my->id != $this->user_id) {
                $contents[$this->post->id] = $this->getProtectedContent('intro');
            } else {

                // Apply badwords filter
                $content = ED::badwords()->filter($this->post->content);

                // Remove codes from the content
                $content = ED::parser()->removeCodes($content);

                // Format the content
                $content = ED::formatContent($this->post);

                // Remove html tags since this is in the intro view.
                $content = strip_tags($content);

                // Truncate the content
                $content = JString::substr($content, 0, $this->config->get('layout_introtextlength')) . JText::_('COM_EASYDISCUSS_ELLIPSES');

                $contents[$this->post->id] = $content;
            }
        }

        return $contents[$this->post->id];
    }

    /**
     * Retrieves the intro text of a post
     *
     * @alternative for ->content
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getContent($debug = false)
    {
        $content = '';

        // Determines if we should trigger the contents
        if ($this->config->get('main_content_trigger_posts')) {

            // We need to keep a copy of raw content so that when calling ED::formatContent(), the returned content might be from the preview column.
            $raw = $this->post->content;

            // Add the br tags in the content, we do it here so that the content triggers's javascript will not get added with br tags
            // here we assign the formatted value into ->content is bcos the trigger is seeing this value.
            $this->post->content = $this->formatContent($debug);
            // $this->post->content = ED::formatContent($this->post);

            $this->events = new stdClass();

            // Triger onContentPrepare here. Since it doesn't have any return value, just ignore this.
            ED::triggerPlugins('content', 'onContentPrepare', $this->post);

            $this->events->afterDisplayTtle = ED::triggerPlugins('content', 'onContentAfterTitle', $this->post, true);
            $this->events->beforeDisplayContent = ED::triggerPlugins('content', 'onContentBeforeDisplay' , $this->post, true );
            $this->events->afterDisplayContent = ED::triggerPlugins('content', 'onContentAfterDisplay', $this->post, true);

            // Assign the processed content back
            $content = $this->post->content;

            // revert back the raw content into post->content;
            $this->post->content = $raw;
        } else {

            $content = ED::badwords()->filter($this->post->preview);
            $content = $this->formatContent($debug);
            // $content = ED::formatContent($this->post);

            //debug code:
            // $content = ED::formatContent($this->post);
        }

        // var_dump($content);exit;

        return $content;
    }

    /**
     * Retrieves the content type of this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getContentType()
    {
        return $this->post->content_type;
    }

    /**
     * Retrieves the owner of the post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getOwner()
    {
        static $owners = array();

        // The key needs to be the post id if this is a guest
        // $key = !$this->post->user_id ? $this->post->id : $this->post->user_id;

        $key = $this->post->id;

        if (!isset($owners[$key])) {

            if ($this->post->user_id) {

                // var_dump($this->post->anonymous && ($this->post->user_id != $this->my->id || !ED::isSiteAdmin()));

                $showAsAnonymous = ($this->post->anonymous && ($this->post->user_id != $this->my->id || !ED::isSiteAdmin())) ? true : false;

                if ($showAsAnonymous) {
                    $user = ED::user('0');
                    $user->name = JText::_('COM_EASYDISCUSS_ANONYMOUS_USER');
                } else {
                    $user = ED::user($this->post->user_id);
                }
            }

            if (!$this->post->user_id) {
                $user = ED::user('0');

                $user->name = $this->post->poster_name;
            }

            $owners[$key] = $user;
        }

        return $owners[$key];
    }

    /**
     * Retrieves the status class of this post
     *
     * @since   1.0
     * @access  public
     * @return  string
     */
    public function getStatusClass()
    {
        if ($this->post->post_status == 1) {
            return '-on-hold';
        }

        if ($this->post->post_status == 2) {
            return '-accepted';
        }

        if ($this->post->post_status == 3) {
            return '-working-on';
        }

        if ($this->post->post_status == 4) {
            return '-reject';
        }

        return;
    }

    /**
     * Retrieves the status message of the post.
     *
     * @since   1.0
     * @access  public
     * @return  string
     */
    public function getStatusMessage()
    {
        if ($this->post->post_status == 1) {
            return JText::_('COM_EASYDISCUSS_POST_STATUS_ON_HOLD');
        }

        if ($this->post->post_status == 2) {
            return JText::_('COM_EASYDISCUSS_POST_STATUS_ACCEPTED');
        }

        if ($this->post->post_status == 3) {
            return JText::_('COM_EASYDISCUSS_POST_STATUS_WORKING_ON');
        }

        if ($this->post->post_status == 4) {
            return JText::_('COM_EASYDISCUSS_POST_STATUS_REJECT');
        }

        return;
    }

    /**
     * Allows caller to set the status of a post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function setStatus($status)
    {
        if ($status == 'hold') {
            return $this->markPostOnHold();
        }

        if ($status == 'accepted') {
            return $this->markPostAccepted();
        }

        if ($status == 'working') {
            return $this->markPostWorkingOn();
        }

        if ($status == 'rejected') {
            return $this->markPostRejected();
        }

        if ($status == 'none') {
            return $this->markPostNoStatus();
        }
    }

    /**
     * Sets an error message
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function setError($message = '')
    {
        $this->error = JText::_($message);
    }

    /**
     * Get an error message
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getError($message = '')
    {
        return $this->error;
    }

    /**
     * Check if has an error message
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function hasError()
    {
        return !empty($this->error);
    }

    /**
     * Gets the type of the post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getPostItemType()
    {
        // incase this attribute is null. we will check against post->parent_id
        if ($this->post->parent_id) {
            return DISCUSS_REPLY_TYPE;
        }


        return DISCUSS_QUESTION_TYPE;
    }

    /**
     * Determines if this post is a question
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isQuestion()
    {
        return $this->getPostItemType() == DISCUSS_QUESTION_TYPE;
    }

    /**
     * Determines if this post is a reply.
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isReply()
    {
        return $this->getPostItemType() == DISCUSS_REPLY_TYPE;
    }

    /**
     * Determines if this post is published
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isPublished()
    {
        return $this->post->published == DISCUSS_ID_PUBLISHED;
    }

    /**
     * Determines if this post is being moderated
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isPending()
    {
        return $this->post->published == DISCUSS_ID_PENDING;
    }

    /**
     * Determines if this post is a private post or not
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isPrivate()
    {
        return $this->post->private == true;
    }


    /**
     * Determines if this post's assignment status is being rejected or not
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isPostRejected()
    {
        return $this->post->post_status == DISCUSS_POST_STATUS_REJECT;
    }

    /**
     * Determines if this post's assignment status is being onhold or not
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isPostOnhold()
    {
        return $this->post->post_status == DISCUSS_POST_STATUS_ON_HOLD;
    }

    /**
     * Determines if this post's assignment status is being accepted or not
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isPostAccepted()
    {
        return $this->post->post_status == DISCUSS_POST_STATUS_ACCEPTED;
    }

    /**
     * Determines if this post's assignment status is now working on or not
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isPostWorkingOn()
    {
        return $this->post->post_status == DISCUSS_POST_STATUS_WORKING_ON;
    }

    /**
     * Determines the current user is it get banned or not
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isUserBanned()
    {
        // Check if the user is banned from the site
        $model = ED::model('bans');

        // TODO: get the user IP
        $options = array('ip' => $this->input->server->get('REMOTE_ADDR'), 'userId' => $this->my->id);

        // if the current user do not get banned return false
        if (!$model->isBanned($options)) {
            return false;
        }

        return true;
    }

    /**
     * Determines if the post is password protected.
     *
     * @since   4.0
     * @access  public
     * @return  boolean True if the post is protected.
     */
    public function isProtected()
    {
        // If there is no password or if password protection is disabled.
        if (!$this->config->get('main_password_protection') || !$this->post->password) {
            return false;
        }

        // Detect if user set any values in the session.
        $session = JFactory::getSession();
        $password = $session->get('DISCUSSPASSWORD_' . $this->post->id, '', 'com_easydiscuss');

        // If user has already entered a password on the session, we know they already bypassed this.
        if ($this->post->password == $password) {
            return false;
        }

        return true;
    }

    /**
     * Get the CSS class suffix for the Post Type
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getPostTypeSuffix()
    {
        if (isset($this->post_type_suffix)) {
            $suffix = $this->post_type_suffix;
        } else {
            $model = ED::model('Posttypes');
            $suffix = $model->getSuffix($this->post->post_type);
        }

        return $suffix;
    }

    /**
     * Get the Post Type
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getPostType()
    {
        if (isset($this->post_type)) {
            $posttype = $this->post_type;
        } else {
            $model = ED::model('Posttypes');
            $posttype = $model->getTitle($this->post->post_type);
        }

        return $posttype;
    }

    /**
     * Shares on slack chat
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function slack()
    {
        $slack = ED::slack();
        return $slack->share($this);
    }

    /**
     * Performs a post to telegram
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function telegram()
    {
        $telegram = ED::telegram();
        return $telegram->share($this);
    }

    /**
     * Maps existing data back to the table
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function toData()
    {
        // Convert the table to an array
        $data = new stdClass();

        $data->id = $this->post->id;
        $data->permalink = $this->getPermalink(true, false, true);
        $data->title = $this->post->title;
        $data->user_id = $this->post->user_id;
        $data->hits = $this->post->hits;
        $data->vote = $this->post->vote;
        $data->state = $this->post->published;
        $data->locked = $this->isLocked();
        $data->created = $this->post->created;
        $data->modified = $this->post->modified;
        $data->content = $this->post->content;
        $data->preview = $this->post->preview;

        // Get the replies and format them
        $data->replies = array();

        $items = $this->getReplies();

        if ($items) {
            foreach ($items as $item) {
                $data->replies[] = $item->toData();
            }
        }

        return $data;
    }

    /**
     * Pre process the post before we save it.
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function preSave()
    {
        // @TODO: We need to convert the bbcode contents into html and store it on the "preview" column

        if ($this->isNew()) {
            // Set all post to be published by default.
            $this->post->published = DISCUSS_ID_PUBLISHED;
        }

        // Detect if post should be moderated.
        $isAdmin = ED::isSiteAdmin();
        $isModerator = ED::isModerator($this->post->category_id);

        // Moderate all posts
        if ($this->config->get('main_moderatepost') && !$isAdmin && !$isModerator) {
            $this->post->published = DISCUSS_ID_PENDING;
            $this->isModerator = true;
        }

        // Determines if the user should still be moderated
        $user = ED::user();

        if ($user->moderateUsersPost() && !$isAdmin && !$isModerator) {
            $this->post->published = DISCUSS_ID_PENDING;
            $this->isModerate = true;
        }

        $post_type = 'post';

        if ($this->isReply()) {
            $post_type = 'reply';
        }

        // @trigger: onBeforeSave
        ED::events()->importPlugin('content');
        ED::events()->onContentBeforeSave($post_type, $this->post, $this->isNew());
    }

    /**
     * Bind url references
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function addReference()
    {
        // API: References.
        $reference = $this->input->get('reference' , '', 'word');
        $referenceId = $this->input->get('reference_id', 0, 'int');

        if (!empty($reference) && !empty($referenceId)) {
            $referenceTable = ED::table('PostsReference');
            $referenceTable->extension = $reference;
            $referenceTable->post_id= $this->post->id;
            $referenceTable->reference_id = $referenceId;

            $referenceTable->store();
        }
    }

    /**
     * Process custom fields that are submitted
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function processCustomFields()
    {
        // Clear off previous records before storing
        $model = ED::model('CustomFields');
        $model->deleteCustomFieldsValue($this->post->id);

        // Process custom fields.
        $fields = $this->input->get('fields', array(), 'default');

        if (!$fields) {
            return false;
        }

        foreach ($fields as $id => $value) {
            // If the value is empty, we'll skip them
            if (!$value) {
                continue;
            }

            // If the value is an array, we need to serialize it
            if (is_array($value)) {
                $value = serialize($value);
            }

            $table = ED::table('CustomFieldsValue');
            $table->field_id = (int) $id;
            $table->value = $value;
            $table->post_id = $this->post->id;

            $table->store();
        }

        return true;
    }

    /**
     * Binds attachment items for this post.
     *
     * @since   4.0
     * @access  public
     *
     */
    public function bindAttachments()
    {
        if (!$this->config->get('attachment_questions')) {
            return false;
        }

        $files = JRequest::getVar('filedata', array(), 'FILES');

        // If there is no attachment, don't continue
        if (!$files) {
            return false;
        }

        $total = count($files['name']);

        // Handle empty files.
        if (!$files['name'][0] || $total < 1) {
            return false;
        }

        // Load up attachment
        for ($i = 0; $i < $total; $i++) {
            $file = array();

            if (!$files['tmp_name'][$i]) {
                continue;
            }

            $file['name'] = $files['name'][$i];
            $file['type'] = $files['type'][$i];
            $file['tmp_name'] = $files['tmp_name'][$i];
            $file['error'] = $files['error'][$i];
            $file['size'] = $files['size'][$i];

            // Upload an attachment
            $attachment = ED::attachment();
            $attachment->upload($this, $file);
        }
        return true;
    }

    /**
     * Post saving method happens after a post is stored on the table.
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function postSave()
    {
        // Add url references for the post
        $this->addReference();

        // After the post is stored in the table, we need to process the custom fields
        $this->processCustomFields();

        $post_type = 'post';

        if ($this->isReply()) {
            $post_type = 'reply';
        }

        // @trigger: onAfterSave
        ED::events()->onContentAfterSave($post_type, $this->post, $this->isNew());

        // The category_id for the replies should change too
        if (!$this->isReply()) {
            $this->moveReplies($this->post->id, $this->post->category_id);
        }

        // Process poll items.
        if ($this->config->get('main_polls')) {
            $this->bindPolls();
        }

        // Bind uploaded attachments
        if ($this->acl->allowed('add_attachment') && $this->config->get('attachment_questions')) {
            $this->bindAttachments();
        }

        // now we need to save / update thread here
        $this->saveThread();
    }

    /**
     * Save the thread accordingly
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function saveThread()
    {

        $isNew = $this->isNew();

        $thread = ED::table('Thread');

        if (! $isNew) {
            $thread->load($this->thread_id);

            if ($this->isQuestion()) {
                // update the thread content and maybe title
                $data = get_object_vars ($this->post);

                // now need to clear unnessary data.
                unset($data['id']);
                unset($data['vote']); // it seems like we dont update this column in post table. so manuall calculation is needed in thread.
                unset($data['parent_id']);
                unset($data['ip']);
                unset($data['thread_id']);

                $thread->bind($data);
                $thread->store();

            } else {
                //this is a reply. we might be updating the isresolved or isanswer state.
                // for now do nothing. will come back to this part later.
                //
            }


        } else {
            if ($this->isQuestion()) {
                // create new thread
                $data = get_object_vars ($this->post);

                //unset unnessary item
                unset($data['id']);
                unset($data['ip']);
                unset($data['parent_id']);
                unset($data['thread_id']);
                unset($data['vote']); // it seems like we dont update this column in post table. so manuall calculation is needed in thread.

                $thread->bind($data);
                $thread->post_id = $this->post->id;

                // need to check if this question has polls or not.
                $thread->has_polls = $this->hasPolls();


                // need to check if this question has attachments or not
                $attachments = $this->getAttachments();
                $thread->num_attachments = count($attachments);

                $thread->store();

                // we need to update the post table for the thread id
                $thread->updatePostThreadId($this->post->id);

            } else {
                // update thread last_user_id and last update date
                $thread->load(array('post_id' => $this->post->parent_id));

                $thread->last_user_id = $this->post->user_id;
                $thread->last_poster_name = $this->post->poster_name;
                $thread->last_poster_email = $this->post->poster_email;
                $thread->replied = $this->post->created;

                $thread->num_replies = $thread->num_replies + 1;

                $thread->store();

                // we need to update the post table fpr the thread id
                $thread->updatePostThreadId($this->post->id);

            }
        }

    }

    /**
     * Bind poll items to the post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function bindPolls()
    {
        // Ensure that polls are enabled
        if ($this->isQuestion() && !$this->config->get('main_polls')) {
            return false;
        }

        // Ensure that polls are enabled
        if (!$this->isQuestion() && !$this->config->get('main_polls_replies')) {
            return false;
        }

        // Get a list of poll choices
        $items = $this->input->get('pollitems', array(), 'default');

        // Normalize the choices
        if (!is_array($items)) {
            $items = array($items);
        }

        // We need to filter out empty items
        $choices = array();

        foreach ($items as $item) {
            if (!$item) {
                continue;
            }

            $choices[] = $item;
        }

        // If the post is being edited and
        // there is only 1 poll item which is also empty,
        // we need to delete existing polls tied to this post.
        if (!$choices && !$this->isNew()) {
            $this->deletePolls();
        }

        // If nothing to add, skip this
        if (!$choices) {
            return false;
        }

        // Check if the multiple polls checkbox is it checked?
        $multiple = $this->input->get('multiplePolls', 0, 'int');

        // Get the poll question here.
        $question = $this->input->get('poll_question', '', 'default');

        // Try to detect which poll items needs to be removed.
        $remove = $this->input->get('pollsremove', '', 'var');

        // Get the poll items.
        $original = $this->input->get('pollitemsOri', '', 'var');

        // Call the model to create the polls
        $model = ED::model('Polls');
        $model->create($this->post->id, $question, $choices, $remove, $multiple, $original);

        return true;
    }

    /**
     * Sends an auto post request to social networks such as Facebook, Twitter etc.
     *
     * @since   4.0
     * @access  public
     * @param   null
     * @return  boolean True if success, false otherwise.
     */
    public function autopost()
    {
        // If the post is not published, we do not want to auto post this
        if (!$this->isPublished()) {
            return false;
        }

        // Get a list of configured oauth sites.
        $model = ED::model('OAuth');
        $sites = $model->getSites();

        foreach ($sites as $site) {

            // Ensure that the site is enabled.
            $enabled = $this->config->get('main_autopost_' . $site->type);

            if (!$enabled) {
                continue;
            }

            $oauth = ED::table('OAuth');
            $oauth->bind($site);

            // Ensure that there is an access token
            if (!$oauth->access_token) {
                continue;
            }

            // Determine if this discussion is already shared on the social site.
            $shared = $model->isAutoposted($this->post->id, $oauth->id);

            // If it was previously shared, we shouldn't be sending it again
            if ($shared) {
                continue;
            }

            // Get the oauth client
            $client = ED::oauth()->getClient($site->type);
            $client->setAccess($oauth->access_token);
            $state = $client->share($this);

            // When the psot is shared we need to keep a record of this to prevent from sending duplicate updates.
            $history = ED::table('OAuthPosts');
            $history->post_id = $this->post->id;
            $history->oauth_id = $oauth->id;
            $history->store();
        }
    }

    /**
     * filter the content length
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function trimEmail($content)
    {
        $config = ED::getConfig();

        if ($config->get('layout_editor') != 'bbcode') {

            $filterHtmlTag = '<p><div><table><tr><td><thead><tbody><br><br />';

            // Remove html + img tags
            $content = strip_tags($content, $filterHtmlTag);

            return $content;
        }

        if ($config->get('main_notification_max_length') > '0') {

            $content = $this->truncateContentByLength($content, '0', $config->get('main_notification_max_length'));
        }

        // Remove video codes from the e-mail since it will not appear on e-mails
        $content = ED::videos()->strip($content);

        return $content;
    }

    /**
     * email content truncation
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function truncateContentByLength($content, $start, $length)
    {
        // By default $start = 0 means start counting from the beginning of the given string until the given $length
        $append = '...';
        $content = substr($content, $start, $length);
        $content = $content . $append;

        return $content;
    }

    /**
     * Notify user that has been mentioned in post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function notifyNames()
    {
        // Detect known names in the post.
        $users = ED::string()->detectNames($this->post->content, array($this->post->user_id));

        if (!$users) {
            return false;
        }

        $question = $this;

        if ($this->isReply()) {
            $question = $this->getParent();
        }

        // Notify all the names
        foreach ($users as $user) {

            $notification = ED::table('Notifications');
            $notification->bind(array(
                                        'title' => JText::sprintf('COM_EASYDISCUSS_MENTIONED_QUESTION_NOTIFICATION_TITLE', $question->getTitle()),
                                        'cid' => $this->post->id,
                                        'type' => DISCUSS_NOTIFICATIONS_MENTIONED,
                                        'target' => $user->id,
                                        'author' => $this->post->user_id,
                                        'permalink' => $this->getPermalink(true, false)
                                    )
            );

            $notification->store();
        }

        return true;
    }

    /**
     * Notify action when the user submit reply
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function replyNotify()
    {
        if (!$this->isReply()) {
            return;
        }

        // Get current submit reply/comment user id
        $reply = ED::user($this->my->id);


        $owner = $this->getOwner();

        $excludeEmails = array();

        $model = ED::model('Posts');

        $question = ED::post($this->post->parent_id);

        if (!$this->post->title) {
            $this->post->title = $question->title;
        }

        $emailData = array();

        // send notification to all comment subscribers that want to receive notification immediately
        $attachments = $this->getAttachments();
        $emailData['attachments'] = $attachments;
        $emailData['postTitle'] = $this->post->title;
        $emailData['comment'] = ED::parseContent($this->post->content);
        $emailData['commentAuthor'] = $owner->getName($this->post->poster_name);
        $emailData['postLink'] = EDR::getRoutedURL('view=post&id=' . $this->post->id, false, true);

        $emailContent = $this->post->content;

        $isEditing = $this->isNew() == true ? false : true;
        $emailContent = ED::bbcodeHtmlSwitcher($this, 'reply', $isEditing);
        $emailContent = $this->trimEmail($emailContent);

        $emailData['replyContent'] = $emailContent;
        $emailData['replyAuthor'] = $owner->getName($this->post->poster_name);
        $emailData['replyAuthorAvatar'] = $owner->getAvatar();
        $emailData['post_id'] = $this->post->parent_id;
        $emailData['cat_id'] = $this->post->category_id;

        if (($this->config->get('main_sitesubscription') ||  $this->config->get('main_postsubscription')) && $this->config->get('notify_subscriber') && $this->isPublished()) {
            $emailData['emailTemplate'] = 'email.subscription.reply.new.php';
            $emailData['emailSubject'] = JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $this->post->id , $this->post->title);

            $posterEmail = $this->post->poster_email ? $this->post->poster_email : $this->my->email;

            // Get the emails of user who subscribe to this post only
            // This does not send to subscribers whom subscribe to site and category
            $subcribersEmails = ED::mailer()->notifyThreadSubscribers($emailData, array($posterEmail, $this->my->email));

            $excludeEmails[] = $posterEmail;
            $excludeEmails = array_merge($excludeEmails, $subcribersEmails);
            $excludeEmails = array_unique($excludeEmails);
        }

        // notify post owner.
        $postOwnerId = $this->post->user_id;
        $postOwner = ED::user($postOwnerId);
        $ownerEmail = $postOwner->user->email;

        if ($this->post->user_type != 'member') {
            $ownerEmail = $this->post->poster_email;
        }

        // if reply under moderation and current reply user id shouldn't match with post owner user id, then send owner a notification.
        if ($this->config->get('notify_owner') && $this->isPublished() && ($postOwnerId != $this->my->id) && !in_array($ownerEmail, $excludeEmails) && !empty( $ownerEmail)) {
            $emailData['owner_email'] = $ownerEmail;
            $emailData['emailSubject'] = JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $this->post->id , $this->post->title);
            $emailData['emailTemplate'] = 'email.post.reply.new.php';
            ED::mailer()->notifyThreadOwner($emailData);

            // Notify Participants
            $excludeEmails[] = $ownerEmail;
            $excludeEmails = array_unique($excludeEmails);
        }

        // notify participants who reply that post
        if ($this->config->get('notify_participants') && $this->isPublished()) {
            $emailData['post_id'] = $this->post->parent_id;
            $emailData['emailSubject'] = JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $this->post->id , $this->post->title);
            $emailData['emailTemplate'] = 'email.post.reply.new.php';
            ED::mailer()->notifyThreadParticipants($emailData, $excludeEmails);
        }

        if ($this->isPending()) {
            // Notify admins.
            // Generate hashkeys to map this current request
            $hashkey = ED::table('Hashkeys');
            $hashkey->uid = $table->id;
            $hashkey->type = DISCUSS_REPLY_TYPE;
            $hashkey->store();

            $approveURL = ED::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=approvePost&key=' . $hashkey->key);
            $rejectURL = ED::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=rejectPost&key=' . $hashkey->key);
            $emailData['moderation']  = '<div style="display:inline-block;width:100%;padding:20px;border-top:1px solid #ccc;padding:20px 0 10px;margin-top:20px;line-height:19px;color:#555;font-family:\'Lucida Grande\',Tahoma,Arial;font-size:12px;text-align:left">';
            $emailData['moderation'] .= '<a href="' . $approveURL . '" style="display:inline-block;padding:5px 15px;background:#fc0;border:1px solid #caa200;border-bottom-color:#977900;color:#534200;text-shadow:0 1px 0 #ffe684;font-weight:bold;box-shadow:inset 0 1px 0 #ffe064;-moz-box-shadow:inset 0 1px 0 #ffe064;-webkit-box-shadow:inset 0 1px 0 #ffe064;border-radius:2px;moz-border-radius:2px;-webkit-border-radius:2px;text-decoration:none!important">' . JText::_( 'COM_EASYDISCUSS_EMAIL_APPROVE_REPLY' ) . '</a>';
            $emailData['moderation'] .= ' ' . JText::_( 'COM_EASYDISCUSS_OR' ) . ' <a href="' . $rejectURL . '" style="color:#477fda">' . JText::_( 'COM_EASYDISCUSS_REJECT' ) . '</a>';
            $emailData['moderation'] .= '</div>';

            $emailData['emailSubject'] = JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_MODERATE', $this->post->title);
            $emailData['emailTemplate'] = 'email.post.reply.moderation.php';

            ED::mailer()->notifyAdministrators($emailData, array(), $this->config->get('notify_admin'), $this->config->get('notify_moderator'));

        } elseif($this->isPublished() && !$this->post->private) {

            $emailData['emailTemplate'] = 'email.post.reply.new.php';
            $emailData['emailSubject']  = JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $this->post->id , $this->post->title);
            $emailData['post_id'] = $this->post->id;

            ED::mailer()->notifyAdministrators($emailData, $excludeEmails, $this->config->get('notify_admin_onreply'), $this->config->get('notify_moderator_onreply'));
        }
    }

    /**
     * Notify moderator who get assigned.
     *
     * @since   4.0
     * @access  public
     * @param
     * @return
     *
     */
    public function notifyAssignedModerator($postassignerId, $postId)
    {
        $author = ED::user($postassignerId);

        $post = ED::post($postId);

        $emailData = array();
        $emailData['postLink'] = EDR::getRoutedURL('view=post&id=' .$postId ,false ,true);
        $emailData['postTitle'] = $post->title;
        $emailData['authorName'] = $author->getName();
        $emailData['authorAvatar'] = $author->getAvatar();

        $subject = JText::sprintf('COM_EASYDISCUSS_POST_ASSIGNED_EMAIL_SUBJECT', $author->getName());

        $notification = ED::getNotification();
        $notification->addQueue($author->user->email ,$subject ,'' ,'email.post.assign' ,$emailData);
    }

    /**
     * Sends a ping request to pingomatic servers.
     *
     * @since   4.0
     * @access  public
     * @param   null
     * @return  bool    True if success, false otherwise.
     *
     */
    public function ping()
    {
        if (!$this->config->get('integration_pingomatic')) {
            return false;
        }

        if ((!$this->isNew() && $this->prevPostStatus != DISCUSS_ID_PENDING) || $this->post->published != DISCUSS_ID_PUBLISHED) {
            return false;
        }

        return ED::Pingomatic()->ping($this->getTitle(), EDR::getRoutedURL('view=post&id=' . $this->post->id, true, true));
    }

    /**
     * Notify users.
     *
     * @since   4.0
     * @access  public
     * @param   null
     * @return  bool    True if success, false otherwise.
     *
     */
    public function notify()
    {
        if ($this->isReply()) {
              // @rule: Add notifications for the thread starter
            if ($this->post->published && $this->config->get('main_notifications_reply')) {
                // Get all users that are subscribed to this post
                $model = ED::model('Posts');
                $participants = $model->getParticipants($this->post->parent_id);

                $question = ED::post($this->post->parent_id);

                // Add the thread starter into the list of participants.
                $participants[] = $question->user_id;

                // Notify all participants
                foreach($participants as $participant) {
                    if ($participant != $this->my->id) {

                        $notification = ED::table('Notifications');

                        $notification->bind(array(
                                'title'     => JText::sprintf('COM_EASYDISCUSS_REPLY_DISCUSSION_NOTIFICATION_TITLE', $question->title),
                                'cid'       => $question->id,
                                'type'      => DISCUSS_NOTIFICATIONS_REPLY,
                                'target'    => $participant,
                                'author'    => $this->post->user_id,
                                'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $question->id,
                                'anonymous' => $this->post->anonymous
                            ));
                        $notification->store();
                    }
                }
            }

            // We will handle email notifications for reply under @replyNotify function instead.
            // Return to @save function
            return;
        }

        // Preparing the Email data
        // badwords filtering for email data.
        $this->post->title = ED::badwords()->filter($this->post->title);
        $this->post->content = ED::badwords()->filter($this->post->content);

        // prepare email content and information.
        $profile = $this->getOwner();

        // For use within the emails.
        $emailData = array();
        $emailData['postTitle'] = $this->post->title;

        $overrideName = (isset($profile->name) && $profile->name) ? $profile->name : '';

        $emailData['postAuthor'] = $profile->getName($overrideName);
        $emailData['postAuthorAvatar'] = $profile->getAvatar();
        $emailData['postLink'] = EDR::getRoutedURL('view=post&id=' . $this->post->id, false, true);
        $emailData['postCategory'] = ED::category($this->post->category_id)->getTitle();

        $emailContent = $this->post->content;

        if ($this->getContentType() != 'html') {
            // the content is bbcode. we need to parse it.
            $emailContent = ED::parser()->bbcode($emailContent);
            $emailContent = ED::parser()->removeBrTag($emailContent);
        }

        // If post is html type we need to strip off html codes.
        if ($this->getContentType() == 'html') {
            $emailContent = strip_tags($this->post->content);
        }

        $emailContent = ED::Mailer()->trimEmail($emailContent);
        $attachments = $this->getAttachments();

        $emailData['attachments'] = $attachments;
        $emailData['postContent'] = $emailContent;
        $emailData['post_id'] = $this->post->id;
        $emailData['cat_id'] = $this->post->category_id;
        $emailData['emailTemplate'] = 'email.subscription.site.new';
        $emailData['emailSubject'] = JText::sprintf('COM_EASYDISCUSS_NEW_QUESTION_ASKED', $this->post->id, $this->post->title);

        if ($this->isModerate) {
            // Generate hashkeys to map this current request
            $hashkey = ED::table('HashKeys');
            $hashkey->uid = $this->post->id;
            $hashkey->type = DISCUSS_QUESTION_TYPE;
            $hashkey->store();

            $approveURL = ED::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=approvePost&key=' . $hashkey->key);
            $rejectURL = ED::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=rejectPost&key=' . $hashkey->key);

            $emailData['moderation'] = ED::Mailer()->getModerationLink($approveURL, $rejectURL);

            $emailData['emailTemplate'] = 'email.subscription.site.moderate.php';
            $emailData['emailSubject']  = JText::sprintf('COM_EASYDISCUSS_NEW_QUESTION_MODERATE', $this->post->id, $this->getTitle());

        } else {

            // If this is a private post, do not notify anyone
            if (!$this->post->private && $this->getCategory()->canAccess()) {
                // Notify site subscribers

                if (($this->isNew() || $this->prevPostStatus == DISCUSS_ID_PENDING) && $this->post->published == DISCUSS_ID_PUBLISHED && !$this->config->get('notify_all')) {
                    if ($this->config->get('main_sitesubscription')) {
                        ED::Mailer()->notifySubscribers($emailData, array($this->my->email));
                    }

                    // Notify category subscribers
                    if ($this->config->get('main_ed_categorysubscription')) {
                        ED::Mailer()->notifySubscribers($emailData , array($this->my->email));
                    }
                }

                // Notify EVERYBODY
                if ($this->config->get('notify_all') && !$this->isModerate) {
                    ED::Mailer()->notifyAllMembers($emailData, array($this->my->email));
                }
            }
        }

        // Notify admins and category moderators
        if (($this->isNew() || $this->prevPostStatus == DISCUSS_ID_PENDING)) {
            ED::Mailer()->notifyAdministrators($emailData, array($this->my->email), $this->config->get('notify_admin'), $this->config->get('notify_moderator'));
        }

    }

    /**
     * Add tags for the post.
     *
     * @since   4.0
     * @access  public
     * @param   null
     * @return  bool    True if success, false otherwise.
     *
     */
    public function addTags()
    {
        if ($this->acl->allowed('add_tag', '0')) {

            $tags = $this->input->get('tags', '', 'POST');

            if (!empty($tags)) {

                $postTagTable = ED::table('PostsTags');

                // lets clear the existings data in posts_tags
                $postTagTable->clearTags($this->post->id);

                foreach ($tags as $tag) {
                    if (!empty($tag)) {
                        $tagTable = ED::table('Tags');

                        //@task: Only add tags if it doesn't exist.
                        if (!$tagTable->exists($tag)) {
                            $tagTable->title = JString::trim($tag);
                            $tagTable->alias = ED::getAlias($tag, 'tag');
                            $tagTable->created = ED::date()->toSql();
                            $tagTable->published = 1;
                            $tagTable->user_id = $this->my->id;

                            $tagTable->store();
                        } else {
                            $tagTable->load($tag, true);
                        }

                        $postTagInfo = array();

                        //@task: Store in the post tag
                        $postTagTable = ED::table('PostsTags');

                        $postTagInfo['post_id'] = $this->post->id;
                        $postTagInfo['tag_id']  = $tagTable->id;

                        $postTagTable->bind($postTagInfo);
                        $postTagTable->store();
                    }
                }
            }
        }
    }

    /**
     * Execute integrations for the post.
     *
     * @since   4.0
     * @access  public
     * @param   null
     * @return  bool    True if success, false otherwise.
     *
     */
    public function integrate()
    {
        // Add activity integrations for replies
        if ($this->isReply() && $this->isPublished()) {
            ED::jomsocial()->addActivityReply($this);
            ED::easysocial()->replyDiscussionStream($this);
        }

        // @rule: Jomsocial activity integrations & points & ranking
        if (($this->isNew() || $this->prevPostStatus == DISCUSS_ID_PENDING) && $this->post->published == DISCUSS_ID_PUBLISHED && !$this->post->private) {

            ED::jomsocial()->addActivityQuestion($this->post);

            if (!isset($this->saveOptions['saveFromEasysocialStory']) && !$this->isReply()) {
                ED::easysocial()->createDiscussionStream($this);
            }

            // Add notification to subscribers
            ED::easysocial()->notify('new.discussion', $this->post);

            // Add logging for user.
            ED::History()->log('easydiscuss.new.discussion', $this->my->id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_NEW_POST', $this->post->title ), $this->post->id);

            ED::Badges()->assign('easydiscuss.new.discussion', $this->my->id);
            ED::Points()->assign('easydiscuss.new.discussion', $this->my->id);

            // Assign badge for EasySocial
            ED::EasySocial()->assignBadge('create.question', $this->my->id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_NEW_POST', $this->post->title));

            // assign new ranks.
            ED::ranks()->assignRank($this->my->id, $this->config->get('main_ranking_calc_type'));

            // aup
            ED::Aup()->assign(DISCUSS_POINTS_NEW_DISCUSSION, $this->my->id, $this->post->title);
        }
    }



    /**
     * Overrides the parent's store behavior
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function save($options = array())
    {
        // Get any save options if available.
        $this->saveOptions = $options;

        // This allows us to perform necessary logics before the post is really saved
        $this->preSave();

        // Now we can store this in the db
        $state = $this->post->store();

        // Try to store the post.
        if (!$state) {
            ED::setMessageQueue($this->post->getError(), DISCUSS_QUEUE_ERROR);
            $this->app->redirect(EDR::getAskRoute($this->getCategory()->id, false));
            return $this->app->close();
        }

        // This allows us to perform necessary logics after the post is really saved.
        $this->postSave();

        // Auto post to slack
        $this->slack();

        // Auto post to telegram
        $this->telegram();

        // Auto post to integrated sites
        $this->autopost();

        // Notify mentioned name
        if ($this->config->get('main_mentions') && $this->my->id) {
            $this->notifyNames();
        }

        // Ping
        $this->ping();

        // Add tag for the post
        $this->addTags();

        // should we send email notifications (question)
        $this->notify();

        // should we send email notifications (reply)
        $this->replyNotify();

        // Subscribe the user to this post (for replies)
        $this->subscribe();

        // // Execute all integration
        $this->integrate();

        return $state;
    }


    /**
     * Updates the `read` status of a post for a particular user.
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function markRead($userId = null)
    {
        static $items = array();

        // Get the user.
        $user = ED::user($userId);

        // If the user is guest, we shouldn't be doing anything
        if (!$user->id || !$this->post->id) {
            return false;
        }

        // Get the posts_read
        $posts = $user->posts_read;

        if ($posts) {
            $posts = unserialize($posts);

            if (!in_array($this->post->id, $posts)) {
                $posts[] = $this->post->id;
            }
        } else {
            $posts = array($this->post->id);
        }

        $user->posts_read = serialize($posts);

        $state = $user->store();

        return $state;
    }

    /**
     * Performs validation for captcha
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function validateCaptcha($data)
    {
        // Get the captcha library
        $captcha = ED::captcha();

        // If this is not a new item, we shouldn't verify it
        if (!$this->isNew()) {
            return true;
        }

        // If captcha is not enabled, skip it altogether
        if (!$captcha->enabled()) {
            return true;
        }

        $valid = $captcha->validate($data);

        if (!$valid) {
            $this->setError('COM_EASYDISCUSS_INVALID_CAPTCHA');

            return false;
        }

        return true;
    }

    /**
     * Get the status of a post.
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function isNew()
    {
        $isNew = true;

        if ($this->original->id) {
            $isNew = false;
        }

        return $isNew;
    }

    /**
     * Formats the content
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function formatContent($debug = false)
    {
        if ($this->post->preview) {
            return $this->post->preview;
        }

        // Determine the current editor that is being configured
        $editor = $this->config->get('layout_editor');

        // If the post is bbcode source and the current editor is bbcode
        if ($this->getContentType() == 'bbcode' && $editor == 'bbcode') {

            $content = $this->post->content;

            // Allow syntax highlighter even on html codes.
            $content = ED::parser()->bbcode($content, $debug);

            // Since this is a bbcode content and source, we want to replace \n with <br /> tags.
            $content = nl2br($content);
        }

        // If the admin decides to switch from bbcode to wysiwyg editor, we need to format it back
        if ($this->getContentType() == 'bbcode' && $editor != 'bbcode') {
            $content = $this->post->content;

            //strip this kind of tag -> &nbsp; &amp;
            $content = strip_tags(html_entity_decode($content));

            // Since the original content is bbcode, we don't really need to do any replacements.
            // Just feed it in through bbcode formatter.
            $content = ED::parser()->bbcode($content);
        }

        // If the admin decides to switch from wysiwyg to bbcode, we need to fix the content here.
        if ($this->getContentType() != 'bbcode' && !is_null($this->getContentType()) && $editor == 'bbcode') {
            $content = $this->post->content;

            // Switch html back to bbcode
            $content = ED::parser()->html2bbcode($content);

            // Update the quote messages
            $content = ED::parser()->quoteBbcode($content);
        }

        // If the content is from wysiwyg and editor is also wysiwyg, we only do specific formatting.
        if ($this->getContentType() != 'bbcode' && $editor != 'bbcode') {
            $content = $this->post->content;

            // Allow syntax highlighter even on html codes.
            $content = ED::parser()->replaceCodes($content);
        }

        // Apply word censorship on the content
        $content = ED::badwords()->filter($content);

        return $content;
    }

    /**
     * Return a list of polls for this discussion
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getPoll()
    {
        static $items = array();

        if (isset($items[$this->post->id])) {
            return $items[$this->post->id];
        }

        $table = ED::table('PollQuestion');
        $exists = $table->load(array('post_id' => $this->post->id));

        if (!$exists) {
            $items[$this->post->id] = false;

            return $items[$this->post->id];
        }


        $poll = ED::poll($table);
        $items[$this->post->id] = $poll;

        return $items[$this->post->id];
    }

    /**
     * Delete the post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function delete()
    {
        // @trigger: onBeforeDelete
        ED::events()->importPlugin('content');
        ED::events()->onContentBeforeDelete('post', $this->post);

        // @rule: Unlink from 3rd party integrations
        $this->removeStream();

        // @rule: Unlink from the references table.
        $this->removeReferences();

        $this->removeSubscription();

        // Delete notifications
        $this->deleteNotifications();

        // Delete attachments
        $this->deleteAttachments();

        // @rule: Delete any childs
        if ($this->isQuestion()) {
            $deletedIds = $this->deleteReplies();

            // delete thread record as well.
            $this->deleteThread();
        }

        // Remove polls when discussion is deleted
        $this->deletePolls();

        // @rule: Delete any tags associated with this post.
        $this->deleteTags();

        // Delete comments related to this post.
        $this->deleteComments();

        // Delete any custom fields value with this post
        $this->deleteCustomFieldsValue();

        // Delete all favourites that associate with this post
        $this->deleteAllFavourites();

        JPluginHelper::importPlugin('finder');
        $dispatcher = JDispatcher::getInstance();

        // Trigger the onFinderAfterDelete event.
        $dispatcher->trigger('onFinderAfterDelete', array('com_easydiscuss.post', $this));

        $state = $this->post->delete();

        // @trigger: onAfterDelete
        ED::events()->onContentAfterDelete('post', $post);

        // If the post owner is registered user, assign points to the post owner
        if ($this->post->user_id) {
            ED::points()->assign('easydiscuss.remove.discussion', $this->post->user_id);

            if ($this->isReply()) {
                ED::points()->assign('easydiscuss.remove.reply', $this->post->user_id);
            }
        }

        // Process aup integrations
        if ($this->isQuestion()) {
            ED::aup()->assign(DISCUSS_POINTS_DELETE_DISCUSSION, $this->post->user_id, $this->post->title);
        } else {
            ED::aup()->assign(DISCUSS_POINTS_DELETE_REPLY, $this->post->user_id, $this->post->title);
        }

        // If this is a reply, and is an answer, we need to clear the parent's answered status
        if ($this->isReply() && $this->isAnswer()) {
            $question = $this->getParent();
            $question->setUnresolved();
        }

        return $state;
    }

    /**
     * Remove any subscription for this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function removeSubscription()
    {
        $model = ED::model('Subscribe');
        $model->removeSubscription($this->post->id);

        return true;
    }


    /**
     * Remove references from the reference table for this particular post.
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function removeReferences()
    {
        $model = ED::model('post');
        $model->removeReferences($this->post->id);

        return true;
    }

    /**
     * When executed, remove any 3rd party integration records.
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function removeStream()
    {
        jimport('joomla.filesystem.file');

        // Remove Easysocial Stream
        ED::easysocial()->deleteDiscussStream($this);

        // @rule: Detect if jomsocial exists.
        $file = JPATH_ROOT . '/components/com_community/libraries/core.php';

        if (!JFile::exists($file)) {
            return;
        }

        if ($this->config->get('integration_jomsocial_activity_new_question') && $this->isQuestion()) {

            $db = ED::db();

            $query  = 'DELETE FROM ' . $db->nameQuote('#__community_activities') . ' '
                    . 'WHERE ' . $db->nameQuote('app') . '=' . $db->Quote('com_easydiscuss') . ' '
                    . 'AND ' . $db->nameQuote('cid') . '=' . $db->Quote($this->post->id);

            $db->setQuery($query);
            $db->Query();

        }
    }

    /**
     * Subscribes to notifications for activities from this post's parent
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function subscribe()
    {
        // This is only for replies
        if (!$this->isReply()) {
            return;
        }

        if (!$this->config->get('main_autopostsubscription')) {
            return false;
        }

        if (!$this->config->get('main_postsubscription')) {
            return false;
        }

        // Get the parent post
        $parent = $this->getParent();

        $data = array();
        $data['type'] = 'post';
        $data['userid'] = $this->post->user_id ? $this->post->user_id : 0;
        $data['email'] = $this->post->user_id ? $this->my->email : $this->post->poster_email;
        $data['cid'] = $parent->id;
        $data['member'] = $this->post->user_id ? true : false;
        $data['name'] = $this->post->user_id ? $this->my->name : $this->post->poster_name;
        $data['interval'] = 'instant';

        // Try to subscribe now
        $model = ED::model('Subscribe');
        $model->addSubscriber($data);
    }

    /**
     * Delete all custom field value for this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function deleteCustomFieldsValue()
    {
        $model = ED::model('CustomFields');
        $state = $model->deleteCustomFieldsValue($this->post->id, 'post');

        return $state;
    }

    /**
     * Delete all fovourites for this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function deleteAllFavourites()
    {
        $model = ED::model('Favourites');
        $state = $model->deleteAllFavourites($this->post->id);

        return $state;
    }

    /**
     * Deletes all attachments from a post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function deleteAttachments()
    {
        // @rule: Delete attachments associated with this post.
        $attachments = $this->getAttachments();

        if (!empty($attachments)) {
            $total = count($attachments);

            for ($i = 0 ; $i < $total; $i++) {
                $attachments[$i]->delete();
            }
        }

        return true;
    }

    /**
     * Delete all notifications that belongs to this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function deleteNotifications()
    {
        $model = ED::model('notification');
        $model->deleteNotifications($this->post->id);
        return true;
    }

    /**
     * Removes all comments related to this post.
     *
     * @since   4.0
     * @access  public
     */
    public function deleteComments()
    {
        $model = ED::model('post');
        $model->deleteComments($this->post->id);
    }

    /**
     * Deletes all polls related to this post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function deletePolls()
    {
        $model = ED::model('Polls');

        return $model->deletePolls($this->post->id);
    }

    /**
     * Performs delete tags
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function deleteTags()
    {
        $model = ED::model('post');
        $model->deleteTags($this->post->id);
        return true;
    }

    /**
     * Perform the delete replies
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function deleteReplies()
    {
        if (!$this->post->id || $this->isReply()) {
            return false;
        }

        // Get the replies for this question
        $model = ED::model('post');
        $replies = $model->getReplies($this->post->id);

        if ($replies) {
            foreach ($replies as $id) {
                $reply = ED::post($id);
                $reply->delete();
            }
        }

        return true;
    }

    /**
     * Allows caller to lock a post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function lock()
    {
        $this->post->islock = true;
        $state = $this->post->store();

        if ($state) {
            $this->updateThread(array('islock' => '1'));
        }

        // Here we need to notify the owner of the post that their discussion is now locked
        if ($this->post->user_id != $this->my->id && $this->config->get('main_notifications_locked')) {
            $notification = ED::table('Notifications');
            $notification->bind(array(
                    'title' => JText::sprintf( 'COM_EASYDISCUSS_LOCKED_DISCUSSION_NOTIFICATION_TITLE', $this->post->title),
                    'cid' => $this->post->id,
                    'type' => DISCUSS_NOTIFICATIONS_LOCKED,
                    'target' => $this->post->user_id,
                    'author' => $this->my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $this->post->id
                ));
            $notification->store();
        }

        return $state;
    }

    /**
     * Allows caller to unlock a post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function unlock()
    {
        $this->post->islock = false;
        $state = $this->post->store();

        if ($state) {
            //let update the thread data
            $this->updateThread(array('islock' => '0'));
        }


        // @rule: Add notifications for the thread starter
        if ($this->post->user_id != $this->my->id && $this->config->get('main_notifications_locked')) {
            $notification = ED::table('Notifications');
            $notification->bind(array(
                    'title' => JText::sprintf('COM_EASYDISCUSS_UNLOCKED_DISCUSSION_NOTIFICATION_TITLE', $this->post->title),
                    'cid' => $this->post->id,
                    'type' => DISCUSS_NOTIFICATIONS_UNLOCKED,
                    'target' => $this->post->user_id,
                    'author' => $this->my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $this->post->id
                ));
            $notification->store();
        }

        return $state;
    }

    /**
     * Allows caller to mark a post as resolved
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function markResolved()
    {
        // Assign the post to resolved
        $this->post->isresolve = DISCUSS_ENTRY_RESOLVED;

        // When post is resolve state, other post status must remove
        $this->post->post_status = DISCUSS_POST_STATUS_OFF;

        $state = $this->post->store();

        if ($state) {
            //let update the thread data
            $this->updateThread(array('isresolve' => DISCUSS_ENTRY_RESOLVED, 'post_status' => DISCUSS_POST_STATUS_OFF));
        }

        // Once a post is marked as resolved, we should assign badges / points / notifications
        if ($this->post->user_id == $this->my->id) {

            ED::history()->log('easydiscuss.resolved.discussion', $this->my->id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_RESOLVED_OWN_DISCUSSION', $this->post->title), $this->post->id);
            ED::badges()->assign('easydiscuss.resolved.discussion', $this->my->id);
            ED::points()->assign('easydiscuss.resolved.discussion', $this->my->id);

            // Assign badge for EasySocial
            ED::easySocial()->assignBadge('resolve.reply', $this->my->id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_RESOLVED_OWN_DISCUSSION', $this->post->title));
        }

        // Add notifications for the thread starter
        if ($this->post->user_id != $this->my->id && $this->config->get('main_notifications_resolved')) {
            $notification = ED::table('Notifications');

            $notification->bind(array(
                    'title' => JText::sprintf('COM_EASYDISCUSS_RESOLVED_DISCUSSION_NOTIFICATION_TITLE', $this->post->title),
                    'cid' => $this->post->id,
                    'type' => DISCUSS_NOTIFICATIONS_RESOLVED,
                    'target' => $this->post->user_id,
                    'author' => $this->my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $this->post->id
                ));
            $notification->store();
        }

        return $state;
    }

    /**
     * System notify post owner who favourite his post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function favNotify(EasyDiscussPost $post)
    {
        // Add notifications to the post owner.
        if ($post->user_id != $this->my->id) {
            $notification = ED::table('Notifications');
            $text = 'COM_EASYDISCUSS_FAVOURITE_DISCUSSION_NOTIFICATION_TITLE';
            $title = $post->title;
            $type = DISCUSS_NOTIFICATIONS_FAVOURITE;

            $notification->bind(array(
                    'title' => JText::sprintf($text, $title),
                    'cid' => $post->id,
                    'type' => $type,
                    'target' => $post->user_id,
                    'author' => $this->my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $post->id
                ));

            $notification->store();
        }
    }

    /**
     * This flags the question as resolved
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function setResolved()
    {
        $this->post->isresolve = DISCUSS_ENTRY_RESOLVED;

        $state = $this->post->store();

        if ($state) {
            $this->updateThread(array('isresolve' => DISCUSS_ENTRY_RESOLVED));
        }

        return $state;
    }

    /**
     * This flags the question as unresolved
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function setUnresolved()
    {
        $this->post->isresolve = DISCUSS_ENTRY_UNRESOLVED;

        $state = $this->post->store();

        if ($state) {
            $this->updateThread(array('isresolve' => DISCUSS_ENTRY_UNRESOLVED));
        }

        return $state;
    }

    /**
     * This sets a post as an answer to the question
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function setAsAnswer()
    {
        // Update reply answered status
        $this->post->answered = true;

        // Try to save the post
        $state = $this->post->store();

        // Get the question
        $question = $this->getParent();
        $question->setResolved();

        // Let update the thread data
        if ($state) {
            $this->updateThread(array('answered' => DISCUSS_ENTRY_RESOLVED));
        }

        // sending notification to person who made the reply
        $emailSubject = JText::sprintf('COM_EASYDISCUSS_YOUR_REPLY_NOW_ACCEPTED', $question->title);
        $emailTemplate = 'email.reply.marked.answered';

        $replyUser = ED::user($this->post->user_id);

        $emailData = array();
        $emailData['postTitle'] = $question->title;
        $emailData['postLink'] = $question->getPermalink(true, false);
        $emailData['replyAuthor'] = ($replyUser->id) ? $replyUser->getName() : $this->post->poster_name;
        $emailData['replyAuthorAvatar'] = $replyUser->getAvatar();

        $emailContent = $this->getContent();

        // filter html content
        $emailContent = $this->trimEmail($emailContent);

        $email = array();

        if (empty($this->post->user_id)) {
            $email[] = $this->post->poster_email;
        } else {
            $email[] = $replyUser->user->email;
        }

        //now send notification.
        $notify = ED::getNotification();

        // Set the replyContent
        $emailData['replyContent'] = $emailContent;
        $notify->addQueue($email, $emailSubject, '', $emailTemplate, $emailData);

        // Create a new EasySocial stream
        ED::easySocial()->acceptedStream($this->post->id, $question);

        // Send notification to post owner when post is marked as answered.
        if ($this->config->get('notify_owner_answer') && $question->user_id != $this->my->id) {

            $email = array();

            // prepare email content and information.
            $emailSubject = JText::sprintf('COM_EASYDISCUSS_REPLY_NOW_ACCEPTED', $question->title);
            $emailTemplate = 'email.reply.answered.php';

            // get owner email.
            if (!empty($question->user_id)) {
                $ownerUser = JFactory::getUser($question->user_id);
                $email[] = $ownerUser->email;
            }

            if (!empty($email)) {
                $notify->addQueue($email, $emailSubject, '', $emailTemplate, $emailData);
            }
        }

        // @Rule : Add badges
        ED::history()->log('easydiscuss.answer.reply', $this->post->user_id, JText::sprintf('COM_EASYDISCUSS_HISTORY_ACCEPTED_REPLY', $question->title), $this->post->id);
        ED::badges()->assign('easydiscuss.answer.reply', $this->post->user_id);
        ED::points()->assign('easydiscuss.answer.reply', $this->post->user_id);

        // Assign badge for EasySocial
        ED::easysocial()->assignBadge('accepted.reply', $this->my->id, JText::sprintf('COM_EASYDISCUSS_HISTORY_ACCEPTED_REPLY', $question->title));

        // Assign in AUP extension
        ED::aup()->assign(DISCUSS_POINTS_ACCEPT_REPLY, $this->post->user_id, JText::sprintf('COM_EASYDISCUSS_HISTORY_ACCEPTED_REPLY', $question->title));

        // Notify the reply owner which post is accepted
        if ($this->post->user_id != $this->my->id && $this->config->get('main_notifications_accepted')) {

            // EasySocial integrations for notify to user which answer is accepted
            ED::easysocial()->notify('accepted.answer', $reply, $question, null, $question->user_id);

            // Notify owner of the discussion
            ED::easySocial()->notify('accepted.answer.owner', $reply, $question, null, $this->post->user_id);

            // @Rule: Add notifications for the reply author
            $notification = ED::table('Notifications');
            $notification->bind(array(
                    'title' => JText::sprintf('COM_EASYDISCUSS_ACCEPT_ANSWER_DISCUSSION_NOTIFICATION_TITLE', $question->title),
                    'cid' => $question->id,
                    'type' => DISCUSS_NOTIFICATIONS_ACCEPTED,
                    'target' => $this->post->user_id,
                    'author' => $this->my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $question->id . '#answer'
                ));
            $notification->store();
        }

        return $state;
    }

    /**
     * Allows caller to mark reject reply as accept answer
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function rejectAnswer()
    {
        // update reply status
        $this->post->answered = false;

        $state = $this->post->store();

        //let update the thread data
        if ($state) {
            $this->updateThread(array('answered' => DISCUSS_ENTRY_UNRESOLVED));
        }

        $question = $this->getParent();
        $question->setUnresolved();

        // send notification now
        $notify = ED::getNotification();

        // Remove history and deduct points
        ED::history()->removeLog('easydiscuss.answer.reply', $this->post->user_id, $this->post->id);
        ED::badges()->assign('easydiscuss.rejectanswer.reply', $this->post->user_id);
        ED::points()->assign('easydiscuss.rejectanswer.reply', $this->post->user_id);

        // notify owner which reply rejected answer
        if ($this->config->get('notify_owner_answer')) {

            // Prepare email content and information.
            $emailSubject = JText::sprintf('COM_EASYDISCUSS_REPLY_NOW_UNACCEPTED', $question->title);
            $emailTemplate = 'email.reply.unanswered';

            $emailData = array();
            $emailData['postTitle'] = $question->title;
            $emailData['postLink'] = $question->getPermalink(true, false);

            // Get post owner's email
            $email = $question->poster_email;

            if ($question->user_id) {
                $email = $question->getOwner()->user->email;
            }

            if (!empty($email)) {
                $notify->addQueue($email, $emailSubject, '', $emailTemplate, $emailData);
            }
        }

        return $state;
    }

    /**
     * Updates the polls count
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function updatePollsCount()
    {
        $db     = ED::db();

        $polls  = $this->getPolls();

        foreach( $polls as $poll )
        {

            // Unset the meVoted and totalVoted
            unset( $poll->meVoted );
            unset( $poll->totalVoted );

            $poll->updateCount();
        }
    }

    public function deleteThread()
    {
        $db = ED::db();

        $query = "delete from " . $db->nameQuote('#__discuss_thread');
        $query .= " where `id` = " . $db->Quote($this->post->thread_id);

        $db->setQuery($query);
        $db->query();
    }

    /**
     *
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function updateThread($columns)
    {
        $db = ED::db();
        $threadId = $this->post->thread_id;

        $items = array();

        foreach($columns as $key => $val) {
            $items[] = $db->nameQuote($key) . " = " . $db->Quote($val);
        }

        $items = implode(',', $items);

        // lets craft the sql for manual table update to avoid unnnessary eror such as undefined column issue.
        $query = "update " . $db->nameQuote('#__discuss_thread');
        $query .= " SET " . $items;
        $query .= " WHERE `id` = " . $db->Quote($threadId);

        // echo $query;exit;

        $db->setQuery($query);
        $db->query();

    }

    /**
     * Allows caller to mark a question as unresolved
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function markUnresolve()
    {
        // Assign the post to unresolved
        $this->post->isresolve = DISCUSS_ENTRY_UNRESOLVED;

        $state = $this->post->store();

        if ($state) {
            $this->updateThread(array('isresolve' => DISCUSS_ENTRY_UNRESOLVED));
        }

        // We need to clear any replies that was set as the answer
        $model = ED::model('Posts');
        $model->clearAcceptedReplies($this);

        return $state;
    }

    /**
     * Allows caller to set a post to on hold
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function markPostOnHold()
    {
        // Turn on the on-hold status,
        // DISCUSS_POST_STATUS_ON_HOLD = 1
        $this->post->post_status = DISCUSS_POST_STATUS_ON_HOLD;

        // When it is on hold, means this post is not resolved
        $this->post->isresolve = DISCUSS_ENTRY_UNRESOLVED;

        $state = $this->post->store();

        if ($state) {
            //let update the thread data
            $this->updateThread(array('isresolve' => DISCUSS_ENTRY_UNRESOLVED, 'post_status' => DISCUSS_POST_STATUS_ON_HOLD));
        }

        // @rule: Add notifications for the thread starter
        if ($this->post->user_id != $this->my->id) {
            $notification = ED::table('Notifications');
            $notification->bind(array(
                    'title' => JText::sprintf('COM_EASYDISCUSS_ON_HOLD_DISCUSSION_NOTIFICATION_TITLE', $this->post->title),
                    'cid' => $this->post->id,
                    'type' => 'onHold',
                    'target' => $this->post->user_id,
                    'author' => $this->my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $this->post->id
                ));
            $notification->store();
        }
        return $state;
    }

    /**
     * Allows caller to set the post status to accepted
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function markPostAccepted()
    {
        // Turn on the accepted status,
        // DISCUSS_POST_STATUS_ACCEPTED = 2
        $this->post->post_status = DISCUSS_POST_STATUS_ACCEPTED;

        // When it is accepted, means this post is not yet resolved
        $this->post->isresolve = DISCUSS_ENTRY_UNRESOLVED;

        $state = $this->post->store();

        if ($state) {
            //let update the thread data
            $this->updateThread(array('isresolve' => DISCUSS_ENTRY_UNRESOLVED, 'post_status' => DISCUSS_POST_STATUS_ACCEPTED));
        }

        // @rule: Add notifications for the thread starter
        if ($this->post->user_id != $this->my->id) {
            $notification = ED::table('Notifications');
            $notification->bind(array(
                    'title' => JText::sprintf('COM_EASYDISCUSS_ACCEPTED_DISCUSSION_NOTIFICATION_TITLE', $this->post->title),
                    'cid' => $this->post->id,
                    'type' => 'accepted',
                    'target' => $this->post->user_id,
                    'author' => $this->my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $this->post->id
                ));
            $notification->store();
        }

        return $state;
    }

    /**
     * Allows caller to set the status of a post to "working"
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function markPostWorkingOn()
    {
        // Turn on the accepted status,
        // DISCUSS_POST_STATUS_WORKING_ON = 3
        $this->post->post_status = DISCUSS_POST_STATUS_WORKING_ON;

        // When it is working on, means it is not resolved
        $this->post->isresolve = DISCUSS_ENTRY_UNRESOLVED;

        $state = $this->post->store();

        if ($state) {
            //let update the thread data
            $this->updateThread(array('isresolve' => DISCUSS_ENTRY_UNRESOLVED, 'post_status' => DISCUSS_POST_STATUS_WORKING_ON));
        }

        // @rule: Add notifications for the thread starter
        if ($this->post->user_id != $this->my->id) {
            $notification = ED::table('Notifications');
            $notification->bind(array(
                    'title' => JText::sprintf('COM_EASYDISCUSS_WORKING_ON_DISCUSSION_NOTIFICATION_TITLE', $this->post->title),
                    'cid' => $this->post->id,
                    'type' => 'workingOn',
                    'target' => $this->post->user_id,
                    'author' => $this->my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $this->post->id
                ));
            $notification->store();
        }


        return $state;
    }

    /**
     * Marks a post as rejected
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function markPostRejected()
    {
        // DISCUSS_POST_STATUS_REJECT = 4
        $this->post->post_status = DISCUSS_POST_STATUS_REJECT;

        // When it is rejected, make sure the post is not resolved
        $this->post->isresolve = DISCUSS_ENTRY_UNRESOLVED;

        $state = $this->post->store();

        if ($state) {
            //let update the thread data
            $this->updateThread(array('isresolve' => DISCUSS_ENTRY_UNRESOLVED, 'post_status' => DISCUSS_POST_STATUS_REJECT));
        }

        // @rule: Add notifications for the thread starter
        if ($this->post->user_id != $this->my->id) {
            $notification = ED::table('Notifications');
            $notification->bind(array(
                    'title' => JText::sprintf('COM_EASYDISCUSS_REJECT_DISCUSSION_NOTIFICATION_TITLE', $this->post->title),
                    'cid' => $this->post->id,
                    'type' => 'reject',
                    'target' => $this->post->user_id,
                    'author' => $this->my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $this->post->id
                ));
            $notification->store();
        }

        return $state;
    }

    /**
     * Allows caller to clear the post status of a post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function markPostNoStatus()
    {
        // DISCUSS_POST_STATUS_OFF = 0
        $this->post->post_status = DISCUSS_POST_STATUS_OFF;

        $state = $this->post->store();

        if ($state) {
            //let update the thread data
            $this->updateThread(array('post_status' => DISCUSS_POST_STATUS_OFF));
        }

        // @rule: Add notifications for the thread starter
        if ($this->post->user_id != $this->my->id) {
            $notification = ED::table('Notifications');
            $notification->bind(array(
                    'title' => JText::sprintf('COM_EASYDISCUSS_NO_STATUS_DISCUSSION_NOTIFICATION_TITLE', $this->post->title),
                    'cid' => $this->post->id,
                    'type' => 'unhold',
                    'target' => $this->post->user_id,
                    'author' => $this->my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $this->post->id
                ));
            $notification->store();
        }

        return $state;
    }

    /**
     * Lock the poll for question
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function lockPolls()
    {
        $model = ED::model('post');
        $state = $model->lockPolls($this->post->id);

        if (!$state) {
            return false;
        }

        return true;
    }

    /**
     * Unlock the poll for question
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function unlockPolls()
    {
        $model = ED::model('post');
        $state = $model->unlockPolls($this->post->id);

        if (!$state) {
            return false;
        }

        return true;
    }

    /**
     * Move replies if the Question is being moved
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function moveReplies($parentId = null, $newCatId = null)
    {
        if (empty($newCatId) || empty($parentId)) {
            return false;
        }

        $model = ED::model('post');
        $state = $model->moveReplies($parentId, $newCatId);

        if (!$state) {
            return false;
        }

        return true;
    }

    /**
     * Resets all the votes for this particular discussion / reply.
     *
     * @since   4.0
     * @access  public
     */
    public function resetVotes()
    {
        $model = ED::model('Votes');
        $model->resetVotes($this->post->id);

        // Once the vote items are removed, we need to update the sum_totalvote column.
        $this->post->sum_totalvote = 0;

        return $this->post->store();
    }

    // to be confirmed //

    /*
     * Function to determine a post should minimise or not.
     * return true or false
     */
    public static function toMinimizePost($count)
    {
        $config = ED::getConfig();

        $breakPoint = $config->get('layout_autominimisepost');
        $minimize = ($count <= $breakPoint && $breakPoint != 0 ) ? true : false;

        return $minimize;
    }

    public function setIsLikedBatch($ids, $userId = null, $type = DISCUSS_ENTITY_TYPE_POST)
    {
        $db = ED::db();

        if(is_null($userId)) {
            $userId = JFactory::getUser()->id;
        }

        if (count($ids) > 0) {

            $query  = 'SELECT `id`, `content_id` FROM `#__discuss_likes`';
            $query .= ' WHERE `type` = ' . $db->Quote($type);

            if (count($ids) == 1) {
                $query .= ' AND `content_id` = ' . $db->Quote($ids[0]);
            } else {
                $query .= ' AND `content_id` IN (' . implode(',', $ids) . ')';
            }

            $query .= ' AND `created_by` = ' . $db->Quote($userId);

            $db->setQuery($query);
            $result = $db->loadObjectList();

            if (count($result) > 0) {

                foreach($result as $item) {
                    $sig = $item->content_id .'-'. $userId .'-'. $type;
                    self::$_isLiked[$sig] = $item->id;
                }
            }

            foreach($ids as $id) {
                $sig = $id .'-'. $userId .'-'. $type;

                if (! isset(self::$_isLiked[$sig])) {
                    self::$_isLiked[$sig] = '';
                }
            }
        }
    }

    public function getRatings()
    {
        if (!isset(self::$ratings[$this->id])) {
            $model = ED::model('Ratings');
            $ratings = $model->preloadRatings(array($this->id));

            if (!$ratings) {
                self::$ratings[$this->id] = new stdClass();
                self::$ratings[$this->id]->ratings = 0;
                self::$ratings[$this->id]->total = 0;

                return self::$ratings[$this->id];
            }

            self::$ratings[$this->id] = $ratings[$this->id];
        }

        return self::$ratings[$this->id];

    }

    public function hasRated($userId = null)
    {
        if (is_null($userId)) {
            $userId = $this->my->id;
        }

        $hash   = '';
        $ipaddr = '';

        if (empty($userId)) {
            // mean this is a guest.
            $hash = JFactory::getSession()->getId();
            $ipaddr = @$_SERVER['REMOTE_ADDR'];
        }

        $model = ED::model('ratings');

        return $model->hasRated($this->id, 'question', $userId, $hash, $ipaddr);
    }

    /**
     * Retrieves a list of user id's that has participated in a discussion
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getParticipants()
    {
        $db = ED::db();

        $query = 'SELECT DISTINCT `user_id` FROM `#__discuss_posts`';
        $query .= ' WHERE `parent_id` = ' . $db->Quote($this->id);

        $db->setQuery($query);
        $participants = $db->loadResultArray();

        $users = array();

        foreach ($participants as $userId) {
            $user = ED::user($userId);
            $users[] = $user;
        }

        return $users;
    }

}
