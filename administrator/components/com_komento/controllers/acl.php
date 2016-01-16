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

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'controller.php' );

class KomentoControllerAcl extends KomentoController
{
	public function change()
	{
		$mainframe	= JFactory::getApplication();
		$component = JRequest::getString( 'component', '' );
		$layout = JRequest::getCmd( 'layout', 'display' );
		$type = JRequest::getCmd( 'type', 'usergroup' );
		$id = JRequest::getInt( 'id', 0 );

		$mainframe->redirect( 'index.php?option=com_komento&view=acl&layout=' . $layout . '&component=' . $component . '&type=' . $type . '&id=' . $id );
	}

	public function apply()
	{
		$this->doSave();

		$mainframe	= JFactory::getApplication();
		$component = JRequest::getString( 'component', '' );
		$layout = JRequest::getCmd( 'layout', 'display' );
		$type = JRequest::getCmd( 'type', 'usergroup' );
		$id = JRequest::getInt( 'id', 0 );

		$mainframe->redirect( 'index.php?option=com_komento&view=acl&layout=' . $layout . '&component=' . $component . '&type=' . $type . '&id=' . $id );
	}

	public function cancel()
	{
		$mainframe	= JFactory::getApplication();
		$mainframe->redirect( 'index.php?option=com_komento' );
	}

	public function save()
	{
		$this->doSave();

		$mainframe	= JFactory::getApplication();
		$mainframe->redirect( 'index.php?option=com_komento' );
	}

	private function doSave()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe	= JFactory::getApplication();

		if( !JRequest::getMethod() == 'POST' )
		{
			$mainframe->enqueueMessage( JText::_('COM_KOMENTO_ACL_STORE_INVALID_REQUEST', 'error') );
			return false;
		}

		// Unset unecessary post data.
		$post	= JRequest::get( 'POST' );
		unset( $post['task'] );
		unset( $post['option'] );
		unset( $post['c'] );

		$token = Komento::_( 'getToken' );
		unset( $post[$token] );

		// check the target component
		if ( !$post['target_component'] )
		{
			$mainframe->enqueueMessage( JText::_('COM_KOMENTO_ACL_MISSING_TARGET_COMPONENT') );
			return false;
		}

		// rememeber user's choice
		// $mainframe->setUserState('com_komento.acl.component', $post['target_component']);

		// Save post data
		$model	= Komento::getModel( 'Acl', true );

		if ( !$model->save($post) )
		{
			$mainframe->enqueueMessage( JText::_('COM_KOMENTO_ACL_STORE_ERROR', 'error') );
			return false;
		}

		$mainframe->enqueueMessage( JText::_('COM_KOMENTO_ACL_STORE_SUCCESS', 'message') );

		// Clear the component's cache
		$cache = JFactory::getCache('com_komento');
		$cache->clean();

		return true;
	}
}
