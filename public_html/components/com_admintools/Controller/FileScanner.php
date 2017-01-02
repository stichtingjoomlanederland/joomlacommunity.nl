<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Site\Controller;

defined('_JEXEC') or die;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Akeeba\Engine\Util\Complexify;
use FOF30\Container\Container;
use FOF30\Controller\Controller;
use JFactory;
use JText;

class FileScanner extends Controller
{
	public function __construct(Container $container, $config = array())
	{
		$config['csrfProtection'] = false;

		parent::__construct($container, $config);

		$this->scanEngineSetup();
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

		Platform::getInstance()->load_configuration(1);
		Factory::resetState();
		Factory::getFactoryStorage()->reset(AKEEBA_BACKUP_ORIGIN);

		$configOverrides['volatile.core.finalization.action_handlers'] = array(
			new \Akeeba\Engine\Finalization\Email()
		);
		$configOverrides['volatile.core.finalization.action_queue'] = array(
			'remove_temp_files',
			'update_statistics',
			'update_filesizes',
			'apply_quotas',
			'send_scan_email'
		);

		// Apply the configuration overrides, please
		$platform = Platform::getInstance();
		$platform->configOverrides = $configOverrides;

		$kettenrad = Factory::getKettenrad();
		$options = array(
			'description' => '',
			'comment'     => '',
			'jpskey'      => ''
		);
		$kettenrad->setup($options);

		Factory::getLog()->open(AKEEBA_BACKUP_ORIGIN);
		Factory::getLog()->log(true, '');

		$kettenrad->tick();
		$kettenrad->tick();

		Factory::saveState(AKEEBA_BACKUP_ORIGIN);

		$array = $kettenrad->getStatusArray();

		try
		{
			Factory::saveState(AKEEBA_BACKUP_ORIGIN);
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
				$curUri = \JUri::getInstance();
				$ssl = $curUri->isSSL() ? 1 : 0;
				$tempURL = \JRoute::_('index.php?option=com_admintools', false, $ssl);
				$uri = new \JUri($tempURL);

				$uri->setVar('view', 'FileScanner');
				$uri->setVar('task', 'step');
				$uri->setVar('key', $this->input->get('key', '', 'raw', 2));

				// Maybe we have a multilingual site?
				$lg = $this->container->platform->getLanguage();
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

		Factory::loadState(AKEEBA_BACKUP_ORIGIN);
		$kettenrad = Factory::getKettenrad();

		$kettenrad->tick();
		$array = $kettenrad->getStatusArray();
		$kettenrad->resetWarnings(); // So as not to have duplicate warnings reports

		try
		{
			Factory::saveState(AKEEBA_BACKUP_ORIGIN);
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
				$curUri = \JUri::getInstance();
				$ssl = $curUri->isSSL() ? 1 : 0;
				$tempURL = \JRoute::_('index.php?option=com_admintools', false, $ssl);
				$uri = new \JUri($tempURL);

				$uri->setVar('view', 'FileScanner');
				$uri->setVar('task', 'step');
				$uri->setVar('key', $this->input->get('key', '', 'raw', 2));

				// Maybe we have a multilingual site?
				$lg = $this->container->platform->getLanguage();
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

		if (!Complexify::isStrongEnough($validKey, false))
		{
			$febEnabled = false;
		}

		if (!$febEnabled)
		{
			@ob_end_clean();
			echo '403 ' . JText::_('COM_ADMINTOOLS_ERROR_NOT_ENABLED');
			flush();
			JFactory::getApplication()->close();
		}

		// Is the key good?
		$key          = $this->input->get('key', '', 'raw', 2);
		$validKeyTrim = trim($validKey);

		if (($key != $validKey) || (empty($validKeyTrim)))
		{
			@ob_end_clean();
			echo '403 ' . JText::_('COM_ADMINTOOLS_ERROR_INVALID_KEY');
			flush();
			JFactory::getApplication()->close();
		}
	}

	private function _customRedirect($url, $header = '302 Found')
	{
		header('HTTP/1.1 ' . $header);
		header('Location: ' . $url);
		header('Content-Type: text/plain');
		header('Connection: close');

		JFactory::getApplication()->close(0);
	}

	/**
	 * Sets up the environment to start or continue a file scan
	 *
	 * @return bool
	 */
	private function scanEngineSetup()
	{
		// Load the Akeeba Engine autoloader
		define('AKEEBAENGINE', 1);
		require_once JPATH_ADMINISTRATOR . '/components/com_admintools/engine/Autoloader.php';

		// Load the platform
		Platform::addPlatform('filescan', JPATH_ADMINISTRATOR . '/components/com_admintools/platform/Filescan');

		// Load the engine configuration
		Platform::getInstance()->load_configuration(1);
		$this->aeconfig = Factory::getConfiguration();

		define('AKEEBA_BACKUP_ORIGIN', 'frontend');

		// Unset time limits
		$safe_mode = true;

		if (function_exists('ini_get'))
		{
			$safe_mode = ini_get('safe_mode');
		}

		if (!$safe_mode && function_exists('set_time_limit'))
		{
			@set_time_limit(0);
		}

		return true;
	}
}