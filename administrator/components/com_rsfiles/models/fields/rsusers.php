<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldRSUsers extends JFormFieldList
{
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
		$script[] = "\t\t".'alert(\''.JText::_('COM_RSFILES_USER_ALREADY_EXISTS',true).'\');';
		$script[] = "\t\t".'return;';
		$script[] = "\t".'}';
		$script[] = "\n";
		$script[] = "\t".'var option = new Option(name,id);';
		$script[] = "\t".'option.setAttribute(\'selected\',\'selected\');';
		$script[] = "\t".'$(\'jform_jusers\').add(option, null);'; 
		$script[] = rsfilesHelper::isJ3() ? "\t".'jQuery(\'#jform_jusers\').trigger("liszt:updated");' : '';
		$script[] = "\t".'SqueezeBox.close();';
		$script[] = '}';
		$script[] = "\n";
		$script[] = 'function removeusers() {';
		$script[] = "\t".'var select = $(\'jform_jusers\');';
		$script[] = "\t".'for (i = select.options.length - 1; i >= 0; i--)';
		$script[] = "\t\t".'if (select.options[i].selected) select.options[i].destroy();';
		$script[] = rsfilesHelper::isJ3() ? "\t".'jQuery(\'#jform_jusers\').trigger("liszt:updated");' : '';
		$script[] = '}';
		
		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
	}

	protected function getOptions() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$jinput = JFactory::getApplication()->input;
		$options= array();
		
		// Get the selected users
		$query->clear();
		$query->select('jusers');
		$query->from('#__rsfiles_groups');
		$query->where('IdGroup = '.$db->quote($jinput->getInt('IdGroup',0)));
		
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