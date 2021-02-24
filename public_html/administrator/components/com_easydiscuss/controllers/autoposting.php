<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussControllerAutoposting extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask('apply', 'save');
	}

	/**
	 * Cleans up the posted data
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function cleanup(&$post)
	{
		// Unset unecessary data.
		unset($post['controller']);
		unset($post['active']);
		unset($post['child']);
		unset($post['layout']);
		unset($post['task']);
		unset($post['option']);
		unset($post['c']);
		unset($post['step']);

		// Unset the token
		$token = ED::getToken();
		unset($post['token']);
	}

	/**
	 * Saves the oauth settings
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function save()
	{
		// Get the type of oauth we are saving
		$type = $this->input->get('type');

		$model = ED::model('Settings');
		
		// Get posted data from request
		$post = $this->input->post->getArray();

		// Unset unecessary data.
		$this->cleanup($post);

		// Ensure that the page id is set
		if (!isset($post['main_autopost_' . $type . '_page_id'])) {
			$post['main_autopost_' . $type . '_page_id'] = '';
		}

		if (!isset($post['main_autopost_linkedin_company_id']) && $type == 'linkedin') {
			$post['main_autopost_linkedin_company_id'] = '';
		}

		// If doesn't select any scope permission then store it as empty
		if (!isset($post['main_autopost_facebook_scope_permissions'])) {
			$post['main_autopost_facebook_scope_permissions'] = '';
		}

		$options = array();

		foreach ($post as $key => $value) {
			$options[$key] = $value;
		}

		// Try to save the settings
		$model->save($options);

		$actionlog = ED::actionlog();
		$actionlogOauthType = strtoupper($type);

		$actionlog->log('COM_ED_ACTIONLOGS_AUTOPOSTING_UPDATED_' . $actionlogOauthType, 'autoposting', array(
			'link' => 'index.php?option=com_easydiscuss&view=autoposting&layout=' . $type
		));

		ED::setMessage('COM_EASYDISCUSS_CONFIGURATION_SAVED', 'success');

		$redirect = JRoute::_('index.php?option=com_easydiscuss&view=autoposting&layout=' . $type, false);

		return ED::redirect($redirect);
	}

	/**
	 * This is the first step of authorization request to oauth providers.
	 *
	 * @since	4.0
	 * @access	public	
	 */
	public function request()
	{
		// Get the oauth type
		$type = $this->input->get('type', '', 'cmd');

		if ($type == 'linkedin') {
			$client = ED::oauth()->getClient('LinkedIn');
			$url = $client->getAuthorizeURL();

			return ED::redirect($url); 
		}

		if ($type == 'github') {
			$client = ED::oauth()->getClient('Github');
			$url = $client->getAuthorizationURL();

			return ED::redirect($url); 
		}

		// Get the oauth client
		$client = ED::oauth()->getClient($type);

		// Get the application key and secret
		$callback = $client->getCallbackUrl();

		// Generate a request token
		$request = $client->getRequestToken();

		// We want to redirect all requests back to the correct form
		$redirect = JRoute::_('index.php?option=com_easydiscuss&view=autoposting&layout=' . $type, false);

		// Request token must not be empty otherwise we can't exchange for an access token.
		if (empty($request->token) || empty($request->secret)) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_AUTOPOST_INVALID_OAUTH_KEY') , DISCUSS_QUEUE_ERROR);
			$this->setRedirect($redirect);
			return;
		}

		// Store the request token temporarily on the table.
		$oauth = ED::table('Oauth');

		// Try to load the record first
		$oauth->load(array('type' => $type));

		// Bind the request tokens
		$param = new JRegistry();
		$param->set('token', $request->token);
		$param->set('secret', $request->secret);

		// Now we need to store this new record
		$oauth->type = $type;
		$oauth->request_token = $param->toString();
		$oauth->store();

		// Get the correct redirection url to the appropriate oauth client's url.
		$destination = $client->getAuthorizationUrl($request);

		ED::redirect($destination);

		return $this->app->close();
	}

	/**
	 * Revokes the access which was granted by the respective oauth providers
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function revoke()
	{
		$type = $this->input->get('type', '', 'cmd');
		$returnUri = $this->input->get('return', '', 'base64');

		// Get the client
		$client = ED::oauth()->getClient($type);

		$table = ED::table('OAuth');
		$table->load(array('type' => $type));

		// Set the access
		$client->setAccess($table->access_token);

		// Default redirection url
		$redirect = JRoute::_('index.php?option=com_easydiscuss&view=autoposting&layout=' . $type, false);

		if ($returnUri) {
			$redirect = base64_decode($returnUri);
		}

		// Revoke the access on Facebook
		$client->revokeApp();

		// Delete the table regardless
		$state = $table->delete();

		if ($state) {
			$actionlog = ED::actionlog();
			$actionlogOauthType = strtoupper($type);

			$actionlog->log('COM_ED_ACTIONLOGS_AUTOPOSTING_REVOKED_' . $actionlogOauthType, 'autoposting', array(
				'link' => 'index.php?option=com_easydiscuss&view=autoposting&layout=' . $type
			));			
		}

		ED::setMessage('COM_EASYDISCUSS_APP_REVOKED_SUCCESS');
		return ED::redirect($redirect);
	}

	/**
	 * This is when the oauth sites redirect the authorization back here.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function grant()
	{
		// Since the callback urls contains the "type" in the query string, we know which client we should be using.
		$type = $this->input->get('type', '', 'cmd');

		$table = ED::table('OAuth');
		$table->load(array('type' => $type));

		// Set the default redirection page
		$redirect = JRoute::_('index.php?option=com_easydiscuss&view=autoposting&layout=' . $type, false);

		// Determines if the user cancelled the operation.
		$denied = $this->input->get('denied', '', 'default');

		if ($denied) {
			$table->delete();

			ED::setMessage(JText::sprintf('Operation was denied by %1$s', $type), ED_MSG_ERROR);
			return ED::redirect($redirect);
		}

		// Get the client
		$client = ED::oauth()->getClient($type);

		// Get the verifier code
		$verifier = $client->getVerifier();

		// If there is no verifier, we have a problem with this authentication.
		if (!$verifier) {
			
			// Delete the record since this request already failed.
			$table->delete();

			throw ED::exception('COM_EASYDISCUSS_AUTOPOST_INVALID_VERIFIER_CODE', ED_MSG_ERROR);
		}

		// Get the request tokens
		$request = json_decode($table->request_token);

		// Try to get the access tokens now.
		$access = $client->getAccessTokens($request->token, $request->secret, $verifier);

		if (!$access || !$access->token || !$access->secret) {
			$table->delete();
			ED::setMessage(JText::_('COM_EASYDISCUSS_AUTOPOST_ERROR_RETRIEVE_ACCESS'), DISCUSS_QUEUE_ERROR);

			return ED::redirect($redirect);
		}

		$registry = new JRegistry();
		$registry->set('token', $access->token);
		$registry->set('secret', $access->secret);
		$registry->set('expires', $access->expires);

		$table->access_token = $registry->toString();
		$table->params = $access->params;
		$table->store();

		ED::setMessage('COM_EASYDISCUSS_AUTOPOST_ACCOUNT_ASSOCIATED_SUCCESSFULLY', DISCUSS_QUEUE_SUCCESS);

		// We need to close the popup now.
		echo '<script>';
		echo "window.opener.doneLogin();";
		echo "window.close();";
		echo '</script>';
		exit;
	}
}
