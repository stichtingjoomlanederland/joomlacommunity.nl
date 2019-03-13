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

class EasyDiscussMigratorBase
{
	public function __construct()
	{
		$this->db = ED::db();
		$this->ajax = ED::ajax();
	}

	public function added($component, $internalId, $externalId, $type)
	{
		$migrator = ED::table('Migrators');
		$migrator->set('component', $component);
		$migrator->set('external_id', $externalId);
		$migrator->set('internal_id', $internalId);
		$migrator->set('type', $type);

		return $migrator->store();
	}

	public function easydiscussCategoryExists($category, $parentId = 0)
	{
		$title = JString::strtolower($category->title);
		$alias = JString::strtolower($category->alias);

		$query = 'select `id` from `#__discuss_category`';
		$query .= ' where lower(`title`) = ' . $this->db->Quote($title);
		$query .= ' AND lower(`alias`) = ' . $this->db->Quote($alias);
		$query .= ' LIMIT 1';

		$this->db->setQuery($query);
		$result = $this->db->loadResult();

		// If easydiscuss category doesn't exist, create a new category using Kunena's category data
		if (!$result) {
			$result = $this->createEasydiscussCategory($category, $parentId);
		}

		return $result;
	}

	public function createEasydiscussCategory($categoryObject, $parentId = 0)
	{
		if (empty($stats)) {
			$stats			= new stdClass();
			$stats->blog	= 0;
			$stats->category= 0;
			$stats->user	= array();
		}

		$category = ED::table('Category');

		$category->title = $categoryObject->title;
		$category->alias = JString::strtolower($categoryObject->alias);

		// If kunena did not define the category publishing state, default it to enabled.
		$category->published = !isset($categoryObject->published) ? true : $categoryObject->published;

		// Set the creator of the category
		$category->created_by = $this->getDefaultSuperUserId();

		$category->parent_id = $parentId;

		// Get category description
		if (isset($categoryObject->description)) {
			$category->description = $categoryObject->description;
		}

		// Now, try to save the category
		$state = $category->store(true);

		// now update the permission for this category.
		if ($state) {
			$model = ED::model('Category');
			$model->updateACL($category->id, array(), null, true);
		}

		return $category->id;
	}

	public function getDefaultSuperUserId()
	{
		$saUserId = '62';
		if (ED::getJoomlaVersion() >= '1.6') {

			$saUsers = ED::getSAUsersIds();

			$saUserId = '42';

			if (count($saUsers) > 0) {
				$saUserId = $saUsers[0];
			}
			
		}

		return $saUserId;
	}

}
