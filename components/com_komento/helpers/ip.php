<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

class KomentoIpHelper
{
	static public $rules = array();

	static function getIP()
	{
		return JRequest::getVar('REMOTE_ADDR', '', 'SERVER');
	}

	public function getRule()
	{
		$component	= Komento::getCurrentComponent();
		$ip			= KomentoIpHelper::getIP();

		$rules		= Komento::getModel( 'ipfilter' )->getRule( $component, $ip );
	}
}
