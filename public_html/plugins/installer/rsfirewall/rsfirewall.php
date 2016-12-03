<?php
/**
* @package RSFirewall!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

class plgInstallerRSFirewall extends JPlugin
{
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		$uri 	= JUri::getInstance($url);
		$parts 	= explode('/', $uri->getPath());
		
		if ($uri->getHost() == 'www.rsjoomla.com' && in_array('com_rsfirewall', $parts)) {
			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsfirewall/helpers/config.php')) {
				return;
			}
			
			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsfirewall/helpers/version.php')) {
				return;
			}
			
			// Load our config
			require_once JPATH_ADMINISTRATOR.'/components/com_rsfirewall/helpers/config.php';
			
			// Load our version
			require_once JPATH_ADMINISTRATOR.'/components/com_rsfirewall/helpers/version.php';
			
			// Load language
			JFactory::getLanguage()->load('plg_installer_rsfirewall');
			
			// Get the version
			$version = new RSFirewallVersion;
			
			// Get the update code
			$code = RSFirewallConfig::getInstance()->get('code');
			
			// No code added
			if (!strlen($code)) {
				JFactory::getApplication()->enqueueMessage(JText::_('PLG_INSTALLER_RSFIREWALL_MISSING_UPDATE_CODE'), 'warning');
				return;
			}
			
			// Code length is incorrect
			if (strlen($code) != 20) {
				JFactory::getApplication()->enqueueMessage(JText::_('PLG_INSTALLER_RSFIREWALL_INCORRECT_CODE'), 'warning');
				return;
			}
			
			// Compute the update hash			
			$uri->setVar('hash', md5($code.$version->key));
			$uri->setVar('domain', JUri::getInstance()->getHost());
			$uri->setVar('code', $code);
			$url = $uri->toString();
		}
	}
}
