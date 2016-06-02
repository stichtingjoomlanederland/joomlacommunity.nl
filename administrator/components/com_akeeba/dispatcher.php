<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 * @since     3.5
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

class AkeebaDispatcher extends F0FDispatcher
{
	public function onBeforeDispatch()
	{
		$result = parent::onBeforeDispatch();

		if ($result)
		{
			// Load Akeeba Strapper
			include_once JPATH_ROOT . '/media/akeeba_strapper/strapper.php';
			AkeebaStrapper::$tag = AKEEBAMEDIATAG;
			AkeebaStrapper::bootstrap();
			AkeebaStrapper::jQueryUI();
			AkeebaStrapper::addJSfile('media://com_akeeba/js/gui-helpers.js');
			AkeebaStrapper::addJSfile('media://com_akeeba/js/system.js');
			AkeebaStrapper::addJSfile('media://com_akeeba/js/akeebaui.js');
			AkeebaStrapper::addJSfile('media://com_akeeba/js/piecon.min.js');
			jimport('joomla.filesystem.file');
			if (JFile::exists(F0FTemplateUtils::parsePath('media://com_akeeba/js/akeebauipro.js', true)))
			{
				AkeebaStrapper::addJSfile('media://com_akeeba/js/akeebauipro.js');
			}
			AkeebaStrapper::addCSSfile('media://com_akeeba/theme/akeebaui.css');
		}

		return $result;
	}

	public function dispatch()
	{
		if ( !class_exists('AkeebaControllerDefault'))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_akeeba/controllers/default.php';
		}

		// Merge the language overrides
		$paths = array(JPATH_ROOT, JPATH_ADMINISTRATOR);
		$jlang = JFactory::getLanguage();
		$jlang->load($this->component, $paths[0], 'en-GB', true);
		$jlang->load($this->component, $paths[0], null, true);
		$jlang->load($this->component, $paths[1], 'en-GB', true);
		$jlang->load($this->component, $paths[1], null, true);

		$jlang->load($this->component . '.override', $paths[0], 'en-GB', true);
		$jlang->load($this->component . '.override', $paths[0], null, true);
		$jlang->load($this->component . '.override', $paths[1], 'en-GB', true);
		$jlang->load($this->component . '.override', $paths[1], null, true);

		F0FInflector::addWord('alice', 'alices');

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

		// Necessary defines for Akeeba Engine
		if ( !defined('AKEEBAENGINE'))
		{
			define('AKEEBAENGINE', 1); // Required for accessing Akeeba Engine's factory class
			define('AKEEBAROOT', dirname(__FILE__) . '/akeeba');
			define('ALICEROOT', dirname(__FILE__) . '/alice');
		}

		// Setup Akeeba's ACLs, honoring laxed permissions in component's parameters, if set
		// Access check, Joomla! 1.6 style.
		$user = JFactory::getUser();
		if ( !$user->authorise('core.manage', 'com_akeeba'))
		{
			return JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		// Make sure we have a profile set throughout the component's lifetime
		$session    = JFactory::getSession();
		$profile_id = $session->get('profile', null, 'akeeba');
		if (is_null($profile_id))
		{
			// No profile is set in the session; use default profile
			$session->set('profile', 1, 'akeeba');
		}

		// Load Akeeba Engine and ALICE
		require_once JPATH_COMPONENT_ADMINISTRATOR . '/engine/Factory.php';

		if (@file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/alice/factory.php'))
		{
			require_once JPATH_COMPONENT_ADMINISTRATOR . '/alice/factory.php';
		}

		// Load the Akeeba Engine configuration
		Platform::addPlatform('joomla25', JPATH_COMPONENT_ADMINISTRATOR . '/platform/joomla25');
		$akeebaEngineConfig = Factory::getConfiguration();
		Platform::getInstance()->load_configuration();

		$jDbo = JFactory::getDbo();

		if ($jDbo->name == 'pdomysql')
		{
			// Prevents the "SQLSTATE[HY000]: General error: 2014" due to resource sharing with Akeeba Engine
			@JFactory::getDbo()->disconnect();
		}

		unset($akeebaEngineConfig);

		// Preload helpers
		require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/escape.php';

		// Load the utils helper library
		Platform::getInstance()->load_version_defines();

		// Create a versioning tag for our static files
		$staticFilesVersioningTag = md5(AKEEBA_VERSION . AKEEBA_DATE);
		define('AKEEBAMEDIATAG', $staticFilesVersioningTag);

		$this->input->set('view', $this->view);

		// Load JHtml behaviours as needed
		$this->loadJHtmlBehaviors();

		parent::dispatch();
	}

	protected function loadJHtmlBehaviors()
	{
		$format = $this->input->getCmd('format', 'html');

		if ($format != 'html')
		{
			return;
		}

		if (version_compare(JVERSION, '3.0.0', 'lt'))
		{
			JHtml::_('behavior.framework');
		}
		else
		{
			if (version_compare(JVERSION, '3.3.0', 'ge'))
			{
				JHtml::_('behavior.core');
			}
			else
			{
				JHtml::_('behavior.framework', true);
			}

			JHtml::_('jquery.framework');
		}
	}
}