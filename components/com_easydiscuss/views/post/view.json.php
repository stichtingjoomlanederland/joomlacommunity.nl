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

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewPost extends EasyDiscussView
{
    /**
     * Allows API to submit a new discussion
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function submit()
    {
        $rest = ED::rest();

        // Get the user object
        $user = $rest->getUser();

        // Required fields
        $data = JRequest::get('post');

        // For contents, we need to get the raw data.
        $data['content'] = $this->input->get('dc_content', '', 'raw');
        $data['user_id'] = $user->id;

        $post = ED::post();
        $post->bind($data);

        // Validate
        $valid = $post->validate();

        if (!$valid) {
            return $rest->error($post->getError());
        }

        // Try to save the discussion now.
        $state = $post->save();

        if (!$state) {
            return $rest->error($post->getError());
        }


        return $rest->success(JText::_("COM_EASYDISCUSS_POST_STORED"), $post->toData());
    }

    /**
     * Allows API to reply to a discussion
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function reply()
    {
        $rest = ED::rest();

        $user = $rest->getUser();

        // Required fields
        $content = $this->input->get('content', '', 'default');
        $postId = $this->input->get('post_id', '', 'int');

        if (!$content) {
            return $rest->error("Invalid content provided");
        }

        if (!$postId) {
            return $rest->error("Invalid post id provided. You cannot reply to an unknown post.");
        }

        $reply = ED::table('Post');
        $reply->parent_id = $postId;
        $reply->content = $content;
        $reply->user_id = $user->id;
        $reply->published = true;
        $reply->store();

        $rest->success('Reply submitted successfully', $reply->toRest());
    }
}
