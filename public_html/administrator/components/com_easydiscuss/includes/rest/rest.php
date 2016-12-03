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

class EasyDiscussRest extends EasyDiscuss
{
	const STATUS_SUCCESS = 200;
	const STATUS_ERROR = 404;

	/**
	 * Authenticates if the user is allowed to perform this action
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function auth()
	{
		// Every authentication request needs the user id and the authentication code.
		$userId = $this->input->get('userId', '', 'int');
		$auth = $this->input->get('auth', '', 'default');

		$user = ED::user($userId);

		if ($auth == $user->auth) {
			return $user;
		}

		return false;
	}

	/**
	 * For callers who requires user to authenticate, this method should be used to verify
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUser()
	{
		$user = $this->auth();

		if ($user === false) {
			return $this->error(JText::_('Invalid username or password'));
		}

		return $user;
	}

	/**
	 * Generates a success
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function success($message = '', $data = array())
	{
		$data = array('code' => self::STATUS_SUCCESS, 'data' => $data, 'message' => JText::_($message));

		return $this->output($data);
	}

	/**
	 * Generates an error message to the caller.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function error($string)
	{
		$data = array('code' => self::STATUS_ERROR, 'message' => JText::_($string));

		return $this->output($data);
	}

	public function output($data = array())
	{
		header('Content-type: text/x-json; UTF-8');
		echo json_encode($data);
		exit;
	}
}
