<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.utilities.date');

class KomentoDate
{
	private $date 		= null;

	public function __construct( $current = '', $tzoffset = null )
	{
		$this->date 	= JFactory::getDate( $current, $tzoffset );
	}

	public function toMySQL()
	{
		if( Komento::joomlaVersion() >= '3.0' )
		{
			return $this->date->toSql();
		}

		return $this->date->toMySQL();
	}

	public function toSql()
	{
		return $this->toMySQL();
	}

	public function toFormat( $format='%Y-%m-%d %H:%M:%S' )
	{
		if( Komento::joomlaVersion() >= '3.0' )
		{
			if( JString::stristr( $format, '%' ) !== false )
			{
				Komento::import( 'helper', 'date' );
				$format = KomentoDateHelper::strftimeToDate( $format );
			}

			return $this->date->format( $format, true );
		}
		else
		{
			// There is no way to have cross version working, except for detecting % in the format
			if( JString::stristr( $format , '%' ) === false )
			{
				if( Komento::isJoomla15() )
				{
					// forced fallback for Joomla 15 if format doesn't have %
					$format = '%c';
				}
				else
				{
					return $this->date->format( $format , true );
				}

			}

			return $this->date->toFormat( $format, true );
		}
	}

	public function __call( $method , $args )
	{
		return call_user_func_array( array( $this->date , $method ) , $args );
	}

}
