<?php
/**
* @version 1.0.0
* @package RSComments! 1.0.0
* @copyright (C) 2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.event.plugin');

// check if RSComments! is installed
if (!file_exists(JPATH_SITE.'/administrator/components/com_rscomments/rscomments.php' )) return;

class plgButtonRscomments extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	/**
	 * Display the button
	 *
	 * @return array A four element array of (article_id, article_title, category_id, object)
	 */
	public function onDisplay($name) {
		$js = "
			function insertRSComments(editor) {
				if (window.Joomla && window.Joomla.editors && window.Joomla.editors.instances && window.Joomla.editors.instances.hasOwnProperty(editor)) {
					content = window.Joomla.editors.instances[editor].getValue();
					
					if (content.match(/{rscomments on}/)) {
						content = content.replace('{rscomments on}', '{rscomments off}');
						Joomla.editors.instances[editor].setValue(content);
					} else if (content.match(/{rscomments off}/)) {
						content = content.replace('{rscomments off}', '{rscomments on}');
						Joomla.editors.instances[editor].setValue(content);
					} else {
						Joomla.editors.instances[editor].replaceSelection('{rscomments on}')
					}	
				}
			}
			";

		JFactory::getDocument()->addScriptDeclaration($js);
		
		$button = new JObject();
		$button->set('modal', false);
		$button->set('class','btn');
		$button->set('onclick', 'insertRSComments(\''.$name.'\');return false;');
		$button->set('text', 'RSComments!');
		$button->set('name', 'blank');
		$button->set('link', '#');
		
		return $button;
	}
}