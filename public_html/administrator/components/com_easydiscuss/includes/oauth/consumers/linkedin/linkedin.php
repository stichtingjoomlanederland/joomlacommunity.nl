<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/consumer.php');

class EasyDiscussLinkedIn extends EDLinkedInConsumer
{
	public function __construct($options = array())
	{
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		$this->config = ED::config();

		$this->apiKey =  $this->config->get('main_autopost_linkedin_id');
		$this->apiSecret = $this->config->get('main_autopost_linkedin_secret');

		$this->redirect = JURI::root() . 'index.php?option=com_easydiscuss&view=auth&layout=linkedin';

		$options = array('appKey' => $this->apiKey, 'appSecret' => $this->apiSecret, 'callbackUrl' => $this->redirect);

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
	 * @since	4.1.7
	 * @access	public
	 */
	public function getAuthorizeURL($redirect =  null)
	{
		$redirect = !is_null($redirect) ? $redirect : $this->redirect;

		// default Linkedin scope permissions
		$scopes = array('r_liteprofile', 'r_emailaddress', 'w_member_social');
		$scopes = implode(',', $scopes);

		$url = parent::_URL_AUTH_V2;
		$url .= '&client_id=' . $this->apiKey;
		$url .= '&redirect_uri=' . urlencode($redirect);
		$url .= '&state=' . $this->constructUserIdInState();
		$url .= '&scope=' . urlencode($scopes);

		return $url;
	}

	private function constructUserIdInState()
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
	 * Method to retrieve user email
	 *
	 * @since	4.1.7
	 * @access	public
	 */
	public function getUserEmail()
	{
		$details = parent::emailAddress();
		$result = json_decode($details['linkedin']);

		$email = '';

		// Decorate the data
		if ($result) {
			$elements = $result->elements;
			$elements = EB::makeArray($elements[0]);

			$email = $elements['handle~']['emailAddress'];
		}

		return $email;
	}

	/**
	 * Retrieves user's linkedin profile
	 *
	 * @since	4.1.7
	 * @access	public
	 */
	private function getUser()
	{
		// Get the information needed from Linkedin
		$details = parent::me('?projection=(id,firstName,lastName,profilePicture(displayImage~:playableStreams))');
		$result = json_decode($details['linkedin']);

		// Format the output
		if ($result) {
			$email = $this->getUserEmail();
			$firstName = $result->firstName;
			$lastName = $result->lastName;

			// Get the preferred local
			$preferredLocale = $firstName->preferredLocale;
			$locale = $preferredLocale->language . '_' . $preferredLocale->country;

			$firstName = $firstName->localized->$locale;
			$lastName = $lastName->localized->$locale;
			$formattedName = $firstName . ' ' . $lastName;

			$obj = new stdClass();
			$obj->id = $result->id;
			$obj->locale = $locale;
			$obj->firstName = $firstName;
			$obj->lastName = $lastName;
			$obj->formattedName = $formattedName;
			$obj->email = $email;
			$obj->profilePicture = $result->profilePicture;

			return $obj;
		}

		return $result;
	}

	/**
	 * Formats the content to send to linkedin
	 *
	 * @since	4.1.7
	 * @access	public
	 */
	public function getData(EasyDiscussPost $post)
	{
		// Get the content
		$content = $post->getIntro();
		$content = strip_tags($content);

		$text = $this->config->get('main_autopost_linkedin_message');
		$text = str_ireplace('{url}', $post->getPermalink(true), $text);
		$text = str_ireplace('{title}', $post->title, $text);

		$options = array(
					'text' => $text,
					'visibility' => 'PUBLIC',
					'submitted-url' => $post->getPermalink(true),
					'submitted-url-title' => $post->title,
					'submitted-url-desc' => $content,
					'userId' => $this->getUser()->id
				);

		// Satisfy linkedin's criteria
		$options['text'] = htmlspecialchars(trim(strip_tags(stripslashes($options['text']))));

		// Linkedin now restricts the message and text size.
		// To be safe, we'll use 380 characters instead of 400.
		$options['text'] = JString::substr($options['text'], 0, 256);

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
	 * @since	4.1.7
	 * @access	public
	 */
	public function getCompanies()
	{
		// Get a list of accounts associated to this user
		$data = $this->company('role=ADMINISTRATOR&state=APPROVED&projection=(*,elements*(*,organizationalTarget~(*)))');
		$result = json_decode($data['linkedin']);

		$companies = array();

		if ($result->status == 200) {
			foreach ($result->company as $item) {
				$company = new stdClass();
				$company->id = (int) $item->id;
				$company->title = (int) $item->name;

				$companies[] = $company;
			}
		}

		return $companies;
	}
}
