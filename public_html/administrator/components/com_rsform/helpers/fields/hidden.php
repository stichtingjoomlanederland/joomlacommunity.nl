<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';

class RSFormProFieldHidden extends RSFormProField
{
	// backend preview
	public function getPreviewInput() {
        $value 		= (string) $this->getProperty('DEFAULTVALUE', '');
		$txt	 	= JText::_('RSFP_HIDDEN_FIELD_PLACEHOLDER');
		$codeIcon   = RSFormProHelper::getIcon('hidden');

        if ($this->hasCode($value)) {
            $txt        = JText::_('RSFP_PHP_CODE_PLACEHOLDER');
            $codeIcon   = RSFormProHelper::getIcon('php');
        }

		$html = '<td>&nbsp;</td><td>'.$codeIcon.$txt.'</td>';
		return $html;
	}
	
	// functions used for rendering in front view
	public function getFormInput() {
		$value 		= (string) $this->getValue();
		$name		= $this->getName();
		$id			= $this->getId();
		$attr		= $this->getAttributes();
		$type 		= 'hidden';
		$additional = '';
		
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
		// Value
		$html .= ' value="'.$this->escape($value).'"';
		// Additional HTML
		$html .= $additional;
		// Close the tag
		$html .= ' />';
		
		return $html;
	}
}