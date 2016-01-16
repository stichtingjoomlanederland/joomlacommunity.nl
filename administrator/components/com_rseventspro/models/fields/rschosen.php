<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('hidden');
class JFormFieldRSChosen extends JFormFieldHidden
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'RSChosen';

	public function __construct() {
		if (!class_exists('rseventsproHelper')) {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php';
		}
		if (!class_exists('JHTMLRSEventsPro')) {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/html.php';
		}
		
		// Load jQuery
		rseventsproHelper::loadjQuery();
		
		// Load Chosen library
		JHtml::_('rseventspro.chosen','.rschosen');
		
		if (!rseventsproHelper::isJ3()) {
			$doc = JFactory::getDocument();
			
			$doc->addStyleDeclaration(".chzn-container { float: left; margin-bottom: 5px;}");
			$doc->addScriptDeclaration(
			"jQuery(document).ready(function() {
				jQuery('#jform_params_categories').parents('div.pane-slider').css('overflow','visible');
				jQuery('#jform_params_categories').parents('fieldset').css('overflow','visible');
			});");
		}
	}
	
	/**
	 * Method to get the field label markup for a spacer.
	 * Use the label text or name from the XML element as the spacer or
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   11.1
	 */
	protected function getLabel() {
		return '';
	}
}