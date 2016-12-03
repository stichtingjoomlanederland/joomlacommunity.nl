<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/radiogroup.php';

class RSFormProFieldFoundationRadioGroup extends RSFormProFieldRadioGroup
{
	
	public function setFlow() {
		$flow = $this->getProperty('FLOW', 'HORIZONTAL');
		
		if ($flow != 'HORIZONTAL') {
			$this->glue = '<br />';
			$this->blocks = array('1' => 'medium-12 columns end', '2' => 'medium-6 columns end', '3' => 'medium-4 columns end', '4' => 'medium-3 columns end', '6' => 'medium-2 columns end');
			$this->gridStart = '<div class="row">';
			$this->gridEnd = '</div>';
			$this->splitterStart = '<div class="{block_size}">';
			$this->splitterEnd = '</div>';
		}
	}
}