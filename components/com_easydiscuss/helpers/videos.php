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

require_once JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';

class DiscussVideosHelper
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

				if( stristr( $rawUrl , 'http://') === false && stristr( $rawUrl , 'https://') === false )
				{
					$rawUrl 	= 'http://' . $rawUrl;
				}
				
				$url	= parse_url( $rawUrl );
				$url	= explode( '.' , $url[ 'host' ] );

				// Not a valid domain name.
				if( count( $url ) == 1 )
				{
					return;
				}

				// Last two parts will always be the domain name.
				$url	= $url[ count( $url ) - 2 ] . '.' . $url[ count( $url ) - 1 ];

				if( !empty( $url ) && array_key_exists( $url , $this->patterns ) )
				{
					$provider	= JString::strtolower( $this->patterns[ $url ] );
					$path		= DISCUSS_HELPERS . '/videos/' . $provider . '.php';

					require_once( $path );

					$class	= 'DiscussVideo' . ucfirst( $this->patterns[ $url ] );

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
