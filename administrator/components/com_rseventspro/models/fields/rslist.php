<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');
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
		
		if (!class_exists('rseventsproHelper')) {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php';
		}
		
		// Load jQuery
		rseventsproHelper::loadjQuery();
		
		$doc = JFactory::getDocument();
		$doc->addScript(JURI::root().'administrator/components/com_rseventspro/assets/js/scripts.js');
		$doc->addScriptDeclaration("jQuery(window).ready(function() { rsepro_change_list(jQuery('#jform_params_list').val()); });");
	}
	
	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getOptions() {
		$xmloptions = parent::getOptions();
		
		$options = array (
			JHTML::_('select.option','all', JText::_('COM_RSEVENTSPRO_EVENTS_VIEW_LIST_TYPE_ALL')),
			JHTML::_('select.option','featured', JText::_('COM_RSEVENTSPRO_EVENTS_VIEW_LIST_TYPE_FEATURED')),
			JHTML::_('select.option','future', JText::_('COM_RSEVENTSPRO_EVENTS_VIEW_LIST_TYPE_FUTURE')),
			JHTML::_('select.option','archived', JText::_('COM_RSEVENTSPRO_EVENTS_VIEW_LIST_TYPE_ARCHIVED')),
			JHTML::_('select.option','user', JText::_('COM_RSEVENTSPRO_EVENTS_VIEW_LIST_TYPE_USER'))
		);
		
		if ($xmloptions) {
			$options = array_merge($options, $xmloptions);
		}
		
		return $options;
	}
}