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

require_once( DISCUSS_ROOT . '/views.php' );

class EasyDiscussViewTags extends EasyDiscussView
{
	function ajaxSubscribe($id)
	{
		$disjax		= new disjax();
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();

		$tag		= DiscussHelper::getTable( 'Tags' );
		$tag->load($id);

		$tpl	= new DiscussThemes();
		$tpl->set( 'tag', $tag );
		$tpl->set( 'my', $my );
		$html	= $tpl->fetch( 'ajax.subscribe.tag.php' );

		$options = new stdClass();
		$options->title = JText::sprintf('COM_EASYDISCUSS_SUBSCRIBE_TO_TAG', $tag->title);
		$options->content = $html;

		$buttons 			= array();

		$button 			= new stdClass();
		$button->title 		= JText::_( 'COM_EASYDISCUSS_BUTTON_CANCEL' );
		$button->action 	= 'disjax.closedlg();';
		$buttons[]			= $button;

		$button 			= new stdClass();
		$button->title 		= JText::_( 'COM_EASYDISCUSS_BUTTON_SUBSCRIBE' );
		$button->action 	= 'discuss.subscribe.tag(' . $tag->id . ')';
		$button->className 	= 'btn-primary';
		$buttons[]			= $button;

		$options->buttons 	= $buttons;

		$disjax->dialog($options);

		$disjax->send();
	}

	function ajaxAddSubscription($type='tag', $email, $name, $interval, $cid='0')
	{
		$disjax		= new Disjax();
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();
		$config 	= DiscussHelper::getConfig();
		$msg		= '';
		$msgClass	= 'dc_success';

		$subscription_info = array();
		$subscription_info['type'] = $type;
		$subscription_info['userid'] = $my->id;
		$subscription_info['email'] = $email;
		$subscription_info['cid'] = $cid;
		$subscription_info['member'] = ($my->id)? '1':'0';
		$subscription_info['name'] = ($my->id)? $my->name : $name;
		$subscription_info['interval'] = $interval;

		//validation
		if(JString::trim($subscription_info['email']) == '')
		{
			$disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
			$disjax->assign( 'dc_subscribe_notification .msg_in' , JText::_('COM_EASYDISCUSS_EMAIL_IS_EMPTY') );
			$disjax->script( 'EasyDiscuss.$( "#dc_subscribe_notification .msg_in" ).addClass( "dc_error" );' );
			$disjax->send();
			return;
		}

		if(JString::trim($subscription_info['name']) == '')
		{
			$disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
			$disjax->assign( 'dc_subscribe_notification .msg_in' , JText::_('COM_EASYDISCUSS_NAME_IS_EMPTY') );
			$disjax->script( 'EasyDiscuss.$( "#dc_subscribe_notification .msg_in" ).addClass( "dc_error" );' );
			$disjax->send();
			return;
		}

		$model	= $this->getModel( 'Subscribe' );
		$sid	= '';


		if($my->id == 0)
		{
			$sid = $model->isTagSubscribedEmail($subscription_info);
			if($sid != '')
			{
				//user found.
				// show message.
				$disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
				$disjax->assign( 'dc_subscribe_notification .msg_in' , JText::_('COM_EASYDISCUSS_ALREADY_SUBSCRIBED_TO_TAG') );
				$disjax->script( 'EasyDiscuss.$( "#dc_subscribe_notification .msg_in" ).addClass( "dc_success" );' );
				$disjax->send();
				return;

			}
			else
			{
				if(!$model->addSubscription($subscription_info))
				{
					$msg = JText::sprintf('COM_EASYDISCUSS_SUBSCRIPTION_FAILED');
					$msgClass = 'dc_error';
				}
			}
		}
		else
		{
			$sid = $model->isTagSubscribedUser($subscription_info);

			if($sid['id'] != '')
			{
				// user found.
				// update the email address
				if(!$model->updatePostSubscription($sid['id'], $subscription_info))
				{
					$msg = JText::sprintf('COM_EASYDISCUSS_SUBSCRIPTION_FAILED');
					$msgClass = 'dc_error';
				}
			}
			else
			{
				//add new subscription.
				if(!$model->addSubscription($subscription_info))
				{
					$msg = JText::sprintf('COM_EASYDISCUSS_SUBSCRIPTION_FAILED');
					$msgClass = 'dc_error';
				}
			}
		}

		$msg = empty($msg)? JText::_('COM_EASYDISCUSS_SUBSCRIPTION_SUCCESS') : $msg;

		$disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
		$disjax->assign( 'dc_subscribe_notification .msg_in' , $msg );
		$disjax->script( 'EasyDiscuss.$( "#dc_subscribe_notification .msg_in" ).addClass( "'.$msgClass.'" );' );
		$disjax->send();
		return;
	}
}
