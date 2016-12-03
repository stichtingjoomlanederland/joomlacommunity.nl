<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/radiogroup.php';

class RSFormProFieldBootstrap2RadioGroup extends RSFormProFieldRadioGroup
{
	protected function buildLabel($data) {
		// For convenience
		extract($data);
		
		return '<label for="'.$this->escape($id).$i.'" class="radio'.($flow == 'HORIZONTAL' ? ' inline' : '').'">'.$this->buildInput($data).$item->label.'</label>';
	}
	
	public function buildItem($data) {
		// BS2 - <label><input></label>
		return $this->buildLabel($data);
	}
	
	public function setFlow() {
		$this->glue = '';
		
		$flow = $this->getProperty('FLOW', 'HORIZONTAL');
			
		if ($flow != 'HORIZONTAL') {
			$this->blocks = array('1' => 'span12', '2' => 'span6', '3' => 'span4', '4' => 'span3', '6' => 'span2');
			$this->splitterStart = '<div class="{block_size}">';
			$this->splitterEnd = '</div>';
		}
	}
}