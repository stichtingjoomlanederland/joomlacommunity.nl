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

class KomentoVideoYoutube
{
	private function getCode( $url )
	{
		/* match http://www.youtube.com/watch?v=TB4loah_sXw&feature=fvst */
		preg_match( '/youtube.com\/watch\?v=(.*)(?=&)/is' , $url , $matches );
		if( !empty( $matches ) )
		{
			return $matches[1];
		}

		/* match http://www.youtube.com/watch?v=sr1eb3ngYko */
		preg_match( '/youtube.com\/watch\?v=(.*)/is' , $url , $matches );
		if( !empty( $matches ) )
		{
			return $matches[1];
		}

		preg_match( '/youtu.be\/(.*)/is' , $url , $matches );

		if( !empty( $matches ) )
		{
			return $matches[1];
		}

		return false;
	}

	public function getEmbedHTML( $url )
	{
		$code	= $this->getCode( $url );

		$config	= Komento::getConfig();
		$width	= $config->get( 'bbcode_video_width' );
		$height	= $config->get( 'bbcode_video_height' );

		if( $code )
		{
			return '<div class="video-container"><iframe title="YouTube video player" width="' . $width . '" height="' . $height . '" src="http://www.youtube.com/embed/' . $code . '?wmode=transparent" frameborder="0" allowfullscreen></iframe></div>';
		}
		else
		{
		    // this video do not have a code. so include the url directly.
			return '<div class="video-container"><iframe title="YouTube video player" width="' . $width . '" height="' . $height . '" src="' . $url . '&wmode=transparent" frameborder="0" allowfullscreen></iframe></div>';
		}
		return false;
	}
}
