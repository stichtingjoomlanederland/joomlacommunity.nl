<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ED::import('admin:/tables/table');

class DiscussPost_types extends EasyDiscussTable
{
	public $id = null;
	public $title = null;
	public $suffix = null;
	public $created = null;
	public $published = null;
	public $alias = null;
	public $type = null;

	public function __construct(&$db)
	{
		parent::__construct('#__discuss_post_types', 'id', $db);
	}

	public function delete($pk = null)
	{
		// @TODO: Delete association if needed
		$state = parent::delete($pk);
		return $state;
	}

	public function updateTopicPostType($oldValue)
	{
		$db = ED::getDBO();

		$query = 'update `#__discuss_posts` set `post_type` = ' . $db->Quote($this->alias);
		$query .= ' where `post_type` = ' . $db->Quote( $oldValue );

		$db->setQuery( $query );
		$db->query();
	}

	/**
	 * Retrieves a list of categories associated with this post type
	 *
	 * @since	4.0.14
	 * @access	public
	 */
	public function getCategories()
	{
		if ($this->type == 'global') {
			return array();
		}

		$model = ED::model('PostTypes');
		$categories = $model->getAssociatedCategories($this);

		return $categories;
	}
}
