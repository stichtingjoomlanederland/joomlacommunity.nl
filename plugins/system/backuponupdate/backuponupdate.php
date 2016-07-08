<?php
/**
 * @package    AkeebaBackup
 * @subpackage backuponupdate
 * @copyright  Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license    GNU General Public License version 3, or later
 *
 * @since      3.3
 */
defined('_JEXEC') or die();

if (!version_compare(PHP_VERSION, '5.4.0', '>='))
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
if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_akeeba'))
{
	return;
}

// Load FOF
if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
{
	return;
}

// If this is not the Professional release, bail out. So far I have only
// received complaints about this feature from users of the Core release
// who never bothered to read the documentation. FINE! If you are bitching
// about it, you don't get this feature (unless you are a developer who can
// come here and edit the code). Fair enough.
JLoader::import('joomla.filesystem.file');
$db = JFactory::getDbo();

// Is Akeeba Backup enabled?
$query = $db->getQuery(true)
            ->select($db->qn('enabled'))
            ->from($db->qn('#__extensions'))
            ->where($db->qn('element') . ' = ' . $db->q('com_akeeba'))
            ->where($db->qn('type') . ' = ' . $db->q('component'));
$db->setQuery($query);
$enabled = $db->loadResult();

if (!$enabled)
{
	return;
}

// Is it the Pro release?
@include_once(JPATH_ADMINISTRATOR . '/components/com_akeeba/version.php');

if (!defined('AKEEBA_PRO'))
{
	return;
}

if (!AKEEBA_PRO)
{
	return;
}

JLoader::import('joomla.application.plugin');

class plgSystemBackuponupdate extends JPlugin
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
	}

	public function onAfterInitialise()
	{
		// Make sure this is the back-end
		$app = JFactory::getApplication();

		if (!in_array($app->getName(), array('administrator', 'admin')))
		{
			return;
		}

		// Get the input variables
		$ji        = new JInput();
		$component = $ji->getCmd('option', '');
		$task      = $ji->getCmd('task', '');
		$backedup  = $ji->getInt('is_backed_up', 0);

		// Perform a redirection on Joomla! Update download or install task, unless we have already backed up the site
		if (($component == 'com_joomlaupdate') && ($task == 'update.install') && !$backedup)
		{
			// Get the backup profile ID
			$profileId = (int) $this->params->get('profileid', 1);

			if ($profileId <= 0)
			{
				$profileId = 1;
			}

			// Get the return URL
			$return_url = JUri::base() . 'index.php?option=com_joomlaupdate&task=update.install&is_backed_up=1';

			// Get the redirect URL
			$token        = JFactory::getSession()->getToken();
			$redirect_url = JUri::base() . 'index.php?option=com_akeeba&view=Backup&autostart=1&returnurl=' . urlencode($return_url) . '&profileid=' . $profileId . "&$token=1";

			// Perform the redirection
			$app = JFactory::getApplication();
			$app->redirect($redirect_url);
		}
	}
}
