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

/*
 * Abraham Williams (abraham@abrah.am) http://abrah.am
 *
 * The first PHP Library to support OAuth for Twitter's REST API.
 * Modify by Alex (lex2508@gmail.com) to support fsock.
 */

/* Load OAuth lib. You can find it at http://oauth.net */
require_once DISCUSS_CLASSES . '/twitter/oauth.php';

/**
 * Twitter OAuth class
 */
if (!class_exists('DiscussTwitterOAuth')){
	class DiscussTwitterOAuth {
		/* Contains the last HTTP status code returned. */
		var $http_code;
		/* Contains the last API call. */
		var $url;
		/* Set up the API root URL. */
		var $host = "https://api.twitter.com/1/";
		/* Set timeout default. */
		var $timeout = 3000;
		/* Set connect timeout. */
		var $connecttimeout = 3000;
		/* Verify SSL Cert. */
		var $ssl_verifypeer = FALSE;
		/* Respons format. */
		var $format = 'json';
		/* Decode returned json data. */
		var $decode_json = TRUE;
		/* Contains the last HTTP headers returned. */
		var $http_info;
		/* Set the useragnet. */
		var $useragent = 'Stackideas TwitterOAuth v0.2.0-beta2';
		/* Immediately retry the API call if the response was not successful. */
		//public $retry = TRUE;

		/**
		 * Set API URLS
		 */
		function accessTokenURL()  { return 'https://api.twitter.com/oauth/access_token'; }
		function authenticateURL() { return 'https://twitter.com/oauth/authenticate'; }
		function authorizeURL()    { return 'https://twitter.com/oauth/authorize'; }
		function requestTokenURL() { return 'https://api.twitter.com/oauth/request_token'; }

		/**
		 * Debug helpers
		 */
		function lastStatusCode() { return $this->http_status; }
		function lastAPICall() { return $this->last_api_call; }

		/**
		 * construct TwitterOAuth object
		 */
		function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) {
			$this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
			$this->consumer = new EDOAuthConsumer($consumer_key, $consumer_secret);
			if (!empty($oauth_token) && !empty($oauth_token_secret)) {
				$this->token = new EDOAuthConsumer($oauth_token, $oauth_token_secret);
			} else {
				$this->token = NULL;
			}
		}

		/**
		 * constructor wrapper for php 4
		 */
		function DiscussTwitterOAuth($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) {
			$this->__construct($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
		}

		/**
		 * Get a request_token from Twitter
		 *
		 * @returns a key/value array containing oauth_token and oauth_token_secret
		 */
		function getRequestToken($oauth_callback = NULL) {
			$parameters = array();
			if (!empty($oauth_callback)) {
				$parameters['oauth_callback'] = $oauth_callback;
			}
			$request = $this->oAuthRequest($this->requestTokenURL(), 'GET', $parameters);
			$token = OAuthUtil::parse_parameters($request);
			$this->token = new EDOAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
			return $token;
		}

		/**
		 * Get the authorize URL
		 *
		 * @returns a string
		 */
		function getAuthorizeURL($token, $sign_in_with_twitter = TRUE) {
			if (is_array($token)) {
				$token = $token['oauth_token'];
			}
			if (empty($sign_in_with_twitter)) {
				return $this->authorizeURL() . "?oauth_token={$token}";
			} else {
				return $this->authenticateURL() . "?oauth_token={$token}";
			}
		}

		/**
		* Exchange request token and secret for an access token and
		* secret, to sign API calls.
		*
		* @returns array("oauth_token" => "the-access-token",
		*                "oauth_token_secret" => "the-access-secret",
		*                "user_id" => "9436992",
		*                "screen_name" => "abraham")
		*/
		function getAccessToken($oauth_verifier = FALSE) {
			$parameters = array();
			if (!empty($oauth_verifier)) {
				$parameters['oauth_verifier'] = $oauth_verifier;
			}
			$request = $this->oAuthRequest($this->accessTokenURL(), 'GET', $parameters);
			$token = OAuthUtil::parse_parameters($request);
			$this->token = new EDOAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);

			return $token;
		}

		/**
		* One time exchange of username and password for access token and secret.
		*
		* @returns array("oauth_token" => "the-access-token",
		*                "oauth_token_secret" => "the-access-secret",
		*                "user_id" => "9436992",
		*                "screen_name" => "abraham",
		*                "x_auth_expires" => "0")
		*/
		function getXAuthToken($username, $password) {
			$parameters = array();
			$parameters['x_auth_username'] = $username;
			$parameters['x_auth_password'] = $password;
			$parameters['x_auth_mode'] = 'client_auth';
			$request = $this->oAuthRequest($this->accessTokenURL(), 'POST', $parameters);
			$token = OAuthUtil::parse_parameters($request);
			$this->token = new EDOAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
			return $token;
		}

		/**
		 * GET wrapper for oAuthRequest.
		 */
		function get($url, $parameters = array()) {
			$response = $this->oAuthRequest($url, 'GET', $parameters);
			if ($this->format === 'json' && $this->decode_json) {
				$json	= new Services_JSON();
				return $json->decode($response);
			}
			return $response;
		}

		/**
		* POST wrapper for oAuthRequest.
		*/
		function post($url, $parameters = array()) {
			$response = $this->oAuthRequest($url, 'POST', $parameters);
			if ($this->format === 'json' && $this->decode_json) {
				$json	= new Services_JSON();
				return $json->decode($response);
			}
			return $response;
		}

		/**
		* DELETE wrapper for oAuthReqeust.
		*/
		function delete($url, $parameters = array()) {
			$response = $this->oAuthRequest($url, 'DELETE', $parameters);
			if ($this->format === 'json' && $this->decode_json) {
				$json	= new Services_JSON();
				return $json->decode($response);
			}
			return $response;
		}

		/**
		* Format and sign an OAuth / API request
		*/
		function oAuthRequest($url, $method, $parameters) {
			if (strrpos($url, 'https://') !== 0 && strrpos($url, 'http://') !== 0) {
				$url = "{$this->host}{$url}.{$this->format}";
			}
			$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters);
			$request->sign_request($this->sha1_method, $this->consumer, $this->token);
			// $request->set_fsock_data();

			if(array_key_exists( 'status', $parameters ))
			{
				$status = $parameters['status'];
			}
			else
			{
				$status = false;
			}

			switch ($method) {
				case 'GET':
				  return $this->http($request->to_url(), 'GET', '', $request->fsockScheme, $request->fsockPort, $status);
				default:
				  return $this->http($request->get_normalized_http_url(), $method, $request->to_postdata(), $request->fsockScheme, $request->fsockPort, $status);
			}
		}

		/**
		 * Make an HTTP request
		 *
		 * @return API results
		 */
		function http($url, $method, $postfields = NULL, $fsockScheme, $fsockPort, $status=false) {
			$this->http_info = array();

			$statusQuery = !empty($status)? "?status=".urlencode($status) : "";

			$out	 = $method." ".$url.$statusQuery." HTTP/1.1\r\n";
			$out	.= "Host: api.twitter.com\r\n";
			$out	.= "Authorization: OAuth ".$postfields."\r\n";
			$out	.= "User-Agent: ".$this->useragent."\r\n";
			$out	.= "Content-type: application/x-www-form-urlencoded\r\n";
			$out	.= "Connection: Close\r\n\r\n";

			$handle = @fsockopen ($fsockScheme.'api.twitter.com', $fsockPort , $errno, $errstr, $this->timeout);

			if(!empty($handle))
			{
				fwrite( $handle , $out );

				$body		= false;
				$contents	= '';
				$headers 	= array();

				while( !feof( $handle ) )
				{
					$return	= fgets( $handle , 1024 );

					if( $body )
					{
						$contents	.= $return;
					}
					else
					{
						$headers[]	 = $return;
					}

					if( $return == "\r\n" )
					{
						$body	= true;
					}
				}
				fclose($handle);

				if(!is_array($headers) || count($headers) < 1)
				{
					$http_code		= false;
				}
				else
				{
					$httpResponse	= explode(" ", $headers[0]);
					$http_code		= $httpResponse[1];
				}
			}
			else
			{
				$http_code	= false;
				$contents	= '';
			}

			$this->http_code = $http_code;
			$this->url = $url;

			return $contents;
		}

		/**
		* Get the header info to store.
		*/
		function getHeader($ch, $header) {
			$i = strpos($header, ':');
			if (!empty($i)) {
				$key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
				$value = trim(substr($header, $i + 2));
				$this->http_header[$key] = $value;
			}
			return strlen($header);
		}
	}
}
