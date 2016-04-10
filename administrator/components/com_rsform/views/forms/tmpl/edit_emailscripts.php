<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<legend><?php echo JText::_('RSFP_USER_EMAIL_SCRIPT'); ?></legend>
<p class="alert alert-info"><?php echo JText::_('RSFP_USER_EMAIL_SCRIPT_DESC'); ?></p>
<textarea class="rs_textarea codemirror-php rs_100" rows="20" cols="75" name="UserEmailScript" id="UserEmailScript"><?php echo $this->escape($this->form->UserEmailScript);?></textarea>

<legend><?php echo JText::_('RSFP_ADMIN_EMAIL_SCRIPT'); ?></legend>
<p class="alert alert-info"><?php echo JText::_('RSFP_ADMIN_EMAIL_SCRIPT_DESC'); ?></p>
<textarea class="rs_textarea codemirror-php rs_100" rows="20" cols="75" name="AdminEmailScript" id="AdminEmailScript"><?php echo $this->escape($this->form->AdminEmailScript);?></textarea>

<legend><?php echo JText::_('RSFP_ADDITIONAL_EMAILS_SCRIPT'); ?></legend>
<p class="alert alert-info"><?php echo JText::_('RSFP_ADDITIONAL_EMAILS_SCRIPT_DESC'); ?></p>
<textarea class="rs_textarea codemirror-php rs_100" rows="20" cols="75" name="AdditionalEmailsScript" id="AdditionalEmailsScript"><?php echo $this->escape($this->form->AdditionalEmailsScript);?></textarea>