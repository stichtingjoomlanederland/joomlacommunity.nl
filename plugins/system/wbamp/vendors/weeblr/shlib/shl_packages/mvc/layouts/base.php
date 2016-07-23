<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2015
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.1.540
 * @date        2016-07-18
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die;

/**
 * Base class for rendering a display layout
 *
 * @since       0.2.1
 */
class ShlMvcLayout_Base implements ShlMvcLayout
{

	/**
	 * Method to render the layout.
	 *
	 * @param   object $displayData Object which properties are used inside the layout file to build displayed output
	 *
	 * @return  string  The necessary HTML to display the layout
	 *
	 * @since   0.2.1
	 */
	public function render($displayData)
	{
		return '';
	}

	/**
	 * Method to escape output.
	 *
	 * @param   string $output The output to escape.
	 *
	 * @return  string  The escaped output.
	 *
	 * @since   0.2.1
	 */
	protected function escape($output, $flags = ENT_COMPAT, $charset = 'UTF-8')
	{
		return htmlspecialchars($output, $flags, $charset);
	}
}
