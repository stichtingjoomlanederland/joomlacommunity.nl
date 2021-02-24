<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');
class JFormFieldGroups extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'Groups';
	
	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getOptions() {
		static $groups;
		
		if (!isset($groups)) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			
			$query->clear()
				->select($db->qn('id','value'))->select($db->qn('name','text'))
				->from($db->qn('#__rseventspro_groups'));
			
			$db->setQuery($query);
			$groups = $db->loadObjectList();
		}
		
		return $groups;
	}
}