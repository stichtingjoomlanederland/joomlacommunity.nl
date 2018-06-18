<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class plgInstallerEasyDiscuss extends JPlugin
{
	const EXTENSION_ID = 'com_easydiscuss';
	const EXTENSION_NAME = 'easydiscuss';
	const UPDATER_URL = 'https://services.stackideas.com/updater/easydiscuss';

	/**
	 * Determines if EasyDiscuss is installed
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function exists()
	{
		$file = JPATH_ADMINISTRATOR . '/components/' . self::EXTENSION_ID . '/includes/' . SELF::EXTENSION_NAME . '.php';

		if (!JFile::exists($file) || !JComponentHelper::isInstalled(self::EXTENSION_ID)) {
			return false;
		}

		require_once($file);

		return true;
	}

	/**
	 * Modifies update url
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		$app = JFactory::getApplication();

		// If EasyBlog doesn't exist or it isn't enabled, there is no point updating it.
		if (!$this->exists() || stristr($url, self::UPDATER_URL) === false) {
			return true;
		}

		// Get user's subscription key
		$config = ED::config();
		$key = $config->get('main_apikey');

		if (!$key) {
			$app->enqueueMessage('Your setup contains an invalid api key. EasyDiscuss will not be updated now. If the problem still persists, please get in touch with the support team at https://stackideas.com/forums', 'error');

			return true;
		}

		$domain = str_ireplace(array('http://', 'https://'), '', rtrim(JURI::root(), '/'));

		$uri = new JURI($url);
		$uri->setVar('from', ED::getLocalVersion());
		$uri->setVar('key', $key);
		$uri->setVar('domain', $domain);
		$url = $uri->toString();

		return true;
	}
}
