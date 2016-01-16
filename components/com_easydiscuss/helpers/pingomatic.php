<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

class DiscussPingomaticHelper
{
	public function ping( $title , $url )
	{
		require_once DISCUSS_CLASSES . '/pingomatic.php';

		$title		= htmlspecialchars( $title );
		$pingomatic	= new DiscussPingomatic();
		$response	= $pingomatic->ping( $title , $url );

		if( $response[ 'status' ] == 'ko' )
		{
			return false;
		}
		return true;
	}
}
