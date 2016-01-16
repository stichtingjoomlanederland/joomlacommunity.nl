<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2009-2014 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 2, or later
 *
 * @since     1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

defined('AKEEBA_BACKUP_ORIGIN') or define('AKEEBA_BACKUP_ORIGIN', 'frontend');

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

class AkeebaControllerBackup extends F0FController
{
	public function __construct($config = array())
	{
		$config['csrf_protection'] = false;

		parent::__construct($config);
	}

	public function execute($task)
	{
		if ($task != 'step')
		{
			$task = 'browse';
		}

		parent::execute($task);
	}

	public function browse()
	{
		// Check permissions
		$this->_checkPermissions();
		// Set the profile
		$this->_setProfile();

		// Get the backup ID
		$backupId = $this->input->get('backupid', null, 'cmd');

		if (strtoupper($backupId) == '[DEFAULT]')
		{
			$db = F0FPlatform::getInstance()->getDbo();
			$query = $db->getQuery(true)
						->select('MAX(' . $db->qn('id') . ')')
						->from($db->qn('#__ak_stats'));

			try
			{
				$maxId = $db->setQuery($query)->loadResult();
			}
			catch (Exception $e)
			{
				$maxId = 0;
			}

			$backupId = 'id' . ($maxId + 1);
		}
		elseif (empty($backupId))
		{
			$backupId = null;
		}

		// Start the backup
		JLoader::import('joomla.utilities.date');
		Factory::resetState(array(
			'maxrun' => 0
		));

		Factory::getTempFiles()->deleteTempFiles();

		$tempVarsTag = AKEEBA_BACKUP_ORIGIN;
		$tempVarsTag .= empty($backupId) ? '' : ('.' . $backupId);

		Factory::getFactoryStorage()->reset($tempVarsTag);

		Factory::loadState(AKEEBA_BACKUP_ORIGIN, $backupId);
		$kettenrad = Factory::getKettenrad();
		$kettenrad->setBackupId($backupId);

		$dateNow = new JDate();

		$description = JText::_('BACKUP_DEFAULT_DESCRIPTION') . ' ' . $dateNow->format(JText::_('DATE_FORMAT_LC2'), true);
		$options = array(
			'description' => $description,
			'comment'     => ''
		);

		$kettenrad->setup($options);
		$kettenrad->tick();
		$kettenrad->tick();
		$array = $kettenrad->getStatusArray();

		try
		{
			Factory::saveState(AKEEBA_BACKUP_ORIGIN, $backupId);
		}
		catch (\RuntimeException $e)
		{
			$array['Error'] = $e->getMessage();
		}

		if ($array['Error'] != '')
		{
			// An error occured
			die('500 ERROR -- ' . $array['Error']);
		}
		else
		{
			$noredirect = $this->input->get('noredirect', 0, 'int');

			if ($noredirect != 0)
			{
				@ob_end_clean();
				header('Content-type: text/plain');
				header('Connection: close');
				echo "301 More work required";
				flush();
				JFactory::getApplication()->close();
			}
			else
			{
				$curUri = JUri::getInstance();
				$ssl = $curUri->isSSL() ? 1 : 0;
				$tempURL = JRoute::_('index.php?option=com_akeeba', false, $ssl);
				$uri = new JUri($tempURL);

				$uri->setVar('view', 'backup');
				$uri->setVar('task', 'step');
				$uri->setVar('key', $this->input->get('key', '', 'none', 2));
				$uri->setVar('profile', $this->input->get('profile', 1, 'int'));

				if (!empty($backupId))
				{
					$uri->setVar('backupid', $backupId);
				}

				// Maybe we have a multilingual site?
				$lg = F0FPlatform::getInstance()->getLanguage();
				$languageTag = $lg->getTag();

				$uri->setVar('lang', $languageTag);

				$redirectionUrl = $uri->toString();

				$this->_customRedirect($redirectionUrl);
			}
		}
	}

