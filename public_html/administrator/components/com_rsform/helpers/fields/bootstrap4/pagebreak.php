<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/pagebreak.php';

class RSFormProFieldBootstrap4PageBreak extends RSFormProFieldPageBreak
{
	// @desc All page breaks should have a 'rsform-button' class for easy styling
	//		 onclick should also be present for convenience.
	public function getAttributes($action = null) {
		$attr = parent::getAttributes();
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		
		$attr['class'] .= 'btn';
		
		if (!is_null($action)) {
			$attr['class'] .= ($action == 'prev' ? ' btn-warning' : ' btn-success').' ';
		} else {
			$attr['class'] .= ' btn-secondary';
		}
		return $attr;
	}
}