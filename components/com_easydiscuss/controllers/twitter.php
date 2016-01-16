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
require_once JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';
require_once DISCUSS_CLASSES . '/twitter/consumer.php';

class EasyDiscussControllerTwitter extends JController
{
	function authorizeAccess()
	{
		$mainframe	= JFactory::getApplication();
		$session	= JFactory::getSession();

		$config				= DiscussHelper::getConfig();
		$consumerKey		= $config->get('integration_twitter_consumer_key');
		$consumerSecretKey	= $config->get('integration_twitter_consumer_secret_key');

		if( $session->has( 'twitter_oauth_request_token', 'discuss' ) )
		{
			//$request_token		= $session->get('twitter_oauth_token', 'discuss');
			$session_request	= JString::str_ireplace(',', "\r\n", $session->get('twitter_oauth_request_token', '', 'discuss'));
			$request_token		= DiscussHelper::getRegistry( $session_request );

			/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
			$connection = new DiscussTwitterOAuth($consumerKey, $consumerSecretKey, $request_token->get('oauth_token', ''), $request_token->get('oauth_token_secret', ''));

			/* Request access tokens from twitter */
			$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

			if(!empty($access_token['oauth_token']) && !empty($access_token['oauth_token_secret']))
			{
				if($session->has( 'twitter_oauth_access_token', 'discuss' ))
				{
					$session->clear( 'twitter_oauth_access_token', 'discuss' );
				}

				$session->set('twitter_oauth_access_token', "user_id=".$access_token["user_id"].",screen_name=".$access_token["screen_name"].",oauth_token=".$access_token['oauth_token'].",oauth_token_secret=".$access_token['oauth_token_secret'], 'discuss');

				$status = true;
				$msg 	= JText::_('COM_EASYDISCUSS_TWITTER_OAUTH_SUCCESS');
			}
			else
			{
				$status = false;
				$msg 	= JText::_('COM_EASYDISCUSS_TWITTER_OAUTH_FAILED');
			}
		}
		else
		{
			$status = false;
			$msg 	= JText::_('COM_EASYDISCUSS_TWITTER_USER_NOT_FOUND');
		}

		echo "<script language=javascript>window.opener.discuss.login.twitter.signin(".$status.", '".$msg."'); self.close();</script>";
	}

	function requestAccess()
	{
		$session = JFactory::getSession();

		if($session->has( 'twitter_oauth_request_token', 'discuss' ))
		{
			$session->clear( 'twitter_oauth_request_token', 'discuss' );
		}

		$config				= DiscussHelper::getConfig();
		$consumerKey		= $config->get('integration_twitter_consumer_key');
		$consumerSecretKey	= $config->get('integration_twitter_consumer_secret_key');

		$consumer			= new DiscussTwitterOAuth($consumerKey, $consumerSecretKey);
		$callback			= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&controller=twitter&task=authorizeAccess', false, true);
		$request_token		= $consumer->getRequestToken($callback);

		$session->set('twitter_oauth_request_token', "oauth_token=".$request_token['oauth_token'].",oauth_token_secret=".$request_token['oauth_token_secret'], 'discuss');

		$this->setRedirect( $consumer->getAuthorizeURL($request_token['oauth_token'], FALSE) );
	}

	function removeAccess()
	{
		$mainframe	= JFactory::getApplication();
		$user		= JFactory::getUser();
		$return		= DiscussRouter::_('index.php?option=com_easydiscuss&view=profile' , false );

		if( $user->id == 0 )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_TWITTER_USER_NOT_FOUND') , 'error');
			$this->setRedirect( $return );
		}

		$twitter	= DiscussHelper::getTable( 'Twitter' );
		if( !$twitter->load( $user->id ) )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_TWITTER_OAUTH_DOESNT_EXIST') , 'error');
			$this->setRedirect( $return );
		}

		$twitter->delete();
		DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_TWITTER_REQUIRE_AUTHENTICATION') );
		$this->setRedirect( $return );
	}
}
