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

class EasyDiscussMaintenanceScriptRemoveOldThemesFiles extends EasyDiscussMaintenanceScript
{
	public static $title = "Remove Old Themes Files";
	public static $description = "Removing the old themes files which are no longer be supported";

	public function main()
	{
		$items = ['bubbles', 'dark', 'flatt', 'pinter', 'timeless', 'zinc'];

		foreach ($items as $item) {
			$path = ED_THEMES . '/' . $item;

			if (JFolder::exists($path)) {
				JFolder::delete($path);
			}
		}

		return true;
	}
}