<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

// Old PHP version detected. EJECT! EJECT! EJECT!
if ( !version_compare(PHP_VERSION, '5.4.0', '>='))
{
	return;
}

// Why, oh why, are you people using eAccelerator? Seriously, what's wrong with you, people?!
if (function_exists('eaccelerator_info'))
{
	$isBrokenCachingEnabled = true;

	if (function_exists('ini_get') && !ini_get('eaccelerator.enable'))
	{
		$isBrokenCachingEnabled = false;
	}

	if ($isBrokenCachingEnabled)
	{
		/**
		 * I know that this define seems pointless since I am returning. This means that we are exiting the file and
		 * the plugin class isn't defined, so Joomla cannot possibly use it.
		 *
		 * LOL. That is how PHP works. Not how that GINORMOUS, STINKY PILE OF BULL CRAP called eAccelerator screws up
		 * your code.
		 *
		 * That disgusting piece of bit rot will exit right after the return statement below BUT it will STILL define
		 * the class. That's right. It ignores ALL THE CODE between here and the class declaration and parses the
		 * class declaration o_O  Therefore the only way to actually NOT load the damn plugin when you are using it on
		 * a server where a masturbating, lobotomized bonobo on meth has installed and enabled the tragic waste of
		 * disk space called eAccelerator is to define a constant and use it to return from the constructor method,
		 * therefore forcing PHP to return null instead of an object. This prompts Joomla to not do anything with the
		 * plugin. Because screw you eAccelerator, that's why.
		 */
		if (!defined('AKEEBA_EACCELERATOR_IS_SO_BORKED_IT_DOES_NOT_EVEN_RETURN'))
		{
			define('AKEEBA_EACCELERATOR_IS_SO_BORKED_IT_DOES_NOT_EVEN_RETURN', 3245);
		}

		return;
	}
}

// Make sure Akeeba Backup is installed
if ( !file_exists(JPATH_ADMINISTRATOR . '/components/com_akeeba'))
{
	return;
}

// Joomla! version check
if (version_compare(JVERSION, '2.5', 'lt'))
{
	// Joomla! earlier than 2.5. Nope.
	return;
}


use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

// Deactivate self
$db    = JFactory::getDbo();
$query = $db->getQuery(true)
			->update($db->qn('#__extensions'))
			->set($db->qn('enabled') . ' = ' . $db->q('0'))
			->where($db->qn('element') . ' = ' . $db->q('akeebabackup'))
			->where($db->qn('folder') . ' = ' . $db->q('quickicon'));
$db->setQuery($query);
$db->execute();

// Load FOF if not already loaded
if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
{
	return;
}

\FOF30\Utils\CacheCleaner::clearPluginsCache();

// Timezone fix; avoids errors printed out by PHP 5.3.3+ (thanks Yannick!)
if (function_exists('date_default_timezone_get') && function_exists('date_default_timezone_set'))
{
	if (function_exists('error_reporting'))
	{
		$oldLevel = error_reporting(0);
	}
	$serverTimezone = @date_default_timezone_get();
	if (empty($serverTimezone) || !is_string($serverTimezone))
	{
		$serverTimezone = 'UTC';
	}
	if (function_exists('error_reporting'))
	{
		error_reporting($oldLevel);
	}
	@date_default_timezone_set($serverTimezone);
}
/*
 * Hopefully, if we are still here, the site is running on at least PHP5. This means that
 * including the Akeeba Backup factory class will not throw a White Screen of Death, locking
 * the administrator out of the back-end.
 */

// Make sure Akeeba Backup is installed, or quit
$akeeba_installed = @file_exists(JPATH_ADMINISTRATOR . '/components/com_akeeba/Engine/Factory.php');

if ( !$akeeba_installed)
{
	return;
}

// Make sure Akeeba Backup is enabled
JLoader::import('joomla.application.component.helper');

if ( !JComponentHelper::isEnabled('com_akeeba', true))
{
	//JError::raiseError('E_JPNOTENABLED', JText('MOD_AKADMIN_AKEEBA_NOT_ENABLED'));
	return;
}

// Joomla! 1.6 or later - check ACLs (and not display when the site is bricked,
// hopefully resulting in no stupid emails from users who think that somehow
// Akeeba Backup crashed their site). It also not displays the button to people
// who are not authorised to take backups - which makes perfect sense!
$continueLoadingIcon = true;
$user                = JFactory::getUser();

