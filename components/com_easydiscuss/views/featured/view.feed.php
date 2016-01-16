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

require_once( DISCUSS_ROOT . '/views.php' );

class EasyDiscussViewFeatured extends EasyDiscussView
{
	function display( $tmpl = null )
	{
		$config = DiscussHelper::getConfig();

		if( !$config->get( 'main_rss') )
		{
			return;
		}

		$document		= JFactory::getDocument();
		$document->link	= JRoute::_('index.php?option=com_easydiscuss&view=featured');

		$sort			= JRequest::getString('sort', 'latest');
		$filter			= JRequest::getString('filter', 'allposts');
		$category		= JRequest::getInt( 'category_id' , 0 );
		$showFeaturedPost = true;

		$jConfig 		= DiscussHelper::getJConfig();

		$postModel		= $this->getModel('Posts');
		$posts			= $postModel->getData( true , $sort , null , $filter , $category, null, $showFeaturedPost);
		$pagination		= $postModel->getPagination( '0' , $sort , $filter , $category, $showFeaturedPost );

		$posts			= DiscussHelper::formatPost($posts);

		foreach( $posts as $row )
		{
			// Assign to feed item
			$title		= $this->escape( $row->title );
			$title		= html_entity_decode( $title );
			$category	= DiscussHelper::getTable( 'Category' );
			$category->load( $row->category_id );

			// load individual item creator class
			$item				= new JFeedItem();
			$item->title		= $title;
			$item->link			= JRoute::_('index.php?option=com_easydiscuss&view=post&id=' . $row->id );
			$item->description	= $row->content;
			$item->date			= DiscussHelper::getDate( $row->created )->toMySQL();
			$item->author		= $row->user->getName();
			$item->category		= $category->getTitle();

			if( $jConfig->get( 'feed_email' ) != 'none' )
			{
				if( $jConfig->get( 'feed_email' ) == 'author' )
				{
					$item->authorEmail	= $row->user->user->email;
				}
				else
				{
					$item->authorEmail	= $jConfig->get( 'mailfrom' );
				}
			}

			$document->addItem( $item );
		}
	}
}
