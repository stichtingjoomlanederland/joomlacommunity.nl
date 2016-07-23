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

class EasyDiscussSharerButtonLinkedin extends EasyDiscuss
{
	public function enabled()
	{
		$config = ED::config();

		if (!$config->get('integration_linkedin')) {
			return false;
		}

		return true;
	}

	public static function html($row, $position = 'vertical')
	{
		$config	= ED::config();

		if (!$config->get('integration_linkedin')) {
			return '';
		}

		$button = $config->get('integration_linkedin_button');
		$dataURL = EDR::getRoutedURL('view=post&id=' . $row->id, false, true);

		// $counter = ' data-counter="top"';

		// if ($position == 'horizontal') {
		// 	$counter = ' data-counter="right"';
		// }

		$counter = ' data-counter="right"';

		$html = '';
		$html = '<div class="social-button linkedin-share">';
		$html .= '<script type="text/javascript" src="https://platform.linkedin.com/in.js"></script>';
		$html .= '<script type="in/share" data-url="' . $dataURL . '"' . $counter . '></script>';
		$html .= '</div>';

		return $html;
	}
}
