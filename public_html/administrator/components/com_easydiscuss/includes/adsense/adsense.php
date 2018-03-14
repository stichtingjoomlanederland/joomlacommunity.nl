<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussAdsense extends EasyDiscuss
{
	public function html()
	{
		$adsenseObj = new stdClass;
		$adsenseObj->header = '';
		$adsenseObj->beforereplies = '';
		$adsenseObj->footer = '';

		$my = JFactory::getUser();

		if (!$this->config->get('integration_google_adsense_enable')) {
			return $adsenseObj;
		}

		if ($this->config->get('integration_google_adsense_display_access') == 'members' && $my->id == 0) {
			return $adsenseObj;
		}

		if ($this->config->get('integration_google_adsense_display_access') == 'guests' && $my->id > 0) {
			return $adsenseObj;
		}

		$namespace = 'site/widgets/adsense/adsense';

		$defaultCode = $this->config->get('integration_google_adsense_code');
		$responsiveCode = $this->config->get('integration_google_adsense_responsive_code');

		if (!$defaultCode || $responsiveCode && $this->config->get('integration_google_adsense_responsive')) {
			$defaultCode = $responsiveCode;
			$namespace = 'site/widgets/adsense/responsive';
		}

		$defaultDisplay = $this->config->get('integration_google_adsense_display', array());

		if ($defaultDisplay) {
			$defaultDisplay = explode(',', $defaultDisplay);
		}

		if ($defaultCode) {
			$theme = ED::themes();
			$theme->set('adsense', $defaultCode);

			$adsenseHTML = $theme->output($namespace);

			foreach ($defaultDisplay as $result) {
				$adsenseObj->$result = $adsenseHTML;
			}
		}

		return $adsenseObj;
	}
}
