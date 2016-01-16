<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

class DiscussPathwayHelper
{
	public function setPathway( $title , $link = '' )
	{
		JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );

		$app 		= JFactory::getApplication();

		$pathway 	= $app->getPathway();

		return $pathway->addItem( $title , $link );
	}

	public function setCategoryPathway( $category )
	{
		$view = 'categories';
		$id = $category->id;

		$activemenu = JFactory::getApplication()->getMenu();
		$activeitem = $activemenu->getActive();

		if( ! empty($activeitem) )
		{
			// if the current active menu is point to the same category, then we skip building the category pathway.
			if ( (strpos( $activeitem->link, 'view=' . $view ) !== false) && (strpos( $activeitem->link, 'category_id=' . $id ) !== false) ) {
				return true;
			}
		}

		$paths 	= $category->getPathway();

		foreach( $paths as $path )
		{
			self::setPathway( $path->title , $path->link );
		}
	}

}
