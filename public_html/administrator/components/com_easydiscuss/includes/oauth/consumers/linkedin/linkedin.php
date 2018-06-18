<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(__DIR__ . '/consumer.php');

class EasyDiscussLinkedIn extends EDLinkedInConsumer
{
	public function __construct($key = '', $secret = '', $redirect = '')
	{
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		$this->config = ED::config();

		if (!$key) {
			$key = $this->config->get('main_autopost_linkedin_id');
		}

		if (!$secret) {
			$secret = $this->config->get('main_autopost_linkedin_secret');
		}

		if (!$redirect) {
			$redirect = 'index.php?option=com_easydiscuss&view=auth&layout=linkedin';
		}

		$this->key = $key;
		$this->secret = $secret;
		$this->redirect	= EDR::getRoutedUrl($redirect, true, true, true);

		$options = array('appKey' => $this->key, 'appSecret' => $this->secret, 'callbackUrl' => $this->redirect);

		parent::__construct($options);
	}

	/**
	 * Retrieves the callback url
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getCallbackUrl()
	{
		return $this->redirect;
	}

	/**
	 * Retrieves the authorization end point url
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAuthorizeURL()
	{
		$url = parent::_URL_AUTH_V2;
		$url .= '&client_id=' . $this->key;
		$url .= '&redirect_uri=' . $this->redirect;
		$url .= '&state=' . $this->construcUserIdInState();

		return $url;
	}

	private function construcUserIdInState()
	{
		$user = ED::user();
		$state = parent::_USER_CONSTANT . $user->id;

		return $state;
	}

	/**
	 * Exchanges the request token with the access token
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAccess()
	{
		$access = $this->retrieveTokenAccess($this->auth_code);

		if (!$access) {
			return false;
		}

		$obj = new stdClass();

		// Convert to object
		if (is_string($access['linkedin'])) {
			$access['linkedin'] = json_decode($access['linkedin']);
		}

		$obj->token = $access['linkedin']->access_token;
		$obj->secret = true;
		$obj->params = '';
		$obj->expires = ED::date();

		// If the expiry date is given
		if (isset($access['linkedin']->expires_in)) {
			$expires = $access['linkedin']->expires_in;

			// Set the expiry date with proper date data
			$obj->expires = ED::date(strtotime('now') + $expires)->toSql();
		}

		return $obj;
	}

	/**
	 * Formats the content to send to linkedin
	 *
	 * @since	4.0
	 * @access	public	
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
	 */
	public function share(EasyDiscussPost $post)
	{
		$options = $this->getData($post);

		// If configured to send to a company page, we need to only send to the company
		$companies = $this->getStoredCompanies();

		if ($companies) {
			$status = $this->sharePost('new', $options, true, false, $companies);

			if (isset($status['success'])) {
				return true;
			}

			return false;
		}

		// If there are no companies, just auto post to their account
		if (!$companies) {
			$status = $this->sharePost('new', $options, true, false);

			if (isset($status['success'])) {
				return true;
			}

			return false;
		}

		return false;
	}

	/**
	 * Set the authorization code
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function setAuthCode($code)
	{
		$this->auth_code = $code;
	}

	/**
	 * Set the access tokens
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function setAccess($access)
	{
		$access = ED::registry($access);
		return parent::setAccessToken($access->get('token'));
	}

	/**
	 * Allows caller to revoke the access which was given by Facebook
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function revokeApp()
	{
		// For oauth 2 we do not need to do anything here
		return true;
	}

	/**
	 * Retrieves a list of groups the user owns.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getCompanies()
	{
		// Get a list of accounts associated to this user
		$result = $this->company('?is-company-admin=true');

		$parser = JFactory::getXML($result['linkedin'], false);
		$result = $parser->children();

		$companies = array();

		if ($result) {

			foreach ($result as $item) {
				$company = new stdClass();

				$company->id = (int) $item->id;
				$company->title = (string) $item->name;

				$companies[] = $company;
			}
		}

		return $companies;
	}
}
