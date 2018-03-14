<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Postproc;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Akeeba\Engine\Postproc\Connector\OneDriveBusiness as ConnectorOneDriveBusiness;

class Onedrivebusiness extends Onedrive
{
	/**
	 * The name of the OAuth2 callback method in the parent window (the configuration page)
	 *
	 * @var   string
	 */
	protected $callbackMethod = 'akeeba_onedrivebusiness_oauth_callback';

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
		$configuration = Factory::getConfiguration();
		$serviceId     = $configuration->get('engine.postproc.onedrivebusiness.service_id');
		$serviceId     = trim($serviceId);

		return new ConnectorOneDriveBusiness($serviceId, $access_token, $refresh_token);
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

	protected function initialiseConnector()
	{
		$configuration = Factory::getConfiguration();
		$serviceId     = $configuration->get('engine.postproc.onedrivebusiness.service_id');
		$serviceId     = trim($serviceId);

		if (empty($serviceId))
		{
			$this->setError('You have not linked Akeeba Backup with your OneDrive for Business account (Service ID is missing)');

			return false;
		}

		return parent::initialiseConnector();
	}


}
