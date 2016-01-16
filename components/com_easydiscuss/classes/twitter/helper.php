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

require_once JPATH_ROOT . '/components/com_easydiscuss/constants.php';
require_once DISCUSS_CLASSES . '/twitter/consumer.php';

class DiscussTwitter extends DiscussTwitterOAuth
{
	var $callback = '';

	public function __construct( $key , $secret , $callback )
	{
		parent::__construct($key, $secret);
		$this->callback = $callback;
	}

	public function getRequestToken($oauth_callback = NULL)
	{
		$request		= parent::getRequestToken($this->callback);
		$obj			= new stdClass();
		$obj->token		= $request['oauth_token'];
		$obj->secret	= $request['oauth_token_secret'];

		return $obj;
	}

	public function getAuthorizationURL( $token, $auto_sign_in=false )
	{
		return parent::getAuthorizeURL( $token, $auto_sign_in );
	}

	public function getVerifier()
	{
		$verifier	= JRequest::getVar( 'oauth_verifier' , '' );
		return $verifier;
	}

	public function getAccess( $token , $secret , $verifier )
	{
		$this->token = new OAuthConsumer($token, $secret);

		$access = $this->getAccessToken($verifier);

		if(empty($access['oauth_token']) && empty($access['oauth_token_secret']))
		{
			return false;
		}

		$obj			= new stdClass();

		$obj->token		= $access['oauth_token'];
		$obj->secret	= $access['oauth_token_secret'];

		$param			= DiscussHelper::getRegistry('');
		$param->set( 'user_id' 	, $access['user_id'] );
		$param->set( 'screen_name' 	, $access['screen_name'] );

		$obj->params	= $param->toString();
		//@todo: expiry

		return $obj;
	}

	/**
	 * Shares a new content on Twitter
	 **/
	public function share( $post )
	{
		$config		= DiscussHelper::getConfig();
		$message	= $config->get( 'main_autopost_twitter_message' );

		$content	=  $this->processMessage($message, $post );

		$parameters	= array('status' => $content);
		$result		= $this->post('statuses/update', $parameters);
		$status		= array('success'=>true, 'error'=>false);

		//for issues with unable to authenticate error, somehow they return errors instead of error.
		if( isset( $result->errors[0]->message ) )
		{
			$status['success'] = false;
			$status['error'] = $result->errors[0]->message;
		}

		//for others error that is not authentication issue.
		if( isset( $result->error ) )
		{
			$status['success'] = false;
			$status['error'] = $result->error;
		}

		return $status['success'];
	}

	public function setAccess( $access )
	{
		$access			= DiscussHelper::getRegistry( $access );
		$this->token	= new OAuthConsumer($access->get('token'), $access->get( 'secret'));

		return $this->token;
	}

	public function revokeApp()
	{
		return true;
	}

	/**
	 * Process message
	 **/
	public function processMessage( $message , $post )
	{
		$search		= array();
		$replace	= array();

		//replace title
		if (preg_match_all("/.*?(\\{title\\})/is", $message, $matches))
		{
			$search[] = '{title}';
			$replace[] = $post->title;
		}

		//replace category
		if (preg_match_all("/.*?(\\{category\\})/is", $message, $matches))
		{
			$category	= DiscussHelper::getTable( 'Category' );
			$category->load( $post->category_id );

			$search[]	= '{category}';
			$replace[]	= $category->title;
		}

		$message = JString::str_ireplace($search, $replace, $message);

		//replace link
		if (preg_match_all("/.*?(\\{url\\})/is", $message, $matches))
		{
			$link	= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id , false , true );

			// @rule: Detect the length of the link
			$length		= JString::strlen( $link );
			$balance	= 140 - $length;

			$parts		= explode( '{url}' , $message );

			$message	= JString::substr( $parts[0] , 0 , 119 );
			$message	.= ' ' . $link;

			return $message;
		}

		return JString::substr($message, 0, 140);
	}
}
