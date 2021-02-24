<?php
/*
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Joomla\Plugin\Console\AkeebaBackup\Extension;

defined('_JEXEC') || die;

use Akeeba\Backup\Admin\Helper\SecretWord;
use Akeeba\Backup\Admin\Model\ControlPanel;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use FOF30\Container\Container;
use Joomla\Application\ApplicationEvents;
use Joomla\Application\Event\ApplicationEvent;
use Joomla\CMS\Application\ConsoleApplication;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Console\Command\AbstractCommand;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\SubscriberInterface;
use RuntimeException;
use Throwable;

class AkeebaBackup extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  7.5.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Application object.
	 *
	 * @var    ConsoleApplication
	 * @since  7.5.0
	 */
	protected $app;

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 *
	 * @since   7.5.0
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			ApplicationEvents::BEFORE_EXECUTE => 'registerCLICommands',
		];
	}

	/**
	 * Registers command classes to the CLI application.
	 *
	 * This is an event handled for the ApplicationEvents::BEFORE_EXECUTE event.
	 *
	 * @param   ApplicationEvent  $event  The before_execite application event being handled
	 *
	 * @since   7.5.0
	 */
	public function registerCLICommands(ApplicationEvent $event)
	{
		// Only register CLI commands if we can boot up the Akeeba Backup component enough to make it usable.
		try
		{
			//$this->initialiseComponent();
		}
		catch (Throwable $e)
		{
			return;
		}
		$this->initialiseComponent();

		/** @var ConsoleApplication $app */
		$app = $event->getApplication();

		// Try to find all commands in the CliCommands directory of the component
		$baseNamespace = '\Akeeba\Backup\Admin\CliCommands\\';
		$files         = Folder::files(JPATH_ADMINISTRATOR . '/components/com_akeeba/CliCommands', '.php');
		$files         = is_array($files) ? $files : [];

		foreach ($files as $file)
		{
			// Construct a fully qualified class name
			$className = $baseNamespace . basename($file, '.php');

			/**
			 * Make sure the class exists. Works around hosts renaming/duplicating files with silly extensions like
			 * .1.php or .disabled.php. We've been seeing that nonsense since as early as 2013.
			 */
			if (!class_exists($className))
			{
				continue;
			}

			// If I can construct the command object add it to the application. Otherwise move to the next file.
			try
			{
				$o = new $className;

				if (!($o instanceof AbstractCommand))
				{
					continue;
				}

				$app->addCommand($o);
			}
			catch (Throwable $e)
			{
				continue;
			}
		}
	}

	private function initialiseComponent(): void
	{
		if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
		{
			throw new RuntimeException('FOF 3.0 is not installed', 500);
		}

		if (!defined('AKEEBA_CACERT_PEM'))
		{
			define('AKEEBA_CACERT_PEM', JPATH_LIBRARIES . '/src/Http/Transport/cacert.pem');
		}

		$container = Container::getInstance('com_akeeba', [], 'admin');

		// Load the FOF language
		$lang = $container->platform->getLanguage();
		$lang->load('lib_fof30', JPATH_ADMINISTRATOR, 'en-GB', true, true);
		$lang->load('lib_fof30', JPATH_ADMINISTRATOR, null, true, false);

		// Load the Akeeba Backup language files
		$lang->load('com_akeeba', JPATH_SITE, 'en-GB', true, true);
		$lang->load('com_akeeba', JPATH_SITE, null, true, false);
		$lang->load('com_akeeba', JPATH_ADMINISTRATOR, 'en-GB', true, true);
		$lang->load('com_akeeba', JPATH_ADMINISTRATOR, null, true, false);

		// Necessary for routing the Alice view
		$container->inflector->addWord('Alice', 'Alices');

		// Load Akeeba Engine
		$this->loadAkeebaEngine();

		// Load the Akeeba Engine configuration
		$retryLoadingEngineConfiguration = false;

		try
		{
			$this->loadAkeebaEngineConfiguration();
		}
		catch (\Exception $e)
		{
			// Maybe the tables are not installed?
			/** @var ControlPanel $cPanelModel */
			$cPanelModel = $container->factory->model('ControlPanel')->tmpInstance();
			$cPanelModel->checkAndFixDatabase();
			$retryLoadingEngineConfiguration = true;
		}

		if ($retryLoadingEngineConfiguration)
		{
			$this->loadAkeebaEngineConfiguration();
		}

		// Prevents the "SQLSTATE[HY000]: General error: 2014" due to resource sharing with Akeeba Engine
		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		// !!!!! WARNING: ALWAYS GO THROUGH JFactory; DO NOT GO THROUGH $this->container->db !!!!!
		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		if (version_compare(PHP_VERSION, '7.999.999', 'le'))
		{
			/** @var DatabaseDriver $jDbo */
			$jDbo = JFactory::getContainer()->get('DatabaseDriver');

			if ($jDbo->getName() == 'pdomysql')
			{
				@$jDbo->disconnect();
			}
		}

		// Make sure the front-end backup Secret Word is stored encrypted
		$params = $container->params;
		SecretWord::enforceEncryption($params, 'frontend_secret_word');

		// Make sure we have a version loaded
		@include_once($container->backEndPath . '/version.php');

		if (!defined('AKEEBA_VERSION'))
		{
			define('AKEEBA_VERSION', 'dev');
			define('AKEEBA_DATE', date('Y-m-d'));
		}
	}

	public function loadAkeebaEngine(): void
	{
		$container = Container::getInstance('com_akeeba', [], 'admin');

		// Necessary defines for Akeeba Engine
		if (!defined('AKEEBAENGINE'))
		{
			define('AKEEBAENGINE', 1);
			define('AKEEBAROOT', $container->backEndPath . '/BackupEngine');
		}

		if (!defined('AKEEBA_BACKUP_ORIGIN'))
		{
			define('AKEEBA_BACKUP_ORIGIN', 'cli');
		}

		// Make sure we have a profile set throughout the component's lifetime
		$profile_id = $container->platform->getSessionVar('profile', null, 'akeeba');

		if (is_null($profile_id))
		{
			$container->platform->setSessionVar('profile', 1, 'akeeba');
		}

		// Load Akeeba Engine
		$basePath = $container->backEndPath;
		require_once $basePath . '/BackupEngine/Factory.php';
	}

	public function loadAkeebaEngineConfiguration(): void
	{
		$container = Container::getInstance('com_akeeba', [], 'admin');
		Platform::addPlatform('joomla3x', $container->backEndPath . '/BackupPlatform/Joomla3x');
		$akeebaEngineConfig = Factory::getConfiguration();
		Platform::getInstance()->load_configuration();
		unset($akeebaEngineConfig);
	}
}
