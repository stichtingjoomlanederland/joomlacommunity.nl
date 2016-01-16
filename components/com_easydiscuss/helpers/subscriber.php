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

class DiscussSubscriberHelper
{
	public static function add( $userObj, $entityObj, $interval=null, $data )
	{
		if( !($userObj instanceof JUser ) ) {
			return false;
		}

		if( $entityObj instanceof DiscussPost ) {
			$type = 'post';
		} elseif( $entityObj instanceof DiscussCategory ) {
			$type = 'category';
		} else {
			// $type = 'site'
			return false;
		}

		$email = ( isset($data['poster_email']) ) ? $data['poster_email'] : '';
		$name = ( isset($data['poster_name']) ) ? $data['poster_name'] : '';

		$subscribe = DiscussHelper::getTable( 'Subscribe' );

		if( empty($userObj->id) ) {
			$subscribe->userid		= 0;
			$subscribe->member		= 0;
			$subscribe->email		= $email;
			$subscribe->fullname	= $name;
		} else {
			$subscribe->userid		= $userObj->id;
			$subscribe->member		= 1;
			$subscribe->email		= $userObj->email;
			$subscribe->fullname	= $userObj->name;
		}

		$subscribe->type		= $type;
		$subscribe->cid			= $entityObj->id;
		$subscribe->interval	= $interval;

		return $subscribe->store();
	}
}


