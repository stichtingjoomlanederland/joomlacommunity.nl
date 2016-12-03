<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/imagebutton.php';

class RSFormProFieldUikitImageButton extends RSFormProFieldImageButton
{
	// @desc All buttons should have a class for easy styling
	public function getAttributes($type='button') {
		$attr = parent::getAttributes();
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		if ($type == 'button') {
			$attr['class'] .= ' ';
			
			// Check for invalid here so that we can add 'rsform-error'
			if ($this->invalid) {
				$attr['class'] .= ' rsform-error';
			}
		} elseif ($type == 'reset') {
			$attr['class'] .= ' ';
		} elseif ($type == 'previous') {
			$attr['class'] .= ' uk-button';
			if (!isset($attr['onclick'])) {
				$attr['onclick'] = '';
			} else {
				$attr['onclick'] = rtrim($attr['onclick'], ';');
			}
		}
		
		return $attr;
	}
}