	public function step()
	{
		// Check permissions
		$this->_checkPermissions();
		// Set the profile
		$this->_setProfile();

		// Get the backup ID
		$backupId = $this->input->get('backupid', null, 'cmd');

		if (empty($backupId))
		{
			$backupId = null;
		}

		Factory::loadState(AKEEBA_BACKUP_ORIGIN, $backupId);
		$kettenrad = Factory::getKettenrad();
		$kettenrad->setBackupId($backupId);

		$kettenrad->tick();
		$array = $kettenrad->getStatusArray();
		$kettenrad->resetWarnings(); // So as not to have duplicate warnings reports

		try
		{
			Factory::saveState(AKEEBA_BACKUP_ORIGIN, $backupId);
		}
		catch (\RuntimeException $e)
		{
			$array['Error'] = $e->getMessage();
		}

		if ($array['Error'] != '')
		{
			@ob_end_clean();
			echo '500 ERROR -- ' . $array['Error'];
			flush();
			JFactory::getApplication()->close();
		}
		elseif ($array['HasRun'] == 1)
		{
			// All done
			Factory::nuke();
			Factory::getFactoryStorage()->reset();
			@ob_end_clean();
			header('Content-type: text/plain');
			header('Connection: close');
			echo '200 OK';
			flush();
			JFactory::getApplication()->close();
		}
		else
		{
			$noredirect = $this->input->get('noredirect', 0, 'int');

			if ($noredirect != 0)
			{
				@ob_end_clean();
				header('Content-type: text/plain');
				header('Connection: close');
				echo "301 More work required";
				flush();
				JFactory::getApplication()->close();
			}

			else
			{
				$curUri = JUri::getInstance();
				$ssl = $curUri->isSSL() ? 1 : 0;
				$tempURL = JRoute::_('index.php?option=com_akeeba', false, $ssl);
				$uri = new JUri($tempURL);

				$uri->setVar('view', 'backup');
				$uri->setVar('task', 'step');
				$uri->setVar('key', $this->input->get('key', '', 'none', 2));
				$uri->setVar('profile', $this->input->get('profile', 1, 'int'));

				if (!empty($backupId))
				{
					$uri->setVar('backupid', $backupId);
				}

				// Maybe we have a multilingual site?
				$lg = F0FPlatform::getInstance()->getLanguage();
				$languageTag = $lg->getTag();

				$uri->setVar('lang', $languageTag);

				$redirectionUrl = $uri->toString();

				$this->_customRedirect($redirectionUrl);
			}
		}
	}

	/**
	 * Check that the user has sufficient permissions, or die in error
	 *
	 */
	private function _checkPermissions()
	{
		// Is frontend backup enabled?
		$febEnabled = Platform::getInstance()->get_platform_configuration_option('frontend_enable', 0) != 0;

		// Is the Secret Key strong enough?
		$validKey = Platform::getInstance()->get_platform_configuration_option('frontend_secret_word', '');

		if (!\Akeeba\Engine\Util\Complexify::isStrongEnough($validKey, false))
		{
			$febEnabled = false;
		}

		if (!$febEnabled)
		{
			@ob_end_clean();
			echo '403 ' . JText::_('ERROR_NOT_ENABLED');
			flush();
			JFactory::getApplication()->close();
		}

		// Is the key good?
		$key          = $this->input->get('key', '', 'none', 2);
		$validKeyTrim = trim($validKey);

		if (($key != $validKey) || (empty($validKeyTrim)))
		{
			@ob_end_clean();
			echo '403 ' . JText::_('ERROR_INVALID_KEY');
			flush();
			JFactory::getApplication()->close();
		}
	}

	private function _setProfile()
	{
		// Set profile
		$profile = $this->input->get('profile', 1, 'int');

		if (!is_numeric($profile))
		{
			$profile = 1;
		}

		$session = JFactory::getSession();
		$session->set('profile', $profile, 'akeeba');

		Platform::getInstance()->load_configuration($profile);
	}

	private function _customRedirect($url, $header = '302 Found')
	{
		header('HTTP/1.1 ' . $header);
		header('Location: ' . $url);
		header('Content-Type: text/plain');
		header('Connection: close');

		JFactory::getApplication()->close(0);
	}
}