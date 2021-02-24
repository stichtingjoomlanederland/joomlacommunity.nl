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

class EasyDiscussMaintenanceScriptRemoveOutdatedFilesIn5 extends EasyDiscussMaintenanceScript
{
	public static $title = "Remove outdated theme files from previous version";
	public static $description = "Remove outdated theme files from previous version.";

	public function main()
	{
		$files = array(
				DISCUSS_THEMES . '/wireframe/toolbar/toolbar.jomsocial.php'
				);

		foreach ($files as $file) {
			if (JFile::exists($file)) {
				JFile::delete($file);
			}
		}

		return true;
	}
}
