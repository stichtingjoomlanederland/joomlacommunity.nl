<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Platform.
 * Implements a combo box field.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldRSUsers extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'RSUsers';
	
	public function __construct($parent = null) {
		parent::__construct($parent);
		
		// Build the script.
		$script   = array();
		$script[] = 'function jSelectUser_jform_jusers(id, name) {';
		$script[] = "\t".'if (id == \'\') {';
		$script[] = "\t\t".'SqueezeBox.close();';
		$script[] = "\t\t".'return;';
		$script[] = "\t".'}';
		$script[] = "\n";
		$script[] = "\t".'var values = $(\'jform_jusers\').options;';
		$script[] = "\t".'var array = new Array();';
		$script[] = "\t".'var j = 0;';
		$script[] = "\t".'for (i=0; i < values.length; i++ ) {';
		$script[] = "\t\t".'array[j] = values[i].value;';
		$script[] = "\t\t".'j++;';
		$script[] = "\t".'}';
		$script[] = "\n";
		$script[] = "\t".'if (array.contains(id)) {';
		$script[] = "\t\t".'alert(\''.JText::_('COM_RSEVENTSPRO_USER_ALREADY_EXISTS',true).'\');';
		$script[] = "\t\t".'return;';
		$script[] = "\t".'}';
		$script[] = "\n";
		$script[] = "\t".'var option = new Option(name,id);';
		$script[] = "\t".'option.setAttribute(\'selected\',\'selected\');';
		$script[] = "\t".'$(\'jform_jusers\').add(option, null);'; 
		$script[] = rseventsproHelper::isJ3() ? "\t".'jQuery(\'#jform_jusers\').trigger("liszt:updated");' : "\t".'$(\'jform_jusers\').fireEvent("liszt:updated");';
		$script[] = "\t".'SqueezeBox.close();';
		$script[] = '}';
		$script[] = "\n";
		$script[] = 'function removeusers() {';
		$script[] = "\t".'var select = $(\'jform_jusers\');';
		$script[] = "\t".'for (i = select.length - 1; i >= 0; i--)';
		$script[] = "\t\t".'select[i].destroy();';
		$script[] = rseventsproHelper::isJ3() ? "\t".'jQuery(\'#jform_jusers\').trigger("liszt:updated");' : "\t".'$(\'jform_jusers\').fireEvent("liszt:updated");';
		$script[] = '}';
		
		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
	}

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
			
			// Get the options
			$query->clear();
			$query->select($db->qn('id','value'))->select($db->qn('name','text'));
			$query->from($db->qn('#__users'));
			$query->where($db->qn('id').' IN ('.implode(',',$users).')');
			
			$db->setQuery($query);
			$options = $db->loadObjectList();
		}
		
		return $options;
	}
}