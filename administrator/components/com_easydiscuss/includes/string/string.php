<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussString extends EasyDiscuss
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


	/**
	 * Try to get an image given the content
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getImage($contents)
	{
		$pattern = '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i';
		preg_match($pattern, $contents, $matches);

		$image = null;

		if ($matches) {
			$image = isset($matches[1]) ? $matches[1] : '';

			if (JString::stristr($matches[1], 'https://') === false && JString::stristr($matches[1], 'http://') === false && !empty($image)) {
				$image	= DISCUSS_JURIROOT . '/' . ltrim($image, '/');
			}
		}

		return $image;
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

		$string = preg_replace_callback('~&#x([0-9a-f]+);~i', function($m) { return chr(hexdec($m[1])); }, $string);
		$string = preg_replace_callback('~&#([0-9]+);~', function($m) { return chr($m[1]); }, $string);

		// replace literal entities
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);
		return strtr($string, $trans_tbl);
	}

	/**
	 * Normalizes a given string to ensure that it is a proper url with protocol
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function normalizeProtocol($str)
	{
		if (JString::stristr($str, 'http://') === false && JString::stristr($str, 'https://') === false) {
			$str = 'http://' . $str;
		}

		return $str;
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

	/**
	 * Deterects a list of name matches using @ symbols
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function detectNames($text, $exclude = array())
	{
		$pattern = '/@[A-Z0-9][A-Z0-9_-]+/i';

		preg_match_all($pattern, $text, $matches);

		if (!isset($matches[0]) || !$matches[0]) {
			return false;
		}

		$result = $matches[0];
		$users = array();

        foreach ($result as $name) {
            $name = JString::str_ireplace(array('@','#'), '', $name);

            // Given a name, try to find the correct user id.
            $id = ED::getUserId($name);

            if (!$id || in_array($id, $exclude)) {
                continue;
            }

            $users[] = ED::user($id);
        }

        return $users;
	}

	public function nameToLink( $text )
	{

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

	/**
	 * Determines if the string is a valid email address
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isValidEmail($data, $strict = false)
	{
		$regex = $strict?
			'/^([.0-9a-z_-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i' :
			'/^([*+!.&#$¦\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i'
		;

		if (preg_match($regex, trim($data), $matches))
		{
			return array($matches[1], $matches[2]);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Converts hyperlink text into real hyperlinks
	 *
	 * @since	4.0.6
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function replaceUrl($tmp, $text)
	{

		$config = ED::config();
		$pattern = '@(?i)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';

		preg_match_all($pattern, $tmp, $matches);

		$targetBlank = $config->get('main_link_new_window') ? ' target="_blank"' : '';
		$noFollow = $config->get('main_link_rel_nofollow') ? ' rel="nofollow"' : '';

		// Do not proceed if there are no links to process
		if (!isset($matches[0]) || !is_array($matches[0]) || empty($matches[0])) {
			return $text;
		}

		$tmplinks = $matches[0];

		$links = array();
		$linksWithProtocols = array();
		$linksWithoutProtocols = array();

		// We need to separate the link with and without protocols to avoid conflict when there are similar url present in the content.
		if ($tmplinks) {
			foreach($tmplinks as $link) {

				if (stristr( $link , 'http://' ) === false && stristr( $link , 'https://' ) === false && stristr( $link , 'ftp://' ) === false ) {
					$linksWithoutProtocols[] = $link; 
				} else if (stristr( $link , 'http://' ) !== false || stristr( $link , 'https://' ) !== false || stristr( $link , 'ftp://' ) === false ) {
					$linksWithProtocols[] = $link; 
				}
			}
		}

		// the idea is the first convert the url to [EDWURLx] and [EDWOURLx] where x is the index. This is to prevent same url get overwritten with wrong value.
		$linkArrays = array();

		// global indexing.
		$idx = 1;

		// lets process the one with protocol
		if ($linksWithProtocols) {
			$linksWithProtocols = array_unique($linksWithProtocols);

			foreach($linksWithProtocols as $link) {

				$mypattern = '[EDWURL' . $idx . ']';

				$text = str_ireplace($link, $mypattern, $text);

				$obj = new stdClass();
				$obj->index = $idx;
				$obj->link = $link;
				$obj->newlink = $link;
				$obj->customcode = $mypattern;

				$linkArrays[] = $obj;

				$idx++;
			}
		}

		// Now we process the one without protocol
		if ($linksWithoutProtocols) {
			$linksWithoutProtocols = array_unique($linksWithoutProtocols);

			foreach($linksWithoutProtocols as $link) {
				$mypattern = '[EDWOURL' . $idx . ']';
				$text = str_ireplace($link, $mypattern, $text);

				$obj = new stdClass();
				$obj->index = $idx;
				$obj->link = $link;
				$obj->newlink = 'http://'. $link;
				$obj->customcode = $mypattern;

				$linkArrays[] = $obj;

				$idx++;
			}
		}

		// Let's replace back the link now with the proper format based on the index given.
		foreach ($linkArrays as $link) {
			$text = str_ireplace($link->customcode, $link->newlink, $text);

			$patternReplace = '@(?<![.*">])\b(?:(?:https?|ftp|file)://|[a-z]\.)[-A-Z0-9+&#/%=~_|$?!:,.]*[A-Z0-9+&#/%=~_|$]@i';

			// Use preg_replace to only replace if the URL doesn't has <a> tag
			$text = preg_replace($patternReplace, '<a href="\0" ' . $targetBlank . $noFollow . '>\0</a>', $text);
		}

		// Not really sure why this is needed as it will cause some of the content to not rendered correctly. 
		// We will comment this out for now. References : #1878
		// $text = JString::str_ireplace('&quot;', '"', $text);

		return $text;
	}

	public static function cleanUrl($url)
	{
		$juri	= JFactory::getURI($url);
		$juri->parse($url);
		$scheme = $juri->getScheme() ? $juri->getScheme() : 'http';
		$juri->setScheme( $scheme );

		return $juri->toString();
	}

	/**
	 * To hightlighted the strings
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hightlight($strings, $query)
	{
		$replace = $query;

	  	if (is_array($query)) {

	 		$replace = array_flip(array_flip($query));
	 		$pattern = array();

		 	foreach ($replace as $k=>$fword) {
		    	$pattern[] = '/\b(' . $fword . ')(?!>)\b/i';
		    	$replace[$k] = '<span class="ed-search-hightlight">$1</span>';
		 	}

		 	return preg_replace($pattern, $replace, $strings);
	 	}

	    $pattern = '/\b(' . $replace . ')(?!>)\b/i';
	    $replace = '<span class="ed-search-hightlight">$1</span>';

	    return preg_replace($pattern, $replace, $strings);
	}
}
