<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class JFormFieldColors extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Colors';

	protected function getInput() {
		JHtml::_('behavior.colorpicker');
		
		// Initialize some field attributes.
		$size		= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : 'class="input-colorpicker"';

        $value = htmlspecialchars(html_entity_decode($this->value, ENT_QUOTES), ENT_QUOTES);
		
		$html = '<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.$value.'" '.$class.' '.$size.' style="background-color: '.$value.';" />';
		
		return $html;
	}
}