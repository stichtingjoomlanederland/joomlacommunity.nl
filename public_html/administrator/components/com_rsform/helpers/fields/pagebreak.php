<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';

class RSFormProFieldPageBreak extends RSFormProField
{
	// backend preview
	public function getPreviewInput() {
		$componentId = $this->getProperty('componentId');
		$pages		 = $this->getProperty('PAGES');
		
		$totalPages  = count($pages);
		$position	 = array_search($componentId, $pages);
		
		$labelPrev 		= $this->getProperty('PREVBUTTON', JText::_('JPREV'));
		$labelNext		= $this->getProperty('NEXTBUTTON', JText::_('JNEXT'));
		
		$html = '<td>&nbsp;</td>';
		$html.= '<td>'.($position > 0 ? '<input type="button" class="btn btn-warning" value="'.$this->escape($labelPrev).'" />' : '').' <input type="button" class="btn btn-success" value="'.$this->escape($labelNext).'" /></td>';
		
		return $html;
	}
	
	// functions used for rendering in front view
	public function getFormInput() {
		$validate 	 = (int) $this->getProperty('VALIDATENEXTPAGE', 'NO');
		$buttonType  = $this->getProperty('BUTTONTYPE', 'TYPEINPUT') == 'TYPEBUTTON' ? 'button' : 'input';
		$componentId = $this->getProperty('componentId');
		$id			 = $this->getId();
		$formId		 = $this->formId;
		$html 		 = '';
		
		$pages		= $this->getProperty('PAGES', array());
		$totalPages = count($pages);
		$position	= array_search($componentId, $pages);
		
		// Build previous button if it's not the first pagebreak.
		if ($position > 0) {
			$label 		= $this->getProperty('PREVBUTTON', JText::_('JPREV'));
			$attr		= $this->getAttributes('prev');
			$additional = '';
			$prevPage	= $position - 1;
			
			// Parse Additional Attributes
			if ($attr) {
				if (strlen($attr['onclick'])) {
					$attr['onclick'] .= ';';
				}
				
				$attr['onclick'] .= "rsfp_changePage($formId, $prevPage, $totalPages)";
				foreach ($attr as $key => $values) {
					$additional .= $this->attributeToHtml($key, $values);
				}
			}
			
			// Start building the HTML input
			if ($buttonType == 'button') {
				$html .= '<button';
			} else {
				$html .= '<input';
			}
			// Set the type
			$html .= ' type="button"';
			// Id
			$html .= ' id="'.$this->escape($id).'Prev"';
			// Additional HTML
			$html .= $additional;
			// Add the label & close the tag
			if ($buttonType == 'button') {
				$html .= ' >'.$this->escape($label).'</button>';
			} else {
				$html .= ' value="'.$this->escape($label).'" />';
			}
		}
		
		// Build next button if it's not the last pagebreak.
		if ($position < $totalPages) {
			$label 		= $this->getProperty('NEXTBUTTON', JText::_('JNEXT'));
			$attr		= $this->getAttributes('next');
			$additional = '';
			$nextPage	= $position + 1;
			
			// Parse Additional Attributes
			if ($attr) {
				if (isset($attr['onclick']) && strlen($attr['onclick'])) {
					$attr['onclick'] .= ';';
				} else {
					$attr['onclick'] = '';
				}

                $validationParams = array(
                    'parent' => $this->errorClass,
                    'field'  => $this->fieldErrorClass
                );

				$attr['onclick'] .= "rsfp_changePage($formId, $nextPage, $totalPages, $validate, " . json_encode($validationParams) . ")";
				foreach ($attr as $key => $values) {
					$additional .= $this->attributeToHtml($key, $values);
				}
			}
			
			// Start building the HTML input
			if ($buttonType == 'button') {
				$html .= '<button';
			} else {
				$html .= '<input';
			}
			// Set the type
			$html .= ' type="button"';
			// Id
			$html .= ' id="'.$this->escape($id).'Next"';
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
	
	// @desc All page breaks should have a 'rsform-button' class for easy styling
	//		 onclick should also be present for convenience.
	public function getAttributes($action = null) {
		$attr = parent::getAttributes();
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		if (!isset($attr['onclick'])) {
			$attr['onclick'] = '';
		}
		if (!is_null($action)) {
			$attr['class'] .= ($action == 'prev' ? 'rsform-button-prev' : 'rsform-button-next').' ';
		}
		$attr['class'] .= 'rsform-button';
		
		return $attr;
	}
}