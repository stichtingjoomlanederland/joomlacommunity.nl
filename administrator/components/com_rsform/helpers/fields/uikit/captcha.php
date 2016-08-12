<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/captcha.php';

class RSFormProFieldUikitCaptcha extends RSFormProFieldCaptcha
{
	protected function getRefreshAttributes() {
		$attr = array(
			'class="rsform-captcha-refresh-button uk-button uk-button-default"'
		);
		
		return implode(' ', $attr);
	}
}