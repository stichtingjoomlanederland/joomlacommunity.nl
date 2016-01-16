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

jimport('joomla.utilities.date');

class DiscussDateHelper
{
	/*
	 * return the jdate with the correct specified timezone offset
	 * param : date string
	 * return : JDate object
	 */
	public static function dateWithOffSet($str='')
	{
		$userTZ = DiscussDateHelper::getOffSet();
		$date	= DiscussHelper::getDate( $str );

		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			$user		= JFactory::getUser();
			$config		= DiscussHelper::getConfig();
			$jConfig	= DiscussHelper::getJConfig();

			// temporary ignore the dst in joomla 1.6

			if($user->id != 0)
			{
				$userTZ	= $user->getParam('timezone');
			}

			if(empty($userTZ))
			{
				$userTZ	= $jConfig->get('offset');
			}

			$tmp = new DateTimeZone( $userTZ );
			$date->setTimeZone( $tmp );
		}
		else
		{
			$date->setOffset( $userTZ );
		}

		return $date;
	}

	public static function getDate($str='')
	{
		return self::dateWithOffSet($str);
	}

	public static function getOffSet16($numberOnly = false)
	{
		jimport('joomla.form.formfield');

		$user		= JFactory::getUser();
		$config		= DiscussHelper::getConfig();
		$jConfig	= DiscussHelper::getJConfig();

		// temporary ignore the dst in joomla 1.6

		if($user->id != 0)
		{
			$userTZ	= $user->getParam('timezone');
		}

		if(empty($userTZ))
		{
			$userTZ	= $jConfig->get('offset');
		}

		if( $numberOnly )
		{
			$newTZ		= new DateTimeZone($userTZ);
			$dateTime	= new DateTime( "now" , $newTZ );

			$offset		= $newTZ->getOffset( $dateTime ) / 60 / 60;
			return $offset;
		}
		else
		{
			//timezone string
			return $userTZ;
		}
	}

	public static function getOffSet( $numberOnly	= false )
	{
		if(DiscussHelper::getJoomlaVersion() >= '1.6')
		{
			//return a timezone object
			return self::getOffSet16($numberOnly);
		}

		$mainframe	= JFactory::getApplication();
		$user		= JFactory::getUser();
		$config		= DiscussHelper::getConfig();

		$userTZ		= '';
		$dstOffset	= $config->get('main_dstoffset', 0);


		if($user->id != 0)
		{
			$userTZ	= $user->getParam('timezone') + $dstOffset;
		}

		//if user did not set timezone, we use joomla one.
		if(empty($userTZ))
		{
			$userTZ	= $mainframe->getCfg('offset') + $dstOffset;
		}

		return $userTZ;
	}

	public static function getLapsedTime( $time )
	{
		$now	= DiscussHelper::getDate();
		$end	= DiscussHelper::getDate( self::dateWithOffset( $time )->toMySQL() );
		$time	= $now->toUnix() - $end->toUnix();

		$tokens = array (
							31536000 	=> 'COM_EASYDISCUSS_X_YEAR',
							2592000 	=> 'COM_EASYDISCUSS_X_MONTH',
							604800 		=> 'COM_EASYDISCUSS_X_WEEK',
							86400 		=> 'COM_EASYDISCUSS_X_DAY',
							3600 		=> 'COM_EASYDISCUSS_X_HOUR',
							60 			=> 'COM_EASYDISCUSS_X_MINUTE',
							1 			=> 'COM_EASYDISCUSS_X_SECOND'
						);

		foreach( $tokens as $unit => $key )
		{
			if ($time < $unit)
			{
				continue;
			}

			$units	= floor( $time / $unit );

			$string	= $units > 1 ?  $key . 'S' : $key;
			$string	= $string . '_AGO';

			$text	= JText::sprintf(strtoupper($string), $units);
			return $text;
		}

	}

	public static function enableDateTimePicker()
	{
		$document	= JFactory::getDocument();

		// load language for datetime picker
		$html = '
		<script type="text/javascript">
		/* Date Time Picker */
		var sJan			= "'.JText::_('COM_EASYDISCUSS_JAN').'";
		var sFeb			= "'.JText::_('COM_EASYDISCUSS_FEB').'";
		var sMar			= "'.JText::_('COM_EASYDISCUSS_MAR').'";
		var sApr			= "'.JText::_('COM_EASYDISCUSS_APR').'";
		var sMay			= "'.JText::_('COM_EASYDISCUSS_MAY').'";
		var sJun			= "'.JText::_('COM_EASYDISCUSS_JUN').'";
		var sJul			= "'.JText::_('COM_EASYDISCUSS_JUL').'";
		var sAug			= "'.JText::_('COM_EASYDISCUSS_AUG').'";
		var sSep			= "'.JText::_('COM_EASYDISCUSS_SEP').'";
		var sOct			= "'.JText::_('COM_EASYDISCUSS_OCT').'";
		var sNov			= "'.JText::_('COM_EASYDISCUSS_NOV').'";
		var sDec			= "'.JText::_('COM_EASYDISCUSS_DEC').'";
		var sAm				= "'.JText::_('COM_EASYDISCUSS_AM').'";
		var sPm				= "'.JText::_('COM_EASYDISCUSS_PM').'";
		var btnOK			= "'.JText::_('COM_EASYDISCUSS_OK').'";
		var btnReset		= "'.JText::_('COM_EASYDISCUSS_RESET').'";
		var btnCancel		= "'.JText::_('COM_EASYDISCUSS_CANCEL').'";
		var sNever			= "'.JText::_('COM_EASYDISCUSS_NEVER').'";
		</script>';

		$document->addCustomTag( $html );
	}

	public static function toFormat($jdate, $format='%Y-%m-%d %H:%M:%S')
	{
		if(is_null($jdate))
		{
			$jdate  = DiscussHelper::getDate();
		}

		if( is_string( $jdate ) )
		{
			$jdate	= DiscussHelper::getDate( $jdate );
		}

		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			$format 	= self::strftimeToDate( $format );

			// There is no way to have cross version working, except for detecting % in the format
			if( JString::stristr( $format , '%') === false )
			{
				return $jdate->format( $format , true );
			}

			// Check for Windows to find and replace the %e modifier correctly
			if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
			{
				$format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
			}

			return $jdate->toFormat( $format, true );
		}
		else
		{
			// Check for Windows to find and replace the %e modifier correctly
			if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
			{
				$format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
			}
		}

		return $jdate->toFormat( $format );
	}


	public static function strftimeToDate( $format )
	{
		$strftimeMap = array(
			// day
			'%a' => 'D', // 00, Sun through Sat
			'%A' => 'l', // 01, Sunday through Saturday
			'%d' => 'd', // 02, 01 through 31
			'%e' => 'j', // 03, 1 through 31
			'%j' => 'z', // 04, 001 through 366
			'%u' => 'N', // 05, 1 for Monday through 7 for Sunday
			'%w' => 'w', // 06, 1 for Sunday through 7 for Saturday

			// week
			'%U' => 'W', // 07, Week number of the year with Sunday as the start of the week
			'%V' => 'W', // 08, ISO-8601:1988 week number of the year with Monday as the start of the week, with at least 4 weekdays as the first week
			'%W' => 'W', // 09, Week number of the year with Monday as the start of the week

			// month
			'%b' => 'M', // 10, Jan through Dec
			'%B' => 'F', // 11, January through December
			'%h' => 'M', // 12, Jan through Dec, alias of %b
			'%m' => 'm', // 13, 01 for January through 12 for December

			// year
			'%C' => '', // 14, 2 digit of the century, year divided by 100, truncated to an integer, 19 for 20th Century
			'%g' => 'y', // 15, 2 digit of the year going by ISO-8601:1988 (%V), 09 for 2009
			'%G' => 'o', // 16, 4 digit version of %g
			'%y' => 'y', // 17, 2 digit of the year
			'%Y' => 'Y', // 18, 4 digit version of %y

			// time
			'%H' => 'H', // 19, hour, 00 through 23
			'%I' => 'h', // 20, hour, 01 through 12
			'%l' => 'g', // 21, hour, 1 through 12
			'%M' => 'i', // 22, minute, 00 through 59
			'%p' => 'A', // 23, AM or PM
			'%P' => 'a', // 24, am or pm
			'%r' => 'h:i:s A', // 25, = %I:%M:%S %p, 09:34:17 PM
			'%R' => 'H:i', // 26, = %H:%M, 21:34
			'%S' => 's', // 27, second, 00 through 59
			'%T' => 'H:i:s', // 28, = %H:%M:%S, 21:34:17
			'%X' => 'H:i:s', // 29, Based on locale without date
			'%z' => 'O', // 30, Either the time zone offset from UTC or the abbreviation (depends on operating system)
			'%Z' => 'T', // 31, The time zone offset/abbreviation option NOT given by %z (depends on operating system)

			// date stamps
			'%c' => 'Y-m-d H:i:s', // 32, Date and time stamps based on locale
			'%D' => 'm/d/y', // 33, = %m/%d/%y, 02/05/09
			'%F' => 'Y-m-d', // 34, = %Y-%m-%d, 2009-02-05
			'%s' => '', // 35, Unix timestamp, same as time()
			'%x' => 'Y-m-d', // 36, Date stamps based on locale

			// misc
			'%n' => '\n', // 37, New line character \n
			'%t' => '\t', // 38, Tab character \t
			'%%' => '%'  // 39, Literal percentage character %
		);

		$dateMap = array(
			// day
			'd', // 01, 01 through 31
			'D', // 02, Mon through Sun
			'j', // 03, 1 through 31
			'l', // 04, Sunday through Saturday
			'N', // 05, 1 for Monday through 7 for Sunday
			'S', // 06, English ordinal suffix, st, nd, rd or th
			'w', // 07, 0 for Sunday through 6 for Saturday
			'z', // 08, 0 through 365

			// week
			'W', // 09, ISO-8601 week number of the year with Monday as the start of the week

			// month
			'F', // 10, January through December
			'm', // 11, 01 through 12
			'M', // 12, Jan through Dec
			'n', // 13, 1 through 12
			't', // 14, Number of days in the month, 28 through 31

			// year
			'L', // 15, 1 for leap year, 0 otherwise
			'o', // 16, 4 digit of the ISO-8601 year number. This has the same value as Y, except that it follows ISO week number (W)
			'Y', // 17, 4 digit of the year
			'y', // 18, 2 digit of the year

			// time
			'a', // 19, am or pm
			'A', // 20, AM or PM
			'B', // 21, Swatch Internet time 000 through 999
			'g', // 22, hour, 1 through 12
			'G', // 23, hour, 0 through 23
			'h', // 24, hour, 01 through 12
			'H', // 25, hour, 00 through 23
			'i', // 26, minute, 00 through 59
			's', // 27, second, 00 through 59
			'u', // 28, microsecond, date() always generate 000000

			// timezone
			'e', // 29, timezone identifier, UTC, GMT
			'I', // 30, 1 for Daylight Saving Time, 0 otherwise
			'O', // 31, +0200
			'P', // 32, +02:00
			'T', // 33, timezone abbreviation, EST, MDT
			'Z', // 34, Timezone offset in seconds, -43200 through 50400

			// full date/time
			'c', // 35, ISO-8601 date, 2004-02-12T15:19:21+00:00
			'r', // 36, RFC 2822 date, Thu, 21 Dec 2000 16:01:07 +0200
			'U'  // 37, Seconds since the Unix Epoch
		);

		foreach( $strftimeMap as $key => $value )
		{
			$format = str_replace( $key, $value, $format );
		}

		return $format;
	}
}
