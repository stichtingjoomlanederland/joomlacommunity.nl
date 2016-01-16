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
class JFormFieldRSList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'RSList';

	function __construct($parent = null) {
		parent::__construct($parent);
		
		$doc = JFactory::getDocument();
		$doc->addScript(JURI::root().'administrator/components/com_rseventspro/assets/js/scripts.js');
		$doc->addScriptDeclaration("window.addEvent('domready', function() { rsepro_change_list(document.getElementById('jform_params_list').value); });");
	}
	
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
		
		$options = array (
			JHTML::_('select.option','all', JText::_('COM_RSEVENTSPRO_EVENTS_VIEW_LIST_TYPE_ALL')),
			JHTML::_('select.option','featured', JText::_('COM_RSEVENTSPRO_EVENTS_VIEW_LIST_TYPE_FEATURED')),
			JHTML::_('select.option','future', JText::_('COM_RSEVENTSPRO_EVENTS_VIEW_LIST_TYPE_FUTURE')),
			JHTML::_('select.option','archived', JText::_('COM_RSEVENTSPRO_EVENTS_VIEW_LIST_TYPE_ARCHIVED')),
			JHTML::_('select.option','user', JText::_('COM_RSEVENTSPRO_EVENTS_VIEW_LIST_TYPE_USER'))
		);
		
		return $options;
	}
}