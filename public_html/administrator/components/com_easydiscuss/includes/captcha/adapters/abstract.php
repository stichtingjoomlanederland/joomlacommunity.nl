<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

abstract class EasyDiscussCaptchaAbstract extends EasyDiscuss
{
	// Properties
	public $table = null;

	// Validates the captcha response
	abstract public function validate($data = array());

	// Generates the captcha form
	abstract public function html();

	// Allows caller to reload the captcha
	abstract public function reload($previousCaptchaId = null);

	// Get the image source for captcha
	abstract public function getImageSource();

	public function isInvisible()
	{
		return false;
	}
}