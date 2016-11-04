<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2016
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.1.587
 * @date		2016-10-31
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die;

/**
 * Interface to handle display layout
 *
 * @package     Joomla.Libraries
 * @subpackage  Layout
 * @since       3.0
 */
interface ShlMvcLayout
{

	/**
	 * Method to render the layout.
	 *
	 * @param   object  $displayData  Object which properties are used inside the layout file to build displayed output
	 *
	 * @return  string  The rendered layout.
	 *
	 * @since   3.0
	 */
	public function render($displayData);
}
