<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';

class RSFormProFieldTextarea extends RSFormProField
{
	// backend preview
	public function getPreviewInput() {
		$value 		 = (string) $this->getProperty('DEFAULTVALUE', '');
		$caption 	 = $this->getProperty('CAPTION','');
		$size 		 = $this->getProperty('SIZE', 0);
		$rows 		 = $this->getProperty('ROWS', 5);
		$cols  		 = $this->getProperty('COLS', 50);
		$placeholder = $this->getProperty('PLACEHOLDER', '');
		$codeIcon 	 = '';
		
		if ($this->hasCode($value)) {
			$value 		= JText::_('RSFP_PHP_CODE_PLACEHOLDER');
			$codeIcon	= RSFormProHelper::getIcon('php');
		}
		
		$html = '<td>'.$caption.'</td>';
		$html .= '<td>'.$codeIcon.'<textarea cols="'.(int) $cols.'" rows="'.(int) $rows.'" '.(!empty($placeholder) ? 'placeholder="'.$this->escape($placeholder).'"' : '').'>'.$this->escape($value).'</textarea></td>';
		
		return $html;
	}
	
	// functions used for rendering in front view
	protected function getEditor() {
		jimport('joomla.html.editor');
		
		static $editor = null;
		
		if (is_null($editor)) {
			$conf 	= JFactory::getConfig();
			$editor = $conf->get('editor');
		}
		
		return JEditor::getInstance($editor);
	}
	
	public function getFormInput() {
		$value 			= (string) $this->getValue();
		$name			= $this->getName();
		$id				= $this->getId();
		$cols  			= $this->getProperty('COLS', 50);
		$rows 			= $this->getProperty('ROWS', 5);
		$editor 		= $this->getProperty('WYSIWYG', 'NO');
		$placeholder 	= $this->getProperty('PLACEHOLDER', '');
		$attr			= $this->getAttributes();
		$additional 	= '';
		
		if ($editor) {
			$this->addScriptDeclaration('RSFormPro.Editors['.json_encode($name).'] = function() { try { return '.$this->getEditor()->getContent($name).' } catch (e) {} };');

			return $this->getEditor()->display($name, $this->escape($value), $cols*10, $rows*10, $cols, $rows, true, $id, null, null,
				array('relative_urls' => '0',
				'cleanup_save' => '0',
				'cleanup_startup' => '0',
				'cleanup_entities' => '0')
			);
		}
		
		// Start building the HTML input
		$html = '<textarea';
		// Parse Additional Attributes
		if ($attr) {
			foreach ($attr as $key => $values) {
				// @new feature - Some HTML attributes (type, size, maxlength) can be overwritten
				// directly from the Additional Attributes area
				if (($key == 'cols' || $key == 'rows') && strlen($values)) {
					${$key} = $values;
					continue;
				}
				$additional .= $this->attributeToHtml($key, $values);
			}
		}
		if ($cols) {
			$html .= ' cols="'.(int) $cols.'"';
		}
		if ($rows) {
			$html .= ' rows="'.(int) $rows.'"';
		}
		// Placeholder
		if (!empty($placeholder)) {
			$html .= ' placeholder="'.$this->escape($placeholder).'"';
		}
		// Name & id
		$html .= ' name="'.$this->escape($name).'"'.
				 ' id="'.$this->escape($id).'"';
		// Additional HTML
		$html .= $additional;
		$html .= '>';
		
		// Add the value
		$html .= $this->escape($value);
		
		// Close the tag
		$html .= '</textarea>';
		
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
	
	// @desc All textboxes should have a 'rsform-text-box' class for easy styling
	public function getAttributes() {
		$attr = parent::getAttributes();
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		$attr['class'] .= 'rsform-text-box';
		
		// Check for invalid here so that we can add 'rsform-error'
		if ($this->invalid) {
			$attr['class'] .= ' rsform-error';
		}
		
		return $attr;
	}
}