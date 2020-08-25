<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

echo $this->form->renderField('notification_message');
echo $this->form->renderField('subscription_message');
echo $this->form->renderField('report_message');