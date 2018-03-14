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
defined('_JEXEC') or die('Restricted access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewCaptcha extends EasyDiscussView
{
	/**
	 * Allows caller to reload captcha
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function reload()
	{
		// Get the current element that is on the page
		$id = $this->input->get('id', 0, 'int');

		// Reload the captcha
		$captcha = ED::captcha();
		$table = $captcha->reload($id);

		// Generate the image source now
		$image = $captcha->getImageSource();

		return $this->ajax->resolve($table->id, $image);
	}
}
