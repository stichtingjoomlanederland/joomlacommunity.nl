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

class KomentoVersionHelper
{
	public static function getJoomlaVersion()
	{
		$jVerArr	= explode('.', JVERSION);
		$jVersion	= $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}

	public static function getVersion()
	{
		$parser	= self::getParser();

		if( !$parser )
			return false;

		$data	= $parser->xpath( 'komento/version' );

		if( Komento::isJoomla30() )
		{
			$data = $data[0];
		}

		return $data;
	}

	public static function getLocalVersion( $buildOnly = false )
	{
		$parser	= self::getLocalParser();

		if( !$parser )
			return false;

		$data	= $parser->xpath( 'version' );

		if( Komento::isJoomla30() )
		{
			$data = $data[0][0];
		}

		if( $buildOnly )
		{
			$data	= explode( '.' , $data );
			return $data[2];
		}
		return $data;
	}

	private static function getParser()
	{
		$data		= new stdClass();

		// Get the xml file
		$site		= KOMENTO_UPDATES_SERVER;
		$xml		= 'stackideas.xml';
		$contents	= '';

		$handle		= @fsockopen( $site , 80, $errno, $errstr, 30);

		if( !$handle )
			return false;

		$out = "GET /$xml HTTP/1.1\r\n";
		$out .= "Host: $site\r\n";
		$out .= "Connection: Close\r\n\r\n";

		fwrite($handle, $out);

		$body		= false;

		while( !feof( $handle ) )
		{
			$return	= fgets( $handle , 1024 );

			if( $body )
			{
				$contents	.= $return;
			}

			if( $return == "\r\n" )
			{
				$body	= true;
			}
		}
		fclose($handle);

		$parser = Komento::getXML( $contents );

		return $parser;
	}

	private static function getLocalParser()
	{
		$data		= new stdClass();

		$contents	= JFile::read( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'komento.xml' );

		$parser = Komento::getXML( $contents );

		return $parser;
	}
}
