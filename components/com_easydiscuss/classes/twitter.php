<?php
/**
* @package      EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once DISCUSS_HELPERS . '/helper.php';

class DiscussTwitter
{
	public static function getButtonHTML( $row, $position = 'vertical' )
	{
		$config	= DiscussHelper::getConfig();

		if( !$config->get('integration_twitter_button') )
		{
			return '';
		}

		$html		= '';
		$style		= $config->get( 'integration_twitter_button_style' );
		$dataURL	= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $row->id, false, true);


		$width  = '80px';

		if( $position == 'horizontal' )
		{
			$style 	= 'horizontal';
			$width  = '80px';
		}
		else
		{
			$width  = '55px';
			$style	= 'vertical';
		}

		$html	= '<div class="social-button retweet" style="width: ' . $width . '"><a href="http://twitter.com/share" class="twitter-share-button" data-url="' . $dataURL . '" data-counturl="'.$dataURL.'" data-count="' . $style .'">Tweet</a><script type="text/javascript" src="https://platform.twitter.com/widgets.js"></script></div>';


		return $html;
	}
}