if ( !$user->authorise('akeeba.backup', 'com_akeeba'))
{
	$continueLoadingIcon = false;
}

// Do we really, REALLY have Akeeba Engine?
if ($continueLoadingIcon)
{
	if ( !defined('AKEEBAENGINE'))
	{
		define('AKEEBAENGINE', 1); // Required for accessing Akeeba Engine's factory class
	}
	try
	{
		@include_once JPATH_ADMINISTRATOR . '/components/com_akeeba/Engine/Factory.php';
		if ( !class_exists('\Akeeba\Engine\Factory', false))
		{
			$continueLoadingIcon = false;
		}
	}
	catch (Exception $e)
	{
		$continueLoadingIcon = false;
	}
}

// Enable self if we have to bail out
if ( !$continueLoadingIcon)
{
	$db    = JFactory::getDbo();
	$query = $db->getQuery(true)
				->update($db->qn('#__extensions'))
				->set($db->qn('enabled') . ' = ' . $db->q('1'))
				->where($db->qn('element') . ' = ' . $db->q('akeebabackup'))
				->where($db->qn('folder') . ' = ' . $db->q('quickicon'));
	$db->setQuery($query);
	$db->execute();

	\FOF30\Utils\CacheCleaner::clearPluginsCache();

	return;
}
unset($continueLoadingIcon);

/**
 * Akeeba Backup Notification plugin
 */
