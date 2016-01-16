<?php
/**
 * @version		$Id: bannerclient.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Bannerclient Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class JFormFieldTables extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Tables';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	public function getOptions()
	{
		// Initialize variables.
		$options = array();

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select("id As value, CONCAT(extension_name,', ',name) As text");
		$query->from('#__spupgrade_tables AS a');
		$query->order('a.id');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}                

		// Merge any additional options in the XML definition.
		//$options = array_merge(parent::getOptions(), $options);

		//array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_SPUPGRADE_NO_TABLES')));

		return $options;
	}
        
        public function getStates() {
		// Initialize variables.
		$states = array();

                for ($i=1;$i<=4;$i++) {
                    $states[$i]['value'] = $i;
                    $states[$i]['text'] = JText::_('COM_SPUPGRADE_STATE_'.$i);
                }
		
		return $states;
	}
}
