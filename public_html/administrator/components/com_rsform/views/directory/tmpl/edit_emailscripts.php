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
		<td colspan="2"><textarea class="rs_textarea codemirror-php" rows="20" cols="75" name="jform[EmailsCreatedScript]" id="EmailsCreatedScript" style="width:100%;"><?php echo $this->escape($this->directory->EmailsCreatedScript);?></textarea></td>
	</tr>
	<tr class="info">
		<td width="250" style="width: 250px;" align="right" class="key"><?php echo JText::_('RSFP_SUBM_DIR_SCRIPTS_EMAIL'); ?></td>
		<td><?php echo JText::_('RSFP_SUBM_DIR_SCRIPTS_EMAIL_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><textarea class="rs_textarea codemirror-php" rows="20" cols="75" name="jform[EmailsScript]" id="EmailsScript" style="width:100%;"><?php echo $this->escape($this->directory->EmailsScript);?></textarea></td>
	</tr>
</table>