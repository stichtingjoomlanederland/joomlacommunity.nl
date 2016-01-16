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

class EasyDiscussControllerLabels extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask( 'add' , 'edit' );
		$this->registerTask( 'savepublishnew' , 'save' );
	}

	public function save()
	{
		$mainframe	= JFactory::getApplication();

		$message	= '';
		$type		= 'success';

		if( JRequest::getMethod() == 'POST' )
		{
			$post	= JRequest::get( 'post' );

			if(empty($post['title']))
			{
				$mainframe->enqueueMessage(JText::_('COM_EASYDISCUSS_INVALID_LABEL'), 'error');

				$url  = 'index.php?option=com_easydiscuss&view=labels';
				$mainframe->redirect(JRoute::_($url, false));
				return;
			}

			$my			= JFactory::getUser();
			$post['created_user_id']	= $my->id;
			$labelId	= JRequest::getVar( 'label_id' , '' );
			$label		= DiscussHelper::getTable( 'Label' );

			$label->load( $labelId );

			$label->bind( $post );

			$label->title = JString::trim($label->title);

			if (!$label->store())
			{
				JError::raiseError(500, $label->getError() );
			}
			else
			{
				$message	= JText::_( 'COM_EASYDISCUSS_LABEL_SAVED' );
			}
		}
		else
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_FORM_METHOD');
			$type		= 'error';
		}

		DiscussHelper::setMessageQueue( $message , $type );
		$saveNew	= JRequest::getBool( 'savenew' , false );
		$saveNew	= JRequest::getCmd( 'task' ) == 'savePublishNew';
		if( $saveNew )
		{
			$mainframe->redirect( 'index.php?option=com_easydiscuss&view=labels&task=labels.edit' );
			$mainframe->close();
		}

		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=labels' );
	}

	public function cancel()
	{
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=labels' );

		return;
	}

	public function edit()
	{
		JRequest::setVar( 'view', 'label' );
		JRequest::setVar( 'labelid' , JRequest::getVar( 'labelid' , '' , 'REQUEST' ) );

		parent::display();
	}

	public function remove()
	{
		$labels		= JRequest::getVar( 'cid' , '' , 'POST' );

		$message	= '';
		$type		= 'success';

		if( empty( $labels ) )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_LABEL_ID');
			$type		= 'error';
		}
		else
		{
			$table		= DiscussHelper::getTable( 'Label' );
			foreach( $labels as $label )
			{
				$table->load( $label );

				if( !$table->delete() )
				{
					$message	= JText::_( 'COM_EASYDISCUSS_REMOVE_LABEL_ERROR' );
					$type		= 'error';

					DiscussHelper::setMessageQueue( $message , $type );

					$this->setRedirect( 'index.php?option=com_easydiscuss&view=labels' );
					return;
				}
			}

			$message	= JText::_('COM_EASYDISCUSS_LABEL_DELETED');
		}

		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=labels' );
	}

	public function publish()
	{
		$labels	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'success';

		if( count( $labels ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_LABEL_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Labels' );

			if( $model->publish( $labels , 1 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_LABEL_PUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_LABEL_PUBLISH_ERROR');
				$type		= 'error';
			}

		}

		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=labels' );
	}

	public function unpublish()
	{
		$labels		= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'success';

		if( count( $labels ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_LABEL_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Labels' );

			if( $model->publish( $labels , 0 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_LABEL_UNPUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_LABEL_UNPUBLISH_ERROR');
				$type		= 'error';
			}

		}

		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=labels' );
	}

	public function orderdown()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		self::orderLabel(1);
	}

	public function orderup()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		self::orderLabel(-1);
	}

	public function orderLabel( $direction )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$app	= JFactory::getApplication();

		// Initialize variables
		$db		= DiscussHelper::getDBO();
		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );

		if (isset( $cid[0] ))
		{
			$row = DiscussHelper::getTable( 'Label' );
			$row->load( (int) $cid[0] );
			$row->move($direction);
		}

		$app->redirect( 'index.php?option=com_easydiscuss&view=labels');
		exit;
	}

	public function saveOrder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$app	= JFactory::getApplication();

		$row = DiscussHelper::getTable( 'Label' );
		$row->rebuildOrdering();

		$message	= JText::_('COM_EASYDISCUSS_LABELS_ORDERING_SAVED');
		$type		= 'message';
		DiscussHelper::setMessageQueue( $message , $type );
		$app->redirect( 'index.php?option=com_easydiscuss&view=labels' );
		exit;
	}
}
