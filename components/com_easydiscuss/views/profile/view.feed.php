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

class EasyDiscussViewProfile extends EasyDiscussView
{
	function display( $tmpl = null )
	{

		$config = DiscussHelper::getConfig();

		if( !$config->get( 'main_rss') )
		{
			return;
		}

		$userid		= JRequest::getInt( 'id' , null );
		$user		= JFactory::getUser( $userid );

		$document		= JFactory::getDocument();
		$document->link	= DiscussRouter::_('index.php?option=com_easydiscuss&view=profile&id=' . $user->id );

		$profile	= DiscussHelper::getTable( 'Profile' );
		$profile->load( $user->id );

		$document->setTitle( $profile->getName() );
		$document->setDescription( $profile->getDescription() );

		$jConfig = DiscussHelper::getJConfig();

		$model	= $this->getModel( 'Posts' );
		$posts	= $model->getPostsBy( 'user' , $profile->id );

		$posts	= DiscussHelper::formatPost($posts);

		foreach( $posts as $row )
		{

			// Assign to feed item
			$title	= $this->escape( $row->title );
			$title	= html_entity_decode( $title );

			// load individual item creator class
			$item				= new JFeedItem();
			$item->title		= $title;
			$item->link			= JRoute::_('index.php?option=com_easydiscuss&view=post&id=' . $row->id );
			$item->description	= $row->content;
			$item->date			= DiscussHelper::getDate( $row->created )->toMySQL();

			if( !empty( $row->tags ) )
			{
				$tagData	= array();
				foreach( $row->tags as $tag )
				{
					$tagData[] = '<a href="' . JRoute::_('index.php?option=com_easydiscuss&view=tags&id=' . $tag->id ) . '">' . $tag->title . '</a>';
				}
				$row->tags	= implode(', ', $tagData);
			}
			else
			{
				$row->tags	= '';
			}

			$item->category		= $row->tags;
			$item->author		= $row->user->getName();

			if( $jConfig->get( 'feed_email' ) != 'none' )
			{
				if( $jConfig->get( 'feed_email' ) == 'author' )
				{
					$item->authorEmail	= $row->user->email;
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
