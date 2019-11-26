<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussGitHub extends EasyDiscuss
{
	public $key = null;
	public $secret = null;
	public $callback = null;

	public $userAccessToken = null;

	public function __construct($key = '', $secret = '', $callback = '')
	{
		$this->config = ED::config();

		if (!$key) {
			$key = $this->config->get('composer_github_client_id');
		}

		if (!$secret) {
			$secret = $this->config->get('composer_github_client_secret');
		}

		if (!$callback) {
			$callback = JURI::root() . 'index.php?option=com_easydiscuss&view=auth&layout=github';
		}

		$this->key = $key;
		$this->secret = $secret;
		$this->callback	= $callback;
		$this->stateCode = md5($key);
	}

	/**
	 * Retrieves the callback url
	 *
	 * @since	4.1.7
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getCallbackUrl()
	{
		return $this->callback;
	}

	/**
	 * Get the verifier code that is sent back by GitHub
	 *
	 * @since	4.1.7
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getVerifier()
	{
		$verifier = JRequest::getVar('code', '', 'default');

		return $verifier;
	}

	/**
	 * Retrieves the authorization end point url
	 *
	 * @since	4.1.7
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getAuthorizationURL()
	{
		$url = 'https://github.com/login/oauth/authorize?client_id=' . $this->key . '&redirect_uri=' . urlencode($this->callback) . '&scope=gist&allow_signup=false&state=' . $this->stateCode;

		return $url;
	}


	/**
	 * Retrieves the access token given the request token, secret and verifier code.
	 *
	 * @since	4.1.7
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getAccessTokens($token, $secret, $code)
	{
		$params = array('code' => urlencode($code), 'client_id' => urlencode($this->key), 'client_secret' => urlencode($this->secret), 'state' => $this->stateCode);
		$str = 'code=' . $params['code'] . '&client_id=' . $params['client_id'] . '&client_secret=' . $params['client_secret'] . '&state=' . $params['state'];

		$ch = curl_init('https://github.com/login/oauth/access_token');

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_POST, count($params));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));

		$output = curl_exec($ch);

		curl_close($ch);

		$result = json_decode($output);

		$obj = new stdClass();
		$obj->token = $result->access_token;
		$obj->secret = $code;
		$obj->params = '';
		$obj->expires = '';

		return $obj;
	}


	/**
	 * Set the access tokens
	 *
	 * @since	4.1.7
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function setAccess($access)
	{
		$access = json_decode($access);
		$options = array('oauth_token' => $access->token, 'oauth_token_secret' => $access->secret);

		$this->access_token = $access->token;
	}

	/**
	 * Allows caller to revoke the access which was given by Facebook
	 *
	 * @since	4.1.7
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function revokeApp()
	{
		return true;
	}
}
