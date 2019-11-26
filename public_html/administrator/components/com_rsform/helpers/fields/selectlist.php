<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/prices.php';
require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/fielditem.php';
require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fieldmultiple.php';

class RSFormProFieldSelectList extends RSFormProFieldMultiple
{	
	// backend preview
	public function getPreviewInput()
	{
		$size  		= $this->getProperty('SIZE', 0);
		$multiple 	= $this->getProperty('MULTIPLE', 'NO');
		
		// Start building the HTML input
		$selectInput = '<select';
		
		// Multiple selectable items?
		if ($multiple) {
			$selectInput .= ' multiple="multiple"';
		}
		
		// Size
		if ($size) {
			$selectInput .= ' size="'.(int) $size.'"';
		}
		
		$selectInput .= '>';
		
		// Add the items
		if ($items = $this->getItems()) {
			foreach ($items as $item) {
				$item = new RSFormProFieldItem($item);
				if ($item->flags['optgroup']) {
					$selectInput .= '<optgroup label="'.$this->escape($item->label).'">';
				} elseif ($item->flags['/optgroup']) {
					$selectInput .= '</optgroup>';
				} else {
					// Start tag
					$selectInput .= '<option';
					// Disabled
					if ($item->flags['disabled']) {
						$selectInput .= ' disabled="disabled"';
					}
					// Checked
					if ($item->value === $this->getItemValue($item)) {
						$selectInput .= ' selected="selected"';
					}
					// Add value
					$selectInput .= '>';
					// Show label
					$selectInput .= $this->escape($item->label);
					// Close tag
					$selectInput .= '</option>';
				}
			}
		}

		// Close the tag
		$selectInput .= '</select>';

		return $this->codeIcon . $selectInput;
	}
	
	// functions used for rendering in front view
	public function getFormInput() {
		$name		= $this->getName();
		$id			= $this->getId();
		$size  		= $this->getProperty('SIZE', 0);
		$multiple 	= $this->getProperty('MULTIPLE', 'NO');
		$attr		= $this->getAttributes();
		$additional = '';
		
		// Get the price instance, if we need it
		$prices = RSFormProPrices::getInstance($this->formId);
		
		// Start building the HTML input
		$html = '<select';
		
		// Multiple selectable items?
		if ($multiple) {
			$html .= ' multiple="multiple"';
		}
		
		// Parse Additional Attributes
		if ($attr) {
			foreach ($attr as $key => $values) {
				// @new feature - Some HTML attributes (type, size, maxlength) can be overwritten
				// directly from the Additional Attributes area
				if ($key == 'size' && strlen($values)) {
					${$key} = $values;
					continue;
				}
				$additional .= $this->attributeToHtml($key, $values);
			}
		}
		
		// Name
		$html .= ' name="'.$this->escape($name).'"';
		// Size
		if ($size) {
			$html .= ' size="'.(int) $size.'"';
		}
		// Id
		$html .= ' id="'.$this->escape($id).'"';
		// Additional HTML
		$html .= $additional;
		$html .= '>';
		
		// Add the items
		if ($items = $this->getItems()) {
			foreach ($items as $item) {
				$item = new RSFormProFieldItem($item);
				if ($item->flags['optgroup']) {
					$html .= '<optgroup label="'.$this->escape($item->label).'">';
				} elseif ($item->flags['/optgroup']) {
					$html .= '</optgroup>';
				} else {
					// Start tag
					$html .= '<option';
					// Disabled
					if ($item->flags['disabled']) {
						$html .= ' disabled="disabled"';
					}
					// Checked
					if ($item->value === $this->getItemValue($item)) {
						$html .= ' selected="selected"';
					}
					// Add value
					$html .= ' value="'.$this->escape($item->value).'">';
					// Show label
					$html .= $this->escape($item->label);
					// Close tag
					$html .= '</option>';
					
					if ($item->flags['price'] !== false) {
						$prices->addPrice($id, $item->value, $item->flags['price']);
					}
				}
			}
		}
		
		// Close the tag
		$html .= '</select>';
		
		return $html;
	}
	
	
	// @desc All select lists should have a 'rsform-select-box' class for easy styling
	public function getAttributes() {
		$attr = parent::getAttributes();
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		$attr['class'] .= 'rsform-select-box';
		
		return $attr;
	}
}