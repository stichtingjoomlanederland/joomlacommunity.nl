<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

echo JHtml::_('rsfieldset.start', 'adminform', '');
echo JHtml::_('rsfieldset.element', $this->form->getLabel('notification_message'), $this->form->getInput('notification_message'));
echo JHtml::_('rsfieldset.end');

echo JHtml::_('rsfieldset.start', 'adminform', '');
echo JHtml::_('rsfieldset.element', $this->form->getLabel('subscription_message'), $this->form->getInput('subscription_message'));
echo JHtml::_('rsfieldset.end');

echo JHtml::_('rsfieldset.start', 'adminform', '');
echo JHtml::_('rsfieldset.element', $this->form->getLabel('report_message'), $this->form->getInput('report_message'));
echo JHtml::_('rsfieldset.end');