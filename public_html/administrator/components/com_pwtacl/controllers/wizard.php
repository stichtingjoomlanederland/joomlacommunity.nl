<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

// No direct access.
defined('_JEXEC') or die;

/**
 * PWT ACL Diagnostic Controller
 *
 * @since   3.0
 */
class PwtaclControllerWizard extends BaseController
{
	/**
	 * Setup the group
	 *
	 * @return  boolean
	 * @since   3.0
	 * @throws  Exception
	 */
	public function groupSetup()
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Get wizard step 1 data
		$data = $this->input->post->get('jform', array(), 'array');

		// Save data in state
		Factory::getApplication()->setUserState('com_pwtacl.wizard', $data);

		// Check for errors
		$error = null;

		// Check for components
		if (!isset($data['core.manage']) && empty($data['core.manage']))
		{
			$error = 'COM_PWTACL_WIZARD_ERROR_COMPONENTS';
		}

		// Check if title is set for new group
		if ($data['new'] == 0 && empty($data['groupid']))
		{
			$error = 'COM_PWTACL_WIZARD_ERROR_GROUPSELECT';
		}

		// Check if title is set for new group
		if ($data['new'] == 1 && empty($data['grouptitle']))
		{
			$error = 'COM_PWTACL_WIZARD_ERROR_GROUPTITLE';
		}

		// Check if new or exisiting user group is selected
		if (!isset($data['new']) && empty($data['new']))
		{
			$error = 'COM_PWTACL_WIZARD_ERROR_GROUP';
		}

		// Show error if we have one
		if ($error)
		{
			$this->setMessage(Text::_($error), 'error');
			$this->setRedirect(Route::_('index.php?option=com_pwtacl&view=wizard', false));

			return false;
		}

		// Setup the group
		/** @var PwtaclModelWizard $wizardModel */
		$wizardModel = $this->getModel('wizard');
		$groupId     = $wizardModel->groupSetup($data);

		// Fix the admin access conflicts
		/** @var PwtaclModelDiagnostics $diagnosticModel */
		$diagnosticModel = $this->getModel('diagnostics');
		$diagnosticModel->fixAdminConflicts(true);

		// Save component access in session
		Factory::getSession()->set('components', $data['core.manage'], 'pwtacl');

		// Redirect to the next step
		$this->setRedirect(Route::_('index.php?option=com_pwtacl&view=wizard&group=' . $groupId, false));

		return true;
	}

	/**
	 * Redirect to the group configured
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception
	 */
	public function finalize()
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Get Group ID
		$groupId = $this->input->getInt('groupid');

		// Clear user state
		Factory::getApplication()->setUserState('com_pwtacl.wizard', null);

		// Redirect to step group
		$this->setMessage(Text::_('COM_PWTACL_WIZARD_SUCCESS'));
		$this->setRedirect(Route::_('index.php?option=com_pwtacl&view=assets&type=group&group=' . $groupId, false));
	}
}
