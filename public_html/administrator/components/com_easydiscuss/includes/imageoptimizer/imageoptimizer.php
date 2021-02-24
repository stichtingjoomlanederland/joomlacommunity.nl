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

jimport('joomla.filesystem.file');

class EasyDiscussImageOptimizer extends EasyDiscuss
{
	/**
	 * Determines if this feature is enabled
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function enabled()
	{
		if (!$this->config->get('optimize_image') || !$this->config->get('optimize_key')) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieves information about a file
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getImageInfo($path)
	{
		static $items = array();

		if (!isset($items[$path])) {
			$items[$path] = false;
			$data = @getimagesize($path);

			if ($data !== false) {
				$items[$path] = $data;
			}
		}

		return $items[$path];
	}

	/**
	 * Optimize images
	 *
	 * @since	5.0.0.
	 * @access	public
	 */
	public function optimize($pathToImage)
	{
		if (!$this->enabled()) {
			return false;
		}

		// Get the file info
		$fileInfo = $this->getImageInfo($pathToImage);

		$post = array(
			'file' => class_exists('CURLFile', false) ? new CURLFile($pathToImage, $fileInfo['mime']) : "@" . $pathToImage,
			'service_key' => $this->config->get('optimize_key'),
			'domain' => rtrim(JURI::root(), '/')
		);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, ED_OPTIMIZER_SERVER);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100000);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$result = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

		// We need to know the response status
		if ($code == 400) {
			return $result;
		}

		// Resave the file
		$state = JFile::write($pathToImage, $result);

		return $state;
	}
}
