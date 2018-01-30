<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/captcha.php';

class RSFormProFieldBootstrap4Captcha extends RSFormProFieldCaptcha
{
	protected function setFieldOutput($image, $input, $refreshBtn, $flow) {
        $html = array();

        $size1 = $flow == 'HORIZONTAL' ? 3 : 12;
        $size2 = $flow == 'HORIZONTAL' ? 9 : 12;

        $html[] = '<div class="row">';
        $html[] = '<div class="col-md-' . $size1 . ' text-center">';
        $html[] = $image;
        $html[] = '</div>';

        $html[] = '<div class="col-md-' . $size2 . '">';
        if ($refreshBtn)
        {
            $html[] = '<div class="input-group">';
            $html[] = $input;
            $html[] = '<span class="input-group-btn">';
            $html[] = $refreshBtn;
            $html[] = '</span>';
            $html[] = '</div>';
        }
        else
        {
            $html[] = $input;
        }

        $html[] = '</div>';
        $html[] = '</div>';

        return implode("\n", $html);
	}
	
	
	// @desc All captcha textboxes should have a 'rsform-captcha-box' class for easy styling
	public function getAttributes() {
		$attr = parent::getAttributes();
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		$attr['class'] .= 'form-control';
		
		return $attr;
	}
	
	protected function getRefreshAttributes() {
		$attr = array(
			'class="rsform-captcha-refresh-button btn btn-secondary"'
		);
		
		return implode(' ', $attr);
	}
}