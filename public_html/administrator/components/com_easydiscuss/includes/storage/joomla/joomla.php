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

class EasyDiscussStorageJoomla implements EasyDiscussStorageInterface
{
	private $lib 	= null;

	public function __construct()
	{
	}

	public function init()
	{
	}

	public function containerExists( $container )
	{
	}

	public function createContainer( $container )
	{
	}

	/**
	 * Returns the absolute path to the object
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The storage id
	 * @return	string	The absolute URI to the object
	 */
	public function getPermalink( $relativePath )
	{
		return rtrim( JURI::root() , '/' ) . '/' . $relativePath;
	}

	/**
	 * Pushes a file to the remote repository
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The relative path to the file
	 * @return
	 */
	public function upload($fileName, $source, $destination, $deleteOriginalFile = false)
	{
		// Here we can ignore the file name since we don't need to do anything to it.
		JFile::copy($source, $destination);

		if ($deleteOriginalFile) {
			JFile::delete($source);
		}

		return true;
	}

	/**
	 * Downloads a file from the remote repository
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function download($relativePath, $saveTo = '')
	{
	}

	/**
	 * Deletes a file from the remote repository
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The relative path to the file
	 * @return
	 */
	public function delete($path)
	{
		return JFile::delete($path);
	}
}
