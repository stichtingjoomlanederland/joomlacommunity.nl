<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<table class="admintable table">
	<tr class="info">
		<td align="right" class="key"><?php echo RSFormProHelper::translateIcon(); ?> <?php echo JText::_('RSFP_META_DESC'); ?></td>
		<td><?php echo JText::_('RSFP_META_DESC_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><textarea rows="10" cols="75" name="MetaDesc" id="MetaDesc" class="rs_textarea" style="width:95%;"><?php echo $this->escape($this->form->MetaDesc); ?></textarea></td>
	</tr>
	<tr class="info">
		<td align="right" class="key"><?php echo RSFormProHelper::translateIcon(); ?> <?php echo JText::_('RSFP_META_KEY'); ?></td>
		<td><?php echo JText::_('RSFP_META_KEY_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><textarea rows="10" cols="75" name="MetaKeywords" id="MetaKeywords" class="rs_textarea" style="width:95%;"><?php echo $this->escape($this->form->MetaKeywords); ?></textarea></td>
	</tr>
	<tr>
		<td align="right" class="key"><?php echo JText::_('RSFP_META_TITLE'); ?></td>
		<td><?php echo $this->lists['MetaTitle'];?></td>
	</tr>
</table>