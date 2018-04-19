<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<h3 class="rsfp-legend"><?php echo JText::_('RSFP_USER_EMAIL_SCRIPT'); ?></h3>
<p class="alert alert-info"><?php echo JText::_('RSFP_USER_EMAIL_SCRIPT_DESC'); ?></p>
<?php echo RSFormProHelper::showEditor('UserEmailScript', $this->form->UserEmailScript, array('classes' => 'rs_100', 'syntax' => 'html')); ?>

<h3 class="rsfp-legend"><?php echo JText::_('RSFP_ADMIN_EMAIL_SCRIPT'); ?></h3>
<p class="alert alert-info"><?php echo JText::_('RSFP_ADMIN_EMAIL_SCRIPT_DESC'); ?></p>
<?php echo RSFormProHelper::showEditor('AdminEmailScript', $this->form->AdminEmailScript, array('classes' => 'rs_100', 'syntax' => 'html')); ?>

<h3 class="rsfp-legend"><?php echo JText::_('RSFP_ADDITIONAL_EMAILS_SCRIPT'); ?></h3>
<p class="alert alert-info"><?php echo JText::_('RSFP_ADDITIONAL_EMAILS_SCRIPT_DESC'); ?></p>
<?php echo RSFormProHelper::showEditor('AdditionalEmailsScript', $this->form->AdditionalEmailsScript, array('classes' => 'rs_100', 'syntax' => 'html')); ?>