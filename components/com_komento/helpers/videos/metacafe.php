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

class KomentoVideoMetaCafe
{
	private function getCode( $url )
	{
		preg_match( '/\/watch\/(.*)(?=(\/).)/i' , $url , $matches );

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
			return '<embed flashVars="playerVars=showStats=yes|autoPlay=no" src="http://www.metacafe.com/fplayer/' . $code . '/easyblog.swf" width="' . $width . '" height="' . $height . '" wmode="transparent" allowFullScreen="true" allowScriptAccess="always" name="Metacafe_' . $code . '" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>';
		}
		return false;
	}
}
