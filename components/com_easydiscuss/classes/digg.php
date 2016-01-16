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

class DiscussDigg
{
	public static function getButtonHTML( $row,  $position = 'vertical' )
	{
		$config	= DiscussHelper::getConfig();

		if( !$config->get('integration_digg') )
		{
			return '';
		}

		$dataURL	= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $row->id, false, true);
		$dataURL	= urlencode( $dataURL );

		$html		= '';

		if( $position == 'horizontal' )
		{
			$class 	= 'DiggCompact';
		}
		else
		{
			$class 	= 'DiggMedium';
		}

		$html	= '<script type="text/javascript">(function() {
		var s = document.createElement(\'SCRIPT\'), s1 = document.getElementsByTagName(\'SCRIPT\')[0];
		s.type = \'text/javascript\';
		s.async = true;
		s.src = \'http://widgets.digg.com/buttons.js\';
		s1.parentNode.insertBefore(s, s1);
		})();
		</script>
		<!-- Wide Button -->
		<div class="social-button digg-share"><a class="DiggThisButton ' . $class . '" href="https://digg.com/submit?url=' . $dataURL . '&amp;title=' . $row->title . '"></a></div>';

		return $html;
	}
}
