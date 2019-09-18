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

class EasyDiscussOauth extends EasyDiscuss
{
	/**
	 * New way to retrieve the oauth client
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getClient($type)
	{
		return $this->createClient($type);
	}

	/**
	 * Get the consumer type based on the given type.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getConsumer($type, $api, $secret, $callback)
	{
		static $clients = array();

		if (!isset($clients[$type])) {

			$clients[$type] = $this->createClient($type, $api, $secret, $callback);
		}

		return $clients[$type];
	}

	/**
	 * Creates a new oauth client object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function createClient($type = '', $api = '', $secret = '', $callback = '')
	{
		$type = strtolower($type);

		$file = __DIR__ . '/consumers/' . $type . '/' . $type . '.php';

		require_once($file);

		$class = 'EasyDiscuss' . ucfirst($type);

		if (!class_exists($class)) {
			return false;
		}

		$client = new $class($api, $secret, $callback);

		return $client;
	}

	/**
	 * Method to show Facebook oauth redirect URI for backend
	 *
	 * @since   4.0.23
	 * @access  public
	 */
	public function getOauthRedirectURI($type = 'facebook')
	{
		$callbackUri = array();

		if ($type == 'facebook') {
			$callbackUri[] = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easydiscuss&controller=autoposting&task=grant&type=facebook';
		}

		if ($type == 'linkedin') {
			$callbackUri[] = rtrim(JURI::root(), '/') . '/index.php?option=com_easydiscuss&view=auth&layout=linkedin';
		}

		if ($type == 'gist') {
			$callbackUri[] = rtrim(JURI::root(), '/') . '/index.php?option=com_easydiscuss&view=auth&layout=gist';
		}

		if ($type == 'twitter') {
			$callbackUri[] = rtrim(JURI::root(), '/') . '/administrator/index.php';
			$callbackUri[] = rtrim(JURI::root(), '/') . '/index.php';
		}

		return $callbackUri;
	}
}
