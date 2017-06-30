<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussThemesHelperPanel
{
	/**
	 * Renders a small info in the panel's body
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function info($text)
	{
		$text = JText::_($text);

		$theme = ED::themes();
		$theme->set('text', $text);
		
		return $theme->output('admin/html/panel.info');
	}

	/**
	 * Renders the panel's head html contents
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function heading($header, $desc = '')
	{
		if (!$desc) {
			$desc = $header . '_DESC';
			$desc = JText::_($desc);
		}

		$header = JText::_($header);
		
		$theme = ED::themes();
		$theme->set('header', $header);
		$theme->set('desc', $desc);

		return $theme->output('admin/html/panel.head');
	}

	/**
	 * @deprecated . Use panel.heading instead
	 *
	 * @since	4.0.14
	 * @access	public
	 */
	public static function head($header, $desc = '')
	{
		return self::heading($header, $desc);
	}

	/**
	 * Renders the popover html contents
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function popover($header, $desc = '')
	{
		if (!$desc) {
			$desc = $header . '_DESC';
			$desc = JText::_($desc);
		}

		$header = JText::_($header);

		$theme = ED::themes();
		$theme->set('header', $header);
		$theme->set('desc', $desc);

		return $theme->output('admin/html/panel.popover');
	}
}
