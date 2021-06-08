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
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\Adapter\ComponentAdapter;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

/**
 * Load the RO Payments installer.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class Com_JdidealgatewayInstallerScript extends InstallerScript
{
	/**
	 * Extension script constructor.
	 *
	 * @since   3.0.0
	 */
	public function __construct()
	{
		$this->minimumJoomla = '3.7';
		$this->minimumPhp    = '7.1';

		$this->deleteFiles = [
			'/administrator/language/nl-NL/nl-NL.com_jdidealgateway.ini',
			'/administrator/language/nl-NL/nl-NL.com_jdidealgateway.sys.ini',
			'/administrator/language/en-GB/en-GB.com_jdidealgateway.ini',
			'/administrator/language/en-GB/en-GB.com_jdidealgateway.sys.ini',
			'/administrator/components/com_jdidealgateway/helpers/cookbook.php',
			'/administrator/components/com_jdidealgateway/helpers/bankconfig.php',
			'/administrator/components/com_jdidealgateway/models/addons/parcls.php',
			'/administrator/components/com_jdidealgateway/models/fields/jdidealgatewaywaitoptions.php',
			'/administrator/components/com_jdidealgateway/models/fields/jdidealgatewaystatus.php',
			'/administrator/components/com_jdidealgateway/models/fields/jdidealgatewayform.php',
			'/administrator/components/com_jdidealgateway/models/fields/jdidealgatewaybanks.php',
			'/administrator/components/com_jdidealgateway/models/forms/jdidealgateway.xml',
			'/administrator/components/com_jdidealgateway/sql/install/install.mysql.utf8.sql',
			'/administrator/components/com_jdidealgateway/sql/update/2.0.sql',
			'/administrator/components/com_jdidealgateway/sql/update/2.1.sql',
			'/administrator/components/com_jdidealgateway/sql/update/2.2.sql',
			'/administrator/components/com_jdidealgateway/sql/update/2.7.1.sql',
			'/administrator/components/com_jdidealgateway/sql/update/2.8.sql',
			'/administrator/components/com_jdidealgateway/sql/update/2.8.2.sql',
			'/administrator/components/com_jdidealgateway/sql/update/4.0.sql',
			'/administrator/components/com_jdidealgateway/tables/jdidealgateway_logs.php',
			'/administrator/components/com_jdidealgateway/tables/jdidealgateway.php',
			'/administrator/components/com_jdidealgateway/views/jdidealgateway/config.xml',
			'/administrator/components/com_jdidealgateway/views/jdidealgateway/tmpl/default_targetpay.php',
			'/administrator/components/com_jdidealgateway/views/jdidealgateway/tmpl/default_sisow.php',
			'/administrator/components/com_jdidealgateway/views/jdidealgateway/tmpl/default_omnikassa_rabo.php',
			'/administrator/components/com_jdidealgateway/views/jdidealgateway/tmpl/default_ogone.php',
			'/administrator/components/com_jdidealgateway/views/jdidealgateway/tmpl/default_mollie.php',
			'/administrator/components/com_jdidealgateway/views/jdidealgateway/tmpl/default_lite_rabobank.php',
			'/administrator/components/com_jdidealgateway/views/jdidealgateway/tmpl/default_lite_abnamro.php',
			'/administrator/components/com_jdidealgateway/views/jdidealgateway/tmpl/default_internetkassa_rabo.php',
			'/administrator/components/com_jdidealgateway/views/jdidealgateway/tmpl/default_internetkassa_abn.php',
			'/administrator/components/com_jdidealgateway/views/jdidealgateway/tmpl/default_general.php',
			'/administrator/components/com_jdidealgateway/views/jdidealgateway/tmpl/default_extra_code.php',
			'/administrator/components/com_jdidealgateway/views/jdidealgateway/tmpl/default_cert_upload.php',
			'/administrator/components/com_jdidealgateway/views/jdidealgateway/tmpl/default_buckaroo.php',
			'/administrator/components/com_jdidealgateway/views/jdidealgateway/tmpl/default_basic.php',
			'/administrator/components/com_jdidealgateway/views/jdidealgateway/tmpl/default_advanced.php',
			'/components/com_jdidealgateway/controllers/index.html',
			'/components/com_jdidealgateway/models/forms/index.html',
			'/components/com_jdidealgateway/models/statusrequest.php',
			'/components/com_jdidealgateway/models/notify.php',
			'/components/com_jdidealgateway/models/index.html',
			'/components/com_jdidealgateway/views/index.html',
			'/components/com_jdidealgateway/views/checkideal/index.html',
			'/components/com_jdidealgateway/views/checkout/index.html',
			'/components/com_jdidealgateway/views/checkout/tmpl/index.html',
			'/components/com_jdidealgateway/views/pay/index.html',
			'/components/com_jdidealgateway/views/pay/tmpl/index.html',
			'/cli/httptest.php',
		];

		$this->deleteFolders = [
			'/administrator/components/com_jdidealgateway/models/addons',
			'/administrator/components/com_jdidealgateway/views/email/tmpl/30',
			'/administrator/components/com_jdidealgateway/views/emails/tmpl/30',
			'/administrator/components/com_jdidealgateway/views/jdidealgateway/tmpl/30',
			'/administrator/components/com_jdidealgateway/views/logs/tmpl/30',
			'/administrator/components/com_jdidealgateway/views/pay',
			'/components/com_jdidealgateway/models/psp',
			'/components/com_jdidealgateway/models/security',
			'/libraries/Jdideal'
		];
	}

	/**
	 * Method to run before an install/update/uninstall method
	 *
	 * @param   string  $type    The type of change (install, update or discover_install).
	 * @param   object  $parent  The class calling this method.
	 *
	 * @return  boolean  True on success | False on failure
	 *
	 * @since   2.0
	 *
	 * @throws  Exception
	 */
	public function preflight($type, $parent): bool
	{
		$this->removeFiles();

		return true;
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @param   string  $type    The type of change (install, update or discover_install).
	 * @param   object  $parent  The class calling this method.
	 *
	 * @return  void
	 *
	 * @since   4.0
	 *
	 * @throws  Exception
	 */
	public function postflight($type, $parent): void
	{
		$this->migrateSettings();
		$this->installCliScript($parent);
		$this->installLibrary($parent);
		$this->installStatuses();
		$this->fixFailureStatus();
		$this->addRecurringProfile();
	}

	/**
	 * Migrate old settings to the new format.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 *
	 * @throws  Exception
	 */
	private function migrateSettings(): void
	{
		$db     = Factory::getDbo();
		$tables = $db->getTableList();
		$table  = $db->getPrefix() . 'jdidealgateway_config';

		if (in_array($table, $tables, true))
		{
			$query = $db->getQuery(true)
				->select(
					$db->quoteName(
						[
							'ideal',
							'payment_extrainfo'
						]
					)
				)
				->from($db->quoteName('#__jdidealgateway_config'))
				->where($db->quoteName('published') . ' = 1');
			$db->setQuery($query);
			$config = $db->loadObject();

			if ($config)
			{
				$settings = new Registry($config->payment_extrainfo);
				$settings->set('psp', str_replace('_', '-', $config->ideal));
				$settings->set('name', $config->ideal);
				$settings->set('alias', $config->ideal);

				$psp = str_replace('_', '-', $config->ideal);

				// Create the profile
				$query->clear()
					->insert($db->quoteName('#__jdidealgateway_profiles'))
					->columns(['name', 'psp', 'alias', 'paymentInfo', 'ordering'])
					->values(
						$db->quote($config->ideal) . ','
						. $db->quote($psp) . ','
						. $db->quote($config->ideal) . ','
						. $db->quote($settings->toString()) . ','
						. 1
					);
				$db->setQuery($query);

				try
				{
					$db->execute();

					$profileId = $db->insertid();

					// Unpublish the migrated setting
					$query->clear()
						->update($db->quoteName('#__jdidealgateway_config'))
						->set($db->quoteName('published') . ' = 0')
						->where($db->quoteName('published') . ' = 1');
					$db->setQuery($query)->execute();

					// Success message
					$messageType   = $settings->get('success_text_id', false) ? 1 : 0;
					$messageTextId = $settings->get('success_text_id', 0);
					$messageText   = $settings->get('success_text', '');
					$this->storeMessage($profileId, 'SUCCESS', $messageType, $messageTextId, $messageText);

					// Failure message
					$messageType   = $settings->get('failed_text_id', false) ? 1 : 0;
					$messageTextId = $settings->get('failed_text_id', 0);
					$messageText   = $settings->get('failed_text', '');
					$this->storeMessage($profileId, 'FAILURE', $messageType, $messageTextId, $messageText);

					// Cancelled message
					$messageType   = $settings->get('cancelled_text_id', false) ? 1 : 0;
					$messageTextId = $settings->get('cancelled_text_id', 0);
					$messageText   = $settings->get('cancelled_text', '');
					$this->storeMessage($profileId, 'CANCELLED', $messageType, $messageTextId, $messageText);

					// Transfer message
					$messageType   = 0;
					$messageTextId = 0;
					$messageText   = $settings->get('IDEAL_OVERBOEKING_MESSAGE', '');
					$this->storeMessage($profileId, 'TRANSFER', $messageType, $messageTextId, $messageText);

					// Warn the users to check their settings
					Factory::getApplication()->enqueueMessage(
						Text::_('COM_ROPAYMENTS_MIGRATION_WARNING'),
						'notice'
					);
				}
				catch (Exception $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}
			}
		}

		$emails = [
			'status_mismatch',
			'admin_order_payment',
			'admin_status_failed',
			'inform_email',
			'jdidealgateway_emailto',
			'customer_change_status'
		];

		$query = $db->getQuery(true)
			->select($db->quoteName(['id', 'paymentInfo']))
			->from($db->quoteName('#__jdidealgateway_profiles'));
		$db->setQuery($query);
		$profiles = $db->loadObjectList();

		$query->clear()
			->update($db->quoteName('#__jdidealgateway_profiles'));

		$params = ComponentHelper::getParams('com_jdidealgateway');

		if ($params->exists($emails[0]) === true)
		{
			foreach ($profiles as $key => $profile)
			{
				$paymentInfo = new Registry($profile->paymentInfo);

				foreach ($emails as $email)
				{
					$value = $paymentInfo->get($email, false);

					if ($value === false)
					{
						$oldValue = $params->get($email, 0);

						if ($email === 'jdidealgateway_emailto' && $oldValue === 0)
						{
							$oldValue = '';
						}

						$paymentInfo->set($email, $oldValue);
					}
				}

				$query
					->clear('set')
					->clear('where')
					->set($db->quoteName('paymentInfo') . ' = ' . $db->quote($paymentInfo->toString()))
					->where($db->quoteName('id') . ' = ' . (int) $profile->id);
				$db->setQuery($query)
					->execute();
			}

			foreach ($emails as $email)
			{
				$params->remove($email);
			}

			$query->clear()
				->update($db->quoteName('#__extensions'))
				->set($db->quoteName('params') . ' = ' . $db->quote($params->toString()))
				->where($db->quoteName('name') . ' = ' . $db->quote('com_jdidealgateway'))
				->where($db->quoteName('type') . ' = ' . $db->quote('component'))
				->where($db->quoteName('element') . ' = ' . $db->quote('com_jdidealgateway'));
			$db->setQuery($query)
				->execute();
		}

		// Set the default profile
		$query->clear()
			->update($db->quoteName('#__jdidealgateway_profiles'))
			->set($db->quoteName('published') . ' = 1')
			->where($db->quoteName('ordering') . ' = 1');
		$db->setQuery($query, 0, 1)
			->execute();

		$columns = $db->getTableColumns('#__jdidealgateway_messages');

		if (array_key_exists('pid', $columns))
		{
			$db->setQuery('ALTER TABLE ' . $db->quoteName('#__jdidealgateway_messages') . ' DROP COLUMN ' . $db->quoteName('pid'))
				->execute();
		}
	}

	/**
	 * Store a migrated message.
	 *
	 * @param   int     $profileId      The profile ID.
	 * @param   string  $orderStatus    The order type the message is for.
	 * @param   int     $messageType    The type of message to render.
	 * @param   int     $messageTextId  The ID of the content item.
	 * @param   string  $messageText    The text to show.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 *
	 * @throws  Exception
	 */
	private function storeMessage(
		int $profileId,
		string $orderStatus,
		int $messageType,
		int $messageTextId,
		string $messageText
	): void {
		$db   = Factory::getDbo();
		$date = new Date;
		$user = Factory::getUser();

		// Get the success message
		$query = $db->getQuery(true)
			->insert($db->quoteName('#__jdidealgateway_messages'))
			->columns(
				$db->quoteName(
					[
						'subject',
						'orderstatus',
						'profile_id',
						'message_type',
						'message_text_id',
						'message_text',
						'language',
						'created',
						'created_by'
					]
				)
			)
			->values(
				$db->quote(ucfirst(strtolower($orderStatus))) . ', '
				. $db->quote($orderStatus) . ', '
				. $profileId . ', '
				. $messageType . ', '
				. $messageTextId . ', '
				. $db->quote($messageText) . ', '
				. $db->quote('*') . ', '
				. $db->quote($date->toSql()) . ', '
				. (int) $user->get('id')
			);
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Install the CLI script.
	 *
	 * @param   object  $parent  The class calling this method.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 *
	 * @throws  RuntimeException
	 */
	private function installCliScript($parent): void
	{
		$src = $parent->getParent()->getPath('source');

		Folder::copy($src . '/cli', JPATH_SITE . '/cli', '', true);
	}

	/**
	 * Install the library.
	 *
	 * @param   object  $parent  The class calling this method.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 *
	 * @throws  RuntimeException
	 */
	private function installLibrary($parent): void
	{
		$src = $parent->getParent()->getPath('source');

		Folder::copy($src . '/libraries', JPATH_LIBRARIES, '', true);
	}

	/**
	 * Check if we need to install any statuses.
	 *
	 * @return  void
	 *
	 * @since   4.9.0
	 */
	private function installStatuses(): void
	{
		$db = Factory::getDbo();

		// Check if there are any statuses
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__jdidealgateway_statuses'));
		$db->setQuery($query);

		$ids = $db->loadColumn();

		if (empty($ids))
		{
			$date   = (new Date)->toSql();
			$userId = Factory::getUser()->id;
			$query->clear()
				->insert($db->quoteName('#__jdidealgateway_statuses'))
				->columns(
					$db->quoteName(
						array(
							'name',
							'jdideal',
							'extension',
							'created',
							'created_by'
						)
					)
				)
				->values(
					$db->quote('Succes') . ',' . $db->quote('C') . ',' . $db->quote('C') . ',' . $db->quote(
						$date
					) . ',' . $userId
				)
				->values(
					$db->quote('Wachten') . ',' . $db->quote('P') . ',' . $db->quote('P') . ',' . $db->quote(
						$date
					) . ',' . $userId
				)
				->values(
					$db->quote('Mislukt') . ',' . $db->quote('F') . ',' . $db->quote('F') . ',' . $db->quote(
						$date
					) . ',' . $userId
				)
				->values(
					$db->quote('Overboeking') . ',' . $db->quote('T') . ',' . $db->quote('T') . ',' . $db->quote(
						$date
					) . ',' . $userId
				)
				->values(
					$db->quote('Geannuleerd') . ',' . $db->quote('X') . ',' . $db->quote('X') . ',' . $db->quote(
						$date
					) . ',' . $userId
				)
				->values(
					$db->quote('Overige statussen') . ',' . $db->quote('O') . ',' . $db->quote('O') . ',' . $db->quote(
						$date
					) . ',' . $userId
				)
				->values(
					$db->quote('Terugstorting') . ',' . $db->quote('R') . ',' . $db->quote('R') . ',' . $db->quote(
						$date
					) . ',' . $userId
				)
				->values(
					$db->quote('Verlopen') . ',' . $db->quote('E') . ',' . $db->quote('E') . ',' . $db->quote(
						$date
					) . ',' . $userId
				)
				->values(
					$db->quote('Chargeback') . ',' . $db->quote('B') . ',' . $db->quote('B') . ',' . $db->quote(
						$date
					) . ',' . $userId
				);
			$db->setQuery($query)->execute();
		}
	}

	/**
	 * Fix the failed status to failure status.
	 *
	 * @return  void
	 *
	 * @since   6.2.1
	 */
	private function fixFailureStatus(): void
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName(['id','paymentInfo']))
			->from($db->quoteName('#__jdidealgateway_profiles'))
			->where($db->quoteName('psp') . ' IN (' . implode(',', $db->quote(['advanced', 'buckaroo', 'kassacompleet', 'onlinekassa'])) . ')');
		$db->setQuery($query);
		$profiles = $db->loadObjectList();

		$query->clear()
			->update($db->quoteName('#__jdidealgateway_profiles'));

		foreach ($profiles as $profile)
		{
			$profile->paymentInfo = str_ireplace('"failedStatus":', '"failureStatus":', $profile->paymentInfo);

			$query->clear('where')
				->clear('set')
				->set($db->quoteName('paymentInfo') . ' = ' . $db->quote($profile->paymentInfo))
				->where($db->quoteName('id') . ' = ' . (int) $profile->id);
			$db->setQuery($query)
				->execute();
		}
	}

	/**
	 * Called on install
	 *
	 * @param   ComponentAdapter  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.8.0
	 *
	 * @throws  Exception
	 */
	public function install(ComponentAdapter $adapter)
	{
		Factory::getApplication()->enqueueMessage(Text::_('COM_ROPAYMENTS_INSTALL_PLUGIN'), 'notice');

		return true;
	}

	/**
	 * Called on update
	 *
	 * @param   ComponentAdapter  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.8.0
	 *
	 * @throws  Exception
	 */
	public function update(ComponentAdapter $adapter): bool
	{
		Factory::getApplication()->enqueueMessage(Text::_('COM_ROPAYMENTS_UPDATE_PLUGIN'), 'notice');

		return true;
	}

	/**
	 * Called on install
	 *
	 * @param   ComponentAdapter  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.8.0
	 *
	 * @throws  Exception
	 */
	public function uninstall(ComponentAdapter $adapter)
	{
		Folder::delete(JPATH_SITE . '/libraries/Jdideal');

		return true;
	}

	/**
	 * Add the profile ID for customers and subscriptions.
	 *
	 * @return  void
	 *
	 * @since   6.3.0
	 */
	private function addRecurringProfile()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName(['id', 'paymentInfo']))
			->from($db->quoteName('#__jdidealgateway_profiles'))
			->where($db->quoteName('alias') . ' = ' . $db->quote('mollie'));
		$db->setQuery($query);
		$profiles = $db->loadObjectList();

		foreach ($profiles as $profile)
		{
			$settings = new Registry($profile->paymentInfo);

			if ((int) $settings->get('recurring') === 0)
			{
				continue;
			}

			$query->clear()
				->select('COUNT(*)')
				->from($db->quoteName('#__jdidealgateway_customers'))
				->where($db->quoteName('profileId') . ' = 0');
			$db->setQuery($query);

			$count = $db->loadResult();

			if ((int) $count === 0)
			{
				continue;
			}

			$query->clear()
				->update($db->quoteName('#__jdidealgateway_customers'))
				->set($db->quoteName('profileId') . ' = ' . (int) $profile->id);
			$db->setQuery($query)
				->execute();

			$query->clear('update')
				->update($db->quoteName('#__jdidealgateway_subscriptions'));
			$db->setQuery($query)
				->execute();
		}
	}
}
