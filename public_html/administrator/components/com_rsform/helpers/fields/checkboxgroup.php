<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/prices.php';
require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/fielditem.php';
require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fieldmultiple.php';

class RSFormProFieldCheckboxGroup extends RSFormProFieldMultiple
{
	protected $glue = '';
	protected $start = '';
	protected $end = '';
	
	protected $gridStart = '';
	protected $gridEnd = '';
	protected $splitterStart = '<div style="float:left; width:{block_size}%">';
	protected $splitterEnd = '</div>';
	protected $blocks = array('1' => '100', '2' => '50', '3' => '33.33333', '4' => '25', '6' => '16.66666');
	protected $columns = array('VERTICAL2COLUMNS' => 2, 'VERTICAL3COlUMNS' => 3, 'VERTICAL4COLUMNS' => 4, 'VERTICAL6COLUMNS' => 6);
	
	// backend preview
	public function getPreviewInput() {
		$id			= $this->getId();
		$flow		= $this->getProperty('FLOW', 'HORIZONTAL');
		$caption 	= $this->getProperty('CAPTION','');
		
		// Add the items
		$parsed = array();
		$i	    = 0;
		
		$data =  array(
			'id' 	=> $id,
			'flow' 	=> $flow,
		);
		
		if ($items  = $this->getItems()) {
			foreach ($items as $item) {
				$item = new RSFormProFieldItem($item);
				
				$data['value'] 	= $this->getItemValue($item);
				$data['i'] 		= $i;
				$data['item'] 	= $item;
				
				$parsed[] = $this->buildItem($data);
				$i++;
			}
		}
		
		$checkboxgroup = '';
		if ($flow != 'HORIZONTAL' && $flow != 'VERTICAL') {
			$columns = (int) $this->columns[$flow];
			$splits = $this->splitItems($parsed, $columns);
			$blocks = array('1' => 'span12', '2' => 'span6', '3' => 'span4', '4' => 'span3', '6' => 'span2');
			if ($columns > 1) {
				foreach ($splits as $block) {
					$checkboxgroup .= '<div class="'.$blocks[$columns].'">';
					$checkboxgroup .= $this->start.implode('', $block).$this->end;
					$checkboxgroup .= '</div>';
				}
			} else {
				$checkboxgroup .= $this->start.implode('', $splits[0]).$this->end;
			}
		} else {
			$checkboxgroup .= $this->start.implode('', $parsed).$this->end;
		}
		
		$html = '<td>'.$caption.'</td><td class="controls formControls preview-checkbox'.($flow == 'HORIZONTAL' ? '-inline' : '').'">'.$this->codeIcon.$checkboxgroup.'</td>';
		
		return $html;
	}
	
	// functions used for rendering in front view
	
	public function getFormInput() {
		$name		= $this->getName();
		$id			= $this->getId();
		
		$attr		= $this->getAttributes();
		$additional = '';
		$flow		= $this->getProperty('FLOW', 'HORIZONTAL');
		// Get the price instance, if we need it
		$prices = RSFormProPrices::getInstance($this->formId);
		
		// Parse Additional Attributes
		if ($attr) {
			foreach ($attr as $key => $values) {
				$additional .= $this->attributeToHtml($key, $values);
			}
		}
		
		// Add the items
		$parsed = array();
		$i	    = 0;
		
		$data =  array(
			'name' 			=> $name,
			'id' 			=> $id,
			'additional' 	=> $additional,
			'prices' 		=> $prices,
			'flow' 			=> $flow,
		);
		
		if ($items = $this->getItems()) {
			foreach ($items as $item) {
				$item = new RSFormProFieldItem($item);
				
				$data['value'] 	= $this->getItemValue($item);
				$data['i'] 		= $i;
				$data['item'] 	= $item;

				$parsed[] 		= $this->buildItem($data);
				
				if ($item->flags['price'] !== false) {
					$prices->addPrice($id, $item->value, $item->flags['price']);
				}
				
				$i++;
			}
		}
		
		$this->setFlow();
		
		$output = '';
		if ($flow != 'HORIZONTAL' && $flow != 'VERTICAL') {
			$columns = (int) $this->columns[$flow];
			$splits = $this->splitItems($parsed, $columns);
			if ($columns > 1) {
				$output .= $this->gridStart;
				foreach ($splits as $block) {
					$output .= str_replace('{block_size}', $this->blocks[$columns], $this->splitterStart);
					$output .= $this->start.implode($this->glue, $block).$this->end;
					$output .= $this->splitterEnd;
				}
				$output .= $this->gridEnd;
			} else {
				$output .= $this->start.implode('', $splits[0]).$this->end;
			}
		} else {
			$output .= $this->start.implode($this->glue, $parsed).$this->end;
		}

        if ($max = (int) $this->getProperty('MAXSELECTIONS'))
        {
            $this->addScriptDeclaration("RSFormPro.limitSelections({$this->formId}, '{$id}', {$max});");
        }
		
		return $output;
	}

	protected function buildLabel($data) {
		// For convenience
		extract($data);
		
		return '<label for="'.$this->escape($id).$i.'">'.$item->label.'</label>';
	}
	
	protected function buildInput($data) {
		// For convenience
		extract($data);
		
		$html = '<input type="checkbox" ';
		
		// Disabled
		if ($item->flags['disabled']) {
			$html .= ' disabled="disabled"';
		}
		
		// Checked
		if ($item->value === $value) {
			$html .= ' checked="checked"';
		}
		
		// Name
		if (isset($name) && strlen($name)) {
			$html .= ' name="'.$this->escape($name).'"';
		}
		
		// Value
		$html .= ' value="'.$this->escape($item->value).'"';
		
		// Id
		$html .= ' id="'.$this->escape($id).$i.'"';
		
		// Additional HTML
		if (!empty($additional)) {
			$html .= $additional;
		}
		
		$html .= ' />';
		
		return $html;
	}
	
	public function buildItem($data) {
		return $this->buildInput($data).$this->buildLabel($data);
	}
	
	public function setFlow() {
		$flow		= $this->getProperty('FLOW', 'HORIZONTAL');
		if ($flow != 'HORIZONTAL') {
			$this->glue = '<br />';
		}
	}
	
	// @desc All select lists should have a 'rsform-checkbox' class for easy styling
	public function getAttributes() {
		$attr = parent::getAttributes();
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		$attr['class'] .= 'rsform-checkbox';
		
		return $attr;
	}
}