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

class EasyDiscussMaintenanceScriptDisableConversation extends EasyDiscussMaintenanceScript
{
	public static $title = "Deprecating Conversation";
	public static $description = "This script will force disabled EasyDiscuss conversation. EasyDiscuss conversation will no longer be available in the future version and it will be removed in the next release.";

	public function main()
	{
		$config = ED::config();

		// Both settings should be tallied, either one should be disabled.
		if ($config->get('main_conversations') || $config->get('main_conversations_notification')) {
			$this->updateConfig('main_conversations', 0);
			$this->updateConfig('main_conversations_notification', 0);
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
