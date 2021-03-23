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

class EasyDiscussMaintenanceScriptRemoveUnusedLibraryFiles extends EasyDiscussMaintenanceScript
{
	public static $title = "Remove Unused Library Files";
	public static $description = "Removing the files which are no longer used";

	public function main()
	{
		$files = [
			ED_ADMIN . '/includes/html'
		];

		foreach ($files as $file) {
			$exists = JFolder::exists($file);

			if ($exists) {
				JFolder::delete($file);
			}
		}

		return true;
	}
}