class plgQuickiconAkeebabackup extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param       object $subject The object to observe
	 * @param       array  $config  An array that holds the plugin configuration
	 *
	 * @since       2.5
	 */
	public function __construct(& $subject, $config)
	{
		/**
		 * I know that this piece of code cannot possibly be executed since I have already returned BEFORE declaring
		 * the class when eAccelerator is detected. However, eAccelerator is a GINORMOUS, STINKY PILE OF BULL CRAP. The
		 * stupid thing will return above BUT it will also declare the class EVEN THOUGH according to how PHP works
		 * this part of the code should be unreachable o_O Therefore I have to define this constant and exit the
		 * constructor when we have already determined that this class MUST NOT be defined. Because screw you
		 * eAccelerator, that's why.
		 */
		if (defined('AKEEBA_EACCELERATOR_IS_SO_BORKED_IT_DOES_NOT_EVEN_RETURN'))
		{
			return;
		}

		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * This method is called when the Quick Icons module is constructing its set
	 * of icons. You can return an array which defines a single icon and it will
	 * be rendered right after the stock Quick Icons.
	 *
	 * @param  $context  The calling context
	 *
	 * @return array A list of icon definition associative arrays, consisting of the
	 *                 keys link, image, text and access.
	 *
	 * @since       2.5
	 */
	public function onGetIcons($context)
	{
		$user                = JFactory::getUser();
		if ( !$user->authorise('akeeba.backup', 'com_akeeba'))
		{
			return;
		}


		if (
				$context != $this->params->get('context', 'mod_quickicon')
				|| !JFactory::getUser()->authorise('core.manage', 'com_installer')
		)
		{
			return;
		}

		$container = \FOF30\Container\Container::getInstance('com_akeeba');

		// Necessary defines for Akeeba Engine
		if ( !defined('AKEEBAENGINE'))
		{
			define('AKEEBAENGINE', 1);
			define('AKEEBAROOT',  $container->backEndPath . '/BackupEngine');
			define('ALICEROOT', $container->backEndPath . '/AliceEngine');

			// Make sure we have a profile set throughout the component's lifetime
			$session    = $container->session;
			$profile_id = $session->get('profile', null, 'akeeba');

			if (is_null($profile_id))
			{
				$session->set('profile', 1, 'akeeba');
			}

			// Load Akeeba Engine
			require_once $container->backEndPath . '/BackupEngine/Factory.php';
		}

		Platform::addPlatform('joomla3x', JPATH_ADMINISTRATOR . '/components/com_akeeba/BackupPlatform/Joomla3x');

		$url = JUri::base();
		$url = rtrim($url, '/');

		$profileId = (int)$this->params->get('profileid', 1);
		$token     = JFactory::getSession()->getToken();

		if ($profileId <= 0)
		{
			$profileId = 1;
		}

		$ret = array(
			'link'  => 'index.php?option=com_akeeba&view=Backup&autostart=1&returnurl=' . urlencode($url) . '&profileid=' . $profileId . "&$token=1",
			'image' => 'akeeba-black',
			'text'  => JText::_('PLG_QUICKICON_AKEEBABACKUP_OK'),
			'id'    => 'plg_quickicon_akeebabackup',
			'group' => 'MOD_QUICKICON_MAINTENANCE',
		);

		if (version_compare(JVERSION, '3.0', 'lt'))
		{
			$ret['image'] = $url . '/../media/com_akeeba/icons/akeeba-48.png';
		}

		if ($this->params->get('enablewarning', 0) == 0)
		{
			// Process warnings
			$warning = false;

			$aeconfig = Factory::getConfiguration();
			Platform::getInstance()->load_configuration();

			// Get latest non-SRP backup ID
			$filters  = array(
				array(
					'field'   => 'tag',
					'operand' => '<>',
					'value'   => 'restorepoint'
				)
			);
			$ordering = array(
				'by'    => 'backupstart',
				'order' => 'DESC'
			);

			/** @var \Akeeba\Backup\Admin\Model\Statistics $model */
			$model = $container->factory->model('Statistics')->tmpInstance();
			$list  = $model->getStatisticsListWithMeta(false, $filters, $ordering);

			if ( !empty($list))
			{
				$record = (object)array_shift($list);
			}
			else
			{
				$record = null;
			}

			// Process "failed backup" warnings, if specified
			if ($this->params->get('warnfailed', 0) == 0)
			{
				if ( !is_null($record))
				{
					$warning = (($record->status == 'fail') || ($record->status == 'run'));
				}
			}

			// Process "stale backup" warnings, if specified
			if (is_null($record))
			{
				$warning = true;
			}
			else
			{
				$maxperiod = $this->params->get('maxbackupperiod', 24);
				JLoader::import('joomla.utilities.date');
				$lastBackupRaw    = $record->backupstart;
				$lastBackupObject = new JDate($lastBackupRaw);
				$lastBackup       = $lastBackupObject->toUnix(false);
				$maxBackup        = time() - $maxperiod * 3600;
				if ( !$warning)
				{
					$warning = ($lastBackup < $maxBackup);
				}
			}

			if ($warning)
			{
				$ret['image'] = 'akeeba-red';
				$ret['text']  = JText::_('PLG_QUICKICON_AKEEBABACKUP_BACKUPREQUIRED');

				if (version_compare(JVERSION, '3.0', 'lt'))
				{
					$ret['image'] = $url . '/../media/com_akeeba/icons/akeeba-warning-48.png';
				}
				else
				{
					$ret['text'] = '<span class="badge badge-important">' . $ret['text'] . '</span>';
				}
			}
		}

		if (version_compare(JVERSION, '3.0', 'gt'))
		{
			$inlineCSS = <<< CSS
.icon-akeeba-black {
	background-image: url("../media/com_akeeba/icons/akeebabackup-16-black.png");
	width: 16px;
	height: 16px;
}

.icon-akeeba-red {
	background-image: url("../media/com_akeeba/icons/akeebabackup-16-red.png");
	width: 16px;
	height: 16px;
}

.quick-icons .nav-list [class^="icon-akeeba-"], .quick-icons .nav-list [class*=" icon-akeeba-"] {
	margin-right: 7px;
}

.quick-icons .nav-list [class^="icon-akeeba-red"], .quick-icons .nav-list [class*=" icon-akeeba-red"] {
	margin-bottom: -4px;
}
CSS;

			JFactory::getApplication()->getDocument()->addStyleDeclaration($inlineCSS);
		}

		// Re-enable self
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
					->update($db->qn('#__extensions'))
					->set($db->qn('enabled') . ' = ' . $db->q('1'))
					->where($db->qn('element') . ' = ' . $db->q('akeebabackup'))
					->where($db->qn('folder') . ' = ' . $db->q('quickicon'));
		$db->setQuery($query);
		$db->execute();

		\FOF30\Utils\CacheCleaner::clearPluginsCache();

		return array($ret);
	}
}
