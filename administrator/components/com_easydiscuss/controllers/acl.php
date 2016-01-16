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

class EasyDiscussControllerAcl extends EasyDiscussController
{
	function apply()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$app	= JFactory::getApplication();

		$cid 	= JRequest::getVar( 'cid' , null);
		$type 	= JRequest::getVar( 'type' , '');
		$add 	= JRequest::getVar( 'add' , '');

		$result		= $this->_store();

		$task = 'edit';
		if($result['type']=='error')
		{
			$task = empty($add)? 'edit':'add';
		}

		DiscussHelper::setMessageQueue( $result[ 'message' ] , $result[ 'type' ] );
		$app->redirect( 'index.php?option=com_easydiscuss&controller=acl&task='.$task.'&cid='.$cid.'&type='.$type );
	}

	function cancel()
	{
		$app 		= JFactory::getApplication();
		$app->redirect( 'index.php?option=com_easydiscuss&view=acls' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'acl' );
		JRequest::setVar( 'cid' , JRequest::getVar( 'cid' , '' ) );
		JRequest::setVar( 'type' , JRequest::getVar( 'type' , '' ) );

		parent::display();
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$app		= JFactory::getApplication();

		$result		= $this->_store();

		DiscussHelper::setMessageQueue( $result[ 'message' ] , $result[ 'type' ] );
		$app->redirect( 'index.php?option=com_easydiscuss&view=acls' );
	}

	function _store()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe	= JFactory::getApplication();

		$message	= '';
		$type		= 'success';

		if( JRequest::getMethod() == 'POST' )
		{
			$cid 		= JRequest::getVar( 'cid' , null , 'POST' );
			$acltype	= JRequest::getVar( 'type' , '' , 'POST' );
			$name 		= JRequest::getVar( 'name' , '' , 'POST' );

			if(!is_null($cid) || !empty($acltype))
			{
				$model = $this->getModel( 'Acl' );

				$db = DiscussHelper::getDBO();

				if($model->deleteRuleset($cid, $acltype))
				{
					$postArray	= JRequest::get( 'post' );
					$saveData	= array();

					// Unset unecessary data.
					unset( $postArray['task'] );
					unset( $postArray['option'] );
					unset( $postArray['c'] );
					unset( $postArray['cid'] );
					unset( $postArray['name'] );
					unset( $postArray['type'] );

					foreach( $postArray as $index => $value )
					{
						if( $index != 'task' );
						{
							$saveData[ $index ]	= $value;
						}
					}

					if( $model->insertRuleset( $cid, $acltype, $saveData ) )
					{
						$message	= JText::_( 'ACL settings successfully saved.' );
					}
					else
					{
						$message	= JText::_( 'There was an error while trying to save the ACL settings.' );
						$type		= 'error';
					}
				}
				else
				{
					$message	= JText::_( 'There was an error while trying to update the ACL.' );
					$type		= 'error';
				}
			}
			else
			{
				$message	= JText::_( 'Invalid ID or ACL type, please try again.' );
				$type		= 'error';
			}
		}
		else
		{
			$message	= JText::_('Invalid request method. This form needs to be submitted through a "POST" request.');
			$type		= 'error';
		}

		return array( 'message' => $message , 'type' => $type);
	}

	function add()
	{
		JRequest::setVar( 'view', 'acl' );
		JRequest::setVar( 'add' , true );
		JRequest::setVar( 'type' , 'assigned' );

		parent::display();
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe	= JFactory::getApplication();

		$bloggers	= JRequest::getVar( 'cid' , '' , 'POST' );

		$message	= '';
		$type		= 'success';

		if( empty( $bloggers ) )
		{
			$message	= JText::_('Invalid blogger id');
			$type		= 'error';
		}
		else
		{
			$model = $this->getModel( 'Acl' );
			foreach( $bloggers as $id )
			{
				$ruleset = $model->getRuleSet('assigned', $id);

				if(!empty($ruleset->id))
				{
					if( !$model->deleteRuleset($id, 'assigned') )
					{
						$message	= JText::_( 'Error removing blogger, ' . $ruleset->name );
						$type		= 'error';
						DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_ERROR );
						$mainframe->redirect( 'index.php?option=com_easydiscuss&view=acls' );
						return;
					}
				}
			}

			$message	= JText::_('Blogger(s) deleted');
		}

		DiscussHelper::setMessageQueue( $message , $type );
		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=acls' );
	}
}
