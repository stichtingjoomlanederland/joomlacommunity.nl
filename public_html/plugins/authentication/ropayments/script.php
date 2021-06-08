<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

/**
 * Load the RO Payments installer.
 *
 * @package  JDiDEAL
 *
 * @since    4.9.0
 */
class PlgauthenticationropaymentsInstallerScript
{
	/**
	 * Run the postflight operations.
	 *
	 * @param   object  $parent  The parent class.
	 *
	 * @return bool True on success | False on failure.
	 *
	 * @throws Exception
	 * @since   4.9.0
	 */
	public function postflight($parent)
	{
		$app = JFactory::getApplication();
		/** @var JDatabaseDriver $db */
		$db  = JFactory::getDbo();

		// Enable the plugin
		$query = $db->getQuery(true)
			->update($db->quoteName('#__extensions'))
			->set($db->quoteName('enabled') . ' =  1')
			->where($db->quoteName('element') . ' = ' . $db->quote('ropayments'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('authentication'))
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$db->setQuery($query);

		if (!$db->execute())
		{
			$app->enqueueMessage(JText::sprintf('PLG_SYSTEM_JDIDEALGATEWAY_PLUGIN_NOT_ENABLED', $db->getErrorMsg()), 'error');

			return false;
		}

		$app->enqueueMessage(JText::_('PLG_SYSTEM_JDIDEALGATEWAY_PLUGIN_ENABLED'));

		return true;
	}
}
