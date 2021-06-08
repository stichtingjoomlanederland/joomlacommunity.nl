<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/**
 * RO Payments helper.
 *
 * @package     JDiDEAL
 * @subpackage  Core
 * @since       2.8.0
 */
class JdidealGatewayHelper
{
	/**
	 * Render submenu.
	 *
	 * @param   string  $vName  The name of the current view.
	 *
	 * @return  void
	 *
	 * @since   2.8.0
	 * @throws  Exception
	 */
	public function addSubmenu(string $vName): void
	{
		JHtmlSidebar::addEntry(
			Text::_('COM_ROPAYMENTS_DASHBOARD'),
			'index.php?option=com_jdidealgateway&view=jdidealgateway',
			$vName === 'jdidealgateway'
		);
		JHtmlSidebar::addEntry(
			Text::_('COM_ROPAYMENTS_LOGS'),
			'index.php?option=com_jdidealgateway&view=logs', $vName === 'logs'
		);
		JHtmlSidebar::addEntry(
			Text::_('COM_ROPAYMENTS_PROFILES'),
			'index.php?option=com_jdidealgateway&view=profiles',
			$vName === 'profiles'
		);
		JHtmlSidebar::addEntry(
			Text::_('COM_ROPAYMENTS_STATUSES'),
			'index.php?option=com_jdidealgateway&view=statuses',
			$vName === 'statuses'
		);
		JHtmlSidebar::addEntry(
			Text::_('COM_ROPAYMENTS_MESSAGES'),
			'index.php?option=com_jdidealgateway&view=messages',
			$vName === 'messages'
		);
		JHtmlSidebar::addEntry(
			Text::_('COM_ROPAYMENTS_EMAILS'),
			'index.php?option=com_jdidealgateway&view=emails',
			$vName === 'emails'
		);
		JHtmlSidebar::addEntry(
			Text::_('COM_ROPAYMENTS_PAYMENTS'),
			'index.php?option=com_jdidealgateway&view=pays', $vName === 'pays'
		);
		JHtmlSidebar::addEntry(
			'<hr />', false
		);
		JHtmlSidebar::addEntry(
			Text::_('COM_ROPAYMENTS_CUSTOMERS'),
			'index.php?option=com_jdidealgateway&view=customers',
			$vName === 'customers'
		);
		JHtmlSidebar::addEntry(
			Text::_('COM_ROPAYMENTS_SUBSCRIPTIONS'),
			'index.php?option=com_jdidealgateway&view=subscriptions',
			$vName === 'subscriptions'
		);
		JHtmlSidebar::addEntry(
			'<hr />', '', false
		);
		JHtmlSidebar::addEntry(
			date(
				Text::_('DATE_FORMAT_LC2'),
				(new DateTime('now', new DateTimeZone("UTC")))->getTimestamp()
			), '', false
		);
	}
}
