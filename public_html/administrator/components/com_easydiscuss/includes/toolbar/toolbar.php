<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussToolbar extends EasyDiscuss
{
	public function render($options = array())
    {
        // Get a list of available views
        $views = JFolder::folders(JPATH_COMPONENT . '/views');

        // Get the active view name
        $active = $this->input->get('view', '', 'cmd');

        // If the current active view doesn't exist on our known views, set the latest to be active by default.
        if (!in_array($active, $views)) {
        	$active = 'index';
        }

        $showToolbar = isset($options['showToolbar']) ? $options['showToolbar'] : $this->config->get('layout_enabletoolbar');
        $showHeader = isset($options['showHeader']) ? $options['showHeader'] : $this->config->get('layout_headers');
        $showSearch = isset($options['showSearch']) ? $options['showSearch'] : $this->config->get('layout_toolbar_searchbar');
        $showRecent = isset($options['showRecent']) ? $options['showRecent'] : $this->config->get('layout_toolbardiscussion');
        $showTags = isset($options['showTags']) ? $options['showTags'] : $this->config->get('layout_toolbartags');
        $showCategories = isset($options['showCategories']) ? $options['showCategories'] : $this->config->get('layout_toolbarcategories');
        $showUsers = isset($options['showUsers']) ? $options['showUsers'] : $this->config->get('layout_toolbarusers');
        $showBadges = isset($options['showBadges']) ? $options['showBadges'] : $this->config->get('layout_toolbarbadges');
        $showSettings = isset($options['showSettings']) ? $options['showSettings'] : $this->config->get('layout_toolbarprofile');
        $showLogin = isset($options['showLogin']) ? $options['showLogin'] : $this->config->get('layout_toolbarlogin');
        $showConversation = isset($options['showConversation']) ? $options['showConversation'] : $this->config->get('layout_toolbar_conversation');
        $showNotification = isset($options['showNotification']) ? $options['showNotification'] : ($this->config->get('layout_toolbar_notification') && $this->config->get('main_notifications'));
        $processLogic = isset($options['processLogic']) ? $options['processLogic'] : true;
        $renderToolbarModule = isset($options['renderToolbarModule']) ? $options['renderToolbarModule'] : true;


        // Get the headers for the toolbar
        $headers = new stdClass;
        $headers->title = JText::_($this->config->get('main_title'));
        $headers->desc = JText::_($this->config->get('main_description'));

        // temporary commented this is because if toolbar and header disable together, that error message will not show out on the page
        // if (!$showToolbar && !$showHeader) {
        //     //skip these all together since no toolbar will be loaded.
        //     return;
        // }

        $query = '';

        if ($showSearch) {
            // Search queries
            $query = $this->input->get('query', '', 'string');
        }


        // If a user is viewing a specific category, we need to ensure that it's setting the correct active menu
        $activeCategory = $this->input->get('category_id', 0, 'int');

        if ($activeCategory !== 0) {
        	$active = 'categories';
        }

        // Rebuild the views
        $tmp = new stdClass();

        foreach ($views as $key) {
            $tmp->$key = false;
        }

        // Reset back the views to the tmp variable
        $views = $tmp;

        // Set the active menu
        if (isset($views->$active)) {
            $views->$active = true;
        }

        if ($processLogic) {

            if ($active == 'profile') {

                $id = $this->input->get('id', 0, 'int');

                if ($this->my->id == $id || $id === 0) {
                    $views->$active = true;
                } else {
                    $views->index = true;
                }
            }

            // When the current viewer is reading a discussion item.
            if ($active == 'post') {
                $postId = $this->input->get('id', 0, 'int');

                if ($postId) {
                    $postModel = ED::model('Posts');
                    $categoryId = $postModel->getCategoryId($postId);

                    // Mark as read
                    ED::notifications()->clear($postId);
                }
            }
        }

        $notificationsCount = 0;
        $conversationsCount = 0;

        if ($showNotification) {
            // Get total notifications for the current viewer
            $model = ED::model('Notification');
            $notificationsCount = $model->getTotalNotifications($this->my->id);
        }

        if ($showConversation) {
            // Get new message count.
            $conversationModel = ED::model('Conversation');
            $conversationsCount = $conversationModel->getCount($this->my->id, array('filter' => 'unread'));
        }


        $postCatId = 0;

        $id = $this->input->get('id', 0, 'int');
        $post = ED::post($id);

        if ($id) {
            $postCatId = $post->category_id;
        }

        $header = '';
        $clusterId = '';

        // Retrieve the mini header for easysocial group.
        if ($active == 'post') {
        	$postId = $this->input->get('id');
        	$post = ED::post($postId);

        	$clusterId = $post->cluster_id;
        }

        if ($active == 'ask' || $active == 'groups') {
            $clusterId = $this->input->get('group_id', '', 'int');
        }

        $esLib = ED::easysocial();

        if ($clusterId) {
        	$header = $esLib->renderMiniHeader($clusterId, $active);
        }

        $group = $esLib->isGroupAppExists();

        // Get all the categories ids
        $sortConfig	= $this->config->get('layout_ordering_category','latest');

        // Set the default return url
        $return = EDR::getLoginRedirect();

        // Get any callback url and use it.
        $url = ED::getCallback();

        if ($url) {
            $return = base64_encode($url);
        }

        // Message queue
        $messageObject = ED::getMessageQueue();

        // Determines if we should use external conversations
        $useExternalConversations = false;

        if (ED::easysocial()->exists() && $this->config->get('integration_easysocial_messaging')) {
            $useExternalConversations = true;
        }

        if (ED::jomsocial()->exists() && $this->config->get('integration_jomsocial_messaging')) {
            $useExternalConversations = true;
        }

        $usernameField = 'COM_EASYDISCUSS_TOOLBAR_USERNAME';

        if (ED::easysocial()->exists() && $this->config->get('main_login_provider') == 'easysocial') {
            $usernameField = ED::easysocial()->getUsernameField();
        }

        $theme = ED::themes();
        $theme->set('active', $active);
        $theme->set('messageObject', $messageObject);
        $theme->set('conversationsCount', $conversationsCount);
        $theme->set('notificationsCount', $notificationsCount);
        $theme->set('return', $return );
        $theme->set('useExternalConversations', $useExternalConversations);
        $theme->set('categoryId', $activeCategory);
        $theme->set('views', $views);
        $theme->set('headers', $headers);
        $theme->set('query', $query);
        $theme->set('post', $post);
        $theme->set('header', $header);
        $theme->set('group', $group);
        $theme->set('usernameField', $usernameField);

        // settings
        $theme->set('showToolbar', $showToolbar);
        $theme->set('showHeader', $showHeader);
        $theme->set('showSearch', $showSearch);
        $theme->set('showRecent', $showRecent);
        $theme->set('showTags', $showTags);
        $theme->set('showCategories', $showCategories);
        $theme->set('showUsers', $showUsers);
        $theme->set('showBadges', $showBadges);
        $theme->set('showSettings', $showSettings);
        $theme->set('showLogin', $showLogin);
        $theme->set('showConversation', $showConversation);
        $theme->set('showNotification', $showNotification);
        $theme->set('renderToolbarModule', $renderToolbarModule);

        return $theme->output('site/toolbar/default');

	}
}
