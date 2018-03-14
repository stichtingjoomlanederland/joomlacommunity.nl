<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/radiogroup.php';

class RSFormProFieldResponsiveRadioGroup extends RSFormProFieldRadioGroup
{
	protected function buildLabel($data) {
		// For convenience
		extract($data);
		
		return '<label for="'.$this->escape($id).$i.'">'.$this->buildInput($data).$item->label.'</label>';
	}
	
	public function buildItem($data) {
		// Responsive - <label><input></label>
		return $this->buildLabel($data);
	}
	
	public function setFlow() {
		$flow = $this->getProperty('FLOW', 'HORIZONTAL');
		if ($flow != 'HORIZONTAL') {
			$this->start = '<p class="rsformVerticalClear">';
			$this->end 	 = '</p>';
			$this->glue  = '</p><p class="rsformVerticalClear">';
			
			$this->blocks = array('1' => 'rsformgrid12', '2' => 'rsformgrid6', '3' => 'rsformgrid4', '4' => 'rsformgrid3', '6' => 'rsformgrid2');
			$this->splitterStart = '<div class="{block_size}">';
			$this->splitterEnd = '</div>';
		}
	}
}