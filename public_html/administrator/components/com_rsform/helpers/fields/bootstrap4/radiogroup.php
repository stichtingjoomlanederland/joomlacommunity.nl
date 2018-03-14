<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/radiogroup.php';

class RSFormProFieldBootstrap4RadioGroup extends RSFormProFieldRadioGroup
{
	protected function buildLabel($data) {
		// For convenience
		extract($data);
		
		return '<div class="form-check'.($flow == 'HORIZONTAL' ? ' form-check-inline' : '').'">'.'<label for="'.$this->escape($id).$i.'" class="form-check-label">'.$this->buildInput($data).$item->label.'</label>'.'</div>';
	}
	
	public function buildItem($data) {
		// BS4 - <label><input></label>
		return $this->buildLabel($data);
	}
	
	public function setFlow() {
		$flow = $this->getProperty('FLOW', 'HORIZONTAL');
			
		if ($flow != 'HORIZONTAL') {
			$this->blocks = array('1' => 'col-sm-12', '2' => 'col-sm-6', '3' => 'col-sm-4', '4' => 'col-sm-3', '6' => 'col-sm-2');
			$this->gridStart = '<div class="row">';
			$this->gridEnd = '</div>';
			$this->splitterStart = '<div class="{block_size}">';
			$this->splitterEnd = '</div>';
		}
	}

    public function getAttributes() {
        $attr = parent::getAttributes();
        if (strlen($attr['class'])) {
            $attr['class'] .= ' ';
        }
        $attr['class'] .= 'form-check-input';

        return $attr;
    }
}