<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');
class JFormFieldFont extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'Font';
	
	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getOptions() {
		$path = JPATH_SITE.'/components/com_rseventspro/helpers/pdf/dompdf/lib/fonts/';
		
		$options 	= array();
		$options[] = JHTML::_('select.option', 'dejavu sans', JText::_('DejaVu Sans (Unicode)'), 'value', 'text', !file_exists($path.'DejaVuSans.ufm'));
		$options[] = JHTML::_('select.option', 'fireflysung', JText::_('Firefly (Unicode)'), 'value', 'text', !file_exists($path.'fireflysung.ufm'));
		// get fonts
		$options[] = JHTML::_('select.option', 'courier', JText::_('Courier'));
		$options[] = JHTML::_('select.option', 'helvetica', JText::_('Helvetica'));
		$options[] = JHTML::_('select.option', 'times', JText::_('Times Roman'));
		
		return $options;
	}
}