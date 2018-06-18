<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Postproc;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Akeeba\Engine\Postproc\Connector\OneDriveBusiness as ConnectorOneDriveBusiness;
use Psr\Log\LogLevel;

class Onedrivebusiness extends Onedrive
{
	/**
	 * The name of the OAuth2 callback method in the parent window (the configuration page)
	 *
	 * @var   string
	 */
	protected $callbackMethod = 'akconfig_onedrivebusiness_oauth_callback';

	/**
	 * The key in Akeeba Engine's settings registry for this post-processing method
	 *
	 * @var   string
	 */
	protected $settingsKey = 'onedrivebusiness';

	/**
	 * Returns an OneDrive connector object instance
	 *
	 * @param   string $access_token
	 * @param   string $refresh_token
	 *
	 * @return  ConnectorOneDriveBusiness
	 */
	protected function getConnectorInstance($access_token, $refresh_token)
	{
		$connector = new ConnectorOneDriveBusiness($access_token, $refresh_token);

		try
		{
			$endPoint = $connector->discoverEndpoint(true);
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - OneDrive for Business endpoint is $endPoint");

			return $connector;
		}
		catch (\RuntimeException $e)
		{
			// If we're here we need to refresh the token
		}

		$refreshResult = $connector->refreshToken();

		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - OneDrive for Business tokens were refreshed");
		$config = Factory::getConfiguration();
		$config->set('engine.postproc.' . $this->settingsKey . '.access_token', $refreshResult['access_token'], false);
		$config->set('engine.postproc.' . $this->settingsKey . '.refresh_token', $refreshResult['refresh_token'], false);

		$profile_id = Platform::getInstance()->get_active_profile();
		Platform::getInstance()->save_configuration($profile_id);

		$endPoint = $connector->discoverEndpoint(true);
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - OneDrive for Business endpoint is $endPoint");

		return $connector;
	}

	/**
	 * Returns the URL to the OAuth2 helper script
	 *
	 * @return string
	 */
	protected function getOAuth2HelperUrl()
	{
		return ConnectorOneDriveBusiness::helperUrl;
	}

}
