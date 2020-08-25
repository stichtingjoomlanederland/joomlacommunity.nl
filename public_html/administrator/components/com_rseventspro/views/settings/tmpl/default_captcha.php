<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

echo JHtml::_('rsfieldset.start', 'adminform');
echo JHtml::_('rsfieldset.element', $this->form->getLabel('captcha'), $this->form->getInput('captcha'));
echo JHtml::_('rsfieldset.element', $this->form->getLabel('captcha_use'), $this->form->getInput('captcha_use'));
echo JHtml::_('rsfieldset.end');

echo $this->form->renderFieldset('captcha');
echo $this->form->renderFieldset('hcaptcha');