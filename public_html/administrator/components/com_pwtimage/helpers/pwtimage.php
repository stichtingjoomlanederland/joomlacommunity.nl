<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

/**
 * PWT Image component helper.
 *
 * @since  1.0.0
 */
class PwtimageHelper extends ContentHelper
{
	/**
	 * A list to map specific paths to other paths. This is to ensure discrepancies between xml files are ignored.
	 *
	 * @var   array
	 * @since 1.3.0
	 */
	static private $synonyms = array(
		'com_content.images.image-intro.image_intro'   => 'com_content.images.image_intro',
		'com_content.images.image-full.image_fulltext' => 'com_content.images.image_fulltext'
	);

	/**
	 * The profile settings
	 *
	 * @var    Registry
	 * @since  1.1.0
	 */
	private $settings;

	/**
	 * The profile ID
	 *
	 * @var    integer
	 * @since  1.1.0
	 */
	private $profileId;

	/**
	 * Construct the helper.
	 *
	 * @param   string $origin The breadcrumb path to load the profile for
	 *
	 * @since   1.1.0
	 */
	public function __construct($origin = 'all')
	{
		$this->loadProfile(isset($this->synonyms[$origin]) ? $this->synonyms[$origin] : $origin);
	}

	/**
	 * Load the profile to apply to the media fields.
	 *
	 * @param   string $origin The breadcrumb path to load the profile for
	 *
	 * @return  void
	 *
	 * @since   1.1.0
	 */
	private function loadProfile($origin = 'all')
	{
		$profileId  = false;
		$useProfile = false;

		if (strstr($origin, ':'))
		{
			list($profileId, $origin) = explode(':', $origin);

			// If there is no origin we have a profile with no extensions selected, so request profile directly
			if (empty($origin))
			{
				$useProfile = true;
			}
		}

		if (!$profileId && empty($origin))
		{
			$origin = 'all';
		}

		$db = Factory::getDbo();

		$query = $db->getQuery(true)
			->select($db->quoteName(array('profiles.settings', 'extensions.path', 'profiles.id')))
			->from($db->quoteName('#__pwtimage_profiles', 'profiles'))
			->leftJoin(
				$db->quoteName('#__pwtimage_extensions', 'extensions')
				. ' ON ' . $db->quoteName('extensions.profile_id') . ' = ' . $db->quoteName('profiles.id')
			)
			->where($db->quoteName('profiles.published') . ' = 1');

		if ($origin)
		{
			$query->where($db->quoteName('extensions.path') . ' IN (' . $db->quote($origin) . ',' . $db->quote('all') . ')');
		}

		if ($profileId)
		{
			$field = 'extensions.profile_id';

			// Check if we need to request the profile directly
			if ($useProfile)
			{
				$field = 'profiles.id';
			}

			$query
				->where($db->quoteName($field) . ' = ' . (int) $profileId)
				->group($db->quoteName($field));
		}

		$settings       = $db->setQuery($query)->loadObjectList('path');
		$this->settings = new Registry;

		if ((count($settings) > 0 && $origin) || $useProfile)
		{
			if (isset($settings[$origin]))
			{
				$settings = array($settings[$origin]);
			}
			elseif (isset($settings['all']))
			{
				$settings = array($settings['all']);
			}

			$profileSettings = array_shift($settings);
			$this->profileId = $profileSettings->id;
			$this->settings  = (new Registry($profileSettings->settings));
		}

		// Check if the user is part of the selected user group
		$user       = Factory::getUser();
		$userGroups = $this->getSetting('usergroups', array());
		$hasAccess  = array_intersect($user->groups, $userGroups);

		if (empty($hasAccess))
		{
			$this->settings = new Registry;
		}
	}

