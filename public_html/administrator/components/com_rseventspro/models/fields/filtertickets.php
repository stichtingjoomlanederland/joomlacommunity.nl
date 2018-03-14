<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldFiltertickets extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'Filtertickets';

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
		
		$id		= JFactory::getApplication()->getUserStateFromRequest('com_rseventspro.subscriptions.filter.event', 'filter_event', '', 'string');
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('id','value'))->select($db->qn('name','text'))
			->from($db->qn('#__rseventspro_tickets'))
			->where($db->qn('ide').' = '.(int) $id)
			->order($db->qn('order').' ASC');
			
		$db->setQuery($query);
		$tickets = $db->loadObjectList();
		
		$options = parent::getOptions();
		return array_merge($options, $tickets);
	}
}