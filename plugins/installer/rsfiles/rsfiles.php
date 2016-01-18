<?php
/**
* @package RSFiles!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

class plgInstallerRSFiles extends JPlugin
{
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		$uri 	= JUri::getInstance($url);
		$parts 	= explode('/', $uri->getPath());
		
		if ($uri->getHost() == 'www.rsjoomla.com' && in_array('com_rsfiles', $parts)) {
			if (!file_exists(JPATH_SITE.'/components/com_rsfiles/helpers/rsfiles.php')) {
				return;
			}
			
			if (!file_exists(JPATH_SITE.'/components/com_rsfiles/helpers/version.php')) {
				return;
			}
			
			// Load our main helper
			require_once JPATH_SITE.'/components/com_rsfiles/helpers/rsfiles.php';
			
			// Load language
			JFactory::getLanguage()->load('plg_installer_rsfiles');
			
			// Get the update code
			$code = rsfilesHelper::getConfig('license_code');
			
			// No code added
			if (!strlen($code)) {
				JFactory::getApplication()->enqueueMessage(JText::_('PLG_INSTALLER_RSFILES_MISSING_UPDATE_CODE'), 'warning');
				return;
			}
			
			// Code length is incorrect
			if (strlen($code) != 20) {
				JFactory::getApplication()->enqueueMessage(JText::_('PLG_INSTALLER_RSFILES_INCORRECT_CODE'), 'warning');
				return;
			}
			
			// Compute the hash
			$hash = rsfilesHelper::genKeyCode();
			
			// Compute the update hash			
			$uri->setVar('hash', $hash);
			$uri->setVar('domain', JUri::getInstance()->getHost());
			$uri->setVar('code', $code);
			$url = $uri->toString();
		}
	}
}
