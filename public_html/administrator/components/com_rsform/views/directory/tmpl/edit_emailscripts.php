<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access'); ?>
<table class="admintable table">
	<tr class="info">
		<td width="250" style="width: 250px;" align="right" class="key"><?php echo JText::_('RSFP_SUBM_DIR_SCRIPTS_EMAIL_CREATED'); ?></td>
		<td><?php echo JText::_('RSFP_SUBM_DIR_SCRIPTS_EMAIL_CREATED_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo RSFormProHelper::showEditor('jform[EmailsCreatedScript]', $this->directory->EmailsCreatedScript, array('classes' => 'rs_100', 'syntax' => 'php', 'id' => 'EmailsCreatedScript')); ?></td>
	</tr>
	<tr class="info">
		<td width="250" style="width: 250px;" align="right" class="key"><?php echo JText::_('RSFP_SUBM_DIR_SCRIPTS_EMAIL'); ?></td>
		<td><?php echo JText::_('RSFP_SUBM_DIR_SCRIPTS_EMAIL_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo RSFormProHelper::showEditor('jform[EmailsScript]', $this->directory->EmailsScript, array('classes' => 'rs_100', 'syntax' => 'php', 'id' => 'EmailsScript')); ?></td>
	</tr>
</table>