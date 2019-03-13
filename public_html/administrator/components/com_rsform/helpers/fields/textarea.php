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
		$count 			= $this->getProperty('SHOW_CHAR_COUNT', 'NO');
		$maxlength		= (int) $this->getProperty('MAXSIZE', 0);
		$attr			= $this->getAttributes();
		$additional 	= '';
		
		if ($editor) {
			$this->addScriptDeclaration('RSFormPro.Editors['.json_encode($name).'] = function() { try { return '.$this->getEditor()->getContent($id).' } catch (e) {} };');

			return $this->getEditor()->display($name, $this->escape($value), $cols*10, $rows*10, $cols, $rows, $this->getProperty('WYSIWYGBUTTONS', 'NO'), $id, null, null,
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

		if ($maxlength)
		{
			$html .= ' maxlength="' . $this->escape($maxlength) . '"';
		}
		if ($count)
		{
			$html .= ' oninput="RSFormPro.showCounter(this, ' . $this->componentId . ')"';
		}
		// Additional HTML
		$html .= $additional;
		$html .= '>';
		
		// Add the value
		$html .= $this->escape($value);
		
		// Close the tag
		$html .= '</textarea>';
		
		if ($count)
		{
			$this->addCounter($html, $maxlength, $this->countString($value));
		}
		
		return $html;
	}

	protected function countString($value)
    {
        if (function_exists('mb_strlen'))
        {
            return mb_strlen($value, 'UTF-8');
        }
        elseif (function_exists('utf8_decode'))
        {
            return strlen(utf8_decode($value));
        }
        else
        {
            return strlen($value);
        }
    }
	
	protected function addCounter(&$html, $maxlength = 0, $start = 0)
	{
		$html .= '<p id="rsfp-counter-' . $this->componentId . '">' . $start . ($maxlength > 0 ? '/' . $maxlength : '') . '</p>';
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
		
		return $attr;
	}
}