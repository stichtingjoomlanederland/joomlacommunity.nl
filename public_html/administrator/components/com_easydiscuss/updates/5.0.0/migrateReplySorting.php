<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/includes/maintenance/dependencies.php');

class EasyDiscussMaintenanceScriptMigrateReplySorting extends EasyDiscussMaintenanceScript
{
	public static $title = "Migrate replies sorting";
	public static $description = "This script will migrate the unused replies sorting to the default reply sorting.";

	public function main()
	{
		$config = ED::config();
		$rSorting = $config->get('layout_replies_sorting', '');

		if ($rSorting == 'voted' || $rSorting == 'likes') {
			$newSorting = array('layout_replies_sorting' => 'oldest');
			$model = ED::model('Settings');
			$model->save($newSorting);
		}

		return true;
	}
}