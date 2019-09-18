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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyDiscussViewAuth extends EasyDiscussView
{
	/**
	 * Authorize linkedin oauth
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function linkedin()
	{
		$code = $this->input->get('code', '', 'default');
		$state = $this->input->get('state', '', 'default');

		// Stored the generated token code
		if ($code) {
			$client = ED::oauth()->getClient('LinkedIn');

			// Set the authorization code
			$client->setAuthCode($code);

			// Get the access token
			$result = $client->getAccess();

			$table = ED::table('OAuth');
			$table->load(array('type' => 'linkedin'));

			if (!$table->id) {
				$table->type = 'linkedin';
			}

			if ($result) {
				$accessToken = new stdClass();
				$accessToken->token  = $result->token;
				$accessToken->secret = $result->secret;

				// Set the access token now
				$table->access_token = json_encode($accessToken);

				// Set the params
				$table->params = json_encode($result);

				$table->store();
			}

			// Since the page that is redirected to here is a popup, we need to close the window
			// ED::info()->set(JText::_('COM_ED_AUTOPOSTING_LINKEDIN_AUTHORIZED_SUCCESS'), 'success');
		} else {
			// ED::info()->set(JText::_('COM_ED_AUTOPOSTING_LINKEDIN_AUTHORIZED_FAILED'), 'success');
		}

		echo '<script type="text/javascript">window.opener.doneLogin();window.close();</script>';
	}

	/**
	 * Authorize Gist oauth
	 *
	 * @since	4.1.7
	 * @access	public
	 */
	public function github()
	{
		$code = $this->input->get('code', '', 'default');
		$state = $this->input->get('state', '', 'default');

		// Stored the generated token code
		if ($code) {
			$client = ED::oauth()->getClient('Github');

			// Get the access token
			$result = $client->getAccessTokens('', '', $code);

			$table = ED::table('OAuth');
			$table->load(array('type' => 'github'));

			if (!$table->id) {
				$table->type = 'github';
			}

			if ($result) {
				$accessToken = new stdClass();
				$accessToken->token  = $result->token;
				$accessToken->secret = $result->secret;

				// Set the access token now
				$table->access_token = json_encode($accessToken);

				// Set the params
				$table->params = json_encode($result);

				$table->store();
			}

		}

		echo '<script type="text/javascript">window.opener.doneLogin();window.close();</script>';

	}
}
