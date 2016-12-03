<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/button.php';

class RSFormProFieldImageButton extends RSFormProFieldButton
{
	// backend preview
	public function getPreviewInput() {
		$caption 	= $this->getProperty('CAPTION', '');
		$reset		= $this->getProperty('RESET', 'NO');
		$src		= $this->getProperty('IMAGEBUTTON', '');
		$srcReset	= $this->getProperty('IMAGERESET', '');
		
		$html ='<td>'.$caption.'</td>';
		$html.='<td>';
		$html.='<input type="image" src="'.$this->escape($src).'"/>';
		if($reset) {
			$html.='&nbsp;&nbsp;<input type="image" src="'.$this->escape($srcReset).'"/>';
		}
		$html.='</td>';
		
		return $html;
	}
	
	// functions used for rendering in front view
	public function getFormInput() {
		// Change the base CSS class
		// Each button type (button, submit, image) needs a different class
		$this->baseClass = 'rsform-image-button';
		
		$name		= $this->getName();
		$id			= $this->getId();
		$label		= $this->getProperty('LABEL', '');
		$reset		= $this->getProperty('RESET', 'NO');
		$src		= $this->getProperty('IMAGEBUTTON', '');
		$attr		= $this->getAttributes('button');
		$type 		= 'image';
		$additional = '';
		$html 		= '';
		
		// Handle pages
		$html .= $this->getPreviousButton();
		
		// Start building the HTML input
		$html .= '<input';
		
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
		// Set the src
		$html .= ' src="'.$this->escape($src).'"';
		// Name & id
		$html .= ' name="'.$this->escape($name).'"'.
				 ' id="'.$this->escape($id).'"';
		// Additional HTML
		$html .= $additional;
		// Add the label & close the tag
		$html .= ' value="'.$this->escape($label).'" />';
		
		// Do we need to append a reset button?
		if ($reset) {
			$label	 	 = $this->getProperty('RESETLABEL', '');
			$src		 = $this->getProperty('IMAGERESET', '');
			$attr	 	 = $this->getAttributes('reset');
			$additional  = '';
			$html 		.= ' ';
			
			// Parse Additional Attributes
			if ($attr) {
				foreach ($attr as $key => $values) {
					$additional .= $this->attributeToHtml($key, $values);
				}
			}
			
			// Start building the HTML input for the reset button
			$html .= '<input';
			// Set the type
			$html .= ' type="image"';
			// Set the src
			$html .= ' src="'.$this->escape($src).'"';
			// Clicking the Reset Button will reset the form
			$html .= ' onclick="rsfp_getForm('.$this->formId.').reset();return false;"';
			// Additional HTML
			$html .= $additional;
			// Add the label & close the tag
			$html .= ' value="'.$this->escape($label).'" />';
		}
		
		return $html;
	}
}