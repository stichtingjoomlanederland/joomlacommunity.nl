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

jimport('joomla.application.component.controller');

require_once DISCUSS_HELPERS . '/date.php';
require_once DISCUSS_HELPERS . '/input.php';
require_once DISCUSS_HELPERS . '/filter.php';

class EasyDiscussControllerBadges extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		// Need to explicitly define this in Joomla 3.0
		$this->registerTask( 'unpublish' , 'unpublish' );
		$this->registerTask( 'publish' 			, 'unpublish' );
		$this->registerTask( 'savePublishNew'	, 'save' );
	}

	public function edit()
	{
		JRequest::setVar( 'view', 'badge' );
		JRequest::setVar( 'id' , JRequest::getInt( 'id' , '' , 'REQUEST' ) );

		parent::display();
	}

	public function add()
	{
		$mainframe	= JFactory::getApplication();

		$mainframe->redirect( 'index.php?option=com_easydiscuss&controller=badges&task=edit' );
	}

	public function cancel()
	{
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=badges' );

		return;
	}

	public function remove()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );
		$ids		= JRequest::getVar( 'cid' );

		foreach( $ids as $id )
		{
			$badge	= DiscussHelper::getTable( 'Badges' );
			$badge->load( $id );
			$badge->delete();
		}

		$app	= JFactory::getApplication();

		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_BADGES_DELETED' ) , DISCUSS_QUEUE_SUCCESS );
		$app->redirect( 'index.php?option=com_easydiscuss&view=badges' );
	}

	public function unpublish()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		$app	= JFactory::getApplication();
		$badge	= DiscussHelper::getTable( 'Badges' );
		$ids	= JRequest::getVar( 'cid' );
		$state	= JRequest::getVar( 'task' ) == 'publish' ? 1 : 0;

		foreach( $ids as $id )
		{
			$id	= (int) $id;
			$badge->load( $id );
			$badge->set( 'published' , $state );
			$badge->store();
		}

		$message	= $state ? JText::_( 'COM_EASYDISCUSS_BADGES_PUBLISHED' ) : JText::_( 'COM_EASYDISCUSS_BADGES_UNPUBLISHED' );

		DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_SUCCESS );
		
		$app->redirect( 'index.php?option=com_easydiscuss&view=badges' );
	}

	/**
	 * Method to save a badge
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function save()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		$app	= JFactory::getApplication();
		$badge	= DiscussHelper::getTable( 'Badges' );
		$id		= JRequest::getInt( 'id' );
		$doc 	= JFactory::getDocument();

		// Load the badge.
		$badge->load( $id );

		$oldTitle	= $badge->title;

		$post	= JRequest::get( 'POST' );
		$badge->bind( $post );

		// Description might contain html codes
		$description 			= JRequest::getVar( 'description' , '' , 'post' , 'string' , JREQUEST_ALLOWRAW );
		$badge->description 	= $description;

		if( !$badge->created )
		{
			$badge->created = DiscussHelper::getDate()->toMySQL();
		}

		// Set the badge alias if necessary.
		if ($badge->title != $oldTitle || $oldTitle == '')
		{
			$badge->alias	= DiscussHelper::getAlias($badge->title);
		}

		// Get the current task
		$task 		= $this->getTask();

		// Test for rules here.
		if( !$badge->title || !$badge->description || !$badge->description )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_BADGE_SAVE_FAILED' ) , DISCUSS_QUEUE_ERROR );

			JRequest::setVar( 'view' , 'badge' );

			return parent::display();
		}

		$badge->store();

		if( $task == 'savePublishNew' )
		{
			$redirect = 'index.php?option=com_easydiscuss&controller=badges&task=edit';
		}
		else
		{
			$redirect = 'index.php?option=com_easydiscuss&view=badges';
		}

		$message	= !empty( $id ) ? JText::_( 'COM_EASYDISCUSS_BADGE_UPDATED' ) : JText::_( 'COM_EASYDISCUSS_BADGE_CREATED' );

		DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_SUCCESS );
		$app->redirect( $redirect );
	}
}
