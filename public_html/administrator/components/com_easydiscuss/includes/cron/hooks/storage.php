<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussCronHookStorage extends EasyDiscuss
{
	/**
	 * Executes during cron's initialization
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function execute()
	{
		// Process remote storage items
		$this->remoteStorage();
	}

	/**
	 * Processes remote storage items
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	private function remoteStorage()
	{
		$this->remoteStorageAttachments();
	}

	/**
	 * Process attachment items
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	private function remoteStorageAttachments()
	{
		// Get the storage library
		$storageType = $this->config->get('storage_attachments');
		$storage = ED::storage($storageType);

		// @TODO: We need to store all failed items

		$model = ED::model('Attachments');
		$attachments = $model->getAttachments($storageType);

		// If current storage type is joomla, we need to pull it back from amazon
		if ($storageType == 'joomla' && $attachments) {

			foreach ($attachments as $attachment) {

				// Download the storage item
				$currentStorage = ED::storage($attachment->storage);
				$relativePath = str_ireplace(JPATH_ROOT, '', $attachment->getAbsolutePath());
				$targetFile = $attachment->getAbsolutePath();

				// Download the main file
				$state = $currentStorage->download($targetFile, $relativePath);

				// Determine if this is an image as we also need to download the thumbnail
				if ($attachment->isImage()) {
					$state = $currentStorage->download($targetFile . '_thumb', $relativePath . '_thumb');
				}

				// If this was successful, we need to update the table
				if ($state) {
					$attachment->set('storage', $storageType);
					$attachment->save();
				}
			}

			return;
		}

		// @TODO: If current storage type is amazon, we need to upload it to amazon
		if ($storageType == 'amazon' && $attachments) {

			foreach ($attachments as $attachment) {

				$currentStorage = ED::storage($attachment->storage);
				$relativePath = str_ireplace(JPATH_ROOT, '', $attachment->getAbsolutePath());
				$sourceFile = $attachment->getAbsolutePath();

				// Now we upload the file
		        $state = $storage->upload($attachment->path, $sourceFile, $relativePath, true);

		        // If this is a thumbnail, upload it to amazon as well
				if ($attachment->isImage()) {
					$state = $storage->upload($attachment->path . '_thumb', $sourceFile . '_thumb', $relativePath . '_thumb', true);
				}


				// If this was successful, we need to update the table
				if ($state) {
					$attachment->set('storage', $storageType);
					$attachment->save();
				}
			}
			return;
		}
	}
}