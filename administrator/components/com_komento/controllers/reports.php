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

class KomentoControllerReports extends KomentoController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'add' , 'edit' );
	}

	function cancel()
	{
		$this->setRedirect( 'index.php?option=com_komento&view=reports' );

		return;
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
			$message	= JText::_('COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID');
			$type		= 'error';
		}
		else
		{
			$model		= Komento::getModel( 'comments' );

			if( $model->remove( $comments ) )
			{
				$message	= JText::_('COM_KOMENTO_COMMENTS_COMMENT_REMOVED');
			}
			else
			{
				$message	= JText::_('COM_KOMENTO_COMMENTS_COMMENT_REMOVE_ERROR');
				$type		= 'error';
			}
		}

		$this->setRedirect( 'index.php?option=com_komento&view=reports' , $message , $type );
	}

	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$task = $this->getTask();

		if( $task === 'unpublish' )
		{
			$this->unpublish();
			return;
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
			$model		= Komento::getModel( 'comments' );

			if( $model->publish( $comments , 1 ) )
			{
				$message	= JText::_('COM_KOMENTO_COMMENTS_COMMENT_PUBLISHED');
			}
			else
			{
				$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_PUBLISH_ERROR' );
				$type		= 'error';
			}

		}

		$this->setRedirect( 'index.php?option=com_komento&view=reports' , $message , $type );
	}

	function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$comments	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $comments ) <= 0 )
		{
			$message	= JText::_('COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID');
			$type		= 'error';
		}
		else
		{
			$model		= Komento::getModel( 'comments' );

			if( $model->unpublish( $comments ) )
			{
				$message	= JText::_('COM_KOMENTO_COMMENTS_COMMENT_UNPUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_KOMENTO_COMMENTS_COMMENT_UNPUBLISH_ERROR');
				$type		= 'error';
			}

		}

		$this->setRedirect( 'index.php?option=com_komento&view=reports' , $message , $type );
	}

	function clear()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$comments	= JRequest::getVar( 'cid' , array(0) , 'POST' );
		$message	= '';
		$type		= 'message';

		if( count( $comments ) <= 0 )
		{
			$message	= JText::_('COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID');
			$type		= 'error';
		}
		else
		{
			$model		= Komento::getModel( 'actions' );

			if( $model->clearReports( $comments ) )
			{
				$message	= JText::_('COM_KOMENTO_COMMENTS_COMMENT_FLAGS_CLEARED');
			}
			else
			{
				$message	= JText::_('COM_KOMENTO_COMMENTS_COMMENT_FLAGS_CLEARED_ERROR');
				$type		= 'error';
			}
		}

		$this->setRedirect( 'index.php?option=com_komento&view=reports' , $message , $type );

	}

	function saveColumns()
	{
		$columns = array(
			'comment',
			'published',
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
		$config->load( 'com_komento_reports_columns' );

		$config->component = 'com_komento_reports_columns';

		$params = $config ? $config->params : '';
		$registry = Komento::getRegistry( $params, 'json' );
		$registry->extend( $data );

		$config->params	= $registry->toString( 'json' );

		if( !$config->store() )
		{
			$message = $config->getError();
			$type = 'error';
		}

		$this->setRedirect( 'index.php?option=com_komento&view=reports' , $message , $type );
	}

	function clean()
	{
		// c=flags&task=clean
		// clean up flags
		// eg delete where comment_id not in (select id from comments)
	}
}
