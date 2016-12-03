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

jimport('joomla.utilities.date');

class EasyDiscussDateObj extends EasyDiscuss
{
	private $date 		= null;




	/**
	 * Legacy
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function toFormat($format = 'l, d F Y')
	{
		return $this->format($format);
	}

	public function setOffset( $offset ) {
		if( DiscussHelper::getJoomlaVersion() >= '3.0' )
		{
			$tz = new DateTimeZone($offset);
			return $this->date->setTimezone($tz);
		}

		return $this->date->setOffset( $offset );
	}



}
