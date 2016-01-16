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

/*
 * String utilities class.
 *
 */

class KomentoStringHelper
{

	public static function getLangCode()
	{
		$lang 		= JFactory::getLanguage();
		$locale 	= $lang->getLocale();
		$langCode    = null;

		if(empty($locale))
		{
			$langCode    = 'en-GB';
		}
		else
		{
			$langTag    = $locale[0];
			$langData    = explode('.', $langTag);
			$langCode   = JString::str_ireplace('_', '-', $langData[0]);
		}
		return $langCode;
	}

	public static function getNoun( $var , $count , $includeCount = false )
	{
		static $zeroIsPlural;

		if (!isset($zeroIsPlural))
		{
			$config			= Komento::getConfig();
			$zeroIsPlural	= $config->get( 'layout_zero_as_plural' );
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
	public static function ejaxPostToArray($params)
	{
		$post		= array();

		foreach($params as $item)
		{
			$pair   = explode('=', $item);

			if( isset( $pair[ 0 ] ) && isset( $pair[ 1 ] ) )
			{
				$key	= $pair[0];
				$value	= KomentoStringHelper::ejaxUrlDecode( $pair[ 1 ] );

				if( JString::stristr( $key , '[]' ) !== false )
				{
					$key			= JString::str_ireplace( '[]' , '' , $key );
					$post[ $key ][]	= $value;
				}
				else
				{
					$post[ $key ] = $value;
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
	public static function ejaxUrlDecode($string)
	{
		$rawStr		= urldecode( rawurldecode( $string ) );

		if( function_exists( 'html_entity_decode' ) )
		{
			return html_entity_decode($rawStr);
		}
		else
		{
			return KomentoStringHelper::unhtmlentities($rawStr);
		}
	}

	/**
	 * A pior php 4.3.0 version of
	 * html_entity_decode
	 */
	public static function unhtmlentities($string)
	{
		// replace numeric entities
		$string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
		$string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
		// replace literal entities
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);
		return strtr($string, $trans_tbl);
	}

	public static function linkTweets( $source )
	{
		// Link hashes
		$pattern	= '/\#(\w*)/i';
		$replace	= '<a target="_blank" href="http://twitter.com/#!/search?q=$1" rel="nofollow">$0</a>';
		$source		= preg_replace( $pattern , $replace , $source );

		// Link authors
		$pattern	= '/\@(\w*)/i';
		$replace	= '<a target="_blank" href="http://twitter.com/$1" rel="nofollow">$0</a>';
		$source		= preg_replace( $pattern , $replace , $source );

		return  $source;
	}

	public static function url2link($string)
	{
		$newString  = $string;

		preg_match('/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms', $newString, $matches);

		$patterns   = array('/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms',
							"/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i",
							"/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i");

		$replace    = array('[bbcode-url]',
							"<a target=\"_blank\" href=\"$1\" rel=\"nofollow\">$1</a>",
							"<a target=\"_blank\" href=\"http://$2\" rel=\"nofollow\">$2</a>");

		$newString	= preg_replace($patterns, $replace, $newString);

		//now convert back again.
		if(count($matches) > 0)
		{
			$patterns   = array('/\[bbcode\-url\]/ms');
			$replace    = array($matches[0]);
			$newString	= preg_replace($patterns, $replace, $newString);
		}

		return $newString;
	}

	public static function htmlAnchorLink( $URL, $string )
	{
		if ($string == '') return '';

		$pos = JString::strpos($URL, 'http://');
		if( $pos === false)
		{
			$URL    = 'http://' . $URL;
		}

		$pattern 	= "/(((http[s]?:\/\/)|(www\.))(([a-z][-a-z0-9]+\.)?[a-z][-a-z0-9]+\.[a-z]+(\.[a-z]{2,2})?)\/?[a-z0-9._\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";
		$newString 	= preg_replace($pattern, '<a href="$1" target="_blank" rel="nofollow">' . $string. '</a>', $URL);

		//this is not a link
		if( $newString == $URL)
		{
			return $string;
		}

		return $newString;
	}

	public static function escape( $var )
	{
		return htmlspecialchars( $var, ENT_COMPAT, 'UTF-8' );
	}

	public static function tidyHTMLContent( $content )
	{
		require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'htmlawed' . DIRECTORY_SEPARATOR . 'htmlawed.php' );

		$htmLawedConfig = array( 'cdata' => 1,
								 'clean_ms_char' => 1,
								 'comment' => 1,
								 'safe' => 1,
								 'tidy' => 1,
								 'valid_xhtml' =>1,
								 'deny_attribute' => '* -title -href -target -alt',
								 'keep_bad' => 6,
								 'anti_link_spam' => array('`.`','')
							);

		//return htmLawed( $content, $htmLawedConfig);
		return htmLawed( $content );
	}

// 	function convert2UTF8( $html )
// 	{
// 		$encoding = 'iso-8859-1';
// 		$encoding   = strtoupper( $encoding );
//
// 		$html 	= @mb_convert_encoding($html, 'UTF-8', $encoding);
// 		return $html;
// 	}

	/* reference: http://publicmind.in/blog/url-encoding/ */
	public static function encodeURL( $url )
	{
		$reserved = array(
		":" => '!%3A!ui',
		"/" => '!%2F!ui',
		"?" => '!%3F!ui',
		"#" => '!%23!ui',
		"[" => '!%5B!ui',
		"]" => '!%5D!ui',
		"@" => '!%40!ui',
		"!" => '!%21!ui',
		"$" => '!%24!ui',
		"&" => '!%26!ui',
		"'" => '!%27!ui',
		"(" => '!%28!ui',
		")" => '!%29!ui',
		"*" => '!%2A!ui',
		"+" => '!%2B!ui',
		"," => '!%2C!ui',
		";" => '!%3B!ui',
		"=" => '!%3D!ui',
		"%" => '!%25!ui',
		);

		$url = str_replace(array('%09','%0A','%0B','%0D'),'',$url); // removes nasty whitespace
		$url = rawurlencode($url);
		$url = preg_replace(array_values($reserved), array_keys($reserved), $url);
		return $url;
	}

	public static function rel2abs($rel, $base)
	{
		/* return if already absolute URL */
		if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;

		/* queries and anchors */
		if (@$rel[0]=='#' || @$rel[0]=='?') return $base.$rel;

		/* parse base URL and convert to local variables:
		   $scheme, $host, $path */
		extract(parse_url($base));

		/* remove non-directory element from path */
		$path = preg_replace('#/[^/]*$#', '', $path);

		/* destroy path if relative url points to root */
		if ( @$rel[0] == '/') $path = '';

		/* dirty absolute URL */
		$abs = "$host$path/$rel";
		/* replace '//' or '/./' or '/foo/../' with '/' */
		$re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
		for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

		/* absolute URL is ready! */
		return $scheme.'://'.$abs;
	}

	/**
	 * @author   "Sebastián Grignoli" <grignoli@framework2.com.ar>
	 * @package  forceUTF8
	 * @version  1.1
	 * @link     http://www.framework2.com.ar/dzone/forceUTF8-es/
	 * @example  http://www.framework2.com.ar/dzone/forceUTF8-es/
	  */

	public static function forceUTF8($text){
	/**
	 * Function forceUTF8
	 *
	 * This function leaves UTF8 characters alone, while converting almost all non-UTF8 to UTF8.
	 *
	 * It may fail to convert characters to unicode if they fall into one of these scenarios:
	 *
	 * 1) when any of these characters:   ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞß
	 *    are followed by any of these:  ("group B")
	 *                                    ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶•¸¹º»¼½¾¿
	 * For example:   %ABREPRESENT%C9%BB. «REPRESENTÉ»
	 * The "«" (%AB) character will be converted, but the "É" followed by "»" (%C9%BB)
	 * is also a valid unicode character, and will be left unchanged.
	 *
	 * 2) when any of these: àáâãäåæçèéêëìíîï  are followed by TWO chars from group B,
	 * 3) when any of these: ðñòó  are followed by THREE chars from group B.
	 *
	 * @name forceUTF8
	 * @param string $text  Any string.
	 * @return string  The same string, UTF8 encoded
	 *
	 */

		if(is_array($text))
		{
		  foreach($text as $k => $v)
		  {
			$text[$k] = KomentoStringHelper::forceUTF8($v);
		  }
		  return $text;
		}

		$max = strlen($text);
		$buf = "";
		for($i = 0; $i < $max; $i++){
			$c1 = $text{$i};
			if($c1>="\xc0"){ //Should be converted to UTF8, if it's not UTF8 already
			  $c2 = $i+1 >= $max? "\x00" : $text{$i+1};
			  $c3 = $i+2 >= $max? "\x00" : $text{$i+2};
			  $c4 = $i+3 >= $max? "\x00" : $text{$i+3};
				if($c1 >= "\xc0" & $c1 <= "\xdf"){ //looks like 2 bytes UTF8
					if($c2 >= "\x80" && $c2 <= "\xbf"){ //yeah, almost sure it's UTF8 already
						$buf .= $c1 . $c2;
						$i++;
					} else { //not valid UTF8.  Convert it.
						$cc1 = (chr(ord($c1) / 64) | "\xc0");
						$cc2 = ($c1 & "\x3f") | "\x80";
						$buf .= $cc1 . $cc2;
					}
				} elseif($c1 >= "\xe0" & $c1 <= "\xef"){ //looks like 3 bytes UTF8
					if($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf"){ //yeah, almost sure it's UTF8 already
						$buf .= $c1 . $c2 . $c3;
						$i = $i + 2;
					} else { //not valid UTF8.  Convert it.
						$cc1 = (chr(ord($c1) / 64) | "\xc0");
						$cc2 = ($c1 & "\x3f") | "\x80";
						$buf .= $cc1 . $cc2;
					}
				} elseif($c1 >= "\xf0" & $c1 <= "\xf7"){ //looks like 4 bytes UTF8
					if($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf" && $c4 >= "\x80" && $c4 <= "\xbf"){ //yeah, almost sure it's UTF8 already
						$buf .= $c1 . $c2 . $c3;
						$i = $i + 2;
					} else { //not valid UTF8.  Convert it.
						$cc1 = (chr(ord($c1) / 64) | "\xc0");
						$cc2 = ($c1 & "\x3f") | "\x80";
						$buf .= $cc1 . $cc2;
					}
				} else { //doesn't look like UTF8, but should be converted
						$cc1 = (chr(ord($c1) / 64) | "\xc0");
						$cc2 = (($c1 & "\x3f") | "\x80");
						$buf .= $cc1 . $cc2;
				}
			} elseif(($c1 & "\xc0") == "\x80"){ // needs conversion
					$cc1 = (chr(ord($c1) / 64) | "\xc0");
					$cc2 = (($c1 & "\x3f") | "\x80");
					$buf .= $cc1 . $cc2;
			} else { // it doesn't need convesion
				$buf .= $c1;
			}
		}
		return $buf;
	}

	public static function forceLatin1($text) {
	  if(is_array($text)) {
		foreach($text as $k => $v) {
		  $text[$k] = KomentoStringHelper::forceLatin1($v);
		}
		return $text;
	  }
	  return utf8_decode( KomentoStringHelper::forceUTF8($text) );
	}

	public static function fixUTF8($text){
	  if(is_array($text)) {
		foreach($text as $k => $v) {
		  $text[$k] = KomentoStringHelper::fixUTF8($v);
		}
		return $text;
	  }

	  $last = "";
	  while($last <> $text){
		$last = $text;
		$text = KomentoStringHelper::forceUTF8( utf8_decode( KomentoStringHelper::forceUTF8($text) ) );
	  }
	  return $text;
	}
}
