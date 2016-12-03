<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldForms extends JFormFieldList
{
	protected $type = 'Forms';
	
	protected function getOptions() {
		// Initialize variables.
		$options = array();
		
		$directory = (string) $this->element['directory'] == 'true' || (string) $this->element['directory'] == '1';
		
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select($db->qn('FormId'))
			  ->select($db->qn('FormTitle'))
			  ->select($db->qn('FormName'))
			  ->from($db->qn('#__rsform_forms'));
		
		if ($directory) {
			$subquery = $db->getQuery(true);
			$subquery->select($db->qn('formId'))
				->from($db->qn('#__rsform_directory'));
			
			$query->where('FormId IN ('.$subquery.')');
		}
		$db->setQuery($query);
		
		$forms = $db->loadObjectList();
		foreach ($forms as $form) {
			$tmp = JHtml::_('select.option', $form->FormId, sprintf('(%d) %s', $form->FormId, $form->FormName));

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);
		
		return $options;
	}
}
