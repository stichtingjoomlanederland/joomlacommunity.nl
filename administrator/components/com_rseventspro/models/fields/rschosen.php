<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('hidden');

/**
 * Form Field class for the Joomla Platform.
 * Implements a combo box field.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
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
		
		// Load Chosen library
		rseventsproHelper::chosen();
		
		if (!rseventsproHelper::isJ3()) {
			$js = "window.addEvent('domready', function() { $$('.rschosen').chosen({ disable_search_threshold : 10 }); $$('.panel div.pane-slider')[0].setStyle('overflow','visible'); });";
			$css = ".chzn-container { float: left; margin-bottom: 5px; } .rs200 { width: 200px; }";
			JFactory::getDocument()->addScriptDeclaration($js);
			JFactory::getDocument()->addStyleDeclaration($css);
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