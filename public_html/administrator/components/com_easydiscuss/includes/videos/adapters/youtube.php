<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class DiscussVideoYoutube
{
	private function getCode($url)
	{
		// Check if the url should be processed here.
		if (stristr($url, 'youtube.com') === false && stristr($url, 'youtu.be') === false) {
			return false;
		}

		// if this URL is youtu.be
		preg_match('/youtu.be\/(.*)/is', $url, $matches);

		if (!empty($matches)) {
			return $matches[1];
		}

		// only process this if the URL is youtube.com
		parse_str(parse_url($url, PHP_URL_QUERY), $data);

		if (!$data) {
			return false;
		}

		return $data;
	}

	public function getEmbedHTML($url, $isAmp = false)
	{
		$config	= ED::config();
		$width = $config->get('bbcode_video_width');
		$height	= $config->get('bbcode_video_height');
		$html = false;
		$listId = '';

		// Contain a list of video parameter query string
		$data = $this->getCode($url);

		if ($data && (isset($data['v']) && $data['v'])) {

			// YouTube video ID
			$videoId = $data['v'];

			// Some of the YouTube video contain the list parameter query string
			if (isset($data['list']) && $data['list']) {
				$listId = '/' . $data['list'];
			}
		
		} else {
			// YouTube video ID
			$videoId = $data;
		}

		if ($videoId) {
			$html = '<div class="ed-video ed-video--16by9"><iframe title="YouTube video player" width="' . $width . '" height="' . $height . '" src="//www.youtube.com/embed/' . $videoId . $listId . '?wmode=transparent" frameborder="0" allowfullscreen></iframe></div>';
		}

		if ($videoId && $isAmp) {
			$html = '<amp-youtube data-videoid="' . $videoId . '" layout="responsive" width="' . $width . '" height="' . $height . '"></amp-youtube>';
		}

		if (!$videoId) {
			// this video do not have a code. so include the url directly.
			$html = '<div class="ed-video ed-video--16-9"><iframe title="YouTube video player" width="' . $width . '" height="' . $height . '" src="' . $url . '&wmode=transparent" frameborder="0" allowfullscreen></iframe></div>';
		}

		if (!$videoId && $isAmp) {
			$html = '<amp-iframe src="' . $url . '" width="' . $width . '" height="' . $height . '" frameborder="0" layout="responsive" sandbox="allow-scripts allow-same-origin"></amp-iframe>';
		}


		return $html;
	}
}
