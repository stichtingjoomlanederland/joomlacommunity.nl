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

class EasyDiscussAvatar extends EasyDiscuss
{
	/**
	 * Uploads user avatar
	 *
	 * @since	5.0.0
	 */
	public function upload($fileData, $userId = false)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		// Check for ACL.
		$acl = ED::acl();

		// TODO: Ensure that the user really has access to upload avatar

		$user = JFactory::getUser();

		// If the id is passed, then use the passed id.
		if ($userId) {
			$user = JFactory::getUser($userId);
		}

		$app = $this->app;
		$config = ED::config();

		$path = $config->get('main_avatarpath');
		$path = rtrim($path, '/');
		$path = EDJString::str_ireplace('/', DIRECTORY_SEPARATOR, $path);

		$relativePath = $path;
		$absolutePath = JPATH_ROOT . '/' . $path;

		// If the absolute path does not exist, create it first
		if (!JFolder::exists($absolutePath)) {
			if (!JFolder::create($absolutePath)) {
				if (ED::isFromAdmin()) {
					ED::redirect(EDR::_('index.php?option=com_easydiscuss&view=users', false), JText::_('COM_EASYDISCUSS_FAILED_TO_CREATE_UPLOAD_FOLDER'), ED_MSG_ERROR);
					return;
				}

				ED::setMessage(JText::_('COM_EASYDISCUSS_FAILED_TO_CREATE_UPLOAD_FOLDER'), ED_MSG_ERROR);
				ED::redirect(EDR::_('index.php?option=com_easydiscuss&view=profile', false));
				return;
			}
		}

		// The file data should have a name
		if (!isset($fileData['name'])) {
			return false;
		}

		// Make sure the avatar filename do not have space
		$fileData['name'] = str_replace(' ', '_', $fileData['name']);

		// Generate a better name for the file
		$fileData['name'] = $user->id . '_' . JFile::makeSafe($fileData['name']);

		// Get the relative path
		$relativeFile = $relativePath . '/' . $fileData['name'];

		// Get the absolute file path
		$absoluteFile = $absolutePath . '/' . $fileData['name'];

		// Test if the file is upload-able
		$message = '';

		if (!ED::image()->canUpload($fileData, $message)) {
			ED::setMessage($message, ED_MSG_ERROR);

			if (ED::isFromAdmin()) {
				ED::redirect(EDR::_('index.php?option=com_easydiscuss&view=users&layout=form&id=' . $user->id), JText::_($error), ED_MSG_ERROR);
				return false;
			}

			ED::redirect(EDR::_('index.php?option=com_easydiscuss&view=profile&layout=edit'), false);
			return false;
		}

		// Determines if the web server is generating errors
		if ((int)$fileData['error'] != 0) {
			ED::setMessage($file['error'], ED_MSG_ERROR);

			if (ED::isFromAdmin()) {
				ED::redirect(EDR::_('index.php?option=com_easydiscuss&view=users&layout=form&id=' . $user->id), $file['error'], ED_MSG_ERROR);
				return false;
			}

			ED::redirect(EDR::_('index.php?option=com_easydiscuss&view=profile&layout=edit'), false);
			return false;
		}

		// We need to delete the old avatar
		$profile = ED::user($user->id);

		// Get the old avatar
		$oldAvatar = $profile->avatar;
		$isNew = false;

		// Delete the old avatar first
		if ($oldAvatar != 'default.png') {
			$session = JFactory::getSession();
			$sessionId = $session->getToken();

			$oldAvatarPath = $absolutePath . '/' . $oldAvatar;

			if (JFile::exists($oldAvatarPath)) {
				JFile::delete($oldAvatarPath);
			}
		} else {
			$isNew = true;
		}

		$width = $config->get('layout_avatarwidth', 160);
		$height = $width;
		$originalWidth = $config->get('layout_originalavatarwidth', 400);
		$originalHeight = $originalWidth;

		$image = ED::simpleimage();
		$image->load($fileData['tmp_name']);

		// By Kevin Lankhorst
		$image->resizeOriginal($originalWidth, $originalHeight, $width, $height);

		$image->save($absoluteFile, $image->image_type);

		return $fileData['name'];
	}
}