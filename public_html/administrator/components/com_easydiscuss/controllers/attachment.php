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

jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');
require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/constants.php');

class EasyDiscussControllerAttachment extends EasyDiscussController
{
	public function displayFile()
	{
		$id = $this->input->get('id', '', 'GET');

		if (empty($id)) {
			return false;
		}

		$attachment	= JTable::getInstance('Attachments', 'Discuss');
		
		if (!$attachment->load($id)) {
			return false;
		}

		$path = ED::attachment()->getStoragePath();

		$file = $path . '/' . $attachment->path;
		
		if (!JFile::exists($file)) {
			return false;
		}

		header('Content-Description: File Transfer');
		header('Content-Type: ' . $attachment->mime);
		header('Content-Disposition: inline');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($file));

		// http://dtbaker.com.au/random-bits/how-to-cache-images-generated-by-php.html
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

	function getFile()
	{
		$id = $this->input->get('id', '', 'GET');

		if (empty($id)) {
			return false;
		}

		$attachment	= JTable::getInstance('Attachments', 'Discuss');
		
		if (!$attachment->load($id)) {
			return false;
		}

		$path = ED::attachment()->getStoragePath();
		$file = $path . '/' . $attachment->path;
		
		if (!JFile::exists($file)) {
			return false;
		}

		$type = explode("/", $attachment->mime);

		header('Content-Description: File Transfer');
		header('Content-Type: ' . $attachment->mime);
		header("Content-Disposition: attachment; filename=\"".basename($attachment->title)."\";");
		header('Content-Transfer-Encoding: binary');
		// header('Expires: 0');
		// header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		// header('Pragma: public');
		header('Content-Length: ' . filesize($file));

		// http://dtbaker.com.au/random-bits/how-to-cache-images-generated-by-php.html
		header("Cache-Control: private, max-age=10800, pre-check=10800");
		header("Pragma: private");
		header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime($file))) {
		  // send the last mod time of the file back
		  header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT', true, 304);
		}

		ob_clean();
		flush();
		readfile($file);
		exit;
	}

	public function deleteFile($id)
	{
		if (empty($id)) {
			return false;
		}

		$attachment	= JTable::getInstance('Attachments', 'Discuss');
		if (!$attachment->load($id)) {
			return false;
		}

		$path = ED::attachment()->getStoragePath();
		$file = $path . '/' . $attachment->path;
		$exists = JFile::exists($file);

		if ($exists) {
			$state = JFile::delete($file);

			if (!$state) {
				return false;
			}
		}

		return $attachment->delete();
	}
}
