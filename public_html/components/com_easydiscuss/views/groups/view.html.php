<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewGroups extends EasyDiscussView
{
	/**
	 * Displays the forums layout
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		$my = $this->my;

		// experimenting to get the group data based on user id
		$lib = ED::easysocial();

		// Check if the group app is exists or not.
		if (!$lib->isGroupAppExists()) {
			ED::getErrorRedirection(JText::_('COM_EASYDISCUSS_NOT_ALLOWED'));
		}
			
		$model = ED::model('groups');

		// If the categoryId is provided, this means that we're in the inner group.
		$groupId = $this->input->get('group_id', 0, 'int');
		$registry = new JRegistry();

		// Try to detect if there's any category id being set in the menu parameter.
		$activeMenu = $this->app->getMenu()->getActive();

		// If there is an active menu, render the params
		if ($activeMenu && !$groupId) {
			$registry->loadString($activeMenu->params);

			if ($registry->get('group_id')) {
				$groupId	= $registry->get('group_id');
			}
		}

		// Get the pagination limit
		$limit = $registry->get('limit',5);
		$limit = ($limit == '-2') ? ED::getListLimit() : $limit;
		$limit = ($limit == '-1') ? $this->jconfig->get('list_limit') : $limit;

		// Add view to this page.
		$this->logView();

		// Set page title.
		ED::setPageTitle('COM_EASYDISCUSS_TITLE_GROUP_DISCUSSIONS');

		// Set the meta of the page.
		ED::setMeta();

		// Add rss feed into headers
		ED::feeds()->addHeaders('index.php?option=com_easydiscuss&view=groups');		

		$options = array(
			'userId' => $my->id,
			'groupId' => $groupId,
			'limit' => $limit
			);

		$posts = $lib->getPostsGroups($options);

		$threads = '';

		// Format the posts.
		if ($posts) {
			$threads = $lib->formatGroupPosts($posts);
		}

		// Get the current active category
		$breadcrumbs = null;
		$header = null;

		// WIP : breadcrumbs
		if ($groupId) {	
			// $breadcrumbs = $activeCategory->getBreadcrumbs();
		}

		// Get the pagination
		$pagination = $model->getPagination();

		$this->set('breadcrumbs', $breadcrumbs);
		$this->set('pagination', $pagination);
		$this->set('threads', $threads);
		$this->set('includeChild', true);

		parent::display('groups/default');
	}

	public function listings()
	{
		$groupId = $this->input->get('group_id', 0, 'int');
		$registry = new JRegistry();

		$lib = ED::easysocial();

		// Try to detect if there's any category id being set in the menu parameter.
		$activeMenu = $this->app->getMenu()->getActive();

		// If there is an active menu, render the params
		if ($activeMenu && !$groupId) {
			$registry->loadString($activeMenu->params);

			if ($registry->get('group_id')) {
				$groupId = $registry->get('group_id');
			}
		}

		// Get the pagination limit
		$limit = $registry->get('limit',5);
		$limit = ($limit == '-2') ? ED::getListLimit() : $limit;
		$limit = ($limit == '-1') ? $this->jconfig->get('list_limit') : $limit;

		// Add view to this page.
		$this->logView();

		// Set page title.
		ED::setPageTitle();

		// Set the meta of the page.
		ED::setMeta();

		// Add rss feed into headers
		ED::feeds()->addHeaders('index.php?option=com_easydiscuss&view=forums&group_id=' . $groupId . '&layout=listings');

		// Get list of categories on the site.
		$model = ED::model('Posts');

		$options = array(
						'sort' => $registry->get('sort'),
						'limitstart' => $this->input->get('limitstart', 0),
						'filter' => $registry->get('filter'),
						'limit' => $this->config->get('layout_post_category_limit', $limit),
						'userId' => $this->my->id,
						'cluster_id' => $groupId
					);

		// Get all the posts in this category and it's childs
		$posts = $model->getDiscussions($options);

		$threads = array();

		// Format the posts.
		if ($posts) {
			$threads = $lib->formatGroupPosts($posts);
		}

		// Get the current active category
		$breadcrumbs = null;

		// WIP : breadcrumbs
		if ($groupId) {
			// $breadcrumbs = $activeCategory->getBreadcrumbs();
		}

		// Get the pagination
		$pagination = $model->getPagination();

		$this->set('breadcrumbs', $breadcrumbs);
		$this->set('threads', $threads);
		$this->set('pagination', $pagination);
		$this->set('includeChild', false);

		parent::display('groups/listings');

	}

	public function getBreadcrumbs()
	{

	}
}
