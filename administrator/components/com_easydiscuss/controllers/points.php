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

require_once DISCUSS_HELPERS . '/date.php';
require_once DISCUSS_HELPERS . '/input.php';
require_once DISCUSS_HELPERS . '/filter.php';

class EasyDiscussControllerPoints extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		// Need to explicitly define this in Joomla 3.0
		$this->registerTask( 'unpublish' , 'unpublish' );
		$this->registerTask( 'publish'	, 'unpublish' );
		$this->registerTask( 'saveNew'	, 'save' );
	}

	public function add()
	{
		$mainframe	= JFactory::getApplication();
		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=points&layout=form' );
		return;
	}

	public function remove()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );
		$ids		= JRequest::getVar( 'cid' );

		foreach( $ids as $id )
		{
			$point	= DiscussHelper::getTable( 'Points' );
			$point->load( $id );
			$point->delete();
		}

		$app	= JFactory::getApplication();

		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_BADGES_DELETED' ) , DISCUSS_QUEUE_SUCCESS );
		$app->redirect( 'index.php?option=com_easydiscuss&view=points' );
	}

	public function unpublish()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		$app	= JFactory::getApplication();
		$point	= DiscussHelper::getTable( 'Points' );
		$ids	= JRequest::getVar( 'cid' );
		$state	= JRequest::getVar( 'task' ) == 'publish' ? 1 : 0;

		foreach( $ids as $id )
		{
			$id	= (int) $id;
			$point->load( $id );
			$point->set( 'published' , $state );
			$point->store();
		}
		$message	= $state ? JText::_( 'COM_EASYDISCUSS_POINTS_PUBLISHED' ) : JText::_( 'COM_EASYDISCUSS_POINTS_UNPUBLISHED' );

		DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_SUCCESS );
		$app->redirect( 'index.php?option=com_easydiscuss&view=points' );
	}

	public function cancel()
	{
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=points' );

		return;
	}

	public function rules()
	{
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=rules&from=points' );

		return;
	}

	public function save()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		$app	= JFactory::getApplication();
		$point	= DiscussHelper::getTable( 'Points' );
		$id		= JRequest::getInt( 'id' );

		$point->load( $id );

		$post	= JRequest::get( 'POST' );
		$point->bind( $post );

		if( empty($point->created) )
		{
			$point->created = DiscussHelper::getDate()->toMySQL();
		}

		// Store the badge
		$point->store();

		$message	= !empty( $id ) ? JText::_( 'COM_EASYDISCUSS_POINTS_UPDATED' ) : JText::_( 'COM_EASYDISCUSS_POINTS_CREATED' );

		$url		= 'index.php?option=com_easydiscuss&view=points';

		if( JRequest::getVar( 'task' ) == 'saveNew' )
		{
			//$url	= 'index.php?option=com_easydiscuss&controller=points&layout=form';
			$url	= 'index.php?option=com_easydiscuss&view=points&layout=form';
		}

		DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_SUCCESS );
		$app->redirect( $url );
	}
}
