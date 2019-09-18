<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

class EasyDiscussControllerAttachment extends EasyDiscussController
{
	/**
	 * Renders the thumbnail of an attachment
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function thumbnail()
	{
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			die('Invalid request');
		}

		$attachment = ED::attachment($id);
		$file = $attachment->getAbsolutePath(true);

		if (!JFile::exists($file)) {
			return JError::raiseError(500, JText::_('File cannot be found'));
		}

		header('Content-Description: File Transfer');
		header('Content-Type: ' . $attachment->mime);
		header('Content-Disposition: inline');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($file));
		header("Cache-Control: private, max-age=10800, pre-check=10800");
		header("Pragma: private");
		header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime($file))) {
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT', true, 304);
		}

		ob_clean();
		flush();
		readfile($file);
		exit;
	}

	/**
	 * Allows caller to download a file
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function download()
	{
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			die('Invalid request');
		}

		$attachment = ED::attachment($id);
		$file = $attachment->getAbsolutePath();

		if (!JFile::exists($file)) {
			return JError::raiseError(500, JText::_('COM_ED_ATTACHMENT_FILE_NOT_FOUND'));
		}

		$post = ED::post($attachment->uid);

		// Ensure that the viewer can view the post then only can download the file.
		if (!$post->canView($this->my->id) || !$post->isPublished()) {
			return JError::raiseError(500, JText::_('COM_ED_ATTACHMENT_DOWNLOAD_INSUFFICIENT_PERMISSION'));
		}

		// Need to check for the discussion whether created from cluster
		if (!$post->isQuestion()) {
			$post = ED::post($post->parent_id);

			$isModerator = ED::isModerator($post->category_id, $this->my->id);

			// Ensure that private post only allow certain user can able to access it
			if ($post->private && $this->my->id != $post->user_id && !$isModerator) {
				return JError::raiseError(500, JText::_('COM_ED_ATTACHMENT_DOWNLOAD_INSUFFICIENT_PERMISSION'));
			}
		}

		// Determine if user are allowed to download file from the discussion which belong to Easysocial cluster.
		if ($post->isCluster()) {
			$easysocial = ED::easysocial();

			if (!$easysocial->isGroupAppExists()) {
				return JError::raiseError(500, JText::_('COM_ED_ATTACHMENT_DOWNLOAD_INSUFFICIENT_PERMISSION'));
			}

			$cluster = $easysocial->getCluster($post->cluster_id, $post->getClusterType());

			if (!$cluster->canViewItem()) {
				return JError::raiseError(500, JText::_('COM_ED_ATTACHMENT_DOWNLOAD_INSUFFICIENT_PERMISSION'));
			}
		}

		header('Content-Description: File Transfer');
		header('Content-Type: ' . $attachment->mime);
		header('Content-Disposition: inline; filename="' . $attachment->title . '"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($file));
		header("Cache-Control: private, max-age=10800, pre-check=10800");
		header("Pragma: private");
		header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime($file))) {
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT', true, 304);
		}

		ob_clean();
		flush();
		readfile($file);
		exit;
	}

	/**
	 * Allow caller to download a file from amazon with a proper file name
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function downloadAmazon()
	{
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			die('Invalid request');
		}

        // Load the attachments
		$attachment = ED::attachment($id);
		$file = $attachment->getAbsolutePath();

        // Get the storage relative path
        $relativePath = $attachment->getStoragePath(true) . '/' . $attachment->path;

        $storage = ED::storage('amazon');
        $link = $storage->getPermalink($relativePath);

        if (!$link) {
        	return JError::raiseError(500, JText::_('File cannot be found'));
        }

		$relativePath = str_ireplace(JPATH_ROOT, '', $attachment->getAbsolutePath());
		$targetFile = $attachment->getAbsolutePath();

		// Download the main file
		$state = $storage->download($targetFile, $relativePath);

		// Check if the download is successful.
		if (!JFile::exists($file)) {
			return JError::raiseError(500, JText::_('File cannot be found'));
		}

		header('Content-Description: File Transfer');
		header('Content-Type: ' . $attachment->mime);
		header('Content-Disposition: inline; filename="' . $attachment->title . '"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($file));
		header("Cache-Control: private, max-age=10800, pre-check=10800");
		header("Pragma: private");
		header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime($file))) {
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT', true, 304);
		}

		ob_clean();
		flush();
		readfile($file);

		// After the attachment is loaded and delivered to user, delete it from local server.
		JFile::delete($file);

		exit;
	}
}
