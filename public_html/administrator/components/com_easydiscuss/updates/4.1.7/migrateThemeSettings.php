<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/includes/maintenance/dependencies.php');

class EasyDiscussMaintenanceScriptMigrateThemeSettings extends EasyDiscussMaintenanceScript
{
	public static $title = "Migrate Simplistic Theme Settings";
	public static $description = "Migrate simplistic theme settings into wireframe theme.";

	public function main()
	{
		$config = ED::config();
		$theme = $config->get('layout_site_theme');

		if ($theme == 'simplistic') {
			// update toolbar color to fit simplistic style.
			$config->set('layout_toolbarcolor', '#fafafa');
			$config->set('layout_toolbarbordercolor', '#e1e1e1');
			$config->set('layout_toolbaractivecolor', '#d6d6d6');
			$config->set('layout_toolbartextcolor', '#777777');

			// now, we update the site theme to wireframe as we no longer use simplistic
			$config->set('layout_site_theme', 'wireframe');
			$config->set('layout_site_theme_base', 'wireframe');

			$jsonString = $config->toString();

			$table = ED::table('Configs');
			$exists = $table->load(array('name' => 'config'));

			if (!$exists) {
				$table->name = 'config';
			}

			$table->params = $jsonString;
			$table->store();
		}

		// Remove the simplistic folder since we no longer have this theme.
		$path = DISCUSS_THEMES . '/simplistic';

		if (JFolder::exists($path)) {
			JFolder::delete($path);
		}

		return true;
	}
}
