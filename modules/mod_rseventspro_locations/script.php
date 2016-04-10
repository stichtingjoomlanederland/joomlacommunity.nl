<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class mod_rseventspro_locationsInstallerScript 
{
	public function install($parent) {}
	
	public function preflight($type, $parent) {
		$app = JFactory::getApplication();
		
		if (file_exists(JPATH_SITE.'/components/com_rseventspro/helpers/version.php')) {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/version.php';
			$version = new RSEventsProVersion;
			$version = $version->version;
			
			if (!version_compare($version, '1.8.0', '>=')) {
				$app->enqueueMessage('You need to have at least version 1.8.0 of RSEvents!Pro in order to continue.', 'error');
				return false;
			}
		}
		
		return true;
	}
}