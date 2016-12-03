<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/captcha.php';

class RSFormProFieldBootstrap3Captcha extends RSFormProFieldCaptcha
{
	protected function setFieldOutput($image, $input, $refreshBtn, $flow) {
		$layout = '';
		if ($flow == 'HORIZONTAL') {
			$layout = '<div class="row"><div class="col-md-3" style="text-align:right">'.$image.'</div><div class="col-md-4">'.$input.'</div>'.(!empty($refreshBtn) ? '<div class="col-md-3">'.$refreshBtn.'</div>' : '').'</div>';
		} else {
			$layout = '<div class="row"><div class="col-md-4" style="text-align:center">'.$image.'</div></div><div class="row"><div class="col-md-4">'.$input.'</div></div>'.(!empty($refreshBtn) ? '<div class="row"><div class="col-md-4" style="text-align:center">'.$refreshBtn.'</div></div>' : '');
		}
		
		return $layout;
	}
	
	
	// @desc All captcha textboxes should have a 'rsform-captcha-box' class for easy styling
	public function getAttributes() {
		$attr = parent::getAttributes();
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		$attr['class'] .= 'form-control rsform-captcha-box';
		
		// Check for invalid here so that we can add 'rsform-error'
		if ($this->invalid) {
			$attr['class'] .= ' rsform-error';
		}
		
		return $attr;
	}
	
	protected function getRefreshAttributes() {
		$attr = array(
			'class="rsform-captcha-refresh-button btn btn-default"'
		);
		
		return implode(' ', $attr);
	}
}