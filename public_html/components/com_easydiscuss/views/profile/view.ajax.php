<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewProfile extends EasyDiscussView
{
	public function tab()
	{
		$type = $this->input->get('type');
		$profileId = $this->input->get('id');

		JRequest::setVar('limitstart', 0);
		JRequest::setVar('viewtype', $type);

		$model = ED::model('Posts');
		$tagsModel = ED::model('Tags');
		$assignedModel = ED::model('Assigned');

		$theme = ED::themes();
		$contents = '';
		$pagination	= null;

		$profile = ED::user($profileId);

		$tabTitle = JText::_('COM_EASYDISCUSS_PROFILE_TAB_' . strtoupper($type));

		// Append page title.
		$pageTitle = JText::sprintf('COM_EASYDISCUSS_PROFILE_PAGE_TITLE', $profile->getName(), $tabTitle);

		if ($type == 'easyblog') {
			$helperFile = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

			if (!$this->easyblog()) {
				$contents = JText::_('COM_EASYDISCUSS_EASYBLOG_DOES_NOT_EXIST');
			} else {
				$blogModel = EB::model('Blog');
				$blogs = $blogModel->getBlogsBy('blogger', $profileId);
				$blogs = EB::formatter('list', $blogs);
				$ebConfig = EB::config();
				$user = JFactory::getUser($profileId);

				// Load EasyBlog's language file
				JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);

				foreach ($blogs as $blog) {
					$theme->set('item', $blog);
					$theme->set('title', $blog->title);
					$theme->set('permalink', $blog->getPermalink());
					$contents .= $theme->output('site/profile/simple.item');
				}
			}
		
			$tabTitle = JText::_('COM_EASYDISCUSS_PROFILE_TAB_EASYBLOG');
			$this->ajax->resolve($contents, $pagination, $pageTitle);
			return $this->ajax->send();
		}

		if ($type == 'komento') {
			if (!$this->komento()) {
				$contents = JText::_('COM_EASYDISCUSS_KOMENTO_DOES_NOT_EXIST');
			} else {
				$commentsModel = KT::model('comments');

				$options = array(
					'sort' => 'latest',
					'userid' => $profileId,
					'threaded' => 0,
					'limit' => ED::getListLimit()
				);

				$comments = $commentsModel->getComments('all', 'all', $options);
				$contents = '';

				$comments = KT::formatter('comment', $comments);

				foreach($comments as &$comment) {
					$theme->set('item', $comment);
					$contents .= $theme->output('site/profile/komento.item');
				}
			}

			$limitstart = (int) $commentsModel->getState('limitstart');
			$limit = (int) $commentsModel->getState('limit');
			$total = (int) $commentsModel->_total;

			$pagination = ED::pagination($total, $limitstart, $limit);

			if ($pagination) {
				$filterArr = array();
				$filterArr['viewtype'] = $type;
				$filterArr['id'] = $profileId;

				$pagination = $pagination->getPagesLinks('profile', $filterArr, true);
			}

			$this->ajax->resolve($contents, $pagination, JText::_('COM_EASYDISCUSS_EMPTY_LIST'));
			return $this->ajax->send();
		}

		if ($type == 'unresolved') {
			$posts = $model->getUnresolvedFromUser($profileId);
			$pagination = $model->getPagination();
		}

		if ($type == 'questions') {
			$options = array('filter' => $type, 'userId' => $profileId, 'includeAnonymous' => false);

			// If the post is anonymous we shouldn't show to public.
			if (ED::user()->id == $profileId) {
				$options['includeAnonymous'] = true;
				$options['private'] = true;
				$options['includeCluster'] = true;
			}

			$posts = $model->getDiscussions($options);
			$pagination = $model->getPagination();
		}

		if ($type == 'favourites') {

			$options = array(
				'userId' => $profileId,
				'filter' => 'favourites'
				);

			// $posts = $model->getData(true, 'latest', null, 'favourites');
			$posts = $model->getDiscussions($options);
			$pagination	= $model->getPagination();
		}

		if ($type == 'assigned') {
			// retrieve the assiged post
			$posts = $assignedModel->getPosts($profileId);
			$pagination = $assignedModel->getPagination();
		}

		if ($type == 'replies') {
			$posts = $model->getRepliesFromUser($profileId, 'lastreplied');
			$pagination	= $model->getPagination();
		}

		if ($type == 'pending') {
			if (ED::isModerator() || ($this->my->id == $profile->id)) {
				$options = array(
					'filter' => $type, 
					'userId' => $profile->id, 
					'includeAnonymous' => true, 
					'includeCluster' => false,
					'private' => true,
					'published' => DISCUSS_ID_PENDING
				);

				$posts = $model->getDiscussions($options);
				$paginationModel = $model->getPagination();
			} else {
				$posts = array();
			}
		}

		// preload post items
		ED::post($posts);

		$posts = ED::formatPost($posts);

		foreach ($posts as $post) {
			$theme->set('post', $post);
			$contents .= $theme->output('site/profile/item');
		}

		if ($pagination) {
			$filterArr = array();
			$filterArr['viewtype'] = $type;
			$filterArr['id'] = $profileId;

			$pagination = $pagination->getPagesLinks('profile', $filterArr, true);
		}

		$this->ajax->resolve($contents, $pagination, $pageTitle);
		return $this->ajax->send();

	}

	public function easyblog()
	{
		$helperFile = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

		if (!Jfile::exists($helperFile)) {
			return false;
		}

		require_once($helperFile);
		return true;

	}

	public function komento()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_komento/includes/komento.php';
		$exists = JFile::exists($file);

		if (!$exists) {
		 return false;
		}

		require_once($file);
		return true;
	}

	public function filter($viewtype = 'user-post', $profileId = null)
	{
		$ajax = new Disjax();
		$mainframe = JFactory::getApplication();
		$config = DiscussHelper::getConfig();
		$acl = DiscussHelper::getHelper('ACL');

		$sort = 'latest';
		$data = null;
		$pagination = null;
		$model = ED::model('Posts');
		$tagsModel = ED::model('Tags');


		switch ($viewtype) {
			case 'user-achievements':

				$profile = ED::user($profileId);
				$data = $profile->getBadges();

				break;

			case 'user-tags':
				$data = $tagsModel->getTagCloud('', '', '', $profileId);
				break;

			case 'user-replies':
				$data = $model->getRepliesFromUser($profileId, 'lastreplied');
				$pagination = $model->getPagination();
				DiscussHelper::formatPost($data);
				break;

			case 'user-unresolved':
				$data = $model->getUnresolvedFromUser($profileId);
				$pagination = $model->getPagination();
				DiscussHelper::formatPost($data);

				break;

			case 'user-post':
			default:

				if (is_null($profileId)) {
					break;
				}

				$model = ED::model('Posts');
				$data = $model->getPostsBy('user', $profileId);
				$data = DiscussHelper::formatPost($data);
				$pagination = $model->getPagination();
				break;
		}

		// replace the content
		$content = '';
		$tpl = new DiscussThemes();

		$tpl->set('profileId', $profileId);

		if ($viewtype == 'user-post' || $viewtype == 'user-replies' || $viewtype == 'user-unresolved') {
			$nextLimit = DiscussHelper::getListLimit();
			if ($nextLimit >= $pagination->total) {
				$ajax->assign($viewtype . ' #dc_pagination', '');
			}

			$tpl->set('posts', $data);
			$content = $tpl->fetch('main.item.php');

			$ajax->assign($viewtype . ' #dc_list', $content);

			//reset the next start limi
			$ajax->value('pagination-start', $nextLimit);

			if ($nextLimit < $pagination->total) {
				$filterArr  = array();
				$filterArr['viewtype'] = $viewtype;
				$filterArr['id'] = $profileId;
				$ajax->assign($viewtype . ' #dc_pagination', $pagination->getPagesLinks('profile', $filterArr, true));
			}
		} else if($viewtype == 'user-tags') {
			$tpl->set('tagCloud', $data);
			$content = $tpl->fetch('tags.item.php');

			$ajax->assign('discuss-tag-list' , $content);

		} else if ($viewtype == 'user-achievements') {
			$tpl->set('badges', $data);
			$content = $tpl->fetch('users.achievements.list.php');
			$ajax->assign('user-achievements' , $content);
		}

		$ajax->script('discuss.spinner.hide("profile-loading");');
		$ajax->script('EasyDiscuss.$("#' . $viewtype . '").show();');
		$ajax->script('EasyDiscuss.$("#' . $viewtype. ' #dc_pagination").show();');

		$ajax->send();
	}

	/**
	 * Method to avatar crop
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function cropPhoto()
	{
		if (!$this->my->id) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED'));
		}

		$this->my = ED::user($this->my->id);

		$path = rtrim($this->config->get('main_avatarpath'), DIRECTORY_SEPARATOR);
		$path = JPATH_ROOT . '/' . $path;

		$photoPath = $path . '/' . $this->my->avatar;
		$originalPath = $path . '/' . 'original_' . $this->my->avatar;
		// @rule: Delete existing image first.
		if (JFile::exists($photoPath)) {
			JFile::delete($photoPath);
		}

		$x1 = $this->input->get('x');
		$y1 = $this->input->get('y');
		$width = $this->input->get('w');
		$height = $this->input->get('h');

		if (is_null($x1) && is_null($y1) && is_null($width) && is_null($height)) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_AVATAR_UNABLE_TO_CROP'));
		}

		$image = ED::simpleimage();
		$image->load($originalPath);

		$image->crop($width, $height, $x1, $y1);

		$image->resize(160, 160);

		$image->save($photoPath);

		$path = trim($this->config->get('main_avatarpath') , '/') . '/' . $this->my->avatar;
		$uri = rtrim(JURI::root() , '/');
		$uri .= '/' . $path;

		return $this->ajax->resolve($uri, 'COM_EASYDISCUSS_AVATAR_CROPPED_SUCCESSFULLY');
	}

	/**
	 * Method to remove the avatar
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeAvatar()
	{
		if (!$this->my->id) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED'));
		}

		$theme = ED::themes();

		$namespace = 'site/user/dialog.photo.delete';
		$output = $theme->output($namespace);

		return $this->ajax->resolve($output);
	}

	/**
	 * Checks if an alias is valid
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function ajaxCheckAlias()
	{
		$alias = $this->input->get('alias', '', 'default');

		// Only allow registered users
		if ($this->my->guest) {
			return;
		}

		// satinize input
		$filter	= JFilterInput::getInstance();
		$alias = $filter->clean($alias, 'STRING');

		// check for existance
		$db = ED::db();
		$query	= 'SELECT `alias` FROM `#__discuss_users` WHERE `alias` = ' . $db->quote($alias) . ' '
				. 'AND ' . $db->nameQuote('id') . '!=' . $db->Quote($this->my->id);
		$db->setQuery($query);

		$exists = $db->loadResult();

		$message = JText::_('COM_EASYDISCUSS_ALIAS_AVAILABLE');

		if ($exists) {
			$message = JText::_('COM_EASYDISCUSS_ALIAS_NOT_AVAILABLE');
		}

		return $this->ajax->resolve($exists, $message);
	}

	/**
	 * Mark all post as read
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function ajaxMarkAllRead()
	{
		if ($this->my->guest) {
			return;
		}

		// Get all unread post
		$model = ED::model('Posts');
		$posts = $model->getUnread($this->my->id);

		if (!$posts) {
			return $this->ajax->resolve(JText::_('COM_EASYDISCUSS_NO_UNREAD_POSTS'));
		}

		$user = ED::user($this->my->id);

		// Mark them as read
		foreach ($posts as $post){
			$user->read($post->id);
		}

		return $this->ajax->resolve(JText::_('COM_EASYDISCUSS_MARKED_READ_POSTS'));
	}

	/**
	 * show user mini header in popbox style
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function popbox()
	{
		$id = $this->input->get('id', 0, 'int');

		// guest should not allowed.
		if (!$id) {
			return $this->ajax->fail(JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE'));
		}

		$user = ED::user($id);

		$theme = ED::themes();
		$theme->set('user', $user);
		$contents = $theme->output('site/html/user.popbox');

		return $this->ajax->resolve($contents);
	}


	/**
	 * show user mini header in popbox style
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function confirmDownload()
	{
		$userId = $this->my->id;

		$table = ED::table('download');
		$table->load(array('userid' => $userId));
		$state = $table->getState();

		$email = $this->my->email;
		$emailPart = explode('@', $email);
		$email = JString::substr($emailPart[0], 0, 2) . '****' . JString::substr($emailPart[0], -1) . '@' . $emailPart[1];

		$theme = ED::themes();
		$theme->set('userId', $userId);
		$theme->set('email', $email);
		$output = $theme->output('site/user/dialogs/gdpr.confirm');

		return $this->ajax->resolve($output);
	}
}
