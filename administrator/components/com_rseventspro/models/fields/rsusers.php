<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldRSUsers extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'RSUsers';

	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getOptions() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$jinput = JFactory::getApplication()->input;
		$options= array();
		
		// Get the selected users
		$query->clear();
		$query->select('jusers');
		$query->from('#__rseventspro_groups');
		$query->where('id = '.$db->quote($jinput->getInt('id',0)));
		
		$db->setQuery($query);
		if ($users = $db->loadResult()) {
			$registry = new JRegistry;
			$registry->loadString($users);
			$users = $registry->toArray();
			JArrayHelper::toInteger($users);
			
			if (!empty($users)) {
				// Get the options
				$query->clear();
				$query->select($db->qn('id','value'))->select($db->qn('name','text'));
				$query->from($db->qn('#__users'));
				$query->where($db->qn('id').' IN ('.implode(',',$users).')');
				
				$db->setQuery($query);
				$options = $db->loadObjectList();
			}
		}
		
		return $options;
	}
}