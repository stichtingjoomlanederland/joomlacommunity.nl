<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');
require_once JPATH_ROOT . '/components/com_easydiscuss/constants.php';

class EasyDiscussControllerAttachment extends EasyDiscussController
{
	function displayFile()
	{
		$id		= JRequest::getVar('id', '', 'GET');
		$config	= DiscussHelper::getConfig();

		if(empty($id))
		{
			return false;
		}

		$attachment	= JTable::getInstance( 'Attachments' , 'Discuss' );
		if(!$attachment->load( $id ))
		{
			return false;
		}

		$path = $config->get( 'attachment_path' );
		$file = JPATH_ROOT . '/media/com_easydiscuss/' . $path  . '/' . $attachment->path;
		if (!JFile::exists($file))
		{
			return false;
		}

		header('Content-Description: File Transfer');
		header('Content-Type: ' . $attachment->mime);
		header('Content-Disposition: inline');
		header('Content-Transfer-Encoding: binary');
		// header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		// header('Pragma: public');
		header('Content-Length: ' . filesize($file));

		// http://dtbaker.com.au/random-bits/how-to-cache-images-generated-by-php.html
		header("Cache-Control: private, max-age=10800, pre-check=10800");
		header("Pragma: private");
		header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
		       &&
		  (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime($file))) {
		  // send the last mod time of the file back
		  header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT',
		  true, 304);
		}

		ob_clean();
		flush();
		readfile($file);
		exit;
	}

	function getFile()
	{
		$id		= JRequest::getVar('id', '', 'GET');
		$config	= DiscussHelper::getConfig();

		if(empty($id))
		{
			return false;
		}

		$attachment	= JTable::getInstance( 'Attachments' , 'Discuss' );
		if(!$attachment->load( $id ))
		{
			return false;
		}

		$path = $config->get( 'attachment_path' );
		$file = JPATH_ROOT . '/media/com_easydiscuss' . $path . '/' . $attachment->path;
		if (!JFile::exists($file))
		{
			return false;
		}

		$type = explode("/", $attachment->mime);


		header('Content-Description: File Transfer');
		header('Content-Type: ' . $attachment->mime);
		header("Content-Disposition: attachment; filename=\"".basename($attachment->title)."\";" );
		header('Content-Transfer-Encoding: binary');
		// header('Expires: 0');
		// header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		// header('Pragma: public');
		header('Content-Length: ' . filesize($file));

		// http://dtbaker.com.au/random-bits/how-to-cache-images-generated-by-php.html
		header("Cache-Control: private, max-age=10800, pre-check=10800");
		header("Pragma: private");
		header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
		       &&
		  (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime($file))) {
		  // send the last mod time of the file back
		  header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT',
		  true, 304);
		}

		ob_clean();
		flush();
		readfile($file);
		exit;
	}

	function deleteFile($id)
	{
		$config	= DiscussHelper::getConfig();

		if(empty($id))
		{
			return false;
		}

		$attachment	= JTable::getInstance( 'Attachments' , 'Discuss' );
		if(!$attachment->load( $id ))
		{
			return false;
		}

		$path = $config->get( 'attachment_path' );
		$file = JPATH_ROOT . 'media/com_easydiscuss/' . $path . '/' . $attachment->path;
		if (JFile::exists($file))
		{
			if (!JFile::delete($file))
			{
				return false;
			}
		}

		return $attachment->delete();
	}
}
