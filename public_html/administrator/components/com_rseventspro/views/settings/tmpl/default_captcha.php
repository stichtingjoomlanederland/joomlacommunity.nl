<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

echo $this->form->renderField('captcha');
echo $this->form->renderField('captcha_use');
echo $this->form->renderFieldset('captcha');
echo $this->form->renderFieldset('hcaptcha');