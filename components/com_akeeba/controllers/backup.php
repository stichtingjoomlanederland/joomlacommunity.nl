<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
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
		$this->checkPermissions();
		// Set the profile
		$this->setProfile();

		// Get the backup ID
		$backupId = $this->input->get('backupid', null, 'cmd');

		if (empty($backupId))
		{
			$backupId = null;
		}

		/** @var AkeebaModelBackups $model */
		$model = F0FModel::getTmpInstance('Backups', 'AkeebaModel');

		JLoader::import('joomla.utilities.date');
		$dateNow = new JDate();

		$model->setState('tag', AKEEBA_BACKUP_ORIGIN);
		$model->setState('backupid', $backupId);
		$model->setState('description', JText::_('COM_AKEEBA_BACKUP_DEFAULT_DESCRIPTION') . ' ' . $dateNow->format(JText::_('DATE_FORMAT_LC2'), true));
		$model->setState('comment', '');

		$array = $model->startBackup();

		$backupId = $model->getState('backupid', null, 'cmd');

		$this->processEngineReturnArray($array, $backupId);
	}

	/**
	 * Step through a front-end legacy backup
	 *
	 * @return  void
	 */
	public function step()
	{
		// Setup
		$this->checkPermissions();
		$this->setProfile();

		// Get the backup ID
		$backupId = $this->input->get('backupid', null, 'cmd');

		if (empty($backupId))
		{
			$backupId = null;
		}

		/** @var AkeebaModelBackups $model */
		$model = F0FModel::getTmpInstance('Backups', 'AkeebaModel');

		$model->setState('tag', AKEEBA_BACKUP_ORIGIN);
		$model->setState('backupid', $backupId);

		$array = $model->stepBackup();

		$backupId = $model->getState('backupid', null, 'cmd');

		$this->processEngineReturnArray($array, $backupId);
	}

	/**
	 * Used by the tasks to process Akeeba Engine's return array. Depending on the result and the component options we
	 * may throw text output or send an HTTP redirection header.
	 *
	 * @param   array   $array     The return array to process
	 * @param   string  $backupId  The backup ID (used to step the backup process)
	 */
	private function processEngineReturnArray($array, $backupId)
	{
		if ($array['Error'] != '')
		{
			@ob_end_clean();
			echo '500 ERROR -- ' . $array['Error'];
			flush();

			JFactory::getApplication()->close();
		}

		if ($array['HasRun'] == 1)
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

		$noredirect = $this->input->get('noredirect', 0, 'int');

		if ($noredirect != 0)
		{
			@ob_end_clean();
			header('Content-type: text/plain');
			header('Connection: close');
			echo "301 More work required -- BACKUPID ###$backupId###";
			flush();

			JFactory::getApplication()->close();
		}

		$curUri  = JUri::getInstance();
		$ssl     = $curUri->isSSL() ? 1 : 0;
		$tempURL = JRoute::_('index.php?option=com_akeeba', false, $ssl);
		$uri     = new JUri($tempURL);

		$uri->setVar('view', 'backup');
		$uri->setVar('task', 'step');
		$uri->setVar('key', $this->input->get('key', '', 'none', 2));
		$uri->setVar('profile', $this->input->get('profile', 1, 'int'));

		if (!empty($backupId))
		{
			$uri->setVar('backupid', $backupId);
		}

		// Maybe we have a multilingual site?
		/** @var JLanguage $language */
		$language    = F0FPlatform::getInstance()->getLanguage();
		$languageTag = $language->getTag();

		$uri->setVar('lang', $languageTag);

		$redirectionUrl = $uri->toString();

		$this->customRedirect($redirectionUrl);
	}

	/**
	 * Check that the user has sufficient permissions, or die in error
	 *
	 */
	private function checkPermissions()
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
			echo '403 ' . JText::_('COM_AKEEBA_COMMON_ERR_NOT_ENABLED');
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

	private function setProfile()
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

	private function customRedirect($url, $header = '302 Found')
	{
		header('HTTP/1.1 ' . $header);
		header('Location: ' . $url);
		header('Content-Type: text/plain');
		header('Connection: close');

		JFactory::getApplication()->close(0);
	}
}