<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/includes/maintenance/dependencies.php');

class EasyDiscussMaintenanceScriptUpdateTextAvatarSettings extends EasyDiscussMaintenanceScript
{
	public static $title = "Update text avatar settings";
	public static $description = "This script will attempt to migrate the text avatar settings to a new settings";

	public function main()
	{
		$config = ED::config();

		// Migrate `layout_avatar` to `layout_text_avatar`
		// Both settings should be tallied, either one should be enabled
		if (!$config->get('layout_avatar') && !$config->get('layout_text_avatar')) {
			$this->updateConfig('layout_text_avatar', 1);
		}

		return true;
	}

	/**
	 * Saves a configuration item
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function updateConfig($key, $value)
	{
		$config = ED::config();
		$config->set($key, $value);

		$jsonString = $config->toString();

		$table = ED::table('Configs');
		$exists = $table->load(array('name' => 'config'));

		if (!$exists) {
			$table->name = 'config';
		}

		$table->params = $jsonString;
		$table->store();
	}
}
