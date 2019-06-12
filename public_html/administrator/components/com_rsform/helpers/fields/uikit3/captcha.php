<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/captcha.php';

class RSFormProFieldUikit3Captcha extends RSFormProFieldCaptcha
{
    // @desc All captcha textboxes should have a 'rsform-captcha-box' class for easy styling
    public function getAttributes() {
        $attr = parent::getAttributes();
        if (strlen($attr['class'])) {
            $attr['class'] .= ' ';
        }
        $attr['class'] .= 'uk-input uk-form-width-medium';

        return $attr;
    }

	protected function getRefreshAttributes() {
		$attr = array(
			'class="rsform-captcha-refresh-button uk-button uk-button-default"'
		);
		
		return implode(' ', $attr);
	}
}