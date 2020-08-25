<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2020 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

namespace Pwtimage;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\Registry\Registry;

/**
 * PWT Image.
 *
 * @package   Pwtimage
 * @since     1.3.2
 */
class Pwtimage
{
	/**
	 * A list to map specific paths to other paths. This is to ensure discrepancies between xml files are ignored.
	 *
	 * @var   array
	 * @since 1.3.0
	 */
	static private $synonyms = [
		'com_content.images.image-intro.image_intro'   => 'com_content.images.image_intro',
		'com_content.images.image-full.image_fulltext' => 'com_content.images.image_fulltext'
	];

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
	 * Allowed image extensions
	 *
	 * @var    string
	 * @since  1.3.2
	 */
	protected $allowedExtensions = '';

	/**
	 * Construct the helper.
	 *
	 * @param   string $origin The breadcrumb path to load the profile for
	 *
	 * @since   1.1.0
	 */
	public function __construct($origin = 'all')
	{
		$this->allowedExtensions = ComponentHelper::getParams('com_media')->get('image_extensions', 'jpg,jpeg,png,gif,bmp');
		$this->loadProfile($this->synonyms[$origin] ?? $origin);
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

		if (strpos($origin, ':') !== false)
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

		if ($useProfile || (count($settings) > 0 && $origin))
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
			$this->profileId = $useProfile ? $profileId : (isset($profileSettings->id) ? $profileSettings->id : 0);
			$this->settings  = (new Registry($profileSettings->settings ?? []));
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
	 * Return the profile settings.
	 *
	 * @return  array  The profile settings.
	 *
	 * @since   1.1.0
	 */
	public function getSettings(): array
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
	public function getProfileId(): int
	{
		return $this->profileId;
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
		return self::$synonyms[$origin] ?? $origin;
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
	public function fileUploadMaxSize(): int
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
	private function parseSize($size): float
	{
		// Remove the non-unit characters from the size.
		$unit = preg_replace('/[^bkmgtpezy]/i', '', $size);

		// Remove the non-numeric characters from the size.
		$size = preg_replace('/[^0-9\.]/', '', $size);

		if ($unit)
		{
			// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
			return round($size * (1024 ** stripos('bkmgtpezy', $unit[0])));
		}

		return round($size);
	}
}
