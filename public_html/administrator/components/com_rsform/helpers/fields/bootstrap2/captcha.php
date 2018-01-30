<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/captcha.php';

class RSFormProFieldBootstrap2Captcha extends RSFormProFieldCaptcha
{
    protected function setFieldOutput($image, $input, $refreshBtn, $flow)
    {
        $html = array();

        $size1 = $flow == 'HORIZONTAL' ? 3 : 12;
        $size2 = $flow == 'HORIZONTAL' ? 9 : 12;

        $html[] = '<div class="row-fluid">';
        $html[] = '<div class="span' . $size1 . ' text-left">';
        $html[] = $image;
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="row-fluid">';
        $html[] = '<div class="span' . $size2 . '">';
        if ($refreshBtn) {
            $html[] = '<div class="input-append">';
            $html[] = $input;
            $html[] = $refreshBtn;
            $html[] = '</div>';
        } else {
            $html[] = $input;
        }

        $html[] = '</div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }

	protected function getRefreshAttributes() {
		$attr = array(
			'class="rsform-captcha-refresh-button btn"'
		);
		
		return implode(' ', $attr);
	}
}