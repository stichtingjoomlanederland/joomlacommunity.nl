<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';

class RSFormProFieldTextbox extends RSFormProField
{
	// backend preview
	public function getPreviewInput() {
		$value 		 = (string) $this->getProperty('DEFAULTVALUE', '');
		$caption 	 = $this->getProperty('CAPTION','');
		$size 		 = $this->getProperty('SIZE', 0);
		$placeholder = $this->getProperty('PLACEHOLDER', '');
		$codeIcon 	 = '';
		
		if ($this->hasCode($value)) {
			$value 		= JText::_('RSFP_PHP_CODE_PLACEHOLDER');
			$codeIcon	= RSFormProHelper::getIcon('php');
		}
		
		$html = '<td>'.$caption.'</td>';
		$html .= '<td>'.$codeIcon.'<input type="text" value="'.$this->escape($value).'" size="'.(int) $size.'" '.(!empty($placeholder) ? 'placeholder="'.$this->escape($placeholder).'"' : '').'/></td>';
		
		return $html;
	}
	
	// functions used for rendering in front view
	public function getFormInput() {
		$value 			= (string) $this->getValue();
		$name 			= $this->getName();
		$id 			= $this->getId();
		$size 			= $this->getProperty('SIZE', 0);
		$maxlength 		= $this->getProperty('MAXSIZE', 0);
		$placeholder 	= $this->getProperty('PLACEHOLDER', '');
		$type 			= $this->getProperty('INPUTTYPE', 'text');
		$attr 			= $this->getAttributes();
		$additional 	= '';
		
		
		$html = '<input';
		if ($attr) {
			foreach ($attr as $key => $values) {
				// @new feature - Some HTML attributes (type, size, maxlength) can be overwritten
				// directly from the Additional Attributes area
				if (($key == 'type' || $key == 'size' || $key == 'maxlength') && strlen($values)) {
					${$key} = $values;
					continue;
				}
				$additional .= $this->attributeToHtml($key, $values);
			}
		}
		// Set the type & value
		$html .= ' type="'.$this->escape($type).'"'.
				 ' value="'.$this->escape($value).'"';
		// Size
		if ($size) {
			$html .= ' size="'.(int) $size.'"';
		}
		// Maxlength
		if ($maxlength && in_array($type, array('text', 'email', 'tel', 'url'))) {
			$html .= ' maxlength="'.(int) $maxlength.'"';
		}
		
		// Placeholder
		if (!empty($placeholder)) {
			$html .= ' placeholder="'.$this->escape($placeholder).'"';
		}
		
		// Additional attributes for type="number" or type="range"
		if (in_array($type, array('number', 'range'))) {
			$min 	= $this->getProperty('ATTRMIN', '');
			$max 	= $this->getProperty('ATTRMAX', '');
			$step 	= $this->getProperty('ATTRSTEP', 1);
			
			if (strlen($min) && is_float((float) $min)) {
				$html .= ' min="'.$this->escape((float) $min).'"';
			}
			
			if (strlen($max) && is_float((float) $max)) {
				$html .= ' max="'.$this->escape((float) $max).'"';
			}
			
			if (strlen($step) && is_float((float) $step)) {
				$html .= ' step="'.$this->escape((float) $step).'"';
			}
		}
		
		// Name & id
		$html .= ' name="'.$this->escape($name).'"'.
				 ' id="'.$this->escape($id).'"';
		// Additional HTML
		$html .= $additional;
		// Close the tag
		$html .= ' />';
		
		return $html;
	}
	
	public function getValue() {
		$rule = $this->getProperty('VALIDATIONRULE', 'none');
		if ($rule == 'password') {
			return '';
		}
		
		return parent::getValue();
	}
	
	public function getAttributes() {
		$attr = parent::getAttributes();
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		$attr['class'] .= 'rsform-input-box';
		
		return $attr;
	}
}