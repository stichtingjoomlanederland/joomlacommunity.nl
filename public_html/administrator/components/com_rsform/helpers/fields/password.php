<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';

class RSFormProFieldPassword extends RSFormProField
{
	// backend preview
	public function getPreviewInput() {
		$caption 		= $this->getProperty('CAPTION', '');
		$value 			= (string) $this->getProperty('DEFAULTVALUE', '');
		$rule 			= $this->getProperty('VALIDATIONRULE', 'none');
		$size  			= $this->getProperty('SIZE', 0);
		$placeholder 	= $this->getProperty('PLACEHOLDER', '');
		
		$codeIcon = '';
		if ($this->hasCode($value)) {
			$value 		= '';
			$codeIcon	= RSFormProHelper::getIcon('php');
		} else {
			if ($rule == 'password') {
				$value = '';
			}
		}
		
		$html = '<td>'.$caption.'</td>'.
				'<td>'.$codeIcon.'<input type="password" value="'.$this->escape($value).'" size="'.(int) $size.'" '.(!empty($placeholder) ? 'placeholder="'.$this->escape($placeholder).'"' : '').'/></td>';
		
		return $html;
	}
	
	// functions used for rendering in front view

	public function getFormInput() {
		$value 			= (string) $this->getValue();
		$name			= $this->getName();
		$id				= $this->getId();
		$size  			= $this->getProperty('SIZE', 0);
		$maxlength 		= $this->getProperty('MAXSIZE', 0);
		$placeholder 	= $this->getProperty('PLACEHOLDER', '');
		$attr			= $this->getAttributes();
		$type 			= 'password';
		$additional 	= '';
		
		// Start building the HTML input
		$html = '<input';
		// Parse Additional Attributes
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
		if ($maxlength) {
			$html .= ' maxlength="'.(int) $maxlength.'"';
		}
		// Placeholder
		if (!empty($placeholder)) {
			$html .= ' placeholder="'.$placeholder.'"';
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
	
	// @desc Overridden here because we need to make sure VALIDATIONRULE is not 'password'
	//		 Passwords shouldn't be shown as a default value
	public function getValue() {
		$rule = $this->getProperty('VALIDATIONRULE', 'none');
		if ($rule == 'password') {
			return '';
		}
		
		return parent::getValue();
	}
	
	// @desc All textboxes should have a 'rsform-password-box' class for easy styling
	public function getAttributes() {
		$attr = parent::getAttributes();
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		$attr['class'] .= 'rsform-password-box';
		
		return $attr;
	}
}