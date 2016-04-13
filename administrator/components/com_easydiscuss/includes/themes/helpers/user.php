<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussThemesHelperUser
{
	/**
	 * Generates the user's role html tag
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function role(DiscussProfile $user)
	{
		$theme = ED::themes();
		$theme->set('user', $user);

		$output = $theme->output('site/html/user.role');

		return $output;
	}

	// /**
	//  * Generates a user avatar html tag
	//  *
	//  * @since	4.0
	//  * @access	public
	//  * @param	string
	//  * @return
	//  */
	// public static function avatar(DiscussProfile $user, $option = array())
	// {
	// 	$rank = isset($options['rank']) ? $options['rank'] : false;
	// 	$role = isset($options['role']) ? $options['role'] : false;
	// 	$status = isset($options['status']) ? $options['status'] : false;
	// 	$type = isset($options['type']) ? $options['type'] : false;

	// 	$theme = ED::themes();
	// 	$theme->set('user', $user);
	// 	$theme->set('rank', $rank);
	// 	$theme->set('role', $role);
	// 	$theme->set('status', $status);
	// 	$theme->set('type', $type);

	// 	$output = $theme->output('site/html/user.avatar');

	// 	return $output;
	// }

	/**
	 * Generates a user avatar html tag
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function avatar(DiscussProfile $user, $options = array())
	{
		$rank = isset($options['rank']) ? $options['rank'] : false;
		$role = isset($options['role']) ? $options['role'] : false;
		$status = isset($options['status']) ? $options['status'] : false;
		$size = isset($options['size']) ? $options['size'] : 'sm';

		// default to true
		$popbox = isset($options['popbox']) ? $options['popbox'] : true;


		$theme = ED::themes();
		$theme->set('user', $user);
		$theme->set('rank', $rank);
		$theme->set('role', $role);
		$theme->set('status', $status);
		$theme->set('size', $size);
		$theme->set('popbox', $popbox);

		$output = $theme->output('site/html/user.avatar');

		return $output;
	}
}
