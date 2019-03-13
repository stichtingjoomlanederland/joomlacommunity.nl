<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access'); ?>
<table class="admintable table">
	<tr class="info">
		<td width="250" style="width: 250px;" align="right" class="key"><?php echo JText::_('RSFP_SUBM_DIR_SCRIPTS_LIST'); ?></td>
		<td><?php echo JText::_('RSFP_SUBM_DIR_SCRIPTS_LIST_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo RSFormProHelper::showEditor('jform[ListScript]', $this->directory->ListScript, array('classes' => 'rs_100', 'syntax' => 'php', 'id' => 'ListScript')); ?></td>
	</tr>
	<tr class="info">
		<td width="250" style="width: 250px;" align="right" class="key"><?php echo JText::_('RSFP_SUBM_DIR_SCRIPTS_DETAILS'); ?></td>
		<td><?php echo JText::_('RSFP_SUBM_DIR_SCRIPTS_DETAILS_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo RSFormProHelper::showEditor('jform[DetailsScript]', $this->directory->DetailsScript, array('classes' => 'rs_100', 'syntax' => 'php', 'id' => 'DetailsScript')); ?></td>
	</tr>
</table>