<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/


defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/birthday.php';

class RSFormProFieldUikit3BirthDay extends RSFormProFieldBirthDay
{
	// @desc All birthday select lists should have a 'rsform-select-box-small' class for easy styling
	public function getAttributes() {
        $attr = parent::getAttributes();
        if (strlen($attr['class'])) {
            $attr['class'] .= ' ';
        }
        $attr['class'] .= 'uk-select uk-width-small';
		
		return $attr;
	}
}