<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');

class JFormFieldScripts extends JFormField {
	
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Scripts';
	
	public function __construct() {
		$doc = JFactory::getDocument();
		
		if (!class_exists('RSCommentsHelper')) {
			require_once JPATH_ADMINISTRATOR.'/components/com_rscomments/helpers/rscomments.php';
		}
		
		// Load jQuery
		RSCommentsHelper::loadjQuery();
		
		$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_rscomments/assets/css/config.css');
		$doc->addScript(JURI::root(true).'/administrator/components/com_rscomments/assets/js/config.js');
	}
	
	public function getInput() {
		return;
	}
	
	public function getLabel() {
		return;
	}
}