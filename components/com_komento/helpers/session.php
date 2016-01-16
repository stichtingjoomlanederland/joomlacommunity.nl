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

/**
 * Comment utilities class.
 *
 */
class KomentoSessionHelper
{
	var $session;

	function __construct()
	{
		$this->session = JFactory::getSession();
		return $this->session;
	}

	function getLastReplyTime()
	{
		return unserialize( $this->session->get('komento_last_reply') );
	}

	function setReplyTime()
	{
		return $this->session->set('komento_last_reply', serialize( Komento::getDate()->toUnix() ) );
	}
}
