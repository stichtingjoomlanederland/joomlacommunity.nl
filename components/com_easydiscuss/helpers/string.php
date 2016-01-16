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

/*
 * String utilities class.
 *
 */
require_once JPATH_ROOT . '/components/com_easydiscuss/constants.php';
require_once DISCUSS_HELPERS . '/filter.php';

class DiscussStringHelper
{
	public function getNoun( $var , $count , $includeCount = false )
	{
		static $zeroIsPlural;

		if (!isset($zeroIsPlural))
		{
			$config	= DiscussHelper::getConfig();
			$zeroIsPlural = $config->get( 'layout_zero_as_plural' );
		}

		$count	= (int) $count;

		$var	= ($count===1 || $count===-1 || ($count===0 && !$zeroIsPlural)) ? $var . '_SINGULAR' : $var . '_PLURAL';

		return ( $includeCount ) ? JText::sprintf( $var , $count ) : JText::_( $var );
	}

	/*
	 * Convert string from ejax post into assoc-array
	 * param - string
	 * return - assc-array
	 */
	public static function ajaxPostToArray($params)
	{
		$post		= array();

		foreach($params as $item)
		{
			$pair   = explode('=', $item);

			if(! empty($pair[0]))
			{
				$val	= DiscussStringHelper::ajaxUrlDecode($pair[1]);

				if(array_key_exists($pair[0], $post))
				{
					$tmpContainer	= $post[$pair[0]];
					if(is_array($tmpContainer))
					{
						$tmpContainer[] = $val;

						//now we ressign into this array index
						$post[$pair[0]] = $tmpContainer;
					}
					else
					{
						//so this is not yet an array? make it an array then.
						$tmpArr		= array();
						$tmpArr[]	= $tmpContainer;

						//currently value:
						$tmpArr[]	= $val;

						//now we ressign into this array index
						$post[$pair[0]] = $tmpArr;
					}
				}
				else
				{
					$post[$pair[0]] = $val;
				}

			}
		}
		return $post;
	}

	/*
	 * decode the encoded url string
	 * param - string
	 * return - string
	 */
	public static function ajaxUrlDecode($string)
	{
		$rawStr	= urldecode( rawurldecode( $string ) );
		if( function_exists( 'html_entity_decode' ) )
		{
			return html_entity_decode($rawStr);
		}
		else
		{
			return DiscussStringHelper::unhtmlentities($rawStr);
		}
	}

	/**
	 * A pior php 4.3.0 version of
	 * html_entity_decode
	 */
	public static function unhtmlentities($string)
	{
		$string = str_replace( '&nbsp;', '', $string);

		// replace numeric entities
		$string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
		$string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
		// replace literal entities
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);
		return strtr($string, $trans_tbl);
	}

	public static function url2link( $string )
	{
		$newString	= $string;
		$patterns	= array("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i",
							"/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i");

		$replace	= array("<a target=\"_blank\" href=\"$1\" rel=\"nofollow\">$1</a>",
							"<a target=\"_blank\" href=\"http://$2\" rel=\"nofollow\">$2</a>");

		$newString	= preg_replace($patterns, $replace, $newString);

		return $newString;
	}

	public static function escape( $var )
	{
		return htmlspecialchars( $var, ENT_COMPAT, 'UTF-8' );
	}

	public function detectNames( $text )
	{
		$pattern	= '/@[A-Z0-9][A-Z0-9_-]+/i';

		preg_match_all( $pattern , $text , $matches );

		if( !isset( $matches[ 0 ] ) || empty( $matches[ 0 ] ) )
		{
			return false;
		}

		return $matches[0];
	}

	public function nameToLink( $text )
	{
		$db 		= DiscussHelper::getDBO();
		$config 	= DiscussHelper::getConfig();
		$usernames 	= self::detectNames( $text );

		if( !$usernames )
		{
			return $text;
		}

		foreach( $usernames as $username )
		{
			$username 	= str_ireplace( '@' , '' , $username );
			//@task: Test if user exists in the system.
			$query	= 'SELECT a.`id` FROM ' . $db->nameQuote( '#__users' ) . ' AS a '
					. 'LEFT JOIN ' . $db->nameQuote( '#__discuss_users' ) . ' AS b '
					. 'ON a.`id`=b.`id` '
					. 'WHERE a.' . $db->nameQuote( 'username' ) . ' = ' . $db->Quote( $username );
			$db->setQuery( $query );
			$result = $db->loadResult();

			if( !$result )
			{
				continue;
			}

			$profile	= DiscussHelper::getTable( 'Profile' );
			$profile->load( $result );

			$link		= $profile->getLink();
			$name		= $profile->getName();
			$text	= str_ireplace( '@' . $username , '<a href="' . $link . '">' . $name . '</a>' , $text );
		}

		return $text;
	}

	public function bytesToSize($bytes, $precision = 2)
	{
		$kilobyte = 1024;
		$megabyte = $kilobyte * 1024;
		$gigabyte = $megabyte * 1024;
		$terabyte = $gigabyte * 1024;

		if (($bytes >= 0) && ($bytes < $kilobyte)) {
			return $bytes . ' B';

		} elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
			return round($bytes / $kilobyte, $precision) . ' KB';

		} elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
			return round($bytes / $megabyte, $precision) . ' MB';

		} elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
			return round($bytes / $gigabyte, $precision) . ' GB';

		} elseif ($bytes >= $terabyte) {
			return round($bytes / $terabyte, $precision) . ' TB';
		} else {
			return $bytes . ' B';
		}
	}

}
