<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussThemesHelperString
{
	/**
	 * Renders a bubble with bg
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function bubble($background)
	{
		$theme = ED::themes();
		$theme->set('background', $background);
		$output = $theme->output('site/helpers/string/bubble');

		return $output;
	}

	/**
	 * Allows escaping of a string value on html
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function escape($string)
	{
		return ED::string()->escape($string);
	}
}
