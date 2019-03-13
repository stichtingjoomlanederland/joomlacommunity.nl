<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class EasyDiscussControllerMaintenanceExecute extends EasyDiscussSetupController
{
	public function execute()
	{
		// Require main engine.
		$this->engine();

		$script = $this->input->get('script', '', 'default');
	
		// Run the maintenance script now
		$maintenance = ED::maintenance();
		$state = $maintenance->runScript($script);

		if (!$state) {
			$message = $maintenance->getError();
			$result = $this->getResultObj($message, false);

			return $this->output($result);
		}

		$title = $maintenance->getScriptTitle($script);
		$message = JText::sprintf('COM_EASYDISCUSS_INSTALLATION_MAINTENANCE_EXECUTED_SCRIPT', $title);

		$result = $this->getResultObj($message, true);

		return $this->output($result);
	}
}
