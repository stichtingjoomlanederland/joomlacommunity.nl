<?php
/**
* @package RSform!Pro
* @copyright (C) 2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgSystemRSFormInstallerScript
{
	public function preflight($type, $parent) {
		if ($type == 'uninstall') {
			return true;
		}
		
		$app = JFactory::getApplication();
		
		try {
			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php')) {
				throw new Exception('Please install the RSForm! Pro component before continuing.');
			}
			
			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/assets.php')) {
				throw new Exception('Please update RSForm! Pro to at least version 1.51.0 before continuing!');
			}
			
			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/version.php';
			$version = new RSFormProVersion;
			
			if (version_compare((string) $version, '1.52.5', '<')) {
				throw new Exception('Please update RSForm! Pro to at least version 1.52.5 before continuing!');
			}
		} catch (Exception $e) {
			$app->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
		
		return true;
	}
}