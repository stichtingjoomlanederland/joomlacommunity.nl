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

class EasyDiscussMaintenanceScriptUpdateCustomFieldsForCategories extends EasyDiscussMaintenanceScript
{
	public static $title = "Update Custom Fields For Categories";
	public static $description = "Update the custom fields to associate with the existing categories";

	public function main()
	{
		// Retrieve all the categories fromt the site
		$model = ED::model('Categories');
		$categories = $model->getData(false);

		// Retrieve all the custom fields fromt the site
		$model = ED::model('CustomFields');
		$customFields = $model->getData(false);

		$customFieldIds = [];

		// Get its ID only
		foreach ($customFields as $customField) {
			$customFieldIds[] = $customField->id;
		}

		foreach ($categories as $category) {
			$table = ED::table('Category');
			$table->load($category->id);

			$params = new JRegistry($table->params);

			// Update the existing category to have associate with all the custom fields
			$params->set('custom_fields', $customFieldIds);

			$table->params = $params->toString();
			$table->store();
		}

		return true;
	}
}