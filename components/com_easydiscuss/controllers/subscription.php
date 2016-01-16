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
jimport( 'joomla.mail.helper' );

class EasyDiscussControllerSubscription extends EasyDiscussController
{
	/**
	 * Processes user subscription.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	null
	 */
	public function subscribe()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		$app 	= JFactory::getApplication();
		$my		= JFactory::getUser();
		$config	= DiscussHelper::getConfig();

		// Get variables from post.
		$type 	= JRequest::getVar( 'type' , null );
		$name 	= JRequest::getVar( 'subscribe_name' , '' );
		$email	= JRequest::getVar( 'subscribe_email' , '' );
		$interval 	= JRequest::getVar( 'subscription_interval' ,'' );
		$cid 		= JRequest::getInt( 'cid' , 0 );

		$redirect	= JRequest::getVar( 'redirect' , '' );

		if( empty( $redirect ) )
		{
			$redirect   = DiscussRouter::_( 'index.php?option=com_easydiscuss' , false );

			if ($type == 'category' && $cid) {
				$redirect = DiscussRouter::getCategoryRoute($cid, false);
			}
		}
		else
		{
			$redirect   = base64_decode($url);
		}

		// Apply filtering on the name.
		$filter 	= JFilterInput::getInstance();
		$name 		= $filter->clean( $name , 'STRING' );
		$email 		= JString::trim( $email );
		$name 		= JString::trim( $name );

		if( !JMailHelper::isEmailAddress($email) )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_INVALID_EMAIL') , 'error' );
			$app->redirect( $redirect );
			$app->close();
		}

		// Check for empty email
		if( empty( $email ) )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_EMAIL_IS_EMPTY') , 'error' );
			$app->redirect( $redirect );
			$app->close();
		}

		// Check for empty name
		if( empty( $name ) )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_NAME_IS_EMPTY') , 'error' );
			$app->redirect( $redirect );
			$app->close();
		}

		$model 			= DiscussHelper::getModel( 'Subscribe' );
		$subscription	= $model->isSiteSubscribed( $type , $email , $cid );

		$data = array();
		$data['type']		= $type;
		$data['userid']		= $my->id;
		$data['email']		= $email;
		$data['cid']		= $cid;
		$data['member']		= ($my->id)? '1':'0';
		$data['name']		= ($my->id)? $my->name : $name;
		$data['interval']	= $interval;

		if( $subscription )
		{
			// Perhaps the user tried to change the subscription interval.
			if( $subscription->interval == $interval )
			{
				DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_SUBSCRIPTION_UPDATED_SUCCESSFULLY' ) , 'success' );
				$app->redirect( $redirect );
				return $app->close();
			}

			// User changed their subscription interval.
			if( !$model->updateSiteSubscription( $subscription->id , $data ) )
			{
				//if($model->updateSiteSubscription($subRecord['id'], $subscription_info))
				DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_SUBSCRIPTION_FAILED' ) , 'error' );
				$app->redirect( $redirect );
				return $app->close();
			}

			// If the user already has an existing subscription, just let them know that their subscription is already updated.
			$intervalMessage 	= JText::_( 'COM_EASYDISCUSS_SUBSCRIPTION_INTERVAL_' . strtoupper( $interval ) );

			DiscussHelper::setMessageQueue( JText::sprintf( 'COM_EASYDISCUSS_SUBSCRIPTION_UPDATED' , $intervalMessage ) , 'success' );
			$app->redirect( $redirect );
			return $app->close();
		}

		// Only new records are added here.
		if( !$model->addSubscription( $data ) )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_SUBSCRIPTION_FAILED' ) , 'error' );
			$app->redirect( $redirect );
			return $app->close();
		}

		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_SUBSCRIPTION_UPDATED_SUCCESSFULLY' ) , 'success' );
		$app->redirect( $redirect );
		return $app->close();
	}

	function unsubscribe()
	{
		$my = JFactory::getUser();

		$redirectLInk = 'index.php?option=com_easydiscuss&view=profile#Subscriptions';

		if( $my->id == 0)
		{
			$redirectLInk = 'index.php?option=com_easydiscuss&view=index';
		}


		//type=site - subscription type
		//sid=1 - subscription id
		//uid=42 - user id
		//token=0fd690b25dd9e4d2dc47a252d025dff4 - md5 subid.subdate
		$data = base64_decode(JRequest::getVar('data', ''));

		$param = DiscussHelper::getRegistry($data);
		$param->type	= $param->get('type', '');
		$param->sid		= $param->get('sid', '');
		$param->uid		= $param->get('uid', '');
		$param->token	= $param->get('token', '');

		$subtable = DiscussHelper::getTable( 'Subscribe' );
		$subtable->load($param->sid);

		$token		= md5($subtable->id.$subtable->created);
		$paramToken = md5($param->sid.$subtable->created);

		if (empty($subtable->id))
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_SUBSCRIPTION_NOT_FOUND') , 'error');
			$this->setRedirect(DiscussRouter::_($redirectLInk, false));
			return false;
		}

		if($token != $paramToken)
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE_FAILED') , 'error');
			$this->setRedirect(DiscussRouter::_($redirectLInk, false));
			return false;
		}

		if(!$subtable->delete($param->sid))
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE_FAILED_ERROR_DELETING_RECORDS') , 'error');
			$this->setRedirect(DiscussRouter::_($redirectLInk, false));
			return false;
		}


		DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE_SUCCESS') );
		$this->setRedirect(DiscussRouter::_($redirectLInk, false));
		return true;
	}
}
