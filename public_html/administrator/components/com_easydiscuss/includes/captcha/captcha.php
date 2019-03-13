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

class EasyDiscussCaptcha extends EasyDiscuss
{
	public $adapter = null;

	/**
	 * Get the captcha adapter
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAdapter()
	{
		// We shouldn't be loading anything if they are not enabled
		if (!$this->enabled()) {
			return false;
		}

		if (!$this->adapter) {
			$type = $this->config->get('antispam_captcha');
		
			$file = __DIR__ . '/adapters/' . strtolower($type) . '.php';
			require_once($file);

			$class = 'EasyDiscussCaptcha' . ucfirst($type);
			$this->adapter = new $class();
		}
		
		return $this->adapter;
	}

	/**
	 * Proxy method to the adapter
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function __call($method, $args)
	{
		$adapter = $this->getAdapter();

		// Call the appropriate method
		return call_user_func_array(array($adapter, $method), $args);
	}

	/**
	 * Performs validation here
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function validate($data)
	{
		// Captcha is disabled, always return true
		if (!$this->enabled()) {
			return true;
		}

		// Determines which adapter to use.
		$adapter = $this->getAdapter();
		$valid = $adapter->validate($data);

		return $valid;
	}

	/**
	 * Determines if we should display the captcha
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function enabled()
	{
		// Get the captcha type
		$type = $this->config->get('antispam_captcha');

		if ($type == 'none') {
			return false;
		}

		// If user is logged in and captcha image is disabled for registered users.
		if ($this->my->id && !$this->config->get('antispam_captcha_registered')) {
			return false;
		}

		// If skip recaptcha after "x" posts count
		$skipAfter = $this->config->get('antispam_skip_captcha');

		if ($skipAfter) {
			// Get user's post count
			$user = ED::user();
			$total = $user->getTotalQuestions();

			if ($total > $skipAfter) {
				return false;
			}
		}

		return true;
	}
}
