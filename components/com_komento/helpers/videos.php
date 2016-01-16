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

require_once( KOMENTO_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );

class KomentoVideosHelper
{
	private $patterns	= array(
								'youtube.com'		=> 'youtube',
								'youtu.be'			=> 'youtube',
								'vimeo.com'			=> 'vimeo',
								'yahoo.com'			=> 'yahoo',
								'metacafe.com'		=> 'metacafe',
								'google.com'		=> 'google',
								'mtv.com'			=> 'mtv',
								'liveleak.com'		=> 'liveleak',
								'revver.com'		=> 'revver',
								'dailymotion.com'	=> 'dailymotion',
								'nicovideo.jp'		=> 'nicovideo'
							);

	private $code		= '/\[video\](.*?)\[\/video\]/ms';

	public function strip( $content )
	{
		$content	= preg_replace( $this->code , '' , $content );

		return $content;
	}

	public function replace( $content )
	{
		preg_match_all( $this->code , $content , $matches );

		$videos	= $matches[0];

		if( !empty( $videos ) )
		{
			foreach( $videos as $video )
			{
				preg_match( $this->code , $video , $match);

				$rawUrl	= $match[1];
				$url	= parse_url( $rawUrl );
				$url	= explode( '.' , $url[ 'host' ] );

				// Last two parts will always be the domain name.
				$url	= $url[ count( $url ) - 2 ] . '.' . $url[ count( $url ) - 1 ];

				if( !empty( $url ) && array_key_exists( $url , $this->patterns ) )
				{
					$provider	= JString::strtolower( $this->patterns[ $url ] );
					$path		= KOMENTO_HELPERS . DIRECTORY_SEPARATOR . 'videos' . DIRECTORY_SEPARATOR . $provider . '.php';

					require_once( $path );

					$class	= 'KomentoVideo' . ucfirst( $this->patterns[ $url ] );

					if( class_exists( $class ) )
					{
						$object		= new $class();

						$html		= $object->getEmbedHTML( $rawUrl );

						$content	= str_ireplace( $video , $html , $content );
					}
				}
			}
		}

		return $content;
	}
}
