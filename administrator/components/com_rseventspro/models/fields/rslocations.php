<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');
class JFormFieldRSLocations extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'RSLocations';

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
		
		$default	= array((object) array('value' => 0, 'text' => JText::_('COM_RSEVENTSPRO_CONF_GOOGLE_NEW_LOCATION')));
		$locations	= rseventsproHelper::getLocations();
		
		if (!isset($this->element['show_root']))
			return array_merge($default, $locations);
		
		return $locations;
	}
}