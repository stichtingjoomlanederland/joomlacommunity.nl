<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/submitbutton.php';

class RSFormProFieldUikit3SubmitButton extends RSFormProFieldSubmitButton
{
	// @desc All buttons should have a class for easy styling
	public function getAttributes($type='button') {
		$attr = parent::getAttributes($type);
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		if ($type == 'button') {
			$attr['class'] .= ' uk-button uk-button-primary';
		} elseif ($type == 'reset') {
			$attr['class'] .= ' uk-button uk-button-danger';
		} elseif ($type == 'previous') {
			$attr['class'] .= ' uk-button uk-button-default';
			if (!isset($attr['onclick'])) {
				$attr['onclick'] = '';
			} else {
				$attr['onclick'] = rtrim($attr['onclick'], ';');
			}
		}
		
		return $attr;
	}
}