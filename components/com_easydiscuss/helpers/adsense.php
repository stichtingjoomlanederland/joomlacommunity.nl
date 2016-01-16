<?php
/**
* @package		EasyDiscuss
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

class DiscussAdsenseHelper
{
	public static function getHTML()
	{
		$config = DiscussHelper::getConfig();

		$adsenseObj = new stdClass;
		$adsenseObj->header			= '';
		$adsenseObj->beforereplies	= '';
		$adsenseObj->footer			= '';

		$defaultCode	= '';
		$defaultDisplay	= '';
		$my				= JFactory::getUser();

		if(! $config->get( 'integration_google_adsense_enable' ))
		{
			return $adsenseObj;
		}

		if( $config->get( 'integration_google_adsense_display_access' ) == 'members' && $my->id == 0 )
		{
			return $adsenseObj;
		}

		if( $config->get( 'integration_google_adsense_display_access' ) == 'guests' && $my->id > 0 )
		{
			return $adsenseObj;
		}


		$adminAdsenseCode		= $config->get('integration_google_adsense_code');
		$adminAdsenseDisplay	= $config->get('integration_google_adsense_display');

		if(! empty($adminAdsenseCode))
		{
			$defaultCode		= $adminAdsenseCode;
			$defaultDisplay		= $adminAdsenseDisplay;
		}

		if(! empty($defaultCode))
		{
			$adTheme	= new DiscussThemes();
			$adTheme->set( 'adsense'	, $defaultCode);
			$adsenseHTML = $adTheme->fetch( 'adsense.php' );

			switch( $defaultDisplay )
			{
				case 'beforereplies':
					$adsenseObj->beforereplies = $adsenseHTML;
					break;
				case 'header':
					$adsenseObj->header = $adsenseHTML;
					break;
				case 'footer':
					$adsenseObj->footer = $adsenseHTML;
					break;
				case 'both':
				default :
					$adsenseObj->header = $adsenseHTML;
					$adsenseObj->footer = $adsenseHTML;
					break;
			}
		}//end if

		return $adsenseObj;
	}
}
