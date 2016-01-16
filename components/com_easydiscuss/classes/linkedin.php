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

class DiscussLinkedIn
{
	public static function getButtonHTML( $row,  $position = 'vertical' )
	{
		$config	= DiscussHelper::getConfig();

		if( !$config->get('integration_linkedin') )
		{
			return '';
		}

		$button		= $config->get( 'integration_linkedin_button' );
		$dataURL	= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $row->id, false, true);

		if( $position == 'horizontal' )
		{
			$counter	= ' data-counter="right"';
		}
		else
		{
			$counter	= ' data-counter="top"';
		}

		$html 	= '';

		$html 		= '<div class="social-button linkedin-share">';
		$html 		.= '<script type="text/javascript" src="https://platform.linkedin.com/in.js"></script>';
		$html 		.= '<script type="in/share" data-url="' . $dataURL . '"' . $counter . '></script>';
		$html 		.= '</div>';

		return $html;
	}
}
