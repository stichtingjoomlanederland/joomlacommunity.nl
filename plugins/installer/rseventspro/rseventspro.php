<?php
/**
* @package RSEvents! Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

class plgInstallerRSEventspro extends JPlugin
{
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		$uri 	= JUri::getInstance($url);
		$parts 	= explode('/', $uri->getPath());
		
		if ($uri->getHost() == 'www.rsjoomla.com' && in_array('com_rseventspro', $parts)) {
			if (!file_exists(JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php')) {
				return;
			}
			
			if (!file_exists(JPATH_SITE.'/components/com_rseventspro/helpers/version.php')) {
				return;
			}
			
			// Load our main helper
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php';
			
			// Load language
			JFactory::getLanguage()->load('plg_installer_rseventspro');
			
			// Get the update code
			$code = rseventsproHelper::getConfig('global_code');
			
			// No code added
			if (!strlen($code)) {
				JFactory::getApplication()->enqueueMessage(JText::_('PLG_INSTALLER_RSEVENTSPRO_MISSING_UPDATE_CODE'), 'warning');
				return;
			}
			
			// Code length is incorrect
			if (strlen($code) != 20) {
				JFactory::getApplication()->enqueueMessage(JText::_('PLG_INSTALLER_RSEVENTSPRO_INCORRECT_CODE'), 'warning');
				return;
			}
			
			// Compute the hash
			$hash = rseventsproHelper::genKeyCode();
			
			// Compute the update hash			
			$uri->setVar('hash', $hash);
			$uri->setVar('domain', JUri::getInstance()->getHost());
			$uri->setVar('code', $code);
			$url = $uri->toString();
		}
	}
}
