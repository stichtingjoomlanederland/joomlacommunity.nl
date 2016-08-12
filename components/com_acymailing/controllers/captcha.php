<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class CaptchaController extends acymailingController{
	function __construct($config = array())
	{
		$config = acymailing_config();

		$formname = JRequest::getCmd('acyformname');
		if(empty($formname)){
			$type = 'component';
		}else{
			$type = 'module';
		}
		$captchaClass = acymailing_get('class.acycaptcha');
		$captchaClass->background_color = $config->get('captcha_background_'.$type);
		$captchaClass->colors = explode(',',$config->get('captcha_color_'.$type));
		$captchaClass->width = $config->get('captcha_width_'.$type);
		$captchaClass->height =  $config->get('captcha_height_'.$type);
		$captchaClass->nb_letters = $config->get('captcha_nbchar_'.$type);
		$captchaClass->letters = $config->get('captcha_chars');

		$captchaClass->state = 'acycaptcha'.$type.$formname;

		$captchaClass->get();
		$captchaClass->displayImage();
	}
}
