<?php
/**
* @package  Komento
* @copyright Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( KOMENTO_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php');

class KomentoViewComment extends KomentoAdminView
{
	var $tag	= null;

	function display($tpl = null)
	{
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();
		$components	= array();
		$result		= Komento::getHelper( 'components' )->getAvailableComponents();

		if( Komento::joomlaVersion() >= '1.6' )
		{
			if(!$user->authorise('komento.manage.comments' , 'com_komento') )
			{
				$mainframe->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				$mainframe->close();
			}
		}

		//Load pane behavior
		jimport('joomla.html.pane');

		$commentId		= JRequest::getVar( 'commentid' , '' );

		$comment		= Komento::getTable( 'Comments' );

		$comment->load( $commentId );

		$this->comment	= $comment;

		// Set default values for new entries.
		if( empty( $comment->created ) )
		{
			Komento::import( 'helper', 'date' );
			$date   = KomentoDateHelper::getDate();
			$now 	= KomentoDateHelper::toFormat($date);

			$comment->created	= $now;
			$comment->published	= true;
		}

		// Set all non published comments to unpublished
		if( $comment->published != 1 )
		{
			$comment->published = 0;
		}

		// @task: Translate each component with human readable name.
		foreach( $result as $item )
		{
			$components[] = JHTML::_( 'select.option', $item, Komento::loadApplication( $item )->getComponentName() );
		}

		$this->assignRef( 'comment'		, $comment );
		$this->assignRef( 'components'		, $components );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		JToolBarHelper::back();
		JToolBarHelper::divider();

		if( $this->comment->id != 0 )
		{
	        JToolBarHelper::title( JText::_('COM_KOMENTO_EDITING_COMMENT'), 'comments' );
		}

		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
	}
}
