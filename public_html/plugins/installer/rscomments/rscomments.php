<?php
/**
 * @package RSComments!
 * @copyright (C) 2015 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

class plgInstallerRSComments extends JPlugin
{
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		$uri 	= JUri::getInstance($url);
		$parts 	= explode('/', $uri->getPath());

		if ($uri->getHost() == 'www.rsjoomla.com' && in_array('com_rscomments', $parts)) {
			if (!file_exists(JPATH_SITE.'/components/com_rscomments/helpers/rscomments.php')) {
				return;
			}

			if (!file_exists(JPATH_SITE.'/components/com_rscomments/helpers/version.php')) {
				return;
			}

			// Load our config
			if (!class_exists('RSCommentsHelper')) {
				require_once JPATH_SITE.'/components/com_rscomments/helpers/rscomments.php';
			}

			// Load our version
			require_once JPATH_SITE.'/components/com_rscomments/helpers/version.php';

			// Load language
			JFactory::getLanguage()->load('plg_installer_rscomments');

			// Get the version
			$version = new RSCommentsVersion();

			// Get the update code
			$code = RSCommentsHelper::getConfig('global_register_code');

			// No code added
			if (!strlen($code)) {
				JFactory::getApplication()->enqueueMessage(JText::_('PLG_INSTALLER_RSCOMMENTS_MISSING_UPDATE_CODE'), 'warning');
				return;
			}

			// Code length is incorrect
			if (strlen($code) != 20) {
				JFactory::getApplication()->enqueueMessage(JText::_('PLG_INSTALLER_RSCOMMENTS_INCORRECT_CODE'), 'warning');
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
