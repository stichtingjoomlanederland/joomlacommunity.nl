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

jimport('joomla.application.component.controller');

class EasyDiscussControllerCustomFields extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();


		$this->registerTask( 'orderup' , 'orderup' );
		$this->registerTask( 'orderdown' , 'orderdown' );

		$this->registerTask( 'add' , 'edit' );
		$this->registerTask( 'apply' , 'save' );
		$this->registerTask( 'savePublishNew' , 'save' );
		$this->registerTask( 'customfields.edit', 'edit' );
	}

	public function edit()
	{
		$mainframe	= JFactory::getApplication();
		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=customfields&layout=form' );
		return;
	}

	public function save()
	{
		$mainframe	= JFactory::getApplication();

		$message	= '';
		$type		= 'message';

		if( JRequest::getMethod() == 'POST' )
		{
			$post	= JRequest::get( 'post' );

			if(empty($post['title']))
			{
				DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_INVALID_CUSTOMFIELDS_TITLE') , DISCUSS_QUEUE_ERROR );

				$url  = 'index.php?option=com_easydiscuss&view=customfields';
				$mainframe->redirect(JRoute::_($url, false));
				return;
			}

			$my							= JFactory::getUser();
			$post['created_user_id']	= $my->id;
			$customId					= JRequest::getVar( 'custom_id' , '' );
			$field						= DiscussHelper::getTable( 'CustomFields' );
			$field->load( $customId );

			$currentOrder = $field->rebuild();

			switch( $post['type'] )
			{
				case 'text':
					$post['params'] = serialize( $post['textValue'] );
				break;

				case 'area':
					$post['params'] = serialize( $post['textAreaValue'] );
				break;

				case 'check':
					$post['params'] = serialize( $post['checkBoxValue'] );
				break;

				case 'radio':
					$post['params'] = serialize( $post['radioBtnValue'] );
				break;

				case 'select':
					$post['params'] = serialize( $post['selectValue'] );
				break;

				case 'multiple':
					$post['params'] = serialize( $post['multipleValue'] );
				break;

				default:
				break;
			}

			$field->bind( $post );

			$message	= JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_SAVED' );

			if (!$field->store())
			{
				JError::raiseError(500, $field->getError() );
			}

			// Save customfields ACL
			$model = $this->getModel( 'CustomFields' );

			// Pass in the custom field id and the form information
			$model->saveCustomFieldRule( $field->id, $post );

		}
		else
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_FORM_METHOD');
			$type		= 'error';
		}

		// ****After Saved****

		$task 	= $this->getTask();

		DiscussHelper::setMessageQueue( $message , $type );

		if( $task == 'savePublishNew' )
		{
			$mainframe->redirect( 'index.php?option=com_easydiscuss&view=customfields&layout=form' );
			return $mainframe->close();
		}

		if( $task == 'apply' )
		{
			$mainframe->redirect( 'index.php?option=com_easydiscuss&view=customfields&layout=form&id=' . $field->id );
			return $mainframe->close();
		}

		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=customfields' );
		return $mainframe->close();
	}

	public function cancel()
	{
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=customfields' );

		return;
	}

	public function remove()
	{
		$customs		= JRequest::getVar( 'cid' , '' , 'POST' );

		$message	= '';
		$type		= 'message';

		if( empty( $customs ) )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_CUSTOMFIELDS_ID');
			$type		= 'error';
		}
		else
		{
			$table		= DiscussHelper::getTable( 'CustomFields' );

			foreach( $customs as $custom )
			{
				$table->load( $custom );

				$ruleModel = $this->getModel( 'CustomFields' );
				$ruleModel->deleteCustomFieldsValue( $custom, 'field' );
				$ruleModel->deleteCustomFieldsRule( $custom );

				if( !$table->delete() )
				{
					$message	= JText::_( 'COM_EASYDISCUSS_REMOVE_CUSTOMFIELDS_ERROR' );
					$type		= 'error';
					DiscussHelper::setMessageQueue( $message , $type );
					$this->setRedirect( 'index.php?option=com_easydiscuss&view=customfields' );
					return;
				}
			}

			$message	= JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_DELETED');
		}
		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=customfields');
	}

	public function publish()
	{
		$customs	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $customs ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_CUSTOMFIELDS_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'CustomFields' );

			if( $model->publish( $customs , 1 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_PUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_PUBLISHED_ERROR');
				$type		= 'error';
			}

		}
		DiscussHelper::setMessageQueue( $message , $type );
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=customfields' );
	}

	public function unpublish()
	{
		$customs		= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $customs ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_CUSTOMFIELDS_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'CustomFields' );

			if( $model->publish( $customs , 0 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_UNPUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_UNPUBLISHED_ERROR');
				$type		= 'error';
			}

		}
		DiscussHelper::setMessageQueue( $message , $type );
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=customfields' );
	}

	public function orderdown()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		self::orderCustomfields(1);
	}

	public function orderup()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		self::orderCustomfields(-1);
	}

	public function orderCustomfields( $direction )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$app	= JFactory::getApplication();

		// Initialize variables
		$db		= DiscussHelper::getDBO();
		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );

		if (isset( $cid[0] ))
		{
			$row = DiscussHelper::getTable( 'CustomFields' );
			$row->load( (int) $cid[0] );
			$row->move($direction);
		}

		$app->redirect( 'index.php?option=com_easydiscuss&view=customfields');
		exit;
	}

	public function saveOrder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$app	= JFactory::getApplication();

		$row = DiscussHelper::getTable( 'CustomFields' );
		$row->rebuildOrdering();

		$message	= JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_ORDERING_SAVED');
		$type		= 'message';

		DiscussHelper::setMessageQueue( $message , $type );

		$app->redirect( 'index.php?option=com_easydiscuss&view=customfields' );
		exit;
	}
}
