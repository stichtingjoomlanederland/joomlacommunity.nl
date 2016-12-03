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

require_once(__DIR__ . '/consumer.php');

class EasyDiscussLinkedIn extends EasyDiscuss
{
	public $key = null;
	public $secret = null;
	public $callback = null;

	public $userAccessToken = null;

	public function __construct($key = '', $secret = '', $callback = '')
	{
		$this->config = ED::config();

		if (!$key) {
			$key = $this->config->get('main_autopost_linkedin_id');
		}

		if (!$secret) {
			$secret = $this->config->get('main_autopost_linkedin_secret');
		}

		if (!$callback) {
			$callback = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easydiscuss&controller=autoposting&task=grant&type=linkedin';
		}

		$this->key = $key;
		$this->secret = $secret;
		$this->callback	= $callback;

		$options = array('appKey' => $key, 'appSecret' => $secret, 'callbackUrl' => $callback);

		$this->client = new EasyDiscussLinkedInConsumer($options);
	}

	/**
	 * Retrieves the callback url
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCallbackUrl()
	{
		return $this->callback;
	}

	/**
	 * Generates a request token
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRequestToken()
	{
		$request = $this->client->retrieveTokenRequest();

		$obj = new stdClass();
		$obj->token = $request['linkedin']['oauth_token'];
		$obj->secret = $request['linkedin']['oauth_token_secret'];

		return $obj;
	}

	/**
	 * Get the verifier code that is sent back by Facebook
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getVerifier()
	{
		$verifier = JRequest::getVar('oauth_verifier', '', 'default');

		return $verifier;
	}

	/**
	 * Retrieves the authorization end point url
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAuthorizationURL($requestToken)
	{
		$url = EasyDiscussLinkedInConsumer::_URL_AUTH . $requestToken->token;

		return $url;
	}

	/**
	 * Retrieves the access token given the request token, secret and verifier code.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAccessTokens($token, $secret, $verifier)
	{
		// Exchange the request token, secret and verifier for an access token.
		$accessToken = $this->client->retrieveTokenAccess($token, $secret, $verifier);

		if (isset($accessToken['linkedin']['oauth_problem'])) {
			return false;
		}

		$obj = new stdClass();
		$obj->token = $accessToken['linkedin']['oauth_token'];
		$obj->secret = $accessToken['linkedin']['oauth_token_secret'];
		$obj->params = '';
		$obj->expires = '';

		// If the expiry date is given
		if (isset($accessToken['linkedin']['oauth_expires_in'])) {
			$expires = $accessToken['linkedin']['oauth_expires_in'];

			// Set the expiry date with proper date data
			$expiration = strtotime('now') + $expires;
			$obj->expires = ED::date($expiration)->toSql();
		}

		return $obj;
	}

	/**
	 * Formats the content to send to linkedin
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getData(EasyDiscussPost $post)
	{
		// Get the content
		$content = $post->getIntro();
		$content = strip_tags($content);

		$comment = $this->config->get('main_autopost_linkedin_message');
		$comment = str_ireplace('{url}', $post->getPermalink(true), $comment);
		$comment = str_ireplace('{title}', $post->title, $comment);

		$options = array(
						'title' => $post->title,
						'comment' => $comment,
						'submitted-url' => $post->getPermalink(true),
						'description' => $content,
						'visibility' => 'anyone'
					);

		// Satisfy linkedin's criteria
		$options['description'] = trim(htmlspecialchars(strip_tags(stripslashes($options['description']))));
		$options['comment'] = htmlspecialchars(trim(strip_tags(stripslashes($options['comment']))));

		// Linkedin now restricts the message and text size.
		// To be safe, we'll use 380 characters instead of 400.
		$options['description'] = trim(JString::substr($options['description'], 0, 395));
		$options['comment'] = JString::substr($options['comment'], 0, 256);

		return $options;
	}

	/**
	 * Retrieve a stored list of companies to auto post to
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getStoredCompanies()
	{
		$items = $this->config->get('main_autopost_linkedin_company_id');
		$items = trim($items);

		if (!$items) {
			return array();
		}

		$companies = explode(',', $items);

		return $companies;
	}

	/**
	 * Shares a message on Linkedin when a new discussion is created
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function share(EasyDiscussPost $post)
	{
		$options = $this->getData($post);

		// If configured to send to a company page, we need to only send to the company
		$companies = $this->getStoredCompanies();

		if ($companies) {
			$status = $this->client->sharePost('new', $options, true, false, $companies);

			if (isset($status['success'])) {
				return true;
			}

			return false;
		}

		// If there are no companies, just auto post to their account
		if (!$companies) {
			$status = $this->client->sharePost('new', $options, true, false);

			if (isset($status['success'])) {
				return true;
			}

			return false;
		}

		return false;
	}

	/**
	 * Set the access tokens
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setAccess($access)
	{
		$access = json_decode($access);
		$options = array('oauth_token' => $access->token, 'oauth_token_secret' => $access->secret);

		$this->client->setTokenAccess($options);
	}

	/**
	 * Allows caller to revoke the access which was given by Facebook
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function revokeApp()
	{
		$result = $this->client->revoke();

		return $result['success'] == true;
	}

	/**
	 * Retrieves a list of groups the user owns.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCompanies()
	{
		// Get a list of accounts associated to this user
		$result = $this->client->company('?is-company-admin=true');

		$parser = JFactory::getXML($result['linkedin'], false);
		$result = $parser->children();

		$companies = array();

		if ($result) {

			foreach ($result as $item) {
				$company = new stdClass();

				$company->id    = (int) $item->id;
				$company->title = (string) $item->name;

				$companies[] = $company;
			}
		}

		return $companies;
	}
}
