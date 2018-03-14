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
     * Renders a post view via REST api
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function display($tpl = null)
    {
        // Get the user object
        $rest = ED::rest();
        $user = $rest->getUser();

        // Get the post 
        $id = $this->input->get('id', 0, 'int');
        $post = ED::post($id);

        // Ensure that the viewer can view the post
        if (!$post->canView($this->my->id)) {
            return $rest->error('COM_EASYDISCUSS_SYSTEM_POST_NOT_FOUND');
        }

        // Determine if user are allowed to view the discussion item that belong to another cluster.
        if ($post->isCluster()) {
            $easysocial = ED::easysocial();

            $cluster = $easysocial->getCluster($post->cluster_id, $post->getClusterType());

            if (!$cluster->canViewItem()) {
                return $rest->error('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS');
            }
        }

        $obj = $post->toData();

        $this->set('post', $obj);

        return parent::display();
    }

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
        $data['content'] = $this->input->get('content', '', 'raw');
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
        // Get user object
        $rest = ED::rest();
        $user = $rest->getUser();

        // Process when a new reply is made from bbcode / wysiwyg editor
        $data = JRequest::get('POST');

        // For contents, we need to get the raw data.
        $data['content'] = $this->input->get('content', '', 'raw');

        $reply = ED::post();
        $reply->bind($data);

        // Get the parent object
        $post = $reply->getParent();
        
        // Validate the data that is being submitted
        $valid = $reply->validate($data, 'replying');

        // if one of the validate is not pass through
        if ($valid === false) {
            return $rest->error($reply->getError());
        }

        // Try to save the reply object now.
        $state = $reply->save();

        if ($state === false) {
            return $rest->error($reply->getError());
        }


        $data = array('post' => $post->toData(), 'reply' => $reply->toData());

        $rest->success('COM_EASYDISCUSS_SUCCESS_REPLY_POSTED', $data);

        return parent::display();
    }
}
