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

require_once DISCUSS_CLASSES . '/json.php';
require_once DISCUSS_CLASSES . '/twitter/twitteroauth.php';

class DiscussTwitterHelper
{
	function post( $access_token, $message )
	{
		$config				= DiscussHelper::getConfig();
		$consumerKey		= $config->get('main_twitter_oauth_consumer_key');
		$consumerSecretKey	= $config->get('main_twitter_oauth_consumer_secret_key');
		$oauth_token		= $access_token->get('oauth_token', '');
		$oauth_token_secret	= $access_token->get('oauth_token_secret', '');

		$connection	= new DiscussTwitterOAuth($consumerKey, $consumerSecretKey, $oauth_token, $oauth_token_secret);

		$parameters	= array('status' => $message);
		$status		= $connection->post('statuses/update', $parameters);

		//for issues with unable to authenticate error, somehow they return errors instead of error.
		if( isset( $status->errors[0]->message ) )
		{
			return $status->errors[0]->message;
		}

		//for others error that is not authentication issue.
		if( isset( $status->error ) )
		{
			return $status->error;
		}

		return true;
	}

	function getScreenName($username, $password)
	{
		/*
		this fucntion is deprecated, as twitter uses oauth now. It will return the username
		when you sign in to twitter and we will automatically stored the screenname into the table.
		*/
		return false;
	}

	function getFollowMeLink($screen_name)
	{
		if(empty($screen_name))
		{
			return '';
		}

		return 'http://twitter.com/'.$screen_name;
	}

	public static function getAuthentication()
	{
		$config				= DiscussHelper::getConfig();
		$consumerKey		= $config->get('integration_twitter_consumer_key');
		$consumerSecretKey	= $config->get('integration_twitter_consumer_secret_key');

		ob_start();

		if(!empty($consumerKey) && !empty($consumerSecretKey))
		{
			$session = JFactory::getSession();

			$twitterUserId				= '';
			$twitterScreenName			= '';
			$twitterOauthToken			= '';
			$twitterOauthTokenSecret	= '';

			if($session->has( 'twitter_oauth_access_token', 'discuss' ))
			{
				$session_request	= JString::str_ireplace(',', "\r\n", $session->get('twitter_oauth_access_token', '', 'discuss'));
				$access_token		= DiscussHelper::getRegistry( $session_request );

				$twitterUserId				= $access_token->get('user_id', '');
				$twitterScreenName			= $access_token->get('screen_name', '');
				$twitterOauthToken			= $access_token->get('oauth_token', '');
				$twitterOauthTokenSecret	= $access_token->get('oauth_token_secret', '');
			}

			//check if this is frontend or backend
// 			$app = JFactory::getApplication();
// 			if ( $app->getClientId() === 1 ) {
// 				$controller = 'c';
// 			}else{
// 				$controller = 'controller';
// 			}

			if(empty($twitterUserId) || empty($twitterOauthToken) || empty($twitterOauthTokenSecret))
			{
				?><p class="small"><?php echo JText::_('COM_EASYDISCUSS_TWITTER_SIGN_IN_DESC');?></p><?php
				?><p class="small"><a href="javascript:void(0)" onclick="Popup=window.open('<?php echo trim(JURI::base(), "/").DiscussRouter::_('/index.php?option=com_easydiscuss&controller=twitter&task=requestAccess', false);?>','Popup','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no, width=800,height=450,top=100'); return false;"><img src="<?php echo DISCUSS_JURIROOT;?>/media/com_easydiscuss/images/twitter_signon.png" border="0" alt="here" /></a></p><?php
			}
			else
			{
				$screen_name = $twitterScreenName? $twitterScreenName : $twitterUserId;
				?><p class="small"><?php echo JText::sprintf('COM_EASYDISCUSS_TWITTER_SIGNED_IN_AS' , $screen_name); ?></p><?php
				?><p class="small"><a href="javascript:void(0);" onclick="discuss.login.twitter.signout();"><?php echo JText::sprintf('COM_EASYDISCUSS_TWITTER_SIGN_OUT' , $screen_name);?></a></p><?php
			}
		}
		else
		{
			?><div><?php echo JText::_('COM_EASYDISCUSS_TWITTER_OAUTH_INTEGRATION_INCOMPLETE');?></div><?php
		}

		$html	= ob_get_contents();
		@ob_end_clean();

		return $html;
	}
}
