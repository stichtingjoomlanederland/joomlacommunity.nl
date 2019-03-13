<?php
/**
 * Akeeba Engine
 * The PHP-only site backup engine
 *
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Postproc\Connector;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Postproc\Connector\Cloudfiles\Exception\Missing\Apikey;
use Akeeba\Engine\Postproc\Connector\Cloudfiles\Exception\Missing\Tenantid;
use Akeeba\Engine\Postproc\Connector\Cloudfiles\Exception\Missing\Username;

class Ovh extends Swift
{
	/**
	 * Ovh constructor.
	 *
	 * @param   string  $tenantId      The OpenStack tenant ID.
	 * @param   string  $username      The OpenStack username.
	 * @param   string  $password      The OpenStack password.
	 *
	 * @since   6.1.0
	 *
	 * @throws  Apikey
	 * @throws  Tenantid
	 * @throws  Username
	 */
	public function __construct($tenantId, $username, $password)
	{
		$authEndpoint = 'https://auth.cloud.ovh.net/v2.0';

		// Data validation
		if (empty($tenantId))
		{
			throw new TenantId('You have not specified your OVH OpenStack Project ID');
		}

		if (empty($username))
		{
			throw new Username('You have not specified your OVH OpenStack Username');
		}

		if (empty($username))
		{
			throw new Apikey('You have not specified your OVH OpenStack Password');
		}

		parent::__construct($authEndpoint, $tenantId, $username, $password);
	}
}
