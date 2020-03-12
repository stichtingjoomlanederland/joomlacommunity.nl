<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2020 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/includes/maintenance/dependencies.php');

class EasyDiscussMaintenanceScriptRemoveGoogleShareFiles extends EasyDiscussMaintenanceScript
{
	public static $title = "Remove Google Share files from previous version";
	public static $description = "Remove Google Share setting files from previous version.";
 	
 	public function main()
	{
		$file = DISCUSS_ADMIN_THEMES . '/default/settings/social/googleshare.php';

		if (JFile::exists($file)) {
			JFile::delete($file);
		}

		return true;
	}
}