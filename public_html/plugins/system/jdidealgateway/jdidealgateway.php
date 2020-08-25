<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;

/**
 * Plugin to handle downloads
 *
 * @package  JDiDEAL
 * @since    4.9.0
 */
class PlgSystemJdidealgateway extends CMSPlugin
{
	/**
	 * @var    string  base update url, to decide whether to process the event or not
	 *
	 * @since  1.0.0
	 */
	private $baseUrl = 'https://rolandd.com/';

	/**
	 * @var    string  Extension identifier, to retrieve its params
	 *
	 * @since  1.0.0
	 */
	private $extension = 'com_jdidealgateway';

	/**
	 * @var    string  Extension title, to retrieve its params
	 *
	 * @since  1.0.0
	 */
	private $extensionTitle = 'RO Payments';

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Adding required headers for successful extension update
	 *
	 * @param   string  &$url      URL from which package is going to be downloaded
	 * @param   array   &$headers  Headers to be sent along the download request (key => value format)
	 *
	 * @return  boolean true    Always true, regardless of success
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		// Are we trying to update our own extensions?
		if (strpos($url, $this->baseUrl) !== 0 && strpos($url, 'https://jdideal.nl') !== 0)
		{
			return true;
		}

		// Get the Download ID from component params
		$downloadId = ComponentHelper::getParams($this->extension)->get('downloadid', '');

		// Set Download ID first
		if (empty($downloadId))
		{
			Factory::getApplication()->enqueueMessage(
				Text::sprintf('PLG_SYSTEM_JDIDEALGATEWAY_DOWNLOAD_ID_REQUIRED',
					$this->extension,
					$this->extensionTitle
				),
				'error'
			);

			return true;
		}
		// Append the Download ID
		else
		{
			$separator = strpos($url, '?') !== false ? '&' : '?';
			$url       .= $separator . 'key=' . $downloadId;
		}

		return true;
	}
}
