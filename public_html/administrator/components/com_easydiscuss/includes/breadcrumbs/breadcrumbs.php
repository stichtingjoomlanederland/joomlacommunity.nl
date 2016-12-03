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

class EasyDiscussBreadcrumbs extends EasyDiscuss
{
	public function insert($title, $link = '')
	{
		JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );

		$pathway = $this->app->getPathway();

		return $pathway->addItem($title, $link);
	}

	/**
	 * Given a category object, construct the breadcrumbs
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function insertCategory(EasyDiscussCategory $category)
	{
		$view = 'categories';
		$id = $category->id;

		$active = $this->app->getMenu()->getActive();

		// if the current active menu is point to the same category, then we skip building the category pathway.
		if ($active && ((strpos($active->link, 'view=' . $view ) !== false) && (strpos($active->link, 'category_id=' . $id) !== false))) {
			return true;
		}

		$paths = $category->getBreadcrumbs();

		foreach ($paths as $path) {
			$this->insert($path->title, $path->link);
		}

		return true;
	}

}
