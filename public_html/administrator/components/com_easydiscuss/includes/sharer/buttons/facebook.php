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

class EasyDiscussSharerButtonFacebook extends EasyDiscuss
{
	public function enabled()
	{
		$config = ED::config();

		if (!$config->get('integration_facebook_like')) {
			return false;
		}

		return true;
	}

	public function html($row, $position = 'vertical')
	{
		$config = ED::config();

		if (!$config->get('integration_facebook_like')) {
			return '';
		}

		$document = JFactory::getDocument();
		$language = $document->getLanguage();
		$language = explode('-' , $language);

		if (count($language) != 2) {
			$language = array('en', 'GB');
		}

		$faces = $config->get('integration_facebook_like_faces') ? 'true' : 'false';
		$width = $config->get('integration_facebook_like_width');
		$verb = $config->get('integration_facebook_like_verb' );
		$theme = $config->get('integration_facebook_like_theme' );
		$send = $config->get( 'integration_facebook_like_send' ) ? 'true' : 'false';

		$height	= ($faces == 'true') ? '70' : '30';
		$locale	= $language[0] . '_' . JString::strtoupper($language[1]);

		$url = EDR::getRoutedURL('view=post&id=' . $row->id, true, true);
		$html = '';


		$layout = 'button_count';

		$html = '<div class="social-button facebook-like">';

		if ($config->get('integration_facebook_scripts')) {
			$html .= '<div id="fb-root"></div><script src="https://connect.facebook.net/' . $locale . '/all.js#xfbml=1"></script>';
		}

		$html .= '<fb:like href="' . $url . '" send="' . $send . '" layout="' . $layout . '" action="' . $verb . '" ';
		$html .= 'locale="' . $locale . '" colorscheme="' . $theme . '" show_faces="' . $faces . '" style="height: ' . $height . 'px;" height="' . $height . '"></fb:like>';
		$html .= '</div>';

		return $html;
	}
}
