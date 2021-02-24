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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

/**
 * Load the RO Payments installer.
 *
 * @package  JDiDEAL
 *
 * @since    2.0.0
 */
class PlgsystemrsfpjdidealInstallerScript
{
	/**
	 * Run the preflight operations.
	 *
	 * @param   object  $parent  The parent class.
	 *
	 * @return  boolean  True on success | False on failure.
	 *
	 * @since   4.7.0
	 */
	public function preflight($parent): bool
	{
		// Check if RO Payments is installed
		if ($parent === 'install' && ComponentHelper::isInstalled('com_jdidealgateway') === 0)
		{
			Factory::getApplication()->enqueueMessage(
				Text::_('PLG_RSFP_PAYMENT_JDIDEAL_JDIDEALGATEWAY_NOT_INSTALLED'),
				'error'
			);

			return false;
		}

		// Check if RSForm! Pro is installed
		if (ComponentHelper::isInstalled('com_rsform') === 0)
		{
			Factory::getApplication()->enqueueMessage(
				Text::_('PLG_RSFP_PAYMENT_JDIDEAL_RSFORM_NOT_INSTALLED'),
				'error'
			);

			return false;
		}

		return true;
	}

	/**
	 * Run the postflight operations.
	 *
	 * @param   object  $parent  The parent class.
	 *
	 * @return  boolean True on success | False on failure.
	 *
	 * @since   2.0
	 */
	public function postflight($parent): bool
	{
		$app = Factory::getApplication();
		/** @var JDatabaseDriver $db */
		$db = Factory::getDbo();

		$this->migrateSettings();

		// Run the uninstall SQL
		if (File::exists(JPATH_SITE . '/plugins/system/rsfpjdideal/sql/uninstall.mysql.rsfpjdideal.sql'))
		{
			$queries = JDatabaseDriver::splitSql(
				file_get_contents(JPATH_SITE . '/plugins/system/rsfpjdideal/sql/uninstall.mysql.rsfpjdideal.sql')
			);

			foreach ($queries as $index => $query)
			{
				$db->setQuery($query)->execute();
			}
		}

		if (File::exists(JPATH_SITE . '/plugins/system/rsfpjdideal/sql/install.mysql.rsfpjdideal.sql'))
		{
			// Run the install SQL
			$queries = JDatabaseDriver::splitSql(
				file_get_contents(JPATH_SITE . '/plugins/system/rsfpjdideal/sql/install.mysql.rsfpjdideal.sql')
			);

			foreach ($queries as $index => $query)
			{
				$db->setQuery($query)->execute();
			}
		}

		// Enable the plugin
		$query = $db->getQuery(true)
			->update($db->quoteName('#__extensions'))
			->set($db->quoteName('enabled') . ' =  1')
			->where($db->quoteName('element') . ' = ' . $db->quote('rsfpjdideal'))
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$db->setQuery($query);

		if (!$db->execute())
		{
			$app->enqueueMessage(
				Text::sprintf('PLG_RSFP_PAYMENT_JDIDEAL_PLUGIN_NOT_ENABLED', $db->getErrorMsg()),
				'error'
			);

			return false;
		}

		$app->enqueueMessage(Text::_('PLG_RSFP_PAYMENT_JDIDEAL_PLUGIN_ENABLED'));

		return true;
	}

	/**
	 * Migrate the settings from the rsform_config table to the rsform_jdideal table
	 *
	 * @return  void
	 *
	 * @since   5.3.0
	 */
	private function migrateSettings(): void
	{
		$db = Factory::getDbo();

		// Load the global settings
		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					[
						'SettingName',
						'SettingValue',
					]
				)
			)
			->from($db->quoteName('#__rsform_config'))
			->where(
				$db->quoteName('SettingName') . ' IN (' . implode(', ', $db->quote(
					[
						'jdideal.thousands',
						'jdideal.decimal',
						'jdideal.nodecimals',
						'jdideal.tax.type',
						'jdideal.tax.value',
						'jdideal.ordernr',
						'jdideal.email',
					]
				)) . ')'
			);
		$db->setQuery($query);
		$settings = $db->loadObjectList();

		if (!$settings)
		{
			return;
		}

		$params = new Registry;

		foreach ($settings as $setting)
		{
		    $name = str_ireplace('jdideal.', '', $setting->SettingName);

		    switch ($name)
            {
                case 'nodecimals':
                    $name = 'numberDecimals';
                    break;
                case 'tax.type':
                    $name = 'taxType';
                    break;
                case 'tax.value':
                    $name = 'taxValue';
                    break;
                case 'ordernr':
                    $name = 'fieldOrderNumber';
                    break;
                case 'email':
                    $name = 'fieldEmail';
                    break;
            }

			$params->set($name, $setting->SettingValue);
		}

		// Load all forms
		$query->clear()
			->select($db->quoteName('FormId'))
			->from($db->quoteName('#__rsform_forms'));
		$db->setQuery($query);
		$formIds = $db->loadColumn();

		foreach ($formIds as $formId)
		{
			$query->clear()
				->select(
					$db->quoteName(
						[
							'form_id',
							'params'
						],
						[
							'formId',
							'params'
						]
					)
				)
				->from($db->quoteName('#__rsform_jdideal'))
				->where($db->quoteName('form_id') . ' = ' . (int) $formId);
			$db->setQuery($query);

			$config = $db->loadObject();

			if (!isset($config->formId))
			{
				$query->clear()
					->insert($db->quoteName('#__rsform_jdideal'))
					->values($formId . ',' . $db->quote($params->toString()));
				$db->setQuery($query)
					->execute();
			}
			else
			{
				$newParams = clone $params;
				$newParams->loadString($config->params);
				$query->clear()
					->update($db->quoteName('#__rsform_jdideal'))
					->set($db->quoteName('params') . ' = ' . $db->quote($newParams->toString()));
			}
		}
	}

	/**
	 * Cleanup after uninstallation.
	 *
	 * @param   object  $parent  The parent class.
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	public function uninstall($parent): void
	{
		File::delete(JPATH_ADMINISTRATOR . '/components/com_jdidealgateway/models/addons/rsformpro.php');
	}
}
