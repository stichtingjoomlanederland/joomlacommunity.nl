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

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewAccount extends EasyDiscussView
{
	/**
	 * Allows remote user to authenticate via a normal http request and returns with an authentication code.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		$username = $this->input->get('username', '', 'default');
		$password = $this->input->get('password', '', 'default');

		// Construct options to provide to joomla
		$options = array('username' => $username, 'password' => $password);

		// Try to log the user in with the provided user login credentials
		$state = $this->app->login($options);

		if ($state === false) {

			$this->set('code', 403);
			$this->set('message', JText::_('Invalid username or password provided'));

			return parent::display();
		}

		$user = ED::user();

		// User logs in successfully. Generate an authentication code for the user
		$hash = md5($this->my->password . JFactory::getDate()->toSql());
		$user->auth = $hash;
		$user->store();

		$this->set('auth', $user->auth);
		$this->set('code', 200);
		$this->set('id', $user->id);

		return parent::display();
	}
}
