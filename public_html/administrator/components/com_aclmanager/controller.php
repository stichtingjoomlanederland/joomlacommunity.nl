<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Load framework base classes
jimport('joomla.application.component.controller');

/**
 * ACL Manager Controller
 */
class AclmanagerController extends JControllerLegacy
{
	/**
	 * @var		string	The default view.
	 */
	protected $default_view = 'home';

	/**
	 * Method to construct
	 */
	function __construct()
	{
		parent::__construct();

		// Load js & css
		$doc = JFactory::getDocument();
		$layout	= JFactory::getApplication()->input->get('layout',null);
		if ($layout =='print') {
			$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_aclmanager/assets/css/print.css?v=247');
		} else {
			$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_aclmanager/assets/css/aclmanager.css?v=247');
		}
	}

	/**
	 * Method to display a view.
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/aclmanager.php';

		// Variables
		$view	= JFactory::getApplication()->input->get('view','home');
		$id		= JFactory::getApplication()->input->get('id');

		// Get needed language files
		AclmanagerHelper::getLanguages();

		// Check for updates
		if($view == 'home') {
			$updateInfo = LiveUpdate::getUpdateInformation();
			if($updateInfo->hasUpdates == 1) {
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_ACLMANAGER_UPDATE_FOUND', $updateInfo->version), 'warning');
			}
		}

		// Check for User Group ID.
		if (($view == 'group') && (!$id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_aclmanager', false));

			return false;
		}

		// Check for User ID.
		if (($view == 'user') && (!$id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_aclmanager', false));

			return false;
		}

		// Check if default asset permissions are stored
		$rules   = json_decode(JAccess::getAssetRules('com_aclmanager'),true);

		if(empty($rules)) {
			$db = JFactory::getDBO();

			// Set default rules
			$query = "UPDATE #__assets SET rules=".$db->Quote('{"core.admin":{"7":1},"core.manage":{"6":1},"core.edit":[],"aclmanager.diagnostic":{"6":1}}')." WHERE name=".$db->Quote('com_aclmanager');
			$db->setQuery($query);
			$db->query();
		}

		parent::display();

		return $this;

	}

	/**
	 * Cancel operation
	 */
	function cancel()
	{
		// Redirect home
		$this->setRedirect('index.php?option=com_aclmanager');
	}
}