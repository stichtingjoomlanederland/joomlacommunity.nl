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
	const helperUrl = 'https://www.akeebabackup.com/oauth2/onedrivebusiness.php';

	/**
	 * The root URL for the OneDrive for Business API, ref https://github.com/OneDrive/onedrive-api-docs/issues/119
	 *
	 * @var   string
	 */
	protected $rootUrl = '';

	/**
	 * Public constructor
	 *
	 * @param   string $accessToken  The access token for accessing OneDrive
	 * @param   string $refreshToken The refresh token for getting new access tokens for OneDrive
	 */
	public function __construct($accessToken, $refreshToken)
	{
		parent::__construct($accessToken, $refreshToken);
	}

	/**
	 * @return string
	 */
	protected function getRefreshUrl()
	{
		return self::helperUrl . '?refresh_token=' . urlencode($this->refreshToken);
	}

	/**
	 * Discover the connection endpoint for Microsoft OneDrive for Business
	 *
	 * @param   bool $apply True to apply the API endpoint on this object
	 *
	 * @return  string
	 *
	 * @see     https://docs.microsoft.com/en-us/onedrive/developer/rest-api/concepts/direct-endpoint-differences
	 */
	public function discoverEndpoint($apply = true)
	{
		// Cache the existing root URL since I need to modify it for fetch() to work
		$cacheRoot = $this->rootUrl;

		// Try to fetch the logged in user's mySite. This is the organization URL linked to their login.
		$this->rootUrl = 'https://graph.microsoft.com/v1.0/';
		$relativeURL   = '/me?$select=mySite';
		$response      = $this->fetch('GET', $relativeURL, array('no-parse' => 1));

		// Restore the previous root
		$this->rootUrl = $cacheRoot;

		// If we fail to find mySite it's a OneDrive Personal user so we have to fall back to the default endpoint
		$endpoint = 'https://api.onedrive.com/v1.0/';
		$decode   = @json_decode($response, true);

		// Is that OneDrive Personal?
		if (isset($decode['error']) && $decode['error']['code'] == 'ResourceNotFound')
		{
			if ($apply)
			{
				$this->rootUrl = $endpoint;

				return $endpoint;
			}
		}

		// Other error?
		if (isset($decode['error']))
		{
			throw new \RuntimeException($decode['error']['code'] . ' :: ' . $decode['error']['message']);
		}

		// Get, apply and return the endpoint for OneDrive for Business
		$endpoint = $decode['mySite'] . '_api/v2.0/';

		if ($apply)
		{
			$this->rootUrl = $endpoint;
		}

		return $endpoint;
	}
}
