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

	/**
	 * Generates the AMP html codes for adsense
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function ampHtml(EasyDiscussPost $post)
	{
		$result = new stdClass();
		$result->header = '';
		$result->beforereplies = '';
		$result->footer = '';

		// Standard code
		$code = $this->config->get('integration_google_adsense_code');
		$data = [];

		if ($code) {
			if (preg_match('/google_ad_client\s*=\s*"([^"]+)"\s*;/', $code, $m)) {
				$data['client'] = $m[1];
			}
			if (preg_match('/google_ad_slot\s*=\s*"([^"]+)"\s*;/', $code, $m)) {
				$data['slot'] = $m[1];
			}
		}

		// Responsive code
		$responsiveCode = $this->config->get('integration_google_adsense_responsive_code');

		if (!$code || $responsiveCode && $this->config->get('integration_google_adsense_responsive')) {
			$code = $responsiveCode;

			// We need to process the code to meet the AMP requirement
			preg_match_all('~ad-(?P<name>\w+)="(?P<val>[^"]*)"~', $code, $m);
			$data = array_combine($m['name'], $m['val']);
		}

		// Ensure that adsense is enabled
		if (!$this->config->get('integration_google_adsense_enable')) {
			return $result;
		}

		if ($this->config->get('integration_google_adsense_display_access') == 'members' && !$this->my->id0) {
			return $result;
		}

		if ($this->config->get('integration_google_adsense_display_access') == 'guests' && $this->my->id) {
			return $result;
		}

		$defaultDisplay = $this->config->get('integration_google_adsense_display', array());

		if ($defaultDisplay) {
			$defaultDisplay = explode(',', $defaultDisplay);
		}

		if ($code) {
			$html = '<amp-ad layout="responsive" width=300 height=100 type="adsense" data-ad-client="' . $data['client'] . '" data-ad-slot="' . $data['slot'] . '"></amp-ad>';

			foreach ($defaultDisplay as $location) {
				$result->$location = $html;
			}
		}

		return $result;
	}
}
