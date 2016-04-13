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

require_once(__DIR__ . '/controller.php');

class EasyDiscussControllerCaptcha extends EasyDiscussController
{
	/**
	 * Generates the captcha image
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function generate()
	{
		// Get the captcha id
		$id = $this->input->get('id', 0, 'int');

		// Load the captcha table
		$captcha = ED::captcha();
		$captcha->load($id);

		// Renew old captcha items
		$captcha->clearExpired();

		// Generate a new hash
		$captcha->generateHash();

		// Get the image resource
		$resource = $captcha->drawImage();

		// Output the new image now
		header('Content-type: image/jpeg');
	    imagejpeg($resource);
	    
	    // Once it is output, we need to flush it.
	    imagedestroy($resource);
	    exit;
	}
}
