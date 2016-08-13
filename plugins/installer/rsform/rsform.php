<?php
/**
* @package RSForm! Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

class plgInstallerRSForm extends JPlugin
{
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		$uri 	= JUri::getInstance($url);
		$parts 	= explode('/', $uri->getPath());
		
		if ($uri->getHost() == 'www.rsjoomla.com' && (in_array('com_rsform', $parts) || in_array('plg_rsform_plugins', $parts))) {
			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/config.php')) {
				return;
			}
			
			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/version.php')) {
				return;
			}
			
			// Load our config
			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/config.php';
			
			// Load our version
			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/version.php';
			
			// Load language
			JFactory::getLanguage()->load('plg_installer_rsform');
			
			// Get the version
			$version = new RSFormProVersion;
			
			// Get the update code
			$code = RSFormProConfig::getInstance()->get('global.register.code');
			
			// No code added
			if (!strlen($code)) {
				JFactory::getApplication()->enqueueMessage(JText::_('PLG_INSTALLER_RSFORM_MISSING_UPDATE_CODE'), 'warning');
				return;
			}
			
			// Code length is incorrect
			if (strlen($code) != 20) {
				JFactory::getApplication()->enqueueMessage(JText::_('PLG_INSTALLER_RSFORM_INCORRECT_CODE'), 'warning');
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
