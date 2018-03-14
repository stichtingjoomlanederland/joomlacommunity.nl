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

class EasyDiscussVideos extends EasyDiscuss
{
	private $patterns	= array(
									'youtube.com'		=> 'youtube',
									'youtu.be'			=> 'youtube',
									'vimeo.com'			=> 'vimeo',
									'metacafe.com'		=> 'metacafe',
									'google.com'		=> 'google',
									'mtv.com'			=> 'mtv',
									'liveleak.com'		=> 'liveleak',
									'revver.com'		=> 'revver',
									'dailymotion.com'	=> 'dailymotion',
									'nicovideo.jp'		=> 'nicovideo',
									'smule.com' => 'smule'
								);

	private $code		= '/\[video\](.*?)\[\/video\]/ms';

	public function strip( $content )
	{
		$content	= preg_replace( $this->code , '' , $content );

		return $content;
	}

	/**
	 * Retrieves the video provider
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getProvider($url)
	{
		$provider = strtolower($this->patterns[$url]);
		$file = __DIR__ . '/adapters/' . $provider . '.php';

		require_once($file);

		$class = 'DiscussVideo' . ucfirst($this->patterns[$url]);

		if (class_exists($class)) {
			$obj = new $class();

			return $obj;
		}

		return false;
	}

	/**
	 * Replace contents
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function replace($content)
	{
		preg_match_all($this->code, $content, $matches);

		$videos	= $matches[0];

		if (!$videos) {
			return $content;
		}

		foreach ($videos as $video) {

			preg_match($this->code, $video , $match);

			$matchUrl = $match[1];

			// make sure the content has no htm tags.
			$rawUrl = strip_tags(html_entity_decode($matchUrl));

			if (stristr($rawUrl, 'http://') === false && stristr($rawUrl, 'https://') === false) {
				$rawUrl = 'http://' . $rawUrl;
			}

			$url = parse_url( $rawUrl );
			$url = explode( '.' , $url['host']);

			// Not a valid domain name.
			if (count($url) == 1) {
				return;
			}

			// Last two parts will always be the domain name.
			$url	= $url[ count( $url ) - 2 ] . '.' . $url[ count( $url ) - 1 ];

			if (!empty($url) && array_key_exists($url, $this->patterns)) {
				$provider = $this->getProvider($url);

				$html = $provider->getEmbedHTML($rawUrl);

				$content = str_ireplace($video, $html, $content);
			}
		}

		return $content;
	}
}
