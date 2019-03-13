<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2019 Perfect Web Team. All rights reserved.
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
 * PWT ACL Diagnostics Controller
 *
 * @since   3.0
 */
class PwtaclControllerDiagnostics extends BaseController
{
	/**
	 * Rebuild the assets table
	 *
	 * @return  void
	 * @since   3.0
	 */
	public function rebuild()
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		/** @var PwtaclModelDiagnostics $model */
		$model = $this->getModel('diagnostics');
		$model->rebuildAssetsTable();

		// Redirect and show message
		$this->setMessage(Text::_('COM_PWTACL_DIAGNOSTICS_STEP_REBUILD_SUCCESS'));
		$this->setRedirect(Route::_('index.php?option=com_pwtacl&view=diagnostics', false));
	}

	/**
	 * Run the diagnostics checks
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception on errors
	 */
	public function runDiagnostics()
	{
		// Initialise variables.
		$step = $this->input->getInt('step', 1);

		/** @var PwtaclModelDiagnostics $model */
		$model   = $this->getModel('diagnostics');
		$changes = $model->runDiagnostics($step);

		echo new JResponseJson($changes);

		Factory::getApplication()->close();

		return;
	}
}
