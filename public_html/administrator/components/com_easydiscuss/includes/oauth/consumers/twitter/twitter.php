<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/consumer.php');

class EasyDiscussTwitter extends EasyDiscuss
{
	public $key = null;
	public $secret = null;
	public $callback = null;
	public $client = null;

	public function __construct($key = '', $secret = '', $callback = '')
	{
		$config = ED::config();

		if (!$key) {
			$key = $config->get('main_autopost_twitter_id');
		}

		if (!$secret) {
			$secret = $config->get('main_autopost_twitter_secret');
		}

		if (!$callback) {
			$callback = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easydiscuss&controller=autoposting&task=grant&type=twitter';
		}

		$this->key = $key;
		$this->secret = $secret;
		$this->callback	= $callback;

		$this->client = new DiscussTwitterOAuth($key, $secret);
	}

	/**
	 * Retrieves the callback url
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getCallbackUrl()
	{
		return $this->callback;
	}

	/**
	 * Generates a request token for twitter
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getRequestToken()
	{
		$request = $this->client->getRequestToken($this->callback);

		$obj = new stdClass();
		$obj->token = $request['oauth_token'];
		$obj->secret = $request['oauth_token_secret'];

		return $obj;
	}

	/**
	 * Retrieves the authorization url
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAuthorizationURL($requestToken)
	{
		// Exchange the token for the url
		$url = $this->client->getAuthorizeURL($requestToken->token);

		return $url;
	}

	/**
	 * Retrieves the verifier code
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getVerifier()
	{
		$verifier = JRequest::getVar('oauth_verifier', '');
		return $verifier;
	}

	/**
	 * Retrieve the access token given the token, secret and verifier
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAccessTokens($requestToken, $secret, $verifier)
	{
		// We need to pass the request token and secret to the library
		$this->client->token = new EDOAuthConsumer($requestToken, $secret);

		// Exchange for access token with the verifier code
		$accessToken = $this->client->getAccessToken($verifier);

		if (!isset($accessToken['oauth_token']) || !isset($accessToken['oauth_token_secret'])) {
			return false;
		}

		$obj = new stdClass();

		$obj->token = $accessToken['oauth_token'];
		$obj->secret = $accessToken['oauth_token_secret'];

		$registry = new JRegistry();
		$registry->set('user_id', $accessToken['user_id']);
		$registry->set('screen_name', $accessToken['screen_name']);

		$obj->params = $registry->toString();

		return $obj;
	}

	/**
	 * Shares a new content on twitter
	 *
	 * @since	4.0.7
	 * @access	public
	 */
	public function share($post, $oauth)
	{
		// Get the token and secret from the Twitter
		$tokenAccess = $this->setAccess($oauth);

		// Pass all the token key and secret
		$lib = new DiscussTwitterOAuth($oauth->key, $oauth->secret, $tokenAccess->key->key, $tokenAccess->key->secret);

		$config	= ED::config();
		$message = $config->get('main_autopost_twitter_message');

		$content = $this->processMessage($message, $post);

		$parameters	= array('status' => $content);
		$result	= $lib->post('statuses/update', $parameters);

		$status	= array('success' => true, 'error' => false);

		//for issues with unable to authenticate error, somehow they return errors instead of error.
		if (isset( $result->errors[0]->message)) {
			$status['success'] = false;
			$status['error'] = $result->errors[0]->message;
		}

		//for others error that is not authentication issue.
		if (isset($result->error)) {
			$status['success'] = false;
			$status['error'] = $result->error;
		}

		return $status['success'];
	}

	/**
	 * Retrieve the Twitter authenticated token
	 *
	 * @since	4.0.7
	 * @access	public
	 */
	public function setAccess($access)
	{
		$access	= ED::registry($access);
		$this->token = new EDOAuthConsumer($access->get('token'), $access->get( 'secret'));

		return $this->token;
	}

	/**
	 * Revokes the oauth
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function revokeApp()
	{
		return true;
	}

	/**
	 * Formats the message to be published on Twitter
	 *
	 * @since	4.0.7
	 * @access	public
	 */
	public function processMessage($message, $post)
	{
		$search	= array();
		$replace = array();

		// replace title
		if (preg_match_all("/.*?(\\{title\\})/is", $message, $matches)) {
			$search[] = '{title}';

			// Get the title to use
			if ($post->isQuestion()) {
				$post->title = $post->getTitle();
			} else {
				$post->title = $post->getParent()->getTitle();
			}

			$replace[] = $post->title;
		}

		// replace category
		if (preg_match_all("/.*?(\\{category\\})/is", $message, $matches)) {
			$category = ED::table('Category');
			$category->load($post->category_id);

			$search[] = '{category}';
			$replace[] = $category->title;
		}

		$message = JString::str_ireplace($search, $replace, $message);

		// replace link
		if (preg_match_all("/.*?(\\{url\\})/is", $message, $matches)) {
			
			// $link = EDR::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $post->id, false, true);
	        
	        // Get the question id
	        $question = $post->getParent();

	        // if that is reply post
			$link = EDR::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $question->id . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $post->id, false, true);

			// If that is question
			if ($post->isQuestion()) {
				$link = EDR::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $post->id, false, true);
			}

			// @rule: Detect the length of the link
			$length	= JString::strlen($link);
			$balance = 140 - $length;

			$parts = explode('{url}', $message);

			$message = JString::substr($parts[0], 0, 119);
			$message .= ' ' . $link;

			return $message;
		}

		return JString::substr($message, 0, 140);
	}
}
