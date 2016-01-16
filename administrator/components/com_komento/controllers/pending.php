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

class KomentoControllerPending extends KomentoController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'add' , 'edit' );
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$comments	= JRequest::getVar( 'cid' , array(0) , 'POST' );
		$message	= '';
		$type		= 'message';

		if( empty( $comments ) )
		{
			$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID' );
			$type		= 'error';
		}
		else
		{
			$model		= Komento::getModel( 'comments' );

			if( $model->remove( $comments ) )
			{
				$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_REMOVED' );
			}
			else
			{
				$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_REMOVE_ERROR' );
				$type		= 'error';
			}
		}

		/*
		$parentId = JRequest::getInt('parentid', 0);
		$parentId = $this->getTable('comments')->load($parentId)->parent_id;
		$this->setRedirect( 'index.php?option=com_komento&view=comments&parentid=' . $parentId , $message , $type );
		*/

		$this->setRedirect( 'index.php?option=com_komento&view=pending' , $message , $type );
	}

	function publish( $publish = '1' )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$task = $this->getTask();

		if( $task === 'unpublish' )
		{
			$publish = 0;
		}

		$comments	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $comments ) <= 0 )
		{
			$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID' );
			$type		= 'error';
		}
		else
		{
			if( $publish )
			{
				$message	= 'COM_KOMENTO_COMMENTS_COMMENT_PUBLISHED';
			}
			else
			{
				$message	= 'COM_KOMENTO_COMMENTS_COMMENT_UNPUBLISHED';
			}

			$model		= Komento::getModel( 'comments' );

			if( !$model->publish( $comments , $publish ) )
			{
				$message .= '_ERROR';
				$type		= 'error';
			}

			$message = JText::_( $message );
		}

		$this->setRedirect( 'index.php?option=com_komento&view=pending' , $message , $type );
	}

	function unpublish()
	{
		$this->publish( '0' );
	}

	function saveColumns()
	{
		$columns = array(
			'comment',
			'published',
			'link',
			'edit',
			'component',
			'article',
			'date',
			'author',
			'email',
			'homepage',
			'ip',
			'latitude',
			'longitude',
			'address',
			'id'
		);

		$message = JText::_( 'COM_KOMENTO_COMMENTS_COLUMNS_SAVED' );
		$type = 'message';

		$data = JRequest::get( 'post' );

		foreach( $data as $key => $value )
		{
			if( !in_array( str_replace( 'column_', '', $key ), $columns ) )
			{
				unset($data[$key]);
			}
		}

		$config = Komento::getTable( 'configs' );
		$config->load( 'com_komento_pending_columns' );

		$config->component = 'com_komento_pending_columns';

		$params = $config ? $config->params : '';
		$registry = Komento::getRegistry( $params, 'json' );
		$registry->extend( $data );

		$config->params	= $registry->toString( 'json' );

		if( !$config->store() )
		{
			$message = $config->getError();
			$type = 'error';
		}

		$this->setRedirect( 'index.php?option=com_komento&view=pending' , $message , $type );
	}

	function clean()
	{
		// access with c=pending&task=clean
		// clean up comments
	}
}
