<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * ACL Manager Diagnostic Controller
 */
class AclmanagerControllerDiagnostic extends JControllerLegacy
{

	/**
	 * Fix wrong stored assets
	 */
	public function fixAssetIssues()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('diagnostic');
		$model->fixAssetIssues();
		$this->setRedirect(JRoute::_('index.php?option=com_aclmanager&view=diagnostic', false));
	}

	/**
	 * Add missing assets
	 */
	public function fixMissingAssets()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('diagnostic');
		$model->fixMissingAssets();
		$this->setRedirect(JRoute::_('index.php?option=com_aclmanager&view=diagnostic', false));
	}

	/**
	 * Fix admin conflicts
	 */
	public function fixAdminConflicts()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('diagnostic');
		$model->fixAdminConflicts();
		$this->setRedirect(JRoute::_('index.php?option=com_aclmanager&view=diagnostic', false));
	}

	/**
	 * Fix orphan assets
	 */
	public function fixOrphanAssets()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('diagnostic');
		$model->fixOrphanAssets();
		$this->setRedirect(JRoute::_('index.php?option=com_aclmanager&view=diagnostic', false));
	}

	/**
	 * Rebuild the assets table
	 */
	public function rebuild()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$this->setRedirect(JRoute::_('index.php?option=com_aclmanager&view=diagnostic', false));

		// Initialise variables.
		$model = $this->getModel('diagnostic');

		if ($model->rebuild()) {
			// Rebuild succeeded.
			$this->setMessage(JText::_('COM_ACLMANAGER_DIAGNOSTIC_REBUILD_SUCCESS'));
			return true;
		} else {
			// Rebuild failed.
			$this->setMessage(JText::_('COM_ACLMANAGER_DIAGNOSTIC_REBUILD_FAILED'));
			return false;
		}
	}
}
