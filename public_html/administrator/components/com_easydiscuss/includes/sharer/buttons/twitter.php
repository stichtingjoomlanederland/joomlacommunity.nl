<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussSharerButtonTwitter extends EasyDiscuss
{
	public function enabled()
	{
		$config = ED::config();

		if (!$config->get('integration_twitter_button')) {
			return false;
		}

		return true;
	}

	public static function html($row, $position = 'vertical')
	{
		$config	= ED::config();

		if (!$config->get('integration_twitter_button')) {
			return '';
		}

		$html = '';
		$style = $config->get('integration_twitter_button_style');
		$dataURL = EDR::getRoutedURL('view=post&id=' . $row->id, false, true);

		$width = '55px';
		$style = 'vertical';
		
		if ($position == 'horizontal') {
			$style = 'horizontal';
			$width = '80px';
		}

		$html = '<div class="social-button retweet"><a href="http://twitter.com/share" class="twitter-share-button" data-url="' . $dataURL . '" data-counturl="'.$dataURL.'" data-count="' . $style .'">Tweet</a><script type="text/javascript" src="https://platform.twitter.com/widgets.js"></script></div>';

		return $html;
	}
}
