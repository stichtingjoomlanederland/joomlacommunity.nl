<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 *
 */

namespace Akeeba\Engine\Postproc\Connector;

// Protection against direct access
defined('AKEEBAENGINE') or die();

class OneDriveBusiness extends OneDrive
{
	/**
	 * The URL of the helper script which is used to get fresh API tokens
	 */
	const helperUrl = 'https://www.example.com/oauth2/onedrivebusiness.php';

	/**
	 * The root URL for the OneDrive for Business API, ref https://github.com/OneDrive/onedrive-api-docs/issues/119
	 *
	 * @var   string
	 */
	protected $rootUrl = '';

	/**
	 * The service ID (URL which serves this particular OneDrive for Business instance)
	 *
	 * @var   string
	 */
	protected $serviceId = '';

	/**
	 * Public constructor
	 *
	 * @param   string $accessToken  The access token for accessing OneDrive
	 * @param   string $refreshToken The refresh token for getting new access tokens for OneDrive
	 */
	public function __construct($serviceId, $accessToken, $refreshToken)
	{
		$this->serviceId = $serviceId;
		$this->rootUrl   = rtrim($this->serviceId, '/') . '/_api/v2.0/';

		parent::__construct($accessToken, $refreshToken);
	}

	/**
	 * @return string
	 */
	protected function getRefreshUrl()
	{
		return self::helperUrl . '?refresh_token=' . urlencode($this->refreshToken) .
			'&service_id=' . urlencode($this->serviceId);
	}
}
