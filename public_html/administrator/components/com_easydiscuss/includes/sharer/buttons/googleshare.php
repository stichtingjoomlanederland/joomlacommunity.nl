<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

class EasyDiscussSharerButtonGoogleShare extends EasyDiscuss
{
	public function enabled()
	{
		$config = ED::config();

		if (!$config->get('integration_googleshare')) {
			return false;
		}

		return true;
	}

	public static function html($row, $position = 'vertical')
	{
		$config	= ED::config();

		if (!$config->get('integration_googleshare')) {
			return '';
		}

		$size = $config->get('integration_googleshare_layout');
		$dataURL = EDR::getRoutedURL('view=post&id=' . $row->id, false, true);

		$googleShare = '';

		$size = 'medium';
		$width = 'auto';

		$googleShare .= '<div class="social-button google-share" style="width:' . $width . '">';
		$googleShare .= '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>';
		$googleShare .= '<g:plus action="share" height=24"" size="' . $size . '" href="' . $dataURL . '"></g:plus>';
		$googleShare .= '</div>';

		return $googleShare;
	}
}
