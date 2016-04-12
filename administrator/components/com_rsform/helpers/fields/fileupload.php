<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';

class RSFormProFieldFileUpload extends RSFormProField
{
	// backend preview
	public function getPreviewInput() {
		
		$caption	 = $this->getProperty('CAPTION', '');
		$html = '<td>'.$caption.'</td><td><input type="file" /></td>';
		return $html;
	}
	
	// functions used for rendering in front view
	public function getFormInput() {
		$name			= $this->getName();
		$id				= $this->getId();
		$attr			= $this->getAttributes();
		$type 			= 'file';
		$additional 	= '';
		
		// MAX_FILE_SIZE is no longer used, didn't provide anything useful
		
		// Start building the HTML input
		$html = '<input';
		// Parse Additional Attributes
		if ($attr) {
			foreach ($attr as $key => $values) {
				// @new feature - Some HTML attributes (type) can be overwritten
				// directly from the Additional Attributes area
				if ($key == 'type' && strlen($values)) {
					${$key} = $values;
					continue;
				}
				$additional .= $this->attributeToHtml($key, $values);
			}
		}
		// Set the type
		$html .= ' type="'.$this->escape($type).'"';
		// Name & id
		$html .= ' name="'.$this->escape($name).'"'.
				 ' id="'.$this->escape($id).'"';
		// Additional HTML
		$html .= $additional;
		// Close the tag
		$html .= ' />';
		
		return $html;
	}
	
	// @desc All upload fields should have a 'rsform-upload-box' class for easy styling
	public function getAttributes() {
		$attr = parent::getAttributes();
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		$attr['class'] .= 'rsform-upload-box';
		
		// Check for invalid here so that we can add 'rsform-error'
		if ($this->invalid) {
			$attr['class'] .= ' rsform-error';
		}
		
		return $attr;
	}
}