<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldRSEvents extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'RSEvents';

	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getOptions() {
		if (!class_exists('rseventsproHelper')) {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php';
		}
		
		$default = array(JHtml::_('select.option', '', JText::_('COM_RSEVENTSPRO_PLEASE_SELECT_EVENT')));
		return array_merge($default, rseventsproHelper::getEvents(true));
	}
}