<?php
/**
 * Akeeba Engine
 * The PHP-only site backup engine
 *
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Postproc;

// Protection against direct access
defined('AKEEBAENGINE') or die();

class Cloudme extends Webdav
{
	public function __construct()
	{
		$this->settingsKey = 'cloudme';

		parent::__construct();
	}

	protected function modifySettings(array &$settings)
	{
		$settings['baseUri'] = 'https://webdav.cloudme.com/' . $settings['userName'] . '/CloudDrive/Documents/CloudMe';
	}
}
