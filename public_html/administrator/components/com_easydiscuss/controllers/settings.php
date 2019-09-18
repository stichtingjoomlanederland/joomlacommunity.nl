<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussControllerSettings extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.settings');
	}

	/**
	 * Saves the settings
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function apply()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the model
		$model = ED::model('Settings');

		// Get the posted data
		$post = $this->input->getArray('post');

		$layout = $this->input->get('layout', '', 'default');

		// Cleanup the post data
		$this->cleanup($post);

		foreach ($post as $index => &$value) {

			// Filter out dummy checkbox_display_xxx items
			if (substr($index, 0, 17) == 'checkbox_display_') {
				continue;
			}

			// Fix google adsense integration codes
			if ($index == 'integration_google_adsense_code') {
				$value = str_ireplace(';"', ';', $value);
			}

			if ($index == 'integration_google_adsense_responsive_code') {
				$value = $this->input->get($index, '', 'raw');
			}

			if ($index == 'main_tnctext') {
				$value = $this->input->get($index, '', 'raw');
			}

			// We need to decode arrays into comma separated values
			if (is_array($value)) {
				$post[$index] = implode(',', $value);
			}
		}

		// Reset the settings for main_allowedelete to use from configuration.ini
		$post['main_allowdelete'] = ED::getDefaultConfigValue('main_allowdelete', '');

		// Reset the settings for layout_featuredpost_style to always use from configuration.ini
		$post['layout_featuredpost_style'] = ED::getDefaultConfigValue('layout_featuredpost_style', 0);

		// For now this code will be commented out to avoid reset the value for work schedule days
		if ($layout == 'work') {
			// we need to reset the settings for work schedule days due to the use of checkbox
			$days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');

			foreach($days as $dd) {
				$dkey = 'main_work_' . $dd;
				if (!isset($post[$dkey])) {
					$post[$dkey] = 0;
				}
			}
		}

		// Since we allow users to change the storage path, if they rename the path, we would also need to 
		// rename the folder
		if (isset($post['storage_path']) && !empty($post['storage_path'])) {
				
			$config = ED::config();

			$newStoragePath = $post['storage_path'];
			$existingStoragePath = $config->get('storage_path');

			if ($newStoragePath != $existingStoragePath) {

				// If the existing storage path is empty, we check for the legacy path
				if (!$existingStoragePath) {

					$legacyPath = '/media/com_easydiscuss/' . $config->get('attachment_path');

					if ($newStoragePath != $legacyPath) {
						$state = JFolder::move(JPATH_ROOT . '/' . ltrim($legacyPath, '/'), JPATH_ROOT . '/' . ltrim($storagePath, '/'));

						if (!$state) {
							ED::setMessage(JText::_('Since there is a change in the attachments path, EasyDiscuss was not able to rename the previous folder', 'error'));
						}
					}
				}

				// If there is an existing value for the storage path, we need to move
				if ($existingStoragePath) {
					$existingStoragePath = JPATH_ROOT . '/' . ltrim($existingStoragePath, '/');
					$newStoragePath = JPATH_ROOT . '/' . ltrim($newStoragePath, '/');

					$state = JFolder::move($existingStoragePath, $newStoragePath);

					if (!$state) {
						ED::setMessage(JText::_('Since there is a change in the attachments path, EasyDiscuss was not able to rename the previous folder', 'error'));
					}
				}
			}
		}

		// Save the settings now
		$result = $model->save($post);

		// Check if any of the configurations are stored as non local
		if (isset($post['amazon_access_key']) && isset($post['amazon_access_secret']) && $layout == 'storage') {
			
			$bucket = $post['amazon_bucket'];
			
			// Get the storage library
			$storageType = $post['storage_attachments'];
			$storage = ED::storage($storageType);

			// If the bucket is set, check if it exists.
			if ($bucket && !$storage->containerExists($bucket)) {
				$storage->createContainer($bucket);
			}

			// If the bucket is empty, we initialize a new bucket based on the domain name
			if (!$bucket) {
				// Initialize the remote storage
				$bucket = $storage->init();

				$config = ED::registry();
				$configTable = ED::table('Configs');

				$config->set('amazon_bucket', $bucket);
		
				$configTable->set('value', $config->toString());
				$configTable->store();
			}
		}

		// Default message
		$message = 'COM_EASYDISCUSS_CONFIGURATION_SAVED';

		ED::setMessage($message, 'success');

		// Get the previously accessed settings page
		$layout = $this->input->get('layout', 'general', 'string');
		$active = $this->input->get('active', '', 'string');

		$redirect = 'index.php?option=com_easydiscuss&view=settings&layout=' . $layout . '&active=' . $active;
		$this->app->redirect($redirect);

		return $this->app->close();
	}


	/**
	 * Allows caller to save their api key
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function saveApi()
	{
		// Check for request forgeries
		ED::checkToken();

		$key = $this->input->get('apikey', '', 'default');
		$from = $this->input->get('from', '', 'default');
		$return = $this->input->get('return', '', 'default');
		$data = array('main_apikey' => $key);

		// Get the model
		$model = ED::model('Settings');

		// Save the apikey
		$model->save($data);

		// If return is specified, respect that
		if (!empty($return)) {
			$return = base64_decode($return);
			$this->app->redirect($return);
		}

		return $this->app->redirect('index.php?option=com_easydiscuss&view=languages');
	}

	/**
	 * Cleans up the posted data
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function cleanup(&$post)
	{
		// Unset unecessary data.
		unset($post['controller']);
		unset($post['active']);
		unset($post['child']);
		unset($post['layout']);
		unset($post['task']);
		unset($post['option']);
		unset($post['c']);

		// Unset the token
		$token = ED::getToken();
		unset($post['token']);
	}
}
