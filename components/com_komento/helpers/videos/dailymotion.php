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

class KomentoVideoDailyMotion
{
	private function getCode( $url )
	{
		preg_match( '/\/video\/([a-z0-9A-Z]*)/i' , $url , $matches );

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
			$html 	= '<iframe frameborder="0" width="' . $width . '" height="' . $height . '" src="http://www.dailymotion.com/embed/video/' . $code . '"></iframe>';

			return $html;
		}
		return false;
	}
}
