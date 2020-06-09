<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2020 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

/**
 * PWT ACL Dashboard Controller
 *
 * @since   3.0
 */
class PwtAclControllerDashboard extends BaseController
{
	/**
	 * Run the diagnostic checks
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception on errors
	 */
	public function tableData()
	{
		/** @var PwtAclModelDashboard $model */
		$model = $this->getModel('dashboard');
		$data  = $model->getTableData();

		echo json_encode($data);

		Factory::getApplication()->close();
	}
}
