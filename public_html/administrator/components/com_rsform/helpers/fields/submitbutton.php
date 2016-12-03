<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/button.php';

class RSFormProFieldSubmitButton extends RSFormProFieldButton
{
	// backend preview
	public function getPreviewInput() {
		$caption 	= $this->getProperty('CAPTION', '');
		$reset		= $this->getProperty('RESET', 'NO');
		$buttonType = $this->getProperty('BUTTONTYPE', 'TYPEINPUT') == 'TYPEBUTTON' ? 'button' : 'input';
		$label		= $this->getProperty('LABEL', '');
		$resetLabel		= $this->getProperty('RESETLABEL', '');
		
		$html = '<td>'.$caption.'</td><td>';
		if ($buttonType == 'button') {
			$html .= '<button type="button" class="btn btn-primary">'.$this->escape($label).'</button>';
		} else {
			$html .= '<input type="button" class="btn btn-primary" value="'.$this->escape($label).'" />';
		}
		if ($reset) {
			if ($buttonType == 'button') {
				$html .= '&nbsp;&nbsp;<button type="reset" class="btn btn-danger">'.$this->escape($resetLabel).'</button>';
			} else {
				$html .= '&nbsp;&nbsp;<input type="reset" class="btn btn-danger" value="'.$this->escape($resetLabel).'"/>';
			}
		}
		
		$html .= '</td>';
		
		return $html;
	}
	
	// functions used for rendering in front view
	public function getFormInput() {
		// Change the base CSS class
		// Each button type (button, submit, image) needs a different class
		$this->baseClass = 'rsform-submit-button';
		
		$name		= $this->getName();
		$id			= $this->getId();
		$buttonType = $this->getProperty('BUTTONTYPE', 'TYPEINPUT') == 'TYPEBUTTON' ? 'button' : 'input';
		$label		= $this->getProperty('LABEL', '');
		$reset		= $this->getProperty('RESET', 'NO');
		$attr		= $this->getAttributes('button');
		$type 		= $this->getProperty('INPUTTYPE', 'submit');
		$additional = '';
		$html 		= '';
		
		// Handle pages
		$html .= $this->getPreviousButton();
		
		// Start building the HTML input
		if ($buttonType == 'button') {
			$html .= '<button';
		} else {
			$html .= '<input';
		}
		
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
		// Add the label & close the tag
		if ($buttonType == 'button') {
			$html .= ' >'.$this->escape($label).'</button>';
		} else {
			$html .= ' value="'.$this->escape($label).'" />';
		}
		
		// Do we need to append a reset button?
		if ($reset) {
			$label	 	 = $this->getProperty('RESETLABEL', '');
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
			if ($buttonType == 'button') {
				$html .= '<button';
			} else {
				$html .= '<input';
			}
			// Set the type
			$html .= ' type="reset"';
			// Additional HTML
			$html .= $additional;
			// Add the label & close the tag
			if ($buttonType == 'button') {
				$html .= ' >'.$this->escape($label).'</button>';
			} else {
				$html .= ' value="'.$this->escape($label).'" />';
			}
		}
		
		return $html;
	}
}