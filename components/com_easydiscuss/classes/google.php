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

class DiscussGoogleOne
{
	public static function getButtonHTML( $row,  $position = 'vertical' )
	{
		$config	= DiscussHelper::getConfig();

		if( !$config->get('integration_googleone') )
		{
			return '';
		}

		$size		= $config->get('integration_googleone_layout');
		$dataURL	= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $row->id, false, true);

		$googleHTML  = '';

		if( $position == 'horizontal' )
		{
			$size 		= 'medium'; 
			$width		= '60px';	
		}
		else
		{
			$size 		= 'tall';
			$width       = '50px';
		}
		

		$googleHTML	.= '<div class="social-button google-plusone" style="width:' . $width . '">';
		$googleHTML	.= '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>';
		$googleHTML	.= '<g:plusone size="' . $size . '" href="' . $dataURL . '"></g:plusone>';
		$googleHTML	.= '</div>';

		return $googleHTML;
	}
}
