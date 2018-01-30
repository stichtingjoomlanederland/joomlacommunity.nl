<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/


defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/birthday.php';

class RSFormProFieldBootstrap4BirthDay extends RSFormProFieldBirthDay
{
	public function getFormInput() {
		$separator	= $this->getProperty('DATESEPARATOR');
		$items = parent::getFormInput();
		
		if (preg_match_all('/<select.*?><\/select>/', $items, $matches))
		{
			$items = $matches[0];
		}
		else
		{
			// This shouldn't be the case (it wasn't the case)
			$items = explode($separator, $items);
		}
		
		// extra classes for proper alignment
        $size = 12 / count($items);
		foreach ($items as $i => &$item) {
			$item = '<div class="col-sm-' . $size . ' col-xs-12">'.$item.'</div>';
		}
		return '<div class="row">'.implode('', $items).'</div>';
	}
	
	// @desc All birthday select lists should have a 'rsform-select-box-small' class for easy styling
	public function getAttributes() {
        $attr = parent::getAttributes();
        if (strlen($attr['class'])) {
            $attr['class'] .= ' ';
        }
        $attr['class'] .= 'form-control';

        return $attr;
	}
}