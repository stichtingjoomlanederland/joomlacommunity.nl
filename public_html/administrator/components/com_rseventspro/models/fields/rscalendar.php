<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

class JFormFieldRSCalendar extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'RSCalendar';
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput() {
		if (!class_exists('rseventsproHelper')) {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php';
		}
		if (!class_exists('JHTMLRSEventsPro')) {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/html.php';
		}
		
		// Load jQuery
		rseventsproHelper::loadjQuery();

		return JHtml::_('rseventspro.rscalendar', $this->name, $this->value);
	}
}