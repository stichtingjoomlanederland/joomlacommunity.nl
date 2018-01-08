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

class EasyDiscussSharerButtonGoogle extends EasyDiscuss
{
	public function enabled()
	{
		$config = ED::config();

		if (!$config->get('integration_googleone')) {
			return false;
		}

		return true;
	}

	public static function html($row, $position = 'vertical')
	{
		$config = ED::config();

		if (!$config->get('integration_googleone')) {
			return '';
		}

		$size = $config->get('integration_googleone_layout');
		$dataURL = EDR::getRoutedURL('view=post&id=' . $row->id, false, true);

		$googleHTML = '';

		$size = 'medium';
		$width = 'auto';

		$googleHTML	.= '<div class="social-button google-plusone" style="width:' . $width . '">';
		$googleHTML	.= '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>';
		$googleHTML	.= '<g:plusone size="' . $size . '" href="' . $dataURL . '"></g:plusone>';
		$googleHTML	.= '</div>';

		return $googleHTML;
	}
}