	/**
	 * Return the requested setting.
	 *
	 * @param   string $setting The setting name to get the value for
	 * @param   string $default The default value to use
	 *
	 * @return  mixed  The requested setting.
	 *
	 * @since   1.1.0
	 */
	public function getSetting($setting, $default = '')
	{
		return $this->settings->get($setting, $default);
	}

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string $vName The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			Text::_('COM_PWTIMAGE_SUBMENU_DASHBOARD'),
			'index.php?option=com_pwtimage&view=dashboard',
			$vName == 'dashboard'
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_PWTIMAGE_SUBMENU_PROFILES'),
			'index.php?option=com_pwtimage&view=profiles',
			$vName == 'profiles'
		);
	}

	/**
	 * Returns a potential mapped origin for a given origin.
	 *
	 * @param   string $origin The origin
	 *
	 * @return  string Either a synonym for the given origin or the origin itself
	 *
	 * @since   1.3.0
	 */
	public static function getSysonymForOrigin($origin)
	{
		return isset(self::$synonyms[$origin]) ? self::$synonyms[$origin] : $origin;
	}

	/**
	 * Retrieve the image folder.
	 *
	 * @param   bool   $base       Set to return only the base folder, not the subfolders.
	 * @param   string $sourcePath The source folder where to store the image.
	 * @param   string $subPath    The subfolder where to store the image.
	 *
	 * @return  string  The name of the image folder prefixed and suffixed with /.
	 *
	 * @since   1.0.0
	 */
	public function getImageFolder($base = false, $sourcePath = null, $subPath = null)
	{
		jimport('joomla.filesystem.folder');

		// Get the settings
		$sourcePath = strlen($sourcePath) > 0 ? $sourcePath : $this->settings->get('sourcePath', '/images');
		$subPath    = strlen($subPath) > 0 ? $subPath : $this->settings->get('subPath');

		// Construct the source path
		if (substr($sourcePath, 0, 1) !== '/')
		{
			$sourcePath = '/' . $sourcePath;
		}

		// Construct the sub path
		$subPath = $this->replaceVariables($subPath);

		if (substr($subPath, 0, 1) !== '/')
		{
			$subPath = '/' . $subPath;
		}

		// Construct the full path
		$imageFolder = $sourcePath . $subPath;

		// Check | try to create thumbnail folder
		$mode = intval($this->getSetting('chmod', 0755), 8);

		if (!JFolder::exists(JPATH_SITE . $imageFolder))
		{
			JFolder::create(JPATH_SITE . $imageFolder, $mode);
		}
		else
		{
			@chmod(JPATH_SITE . $imageFolder, $mode);
		}

		return $base ? $sourcePath : $imageFolder;
	}

	/**
	 * Do a placeholder replacement.
	 *
	 * @param   string $subPath The path to replace the variables in
	 *
	 * @return  string  The replaced string.
	 *
	 * @since   1.0.0
	 */
	public function replaceVariables($subPath)
	{
		$user     = Factory::getUser();
		$username = ($user->name) ? OutputFilter::stringURLSafe($user->name) : 'guest';
		$find     = array('{year}', '{month}', '{day}', '{Y}', '{m}', '{d}', '{W}', '{userid}', '{username}');
		$replace  = array(date('Y'), date('m'), date('d'), date('Y'), date('m'), date('d'), date('W'), $user->id, $username);

		return str_replace($find, $replace, $subPath);
	}

	/**
	 * Get the maximum upload size.
	 *
	 * Thanks to Drupal
	 *
	 * @return  integer  The maximum allowed upload size.
	 *
	 * @since   1.0.0
	 */
	public function fileUploadMaxSize()
	{
		static $maximumSize = -1;

		if ($maximumSize < 0)
		{
			// Start with post_max_size.
			$maximumSize = $this->parseSize(ini_get('post_max_size'));

			// If upload_max_size is less, then reduce. Except if upload_max_size is
			// zero, which indicates no limit.
			$maximumUpload = $this->parseSize(ini_get('upload_max_filesize'));

			if ($maximumUpload > 0 && $maximumUpload < $maximumSize)
			{
				$maximumSize = $maximumUpload;
			}
		}

		return $maximumSize;
	}

	/**
	 * Parse the size of an image.
	 *
	 * @param   string $size The size to parse
	 *
	 * @return  float  The rounded value.
	 *
	 * @since   1.0.0
	 */
	private function parseSize($size)
	{
		// Remove the non-unit characters from the size.
		$unit = preg_replace('/[^bkmgtpezy]/i', '', $size);

		// Remove the non-numeric characters from the size.
		$size = preg_replace('/[^0-9\.]/', '', $size);

		if ($unit)
		{
			// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
			return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
		}

		return round($size);
	}

	/**
	 * Get a token for sending a request to the frontend controller.
	 *
	 * @return  string  A string with a token name and value.
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function getToken()
	{
		$tokenName  = 'sessionId';
		$tokenValue = Factory::getSession()->getId();

		if (Factory::getApplication()->getClientId() === 0)
		{
			$tokenName  = Session::getFormToken();
			$tokenValue = 1;
		}

		return $tokenName . ':' . $tokenValue;
	}

	/**
	 * Return the profile settings.
	 *
	 * @return  array  The profile settings.
	 *
	 * @since   1.1.0
	 */
	public function getSettings()
	{
		return $this->settings->toArray();
	}

	/**
	 * Return the profile ID.
	 *
	 * @return  integer  The profile ID.
	 *
	 * @since   1.0.0
	 */
	public function getProfileId()
	{
		return $this->profileId;
	}
}
