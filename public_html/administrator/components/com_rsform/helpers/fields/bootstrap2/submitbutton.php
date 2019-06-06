<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/submitbutton.php';

class RSFormProFieldBootstrap2SubmitButton extends RSFormProFieldSubmitButton
{
	// @desc All buttons should have a class for easy styling
	public function getAttributes($type='button') {
		$attr = parent::getAttributes($type);
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		if ($type == 'button') {
			$attr['class'] .= ' btn btn-primary';
		} elseif ($type == 'reset') {
			$attr['class'] .= ' btn btn-danger';
		} elseif ($type == 'previous') {
			$attr['class'] .= ' btn btn-warning';
			if (!isset($attr['onclick'])) {
				$attr['onclick'] = '';
			} else {
				$attr['onclick'] = rtrim($attr['onclick'], ';');
			}
		}
		
		return $attr;
	}
}