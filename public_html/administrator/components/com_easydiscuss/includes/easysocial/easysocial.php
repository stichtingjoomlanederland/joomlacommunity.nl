<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussEasySocial extends EasyDiscuss
{
	static $file = null;
	private $exists	= false;

	public function __construct()
	{
		parent::__construct();

		$lang = JFactory::getLanguage();
		$lang->load('com_easydiscuss' , JPATH_ROOT);

		self::$file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/easysocial.php';
		$this->exists = $this->exists();
	}

	/**
	 * Determines if EasySocial is installed on the site.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			jimport('joomla.filesystem.file');

			$file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/easysocial.php';
			$fileExists = JFile::exists($file);
			$enabled = JComponentHelper::isEnabled('com_easysocial');

			if (!$fileExists || !$enabled) {
				$exists = false;
				return $exists;
			}

			include_once($file);
			$exists = true;
		}

		return $exists;
	}

	/**
	 * Determines if this is EasySocial prior to 2.x
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isLegacy()
	{
		if (!$this->exists()) {
			return;
		}

		// Get the current version.
		$local = ES::getLocalVersion();

		$legacy = version_compare($local, '2.0.0') == -1 ? true : false;

		return $legacy;
	}

	/**
	 * Generates the badges for toolbar
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getToolbarBadgesHtml()
	{
		static $output = null;

		if (is_null($output)) {
			$user = ES::user();
			$badges = $user->getBadges();

			$theme = ED::themes();
			$theme->set('badges', $badges);

			$output = $theme->output('site/toolbar/badges');
		}

		return $output;
	}

	/**
	 * Renders the toolbar dropdown html
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getToolbarDropdown()
	{		
		$theme = ED::themes();
		$theme->set('esConfig', ES::config());
		$output = $theme->output('site/toolbar/easysocial');

		return $output;
	}

	/**
	 * Initializes EasySocial
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function init()
	{
		static $loaded 	= false;

		if (!$loaded) {

			require_once(self::$file);

			$doc = JFactory::getDocument();

			// We also need to render the styling from EasySocial.
			if ($doc->getType() == 'html') {

				if (method_exists('ES', 'initialize')) {
					ES::initialize();
				} else {
					$fdoc = ES::document();
					$fdoc->init();

					$page = ES::page();
					$page->processScripts();
				}
			}

			ES::language()->load('com_easysocial', JPATH_ROOT);

			$loaded = true;
		}

		return $loaded;
	}

	/**
	 * Retrieves the conversations link in EasySocial
	 *
	 * @since	4.0.5
	 * @access	public
	 */
	public function getConversationsRoute($xhtml = true)
	{
		if (!$this->exists()) {
			return;
		}

		$link = ESR::conversations(array(), $xhtml);

		return $link;
	}

	/**
	 * Retrieves the cover for the user
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getCover($id = null)
	{
		if (!$this->exists()) {
			return;
		}

		$user = ES::user($id);
		$cover = $user->getCoverData();

		return $cover;
	}

	/**
	 * Assign badge
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function assignBadge($rule, $creatorId, $message)
	{
		if (!$this->exists()) {
			return false;
		}

		$creator = ES::user($creatorId);

		$badge = ES::badges();
		$state = $badge->log('com_easydiscuss', $rule, $creator->id, $message);

		return $state;
	}


	/**
	 * Assign points
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function assignPoints($rule , $creatorId = null, $post = null)
	{
		if(!$this->exists() || !$this->config->get('integration_easysocial_points'))
		{
			return false;
		}

		// Since all the "rule" in EasyDiscuss is prepended with discuss. , we need to remove it
		$rule 		= str_ireplace('easydiscuss.' , '' , $rule);
		$creator 	= ES::user($creatorId);

		$points		= ES::points();

		if ($rule == 'new.comment') {
			$params	= $points->getParams('new.comment', 'com_easydiscuss');

			$length	= JString::strlen($post->comment);
			$state 	= false;

			if (!$params) {

				// For earlier versions of EasySocial
				// This could be older version of EasySocial
				$points->assign($rule, 'com_easydiscuss', $creator->id);
			} else {
				$min = isset($params->get('min')->value) ? $params->get('min')->value : $params->get('min')->default;

				// Get the content length
				if ($length > $min || $min == 0) {
					$state 	= $points->assign($rule , 'com_easydiscuss', $creator->id);
				}
			}

			return $state;
		}

		if ($rule == 'new.reply') {

			$params 	= false;

			if (method_exists($points, 'getParams')) {
				$params	= $points->getParams('new.reply', 'com_easydiscuss');
			}

			$length	= JString::strlen($post->content);
			$state 	= false;

			if (!$params) {
				// For earlier versions of EasySocial
				// This could be older version of EasySocial
				$points->assign($rule, 'com_easydiscuss', $creator->id);
			} else {
				$min = isset($params->get('min')->value) ? $params->get('min')->value : $params->get('min')->default;

			// Get the content length
			if ($length > $min || $min == 0) {
				$state 	= $points->assign($rule, 'com_easydiscuss', $creator->id);
			}
		}

			return $state;
		}

		$state 		= $points->assign($rule , 'com_easydiscuss' , $creator->id);

		return $state;
	}

	/**
	 * Creates a new stream for new discussion
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function createDiscussionStream($post)
	{
		if (!$this->exists() || !$this->config->get('integration_easysocial_activity_new_question')) {
			return;
		}

		$stream = ES::stream();
		$template = $stream->getTemplate();

		$contextType = 'discuss';

		$esClusterType = array('event', 'group');
		$contribution = $post->getDiscussionContribution();

		if ($contribution) {
			if (in_array($contribution->type, $esClusterType)) {
				$template->setCluster($contribution->id, $contribution->type);
				$contextType = 'easydiscuss';
			}
		}

		// Get the stream template
		$template->setActor($post->user_id, SOCIAL_TYPE_USER);
		$template->setContext($post->id, $contextType, $post);

		$template->setVerb('create');
		$template->setAccess('core.view');

		$state 	= $stream->add($template);

		return $state;
	}

	/**
	 * Creates a new stream for new replies
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function replyDiscussionStream($post)
	{
		if (!$this->exists() || !$this->config->get('integration_easysocial_activity_reply_question')) {
			return;
		}

		$stream = ES::stream();
		$template = $stream->getTemplate();
		$question = ED::post($post->parent_id);

		$rawPost = ED::post($post->id);

		$category = ED::category($question->category_id);

		$obj = new stdClass();
		$obj->post = $rawPost;
		$obj->question = $question;
		$obj->cat = $category;

		$contextType = 'discuss';

		$esClusterType = array('event', 'group');
		$contribution = $question->getDiscussionContribution();

		if ($contribution) {
			if (in_array($contribution->type, $esClusterType)) {
				$template->setCluster($contribution->id, $contribution->type);
				$contextType = 'easydiscuss';
			}
		}

		// Get the stream template
		$template->setActor($post->user_id, SOCIAL_TYPE_USER);
		$template->setContext($post->id, $contextType, $obj);

		$template->setVerb('reply');

		// Store the reply permalink in params
		// This needs to done here because we need to current total replies
		$registry = ES::registry();
		$registry->set('replyPermalink', $post->getReplyPermalink());

		// Set the template params
		$template->setParams($registry);

		$template->setPublicStream('core.view');
		$state = $stream->add($template);

		return $state;
	}

	/**
	 * Creates a new stream for new replies
	 *
	 * @since	4.1.3
	 * @access	public
	 */
	public function commentDiscussionStream($comment, $post, $question)
	{
		if (!$this->exists() || !$this->config->get('integration_easysocial_activity_comment')) {
			return;
		}

		$stream = ES::stream();
		$template = $stream->getTemplate();

		$obj = new stdClass();
		$obj->comment = $comment;
		$obj->post = $post;
		$obj->question = $question;

		// For user app, we use a different element
		$context = 'discuss';

		// Determine if this is being posted in an event or group app
		$clusters = array('event', 'group');
		$contribution = $post->getDiscussionContribution();

		if ($contribution && in_array($contribution->type, $clusters)) {
			$template->setCluster($contribution->id, $contribution->type);
			$context = 'easydiscuss';
		}

		$template->setActor($comment->user_id, SOCIAL_TYPE_USER);
		$template->setContext($comment->id, $context, $obj);
		$template->setVerb('comment');
		$template->setPublicStream('core.view');

		$state = $stream->add($template);

		return $state;
	}

	/**
	 * Creates a new stream for new likes
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function likesStream($post , $question)
	{
		if(!$this->exists() || !$this->config->get('integration_easysocial_activity_likes'))
		{
			return;
		}

		$stream 	= ES::stream();
		$template 	= $stream->getTemplate();
		$actor 		= ES::user();

		$obj 			= new stdClass();
		$obj->post		= $post;
		$obj->question	= $question;

		// Get the stream template
		$template->setActor($actor->id , SOCIAL_TYPE_USER);
		$template->setContext($post->id , 'discuss' , $obj);

		$template->setVerb('likes');

		$template->setPublicStream('core.view');
		$state 	= $stream->add($template);

		return $state;
	}

	/**
	 * Creates a new stream for new likes
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function rankStream($rank)
	{
		if(!$this->exists() || !$this->config->get('integration_easysocial_activity_ranks'))
		{
			return;
		}

		$stream 	= ES::stream();
		$template 	= $stream->getTemplate();

		$obj 			= new stdClass();
		$obj->id 		= $rank->rank_id;
		$obj->user_id 	= $rank->user_id;
		$obj->title		= $rank->title;

		// Get the stream template
		$template->setActor($rank->user_id , SOCIAL_TYPE_USER);
		$template->setContext($rank->rank_id , 'discuss' , $obj);

		$template->setVerb('ranks');

		$template->setPublicStream('core.view');
		$state 	= $stream->add($template);

		return $state;
	}

	/**
	 * Creates a new stream for new likes
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function favouriteStream($post)
	{
		if (!$this->exists() || !$this->config->get('integration_easysocial_activity_favourite')) {
			return;
		}

		$stream = ES::stream();
		$template = $stream->getTemplate();

		// Get the stream template
		$template->setActor(ES::user()->id, SOCIAL_TYPE_USER);
		$template->setContext($post->id, 'discuss', $post);

		$template->setVerb('favourite');

		$template->setPublicStream('core.view');
		$state 	= $stream->add($template);

		return $state;
	}

	/**
	 * Creates a new stream for accepted items
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function acceptedStream($post , $question)
	{
		if (!$this->exists() || !$this->config->get('integration_easysocial_activity_accepted')) {
			return;
		}

		$stream = ES::stream();
		$template = $stream->getTemplate();

		$obj = new stdClass();
		$obj->post = $post;
		$obj->question = $question;

		$contextType = 'discuss';

		$esClusterType = array('event', 'group');
		$contribution = $question->getDiscussionContribution();

		if ($contribution) {
			if (in_array($contribution->type, $esClusterType)) {
				$template->setCluster($contribution->id, $contribution->type);
				$contextType = 'easydiscuss';
			}
		}

		// Get the stream template
		$template->setActor($post->user_id, SOCIAL_TYPE_USER);
		$template->setContext($post->id, $contextType, $obj);

		$template->setVerb('accepted');

		$template->setPublicStream('core.view');
		$state = $stream->add($template);

		return $state;
	}

	/**
	 * Creates a new stream for new likes
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function voteStream($post)
	{
		if(!$this->exists() || !$this->config->get('integration_easysocial_activity_vote'))
		{
			return;
		}

		$stream 	= ES::stream();
		$template 	= $stream->getTemplate();

		// The actor should always be the person that is voting.
		$my 		= ES::user();

		// Get the stream template
		$template->setActor($my->id , SOCIAL_TYPE_USER);
		$template->setContext($post->id , 'discuss' , $post);

		$template->setVerb('vote');

		$template->setPublicStream('core.view');
		$state 	= $stream->add($template);

		return $state;
	}

	private function getRecipients($action , $post)
	{
		$recipients 	= array();

		if ($action == 'new.discussion') {
			$rows = ED::mailer()->getSubscribers('site', 0, 0 , array() , array($post->user_id));

			if (!$rows) {
				return false;
			}

			foreach ($rows as $row) {
				// We don't want to add the owner of the post to the recipients
				if ($row->userid != $post->user_id) {
					$recipients[]	= $row->userid;
				}
			}

			return $recipients;
		}

		if ($action == 'new.moderate.discussion') {
			$model = ED::model('Category');
			$moderators = $model->getModerators($post->post->category_id);

			foreach ($moderators as $moderator) {
				$recipients[] = $moderator;
			}

			return $recipients;
		}

		if ($action == 'new.reply') {
			// Get all users that are subscribed to this post
			$model	= ED::model('Posts');
			$rows	= $model->getParticipants($post->parent_id);

			if (!$rows) {
				return false;
			}

			// Add the thread starter into the list of participants.
			$question = ED::post($post->parent_id);
			$rows[]		= $question->user_id;

			foreach ($rows as $id) {
				if ($id != $post->user_id) {
					$recipients[]	= $id;
				}
			}

			return $recipients;
		}

		if ($action == 'new.mentions') {
			// Detect known names in the post.
			$users = ED::string()->detectNames($post->content, array($post->user_id));

			if (!$users) {
				return false;
			}

			return $users;
		}
	}

	/**
	 * Retrieve the pm button
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPmHtml($targetId, $layout = 'list')
	{
		if (!$this->exists()) {
			return;
		}

		// Initialize scripts
		$this->init();

		$user = ES::user($targetId);

		$namespace = $layout == 'list' ? 'user.pm' : 'user.popbox.pm';

		$theme = ED::themes();
		$theme->set('user', $user);
		$output = $theme->output('site/easysocial/' . $namespace);

		return $output;
	}

	/**
	 * Retrieves the popbox code for avatars
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPopbox($userId)
	{
		if (!$this->exists() || !$this->config->get('integration_easysocial_popbox') || !$userId) {
			return;
		}

		// Initialize our script
		$this->init();

		$popbox = ' data-user-id="' . $userId . '" data-popbox="module://easysocial/profile/popbox" ';

		return $popbox;
	}

	/**
	 * Retrieve username field for login form
	 *
	 * @since   3.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getUsernameField()
	{
		$esConfig = ES::config();

		$usernameField = $esConfig->get('general.site.loginemail') ? 'COM_EASYDISCUSS_LOGIN_NAME_OR_EMAIL' : 'COM_EASYDISCUSS_USERNAME';

		if ($esConfig->get('registrations.emailasusername')) {
			$usernameField = 'COM_EASYDISCUSS_LOGIN_EMAIL';
		}

		return $usernameField;
	}

	/**
	 * Notify site subscribers whenever a new blog post is created
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function notify($action, $post, $question = null, $comment = null, $actor = null)
	{
		if (!$this->exists()) {
			return;
		}

		if ($post->isCluster()) {
			return $this->notifyCluster($action, $post, $question, $comment, $actor);
		}

		// We don't want to notify via e-mail
		$emailOptions = false;
		$recipients = array();
		$rule = '';

		$recipients = $this->getRecipients($action, $post);

		// retrieve the current category post language
		$postCatLang = $post->getCategoryLanguage();

		// For some reason if the category language columns is stored empty data, we will override this.
		if (empty($postCatLang)) {
			$postCatLang = '*';
		}

		// retrieve infront language code
		$langcode = substr($postCatLang, 0, 2);

		// determine the site got enable multilingual or not
		$isSiteMultilingualEnabled = ED::isSiteMultilingualEnabled();

		if ($action == 'new.discussion') {

			if (!$this->config->get('integration_easysocial_notify_create')) {
				return;
			}

			if (!$recipients) {
				return;
			}

			$defaultPermalink = 'index.php?option=com_easydiscuss&view=post&id=' . $post->id;

			if ($isSiteMultilingualEnabled) {

				// if the post post in language category
				if ($postCatLang != '*') {
					$permalink = EDR::_('view=post&id=' . $post->id . '&lang=' . $langcode, true, null, false);

				} else {
					$permalink = $defaultPermalink;
				}

			} else {
				$permalink = EDR::_($defaultPermalink, true, null, false);
			}

			$image = '';

			$options = array('actor_id' => $post->user_id, 'uid' => $post->id, 'title' => JText::sprintf('COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_NEW_POST', $post->title), 'type' => 'discuss', 'url' => $permalink);

			$rule = 'discuss.create';
		}

		if ($action == 'new.moderate.discussion') {

			if (!$this->config->get('integration_easysocial_notify_moderate')) {
				return;
			}

			if (!$recipients) {
				return;
			}

			$defaultPermalink = 'index.php?option=com_easydiscuss&view=ask&id=' . $post->id;

			if ($isSiteMultilingualEnabled) {

				// if the post post in language category
				if ($postCatLang != '*') {
					$permalink = EDR::_('view=post&id=' . $post->id . '&lang=' . $langcode, true, null, false);

				} else {
					$permalink = $defaultPermalink;
				}

			} else {
				$permalink = EDR::_($defaultPermalink, true, null, false);
			}

			$image = '';

			$options = array('actor_id' => $post->user_id, 'uid' => $post->id, 'title' => JText::sprintf('COM_ED_EASYSOCIAL_NOTIFICATION_NEW_MODERATE_POST', $post->title), 'type' => 'discuss', 'url' => $permalink);

			$rule = 'discuss.moderate';
		}

		if ($action == 'new.reply') {

			if (!$this->config->get('integration_easysocial_notify_reply')) {
				return;
			}

			if (!$recipients) {
				return;
			}

			$defaultPermalink = 'index.php?option=com_easydiscuss&' . $post->getReplyPermalink();

			if ($isSiteMultilingualEnabled) {

				// if the post post in language category
				if ($postCatLang != '*') {

					$defaultPermalink = 'index.php?option=com_easydiscuss&lang=' . $langcode . '&' . $post->getReplyPermalink();
					$permalink = EDR::_($defaultPermalink, true, null, false);

				} else {
					$permalink = $defaultPermalink;
				}

			} else {
				$permalink = EDR::_($defaultPermalink, true, null, false);
			}

			$options = array('actor_id' => $post->user_id, 'uid' => $post->id, 'title' => JText::sprintf('COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_REPLY', $question->title), 'type' => 'discuss', 'url' => $permalink);

			$rule = 'discuss.reply';
		}

		if ($action == 'new.comment') {

			if (!$this->config->get('integration_easysocial_notify_comment')) {
				return;
			}

			// The recipient should only be the post owner
			$recipients = array($post->user_id);

			// Do not notify user when they comment on their own post
			if ($comment->user_id == $post->user_id) {
				return;
			}

			$defaultPermalink = 'index.php?option=com_easydiscuss&' . $post->getReplyPermalink();

			if ($isSiteMultilingualEnabled) {

				// if the post post in language category
				if ($postCatLang != '*') {
					$defaultPermalink = 'index.php?option=com_easydiscuss&lang=' . $langcode . '&' . $post->getReplyPermalink();
					$permalink = EDR::_($defaultPermalink, true, null, false);

				} else {
					$permalink = $defaultPermalink;
				}

			} else {
				$permalink = EDR::_($defaultPermalink, true, null, false);
			}

			$content = JString::substr($comment->comment, 0, 25) . '...';
			$options = array('actor_id' => $comment->user_id, 'uid' => $comment->id, 'title' => JText::sprintf('COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_COMMENT', $content), 'type' => 'discuss', 'url' => $permalink);

			$rule = 'discuss.comment';
		}

		if ($action == 'accepted.answer') {

			if (!$this->config->get('integration_easysocial_notify_accepted')) {
				return;
			}

			// The recipient should only be the post owner
			$recipients = array($post->user_id);

			$defaultPermalink = 'index.php?option=com_easydiscuss&view=post&id=' . $question->id . '#answer';

			if ($isSiteMultilingualEnabled) {

				// if the post post in language category
				if ($postCatLang != '*') {
					$permalink = EDR::_('view=post&id=' . $question->id . '&lang=' . $langcode, true, null, false) . '#answer';

				} else {
					$permalink = $defaultPermalink;
				}

			} else {
				$permalink = EDR::_($defaultPermalink, true, null, false);
			}

			$options = array('actor_id' => $actor, 'uid' => $post->id, 'title' => JText::sprintf('COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_ACCEPTED_ANSWER', $question->title), 'type' => 'discuss', 'url' => $permalink);

			$rule = 'discuss.accepted';
		}

		if ($action == 'accepted.answer.owner') {

			if (!$this->config->get('integration_easysocial_notify_accepted')) {
				return;
			}

			// The recipient should only be the post owner
			$recipients = array($question->user_id);

			$defaultPermalink = 'index.php?option=com_easydiscuss&view=post&id=' . $question->id . '#answer';

			if ($isSiteMultilingualEnabled) {

				// if the post post in language category
				if ($postCatLang != '*') {
					$permalink = EDR::_('view=post&id=' . $question->id . '&lang=' . $langcode, true, null, false) . '#answer';

				} else {
					$permalink = $defaultPermalink;
				}

			} else {
				$permalink = EDR::_($defaultPermalink, true, null, false);
			}

			$options = array('actor_id' => $actor, 'uid' => $post->id, 'title' => JText::sprintf('COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_ACCEPTED_ANSWER_OWNER', $question->title), 'type' => 'discuss', 'url' => $permalink);

			$rule = 'discuss.accepted.owner';
		}

		if ($action == 'new.likes') {

			if (!$this->config->get('integration_easysocial_notify_likes')) {
				return;
			}

			// The recipient should only be the post owner
			$recipients = array($post->user_id);

			$defaultReplyPermalink = 'index.php?option=com_easydiscuss&' . $post->getReplyPermalink();
			$defaultQuestionPermalink = 'index.php?option=com_easydiscuss&view=post&id=' . $post->id;

			if ($isSiteMultilingualEnabled) {

				// if the post post in language category
				if ($postCatLang != '*') {

					$permalink = 'index.php?option=com_easydiscuss&lang=' . $langcode . '&' . $post->getReplyPermalink();

					if ($post->isQuestion()) {
						$permalink = $defaultQuestionPermalink . '&lang=' . $langcode;
					}

					$permalink = EDR::_($permalink, true, null, false);

				} else {
					$permalink = $defaultReplyPermalink;

					if ($post->isQuestion()) {
						$permalink = $defaultQuestionPermalink;
					}
				}

			} else {

				$permalink = EDR::_($defaultReplyPermalink, true, null, false);

				if ($post->isQuestion()) {
					$permalink = EDR::_($defaultQuestionPermalink, true, null, false);
				}
			}

			$options = array('actor_id' => JFactory::getUser()->id, 'uid' => $post->id, 'title' => JText::_('COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_LIKES'), 'type' => 'discuss', 'url' => $permalink);

			$rule = 'discuss.likes';
		}

		if ($action == 'new.mentions') {

			if (!$this->config->get('integration_easysocial_notify_mentions')) {
				return;
			}

			if (!$recipients) {
				return;
			}

			$defaultReplyPermalink = 'index.php?option=com_easydiscuss&view=post&id=' . $question->id . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $post->id;
			$defaultQuestionPermalink = 'index.php?option=com_easydiscuss&view=post&id=' . $post->id;

			if ($isSiteMultilingualEnabled) {

				// if the post post in language category
				if ($postCatLang != '*') {

					$permalink = EDR::_($defaultQuestionPermalink . '&lang=' . $langcode, true, null, false);

					if ($post->isReply()) {
						$permalink = EDR::_('view=post&id=' . $question->id . '&lang=' . $langcode, true, null, false) . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $post->id;
					}

				} else {
					$permalink = $defaultQuestionPermalink;

					if ($post->isReply()) {
						$permalink = $defaultReplyPermalink;
					}
				}

			} else {

				$permalink = EDR::_($defaultQuestionPermalink, true, null, false);

				if ($post->isReply()) {
					$permalink = EDR::_($defaultReplyPermalink, true, null, false);
				}
			}

			$options = array('actor_id' => $post->user_id, 'uid' => $post->id, 'title' => JText::sprintf('COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_MENTIONS', $question->title), 'type' => 'discuss', 'url' => $permalink);

			$rule = 'discuss.mentions';
		}

		if ($action == 'new.voteup' || $action == 'new.votedown') {

			if (!$this->config->get('integration_easysocial_notify_vote')) {
				return;
			}

			// The recipient should only be the post owner
			$recipients = array($post->user_id);

			$defaultReplyPermalink = 'index.php?option=com_easydiscuss&view=post&id=' . $question->id . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $post->id;
			$defaultQuestionPermalink = 'index.php?option=com_easydiscuss&view=post&id=' . $post->id;

			if ($isSiteMultilingualEnabled) {

				// if the post post in language category
				if ($postCatLang != '*') {

					$permalink = EDR::_('view=post&id=' . $question->id . '&lang=' . $langcode, true, null, false) . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $post->id;

					if ($post->isQuestion()) {
						$permalink = EDR::_('view=post&id=' . $post->id . '&lang=' . $langcode, true, null, false);
					}

				} else {
					$permalink = $defaultReplyPermalink;

					if ($post->isQuestion()) {
						$permalink = $defaultQuestionPermalink;
					}
				}

			} else {

				$permalink = EDR::_($defaultReplyPermalink, true, null, false);

				if ($post->isQuestion()) {
					$permalink = EDR::_($defaultQuestionPermalink, true, null, false);
				}
			}

			$string = ($action == 'new.voteup') ? 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_VOTE_UP' : 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_VOTE_DOWN';

			$options = array('actor_id' => JFactory::getUser()->id, 'uid' => $post->id, 'title' => JText::_($string), 'type' => 'discuss', 'url' => $permalink);

			$rule = 'discuss.vote';
		}

		if (empty($rule)) {
			return false;
		}

		// Send notifications to the receivers when they unlock the badge
		ES::notify($rule, $recipients, $emailOptions, $options);
	}

	/**
	 * Determines if the dropdown toolbar should be rendering easysocial items
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function hasToolbar($userId = null)
	{
		if (!$this->exists()) {
			return false;
		}

		if (!$this->config->get('integration_easysocial_toolbar') || !$this->exists()) {
			return false;
		}

		if (!is_null($userId)) {
			$user = ES::user($userId);

			if (!$user->hasCommunityAccess()) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Notify cluster members when discussion is created.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function notifyCluster($action, $post, $question = null, $comment = null, $actor = null)
	{
		$model = ES::model('Groups');

		$group = ES::group($post->isCluster());

		$options = array('exclude' => $post->user_id, 'state' => SOCIAL_GROUPS_MEMBER_PUBLISHED);
		$targets = $model->getMembers($group->id , $options);

		if (!$targets) {
			return;
		}

		// TODO: alert all group members when creating new discussion.
		$actor = ES::user($post->user_id);
		$params = new stdClass();
		$params->actor = $actor->getName();
		$params->userName = $actor->getName();
		$params->userLink = $actor->getPermalink(false, true);
		$params->userAvatar = $actor->getAvatar(SOCIAL_AVATAR_LARGE);
		$params->groupName = $group->getName();
		$params->groupAvatar = $group->getAvatar();
		$params->groupLink = $group->getPermalink(false, true);
		$params->title = $post->getTitle();
		$params->content = $post->getIntro();
		$params->permalink = $post->getPermalink();

		// Send notification e-mail to the target
		$options = new stdClass();
		$options->title = 'COM_EASYSOCIAL_EMAILS_GROUP_NEW_DISCUSSION_SUBJECT';
		$options->template = 'site/group/discussion.create';
		$options->params = $params;

		// Set the system alerts
		$system = new stdClass();
		$system->uid = $group->id;
		$system->title = JText::sprintf('COM_EASYSOCIAL_GROUPS_NOTIFICATION_NEW_DISCUSSION', $actor->getName(), $group->getName());
		$system->actor_id = $actor->id;
		$system->target_id = $group->id;
		$system->context_type = 'groups';
		$system->type = SOCIAL_TYPE_GROUP;
		$system->url = $params->permalink;
		$system->context_ids = $post->id;

		ES::notify('easydiscuss.group.create', $targets, $options, $system, $group->notification);
	}

	public function deleteDiscussStream($post, $cluster = false)
	{
		if (!$this->exists() || !$this->config->get('integration_easysocial_activity_new_question')) {
			return;
		}

		$stream = ES::stream();

		if ($cluster) {
			// If group post, delete the group app stream instead.
			$state = $stream->delete($post->id, 'easydiscuss');
		} else {
			$state = $stream->delete($post->id, 'discuss');
		}

		return $state;
	}

	public function getCluster($id, $type = 'group')
	{
		$type = 'load' . ucfirst($type);

		return $this->$type($id);
	}

	public function getPostsGroups($options = array())
	{
		$model = ED::model('groups');

		$posts = $model->getPostsGroups($options);

		return $posts;
	}

	public function formatGroupPosts($posts)
	{
		$threads = array();

		// Format normal entries
		$posts = ED::formatPost($posts);

		// Grouping the posts based on categories.
		foreach ($posts as $post) {

			if (!isset($threads[$post->group_id])) {
				$thread = new stdClass();
				$thread->group = ES::group($post->group_id);
				$thread->posts = array();

				$threads[$post->group_id] = $thread;
			}

			$threads[$post->group_id]->posts[] = $post;
		}

		return $threads;
	}

	public function renderMiniHeader($clusterId, $view = 'groups')
	{
		if (!$this->exists()) {
			return;
		}

		// load the group
		$group = $this->loadGroup($clusterId);

		if (!$group) {
			return;
		}

		$returnUrl = base64_encode(JRequest::getURI());

		// Initialize EasySocial's css files
		$this->init();

		$themes = ED::themes();

		$isMobile = ES::responsive()->isMobile();

		$extraClass = ($isMobile) ? ' is-mobile' : ' is-desktop';

		$output = '';

		ob_start();
		echo '<div id="es" class="es' . $extraClass . '" style="margin-bottom: 15px;">';
		echo $themes->output('site/groups/header.easysocial', array('group' => $group, 'view' => $view, 'returnUrl' => $returnUrl));
		echo '</div>';
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	public function loadGroup($groupId)
	{
		if (!$this->exists()) {
			return false;
		}

		$group = ES::group($groupId);

		if ($group->id) {
			return $group;
		}

		return false;
	}

	public function isGroupAppExists()
	{
		if (!$this->exists()) {
			return false;
		}

		static $items = array();

		$model = ES::model('Apps');

		$options = array(
			'group' => 'group'
			);

		$apps = $model->getApps($options);

		foreach ($apps as $app) {
			if ($app->element != 'easydiscuss') {
				continue;
			}

			if ($app->state > 0) {
				return true;
			}
		}

		return false;
	}

	public function decodeAlias($alias)
	{
		$id = $alias;

		if (strpos($alias , ':') !== false) {
			$parts = explode(':', $alias , 2);

			$id = $parts[0];
		}

		return $id;
	}

	/**
	 * Retrieve ES user's points
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getUserPoints($userId)
	{
		if (!$this->exists()) {
			return false;
		}

		if (!$this->config->get('integration_easysocial_points')) {
			return false;
		}

		if (!$userId) {
			return false;
		}

		$model = ES::model('Points');
		$point = $model->getPoints($userId);

		return $point;
	}
}
