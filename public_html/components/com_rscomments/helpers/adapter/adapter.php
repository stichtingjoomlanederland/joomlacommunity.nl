<?php
/**
* @package RSJoomla! Adapter
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');

function RSCommentsAutoload($class) {
	$class = strtolower($class);
	$prefix = 'rscommentsadapter';

	if (strpos($class, $prefix) === 0) {
		// Grab name and filter it
		$name = preg_replace('/[^a-z]/i', '', substr($class, strlen($prefix)));

		// Supported Joomla! versions
		$versions = array('4.0', '3.0');

		// Iterate through and find the first available class
		foreach ($versions as $version) {
			$file = __DIR__ . '/' . $version . '/' . $name . '.php';
			if (version_compare(JVERSION, $version, '>=') && file_exists($file)) {
				require_once $file;
				break;
			}
		}
	}
}

spl_autoload_register('RSCommentsAutoload');