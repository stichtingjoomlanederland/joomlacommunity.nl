<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2020 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

/**
 * PWT Image component helper.
 *
 * @since  1.0.0
 */
class PwtimageHelper extends ContentHelper
{
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
}
