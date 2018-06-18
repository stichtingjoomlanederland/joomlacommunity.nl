<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewEasyDiscuss extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		// Check for user's access
		$this->checkAccess('core.manage');

		// Set the panel title
		$this->title('COM_EASYDISCUSS_DASHBOARD');

		// This determines if the buttons should be visible to the viewer.
		if ($this->my->authorise('core.admin', 'com_easydiscuss')) {
			JToolBarHelper::preferences('com_easydiscuss');
		}

		// Get the dashboard model
		$model = ED::model('Dashboard');

		// Stats
		$totalPosts = $model->getTotalPosts();
		$totalCategories = $model->getTotalCategories();
		$totalTags = $model->getTotalTags();

		// Get posts graph
		$postsHistory = $model->getPostsGraph();

		// Format the ticks for the posts
		$postsTicks = array();
		$i = 0;

		foreach ($postsHistory->dates as $dateString) {
			
			// Normalize the date string first
			$dateString = str_ireplace('/', '-', $dateString);
			$date = ED::date($dateString);

			$postsTicks[] = $date->display('jS M');
		}

		$postsCreated = json_encode($postsHistory->count);
		$postsTicks = json_encode($postsTicks);	

		$categoryModel = ED::model('Categories', true);
		$items = $categoryModel->getAllCategories();
		$categories = array();

		foreach ($items as $item) {
			$category = ED::table('Category');
			$category->load($item->id);

			$categories[] = $category;
		}

		// Get the chart data
		$postsPie = $model->getPostsPie();
		$monthPie = $model->getMonthPie();
		$categoryPie = $model->getCategoryPie();

		$totalUsers = ED::model('users')->getTotalUsers();

		// Get the total of the user roles
		$totalUserRoles = ED::model('roles')->getTotalRoles();
		
		// Get the total of the post types
		$totalTypes = ED::model('posttypes')->getTotalTypes();

		$this->set('postsTicks', $postsTicks);
		$this->set('postsCreated', $postsCreated);
		$this->set('totalPosts', $totalPosts);
		$this->set('totalCategories', $totalCategories);
		$this->set('totalTags', $totalTags);
		$this->set('totalUsers', $totalUsers);
		$this->set('totalUserRoles', $totalUserRoles);
		$this->set('totalTypes', $totalTypes);
		$this->set('categories', $categories);
		$this->set('postsPie', $postsPie);
		$this->set('monthPie', $monthPie);
		$this->set('categoryPie', $categoryPie);

		parent::display('dashboard/default');
	}
}
