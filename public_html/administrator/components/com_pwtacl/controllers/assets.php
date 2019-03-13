<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Access\Access;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

// No direct access.
defined('_JEXEC') or die;

/**
 * PWT ACL Assets Controller
 *
 * @since   3.0
 */
class PwtaclControllerAssets extends BaseController
{
	/**
	 * Clear permissions for group.
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception on errors
	 */
	public function clear()
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$group = $this->input->get('group', 0);

		/** @var PwtaclModelAssets $model */
		$model = $this->getModel('assets');
		$model->clear($group);

		// Redirect and show message
		$this->setMessage(Text::_('COM_PWTACL_ASSETS_CLEAR_SUCCESS'));
		$this->setRedirect(Route::_('index.php?option=com_pwtacl&view=assets&type=group&id=' . $group, false));
	}

	/**
	 * Reset permissions for group.
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception on errors
	 */
	public function reset()
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$group = $this->input->get('group', 0);

		/** @var PwtaclModelAssets $model */
		$model = $this->getModel('assets');
		$model->reset($group);

		// Redirect and show message
		$this->setMessage(Text::_('COM_PWTACL_ASSETS_RESET_SUCCESS'));
		$this->setRedirect(Route::_('index.php?option=com_pwtacl&view=assets&type=group&id=' . $group, false));
	}

	/**
	 * Method to save an action
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception
	 */
	public function saveAction()
	{
		$assetId = $this->input->getInt('assetid');
		$action  = $this->input->get('action');
		$groupId = $this->input->getInt('groupid');
		$setting = $this->input->get('setting');

		/** @var PwtaclModelAssets $model */
		$model = $this->getModel('assets');
		$model->saveAction($assetId, $action, $groupId, $setting);

		Factory::getApplication()->close();
	}

	/**
	 * Copy permissions for group.
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception on errors
	 */
	public function copy()
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$group  = $this->input->get('group', 0);
		$copyTo = $this->input->getInt('copy-group');

		/** @var PwtaclModelAssets $model */
		$model = $this->getModel('assets');
		$model->copy($group, $copyTo);

		// Redirect and show message
		$this->setMessage(Text::_('COM_PWTACL_ASSETS_COPY_SUCCESS'));
		$this->setRedirect(Route::_('index.php?option=com_pwtacl&view=assets&type=group&id=' . $group, false));
	}

	/**
	 * Export permissions for group.
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception on errors
	 */
	public function export()
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$group = $this->input->get('group', 0);

		/** @var PwtaclModelAssets $model */
		$model  = $this->getModel('assets');
		$export = $model->export($group);

		// Get Site name
		$basename  = ApplicationHelper::stringURLSafe(Factory::getApplication()->get('sitename'));
		$groupname = ApplicationHelper::stringURLSafe(Access::getGroupTitle($group));

		// Prepare JSON export file
		header('MIME-Version: 1.0');
		header('Content-Disposition: attachment; filename="' . $basename . '_pwtacl-permissions_' . $groupname . '.json"');
		header('Content-Transfer-Encoding: binary');

		echo json_encode($export);

		Factory::getApplication()->close();
	}

	/**
	 * Import permissions for group.
	 *
	 * @return  boolean
	 * @since   3.0
	 * @throws  Exception on errors
	 */
	public function import()
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$group = $this->input->get('group', 0);
		$file  = $this->input->files->get('import-group');

		// Check for file/upload errors
		if (!is_array($file) || $file['error'] || $file['size'] < 1 || $file['type'] !== 'application/json')
		{
			$this->setMessage(Text::_('COM_PWTACL_ASSETS_IMPORT_JSON_INVALID'), 'error');
			$this->setRedirect(Route::_('index.php?option=com_pwtacl&view=assets&type=group&id=' . $group, false));

			return false;
		}

		// Parse the uploaded JSON
		$json     = file_get_contents($file['tmp_name']);
		$response = json_decode($json, true);

		// Did we received invalid JSON data?
		if (!$response)
		{
			$this->setMessage(Text::_('COM_PWTACL_ASSETS_IMPORT_JSON_INVALID'), 'error');
			$this->setRedirect(Route::_('index.php?option=com_pwtacl&view=assets&type=group&id=' . $group, false));

			return false;
		}

		// Is this a file generated by PWT ACL?
		if (!isset($response['generator']) || $response['generator'] !== 'PWT ACL')
		{
			$this->setMessage(Text::_('COM_PWTACL_ASSETS_IMPORT_JSON_NOTPWTACL'), 'error');
			$this->setRedirect(Route::_('index.php?option=com_pwtacl&view=assets&type=group&id=' . $group, false));

			return false;
		}

		// Get the permissions
		$permissions = $response['permissions'];

		// Store the permissions
		/** @var PwtaclModelAssets $model */
		$model = $this->getModel('assets');
		$model->import($group, $permissions);

		// Redirect and show message
		$this->setMessage(Text::_('COM_PWTACL_ASSETS_IMPORT_SUCCESS'));
		$this->setRedirect(Route::_('index.php?option=com_pwtacl&view=assets&type=group&id=' . $group, false));

		return true;
	}
}
