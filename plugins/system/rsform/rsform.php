<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2013 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * RSForm! Pro system plugin
 */
class plgSystemRSForm extends JPlugin
{
	public function __construct( &$subject, $config ) {
		parent::__construct( $subject, $config );
	}

	public function onBeforeRender() {
		if (JFactory::getApplication()->isSite() && !class_exists('RSFormProHelper')) {
			if (file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php')) {
				require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php';
				RSFormProAssets::$replace = true;
			}
		}
	}

	public function onAfterRender() {
		$mainframe 	= JFactory::getApplication();
		$doc 		= JFactory::getDocument();
		if ($doc->getType() != 'html' || $mainframe->isAdmin()) {
			return;
		}
		$option = JRequest::getVar('option');
		$task 	= JRequest::getVar('task');
		if ($option == 'com_content' && $task == 'edit')
			return;

		$content = JResponse::getBody();

		if (strpos($content, '{rsform ') === false)
			return true;

		// expression to search for
		$pattern = '#\{rsform ([0-9]+)\}#i';
		if (preg_match_all($pattern, $content, $matches))
		{
			static $found_textarea;
			
			$lang = JFactory::getLanguage();
			$lang->load('com_rsform', JPATH_SITE);
			
			$db = JFactory::getDBO();
			foreach ($matches[0] as $j => $match)
			{
				// within <textarea>
				$tmp = explode($match, $content, 2);
				$before = strtolower(reset($tmp));
				$before = preg_replace('#\s+#', ' ', $before);
				
				// we have a textarea
				if (strpos($before, '<textarea') !== false)
				{
					// find last occurrence
					$tmp = explode('<textarea', $before);
					$textarea = end($tmp);
					// found & no closing tag
					if (!empty($textarea) && strpos($textarea, '</textarea>') === false)
						continue;
				}
					
				$formId = $matches[1][$j];
				$content = str_replace($matches[0][$j], RSFormProHelper::displayForm($formId,true), $content);
			}
		}
		
		JResponse::setBody($content);
	}
}