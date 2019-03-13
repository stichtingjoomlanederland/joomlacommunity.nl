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

class EasyDiscussUrl extends EasyDiscuss
{
	public static function replace( $tmp , $text )
	{
		$config = DiscussHelper::getConfig();
		$pattern = '@(?i)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';

		preg_match_all($pattern, $tmp, $matches);

		$targetBlank = $config->get('main_link_new_window') ? ' target="_blank" rel="noreferrer"' : '';

		if (!isset($matches[0]) || !is_array($matches[0])) {
			return;
		}

		// to avoid infinite loop, unique the matches
		$links = $matches[0];

		foreach ($links as &$link) {
			$link = JString::strtolower($link);
		}

		$uniques = array_unique($links);

		foreach ($uniques as $match) {
			
			$matchProtocol 	= $match;

			if (stristr( $matchProtocol , 'http://' ) === false && stristr( $matchProtocol , 'https://' ) === false && stristr( $matchProtocol , 'ftp://' ) === false ) {
				$matchProtocol	= 'http://' . $matchProtocol;
			}

			$text = JString::str_ireplace($match, '<a href="' . $matchProtocol . '"' . $targetBlank . '>' . $match . '</a>', $text);
		}

		$text = JString::str_ireplace('&quot;', '"', $text);

		return $text;
	}

	public static function clean( $url )
	{
		$juri	= JFactory::getURI($url);
		$juri->parse($url);
		$scheme = $juri->getScheme() ? $juri->getScheme() : 'http';
		$juri->setScheme( $scheme );

		return $juri->toString();
	}
}
