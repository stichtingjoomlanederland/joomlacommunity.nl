<?php
/**
 * @package		akgeoip
 * @copyright	Copyright (c)2014 Nicholas K. Dionysopoulos
 * @license		GNU General Public License version 3, or later
 *
 * This plugin contains code from the following projects:
 * -- Composer, (c) Nils Adermann <naderman@naderman.de>, Jordi Boggiano <j.boggiano@seld.be>
 * -- GeoIPv2, (c) MaxMind www.maxmind.com
 * -- Guzzle, (c) 2011 Michael Dowling, https://github.com/mtdowling <mtdowling@gmail.com>
 * -- MaxMind DB Reader PHP API, (c) MaxMind www.maxmind.com
 * -- Symfiny, (c) 2004-2013 Fabien Potencier
 *
 * Third party software is distributed as-is, each one having its own copyright and license.
 * For more information please see the respective license and readme files, found under
 * the lib directory of this plugin.
 */

defined('_JEXEC') or die();

// PHP version check
if(defined('PHP_VERSION')) {
	$version = PHP_VERSION;
} elseif(function_exists('phpversion')) {
	$version = phpversion();
} else {
	$version = '5.0.0'; // all bets are off!
}
if(!version_compare($version, '5.3.0', '>=')) return;

JLoader::import('joomla.application.plugin');

class plgSystemAkgeoip extends JPlugin
{
	public function __construct(&$subject, $config = array())
	{
		require_once 'lib/akgeoip.php';
		require_once 'lib/vendor/autoload.php';

		$this->loadLanguage('plg_system_akgeoip');

		return parent::__construct($subject, $config);
	}
}