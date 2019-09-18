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

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewAutoposting extends EasyDiscussAdminView
{
	/**
	 * Displays the auto posting form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// If user access this page manually, automatically redirect to the facebook page
		$this->app->redirect('index.php?option=com_easydiscuss&view=autoposting&layout=facebook');

		parent::display('autoposting/default');
	}

	/**
	 * Renders the facebook auto posting form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function facebook($tpl = null)
	{
		// Set page title
		$this->title('COM_EASYDISCUSS_AUTOPOST_FACEBOOK');

		// Generate a default callback url
		$callback = EDR::getRoutedURL('index.php?option=com_easydiscuss&view=autoposting&layout=facebook', false, true);

		// Get the oauth library
		$client = ED::oauth()->getConsumer('facebook', $this->config->get('main_autopost_facebook_id'), $this->config->get('main_autopost_facebook_secret'), $callback);

		// Load the oauth table
		$table = ED::table('Oauth');
		$table->loadByType('facebook');

		// Register buttons here
		JToolBarHelper::apply();

		// Determines if facebook has already been associated
		$associated	= (bool) $table->id && $table->access_token;

		// Get a list of pages
		$pages = array();
		$storedPages = array();

		if ($associated && $table->access_token) {
			$pages = $this->getFacebookPage($client, $table->access_token);

			// Get a list of stored pages
			$storedPages = $this->config->get('main_autopost_facebook_page_id', array());

			if ($storedPages) {
				$storedPages = explode(',', $storedPages);
			}
		}

		// Get a list of groups
		$groups = array();
		$storedGroups = array();

		if ($associated && $table->access_token) {
			$groups = $this->getFacebookGroup($client, $table->access_token);

			// Get a list of stored groups
			$storedGroups = $this->config->get('main_autopost_facebook_group_id', array());

			if ($storedGroups) {
				$storedGroups = explode(',', $storedGroups);
			}
		}

		$authorizationURL = 'index.php?option=com_easydiscuss&controller=autoposting&task=request&type=facebook';

		// retrieve Facebook oauth valid URIs
		$oauthURIs = ED::oauth()->getOauthRedirectURI();

		// Retrieve Facebook scopes permission
		$selectedScopePermissions = $this->config->get('main_autopost_facebook_scope_permissions');

		if ($selectedScopePermissions) {
			$selectedScopePermissions = explode(',', $selectedScopePermissions);
		}

		$this->set('oauthURIs', $oauthURIs);
		$this->set('authorizationURL', $authorizationURL);
		$this->set('associated', $associated);
		$this->set('groups', $groups);
		$this->set('storedGroups', $storedGroups);
		$this->set('pages', $pages);
		$this->set('storedPages', $storedPages);
		$this->set('selectedScopePermissions', $selectedScopePermissions);

		parent::display('autoposting/facebook');
	}

	/**
	 * Renders the twitter auto posting form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function twitter($tpl = null)
	{
		// Set page attributes
		$this->title('COM_EASYDISCUSS_AUTOPOST_TWITTER');

		// Register buttons here
		JToolBarHelper::apply();

		// Generate callback url
		$callback = EDR::getRoutedURL('index.php?option=com_easydiscuss&view=autoposting', false, true);

		// Get the oauth library
		$client = ED::oauth()->getClient('Twitter');

		// Load the oauth table
		$table = ED::table('OAuth');
		$table->loadByType('twitter');

		// Determines if twitter has already been associated
		$associated = (bool) $table->id && $table->access_token;

		$authorizationURL = 'index.php?option=com_easydiscuss&controller=autoposting&task=request&type=twitter';

		$oauthURIs = ED::oauth()->getOauthRedirectURI('twitter');

		$this->set('authorizationURL', $authorizationURL);
		$this->set('associated', $associated);
		$this->set('oauthURIs', $oauthURIs);

		parent::display('autoposting/twitter');
	}

	/**
	 * Renders the twitter auto posting form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function linkedin($tpl = null)
	{
		// Set page attributes
		$this->title('COM_EASYDISCUSS_AUTOPOST_LINKEDIN');

		// Register buttons here
		JToolBarHelper::apply();

		// Get the oauth library
		$client = ED::oauth()->getClient('Linkedin');

		// Load the oauth table
		$table = ED::table('OAuth');
		$table->loadByType('linkedin');

		// Determines if twitter has already been associated
		$associated = (bool) $table->id && $table->access_token;

		$authorizationURL = 'index.php?option=com_easydiscuss&controller=autoposting&task=request&type=linkedin';

		// Get linkedin companies
		$storedCompanies = array();
		$companies = array();

		// Try to get the companies the user manages.
		if ($associated) {
			$client->setAccess($table->access_token);
			$data = $client->getCompanyLists('role=ADMINISTRATOR&state=APPROVED&projection=(elements*(organizationalTarget~)');
			$result = json_decode($data['linkedin']);

			if ($result->status == 200) {

				$elements = $result->elements;

				foreach ($elements as $element) {
					$element = EB::makeArray($element);

					$company = new stdClass();
					$company->id = $element['organizationalTarget~']['id'];
					$company->name = $element['organizationalTarget~']['localizedName'];

					$companies[] = $company;
				}
			}

			// Get a list of stored groups
			$storedCompanies = $this->config->get('main_autopost_linkedin_company_id', array());

			if ($storedCompanies) {
				$storedCompanies = explode(',', $storedCompanies);
			}
		}

		$oauthURIs = ED::oauth()->getOauthRedirectURI('linkedin');

		$this->set('storedCompanies', $storedCompanies);
		$this->set('companies', $companies);
		$this->set('authorizationURL', $authorizationURL);
		$this->set('associated', $associated);
		$this->set('oauthURIs', $oauthURIs);

		parent::display('autoposting/linkedin');
	}

	/**
	 * Renders the telegram auto posting settings form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function telegram($tpl = null)
	{
		// Set page attributes
		$this->title('COM_EASYDISCUSS_AUTOPOST_TELEGRAM');

		// Register buttons here
		JToolBarHelper::apply();

		parent::display('autoposting/telegram');
	}

	/**
	 * Renders the slack auto posting settings form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function slack($tpl = null)
	{
		// Set page attributes
		$this->title('COM_EASYDISCUSS_AUTOPOST_SLACK');

		// Register buttons here
		JToolBarHelper::apply();

		parent::display('autoposting/slack');
	}

	/**
	 * Renders the wunderlist auto posting settings form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function wunderlist($tpl = null)
	{
		// Set page attributes
		$this->title('COM_EASYDISCUSS_AUTOPOST_WUNDERLIST');

		// Register buttons here
		JToolBarHelper::apply();

		// Get the oauth library
		$client = ED::oauth()->getClient('Wunderlist');

		// Load the oauth table
		$table = ED::table('OAuth');
		$table->loadByType('wunderlist');

		// Determines if twitter has already been associated
		$associated = (bool) $table->id && $table->access_token;

		$authorizationURL = 'index.php?option=com_easydiscuss&controller=autoposting&task=request&type=wunderlist';

		// Get linkedin companies
		$lists = array();
		$storedLists = array();

		if ($associated) {
			$client->setAccess($table->access_token);
			$lists = $client->getLists();

			// Get a list of stored lists
			$storedLists = $this->config->get('main_autopost_wunderlist_list_id', array());

			if ($storedLists) {
				$storedLists = explode(',', $storedLists);
			}
		}

		$this->set('lists', $lists);
		$this->set('storedLists', $storedLists);
		$this->set('authorizationURL', $authorizationURL);
		$this->set('associated', $associated);

		parent::display('autoposting/wunderlist');
	}


	/**
	 * Fetch Facebook groups
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getFacebookGroup($client, $token)
	{
		$client->setAccess($token);

		// Get groups that are available
		$groups = array();

		try {
			$groups = $client->getGroups();
		} catch(Exception $e) {
		}

		return $groups;
	}

	/**
	 * Fetch Facebook groups
	 *
	 * @since	4.0
	 * @access	public
	 */
	 public function getFacebookPage($client, $token)
	 {
	 	$client->setAccess($token);

	 	// Get pages that are available
	 	$pages = array();

	 	try {
	 		$pages = $client->getPages();
	 	} catch(Exception $e){
	 	}

	 	return $pages;
	 }
}
