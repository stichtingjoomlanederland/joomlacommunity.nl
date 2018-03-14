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

class EasyDiscussFeeds extends EasyDiscuss
{
	/**
	 * Attach rss into the header of the page so that browsers know the existance of the rss feed
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function addHeaders($feedUrl)
	{
		// If rss is disabled or the current view type is not of html, do not add the headers
		if (!$this->config->get('main_rss') || $this->doc->getType() != 'html') {
			return;
		}

		$enabled = $this->config->get('main_feedburner');
		$url = $this->config->get('main_feedburner_url');

		$sef = EDR::isSefEnabled();
		$concat = $sef ? '?' : '&';

		if ($enabled && !empty($url)) {
			return $this->doc->addHeadLink($url, 'alternate', 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0'));
		}

		$rss = EDR::_($feedUrl) . $concat . 'format=feed&type=rss';
		$atom = EDR::_($feedUrl) . $concat . 'format=feed&type=atom';

		// Add default rss feed link
		$this->doc->addHeadLink($rss, 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
		$this->doc->addHeadLink($atom, 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );
	}

	/**
	 * Formats a feed url and return the appropriate url
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getFeedURL($url , $atom = false)
	{
		$sef = EDR::isSefEnabled();
		$join = $sef ? '?' : '&';
		$url = EDR::_($url) . $join . 'format=feed';
		$url .= $atom ? '&type=atom' : '&type=rss';

		return $url;
	}
}
