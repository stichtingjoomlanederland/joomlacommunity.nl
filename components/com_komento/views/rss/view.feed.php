<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( KOMENTO_ROOT . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'view.php' );

jimport( 'joomla.document.feed.feed' );
jimport( 'joomla.html.toolbar' );

class KomentoViewRss extends KomentoView
{
	function display( $tmpl = null )
	{
		$config	= Komento::getConfig();

		if( !$config->get( 'enable_rss') )
		{
			return;
		}

		require_once( KOMENTO_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );

		$component 		= JRequest::getString( 'component', 'all' );
		$cid			= JRequest::getInt( 'cid' );
		$cid 			= !$cid ? 'all' : $cid;

		$userid			= JRequest::getInt( 'userid', null );

		if( $component != 'all' && $cid != 'all' )
		{
			$application	= Komento::loadApplication( $component )->load( $cid );

			if( $application === false )
			{
				$application = Komento::getErrorApplication( $component, $cid );
			}

			$contentTitle	= ($component != 'all' && $cid != 'all') ? $application->getContentTitle() : '';
		}

		$username		= $userid !== null ? Komento::getProfile( $userid )->getName() : '';

		$document		= JFactory::getDocument();

		// to do: get permalink or view name from api/componentlist

		if($component != 'all' && $cid != 'all')
		{
			$document->link	= $application->getContentPermalink();
		}
		else
		{
			$document->link	= JURI::root();
		}

		// to do : get article name from api/componentlist

		// document title based on request parameter
		// integrate settings for rss
		// allow all component rss
		// allow all article rss
		// allow all user rss
		// allow all guest rss

		$document->setTitle( JText::_( 'COM_KOMENTO_FEEDS_LATEST_TITLE' ) );
		$document->setDescription( JText::_( 'COM_KOMENTO_FEEDS_LATEST_TITLE_DESCRIPTION' ) );

		if($component == 'all')
		{
			if($cid != 'all')
			{
				// impossible all component and specific article
				echo 'Error! Component = all, cid != all.';
				exit;
			}

			// all component all article

			if( is_null( $userid ) )
			{
				// all users/guests
				$document->setTitle( JText::_( 'COM_KOMENTO_FEEDS_ALL_COMMENTS_TITLE' ) );
				$document->setDescription( JText::_( 'COM_KOMENTO_FEEDS_ALL_COMMENTS_TITLE_DESCRIPTION' ) );
			}
			else if( $userid === 0 )
			{
				// for all guest $userid = 0
				$document->setTitle( JText::_( 'COM_KOMENTO_FEEDS_GUEST_COMMENTS_TITLE' ) );
				$document->setDescription( JText::_( 'COM_KOMENTO_FEEDS_GUEST_COMMENTS_TITLE_DESCRIPTION' ) );
			}
			else
			{
				// specific user
				$document->setTitle( JText::_( 'COM_KOMENTO_FEEDS_COMMENTS_FROM_USER_TITLE' ) . ' : ' . $username );
				$document->setDescription( JText::_( 'COM_KOMENTO_FEEDS_COMMENTS_FROM_USER_TITLE_DESCRIPTION' ) . ' : ' . $username);
			}
		}
		else
		{
			// specific component

			if($cid == 'all')
			{
				// all article

				if( is_null( $userid ) )
				{
					// all users/guests
					$document->setTitle( JText::_( 'COM_KOMENTO_FEEDS_COMMENTS_FOR_COMPONENT_TITLE' ) . ' : ' . Komento::loadApplication( $component )->getComponentName() );
					$document->setDescription( JText::_( 'COM_KOMENTO_FEEDS_COMMENTS_FOR_COMPONENT_TITLE_DESCRIPTION' ) );
				}
				else if( $userid === 0 )
				{
					// for all guest $userid = 0
					$document->setTitle( JText::_( 'COM_KOMENTO_FEEDS_GUEST_COMMENTS_FOR_COMPONENT_TITLE' ) . ' : ' . Komento::loadApplication( $component )->getComponentName() );
					$document->setDescription( JText::_( 'COM_KOMENTO_FEEDS_GUEST_COMMENTS_FOR_COMPONENT_TITLE_DESCRIPTION' ) );
				}
				else
				{
					// specific user
					$document->setTitle( JText::_( 'COM_KOMENTO_FEEDS_COMMENTS_FROM_USER_FOR_COMPONENT_TITLE' ) . ' : ' . $username . ' : ' . Komento::loadApplication( $component )->getComponentName() );
					$document->setDescription( JText::_( 'COM_KOMENTO_FEEDS_COMMENTS_FROM_USER_FOR_COMPONENT_TITLE_DESCRIPTION' ) . ' : ' . $username);
				}
			}
			else
			{
				// specific article

				if( is_null( $userid ) )
				{
					// all users/guests
					$document->setTitle( JText::_( 'COM_KOMENTO_FEEDS_COMMENTS_FOR_COMPONENT_OF_ARTICLE_TITLE' ) . ' : ' . Komento::loadApplication( $component )->getComponentName() . ' : ' . $contentTitle);
					$document->setDescription( JText::_( 'COM_KOMENTO_FEEDS_COMMENTS_FOR_COMPONENT_OF_ARTICLE_TITLE_DESCRIPTION' ) );
				}
				else if( $userid === 0 )
				{
					// for all guest $userid = 0
					$document->setTitle( JText::_( 'COM_KOMENTO_FEEDS_GUEST_COMMENTS_FOR_COMPONENT_OF_ARTICLE_TITLE' ) . ' : ' . Komento::loadApplication( $component )->getComponentName() . ' : ' . $contentTitle);
					$document->setDescription( JText::_( 'COM_KOMENTO_FEEDS_GUEST_COMMENTS_FOR_COMPONENT_OF_ARTICLE_TITLE_DESCRIPTION' ) );
				}
				else
				{
					// specific user
					$document->setTitle( JText::_( 'COM_KOMENTO_FEEDS_COMMENTS_FROM_USER_FOR_COMPONENT_TITLE_OF_ARTICLE' ) . ' : ' . $username . ' : ' . Komento::loadApplication( $component )->getComponentName() . ' : ' . $contentTitle);
					$document->setDescription( JText::_( 'COM_KOMENTO_FEEDS_COMMENTS_FROM_USER_FOR_COMPONENT_TITLE_OF_ARTICLE_DESCRIPTION' ) . ' : ' . $username);
				}
			}
		}

		$options		= array(
			'sort'		=> 'latest',
			'limit'		=> $config->get( 'rss_max_items' ),
			'userid'	=> $userid === null ? 'all' : $userid,
			'threaded'	=> 0
		);
		$commentsModel	= Komento::getModel( 'comments' );
		$comments		= $commentsModel->getComments( $component, $cid, $options );

		if(!empty($comments))
		{
			foreach( $comments as $row )
			{
				$row		= Komento::getHelper( 'comment' )->process( $row );

				// Todo : configurable
				$title = 'Comment - ' . $row->created;

				// Assign to feed item
				$item				= new JFeedItem();
				$item->title 		= $title;
				$item->link 		= $row->permalink;
				$item->description 	= $row->comment;
				$item->date			= $row->unformattedDate;
				$item->author		= $row->name;
				$item->authorEmail	= $row->email;

				$document->addItem( $item );
			}
		}
	}
}
