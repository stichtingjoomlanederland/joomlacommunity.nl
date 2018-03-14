<?php
/**
* @package RSJoomla! Adapter
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

/**
 * Utility class for Fieldset elements.
 *
 * @package     RSJoomla!
 */
abstract class JHtmlRSFieldset {
	
	/**
	 * Creates the begining of the fieldset
	 *
	 * @param   string  $class   The class identifier.
	 * @legend  string  $legend  The legend.
	 *
	 * @return  string
	 */
	public static function start($class = 'adminform', $legend = null) {
		$html	= array();
		$html[] = '<fieldset class="' . $class . '">';
		
		if ($legend) {
			$html[] = "\t".'<legend>' . $legend . '</legend>';
		}
		
		$html[] = "\t".'<ul class="adminformlist">';
		
		return implode("\n",$html);
	}

	/**
	 * Close the current fieldset
	 *
	 * @return  string  HTML to close the pane
	 */
	public static function end() {
		$html	= array();
		$html[]	= "\t".'</ul>';
		$html[]	= '</fieldset>';
		$html[]	= '<div class="clr"></div>';

		return implode("\n",$html);
	}
	
	/**
	 * Begins the display of the field.
	 *
	 * @label   string  $label  The elements label.
	 * @label   string  $input  The elements input.
	 *
	 * @return  string  HTML to start a fieldset element
	 */
	public static function element($label, $input, $id = null) {
		$id = $id ? ' id="'.$id.'"' : '';
		
		$html   = array();
		$html[] = "\t\t".'<li'.$id.'>';
		$html[] = "\t\t\t".$label.$input;
		$html[] = "\t\t".'</li>';
		
		return implode("\n",$html);
	}
}