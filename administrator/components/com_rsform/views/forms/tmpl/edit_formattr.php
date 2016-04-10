<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<fieldset>
	<legend><?php echo JText::_('RSFP_FORM_ATTR_HTML'); ?></legend>
	<table class="admintable" width="100%">
		<tr>
			<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_FORM_ACTION'); ?></td>
			<td><input name="CSSAction" class="rs_inp rs_80" value="<?php echo $this->escape($this->form->CSSAction); ?>" size="105" id="CSSAction" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><?php echo JText::_('RSFP_FORM_ACTION_DESC'); ?></td>
		</tr>
		<tr>
			<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_FORM_CSS_NAME'); ?></td>
			<td><input name="CSSName" class="rs_inp rs_80" value="<?php echo $this->escape($this->form->CSSName); ?>" size="105" id="CSSName" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><?php echo JText::_('RSFP_FORM_CSS_NAME_DESC'); ?></td>
		</tr>
		<tr>
			<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_FORM_ADDITIONAL_ATTRIBUTES'); ?></td>
			<td><input name="CSSAdditionalAttributes" class="rs_inp rs_80" value="<?php echo $this->escape($this->form->CSSAdditionalAttributes); ?>" size="105" id="CSSAdditionalAttributes" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><?php echo JText::_('RSFP_FORM_ADDITIONAL_ATTRIBUTES_DESC'); ?></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend><?php echo JText::_('RSFP_FORM_ATTR_CSS'); ?></legend>
	<table class="admintable" width="100%">
		<tr>
			<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_FORM_CSS_ID'); ?></td>
			<td><input name="CSSId" class="rs_inp rs_80" value="<?php echo $this->escape($this->form->CSSId); ?>" size="105" id="CSSId" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><?php echo JText::_('RSFP_FORM_CSS_ID_DESC'); ?></td>
		</tr>
		<tr>
			<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_FORM_CSS_CLASS'); ?></td>
			<td><input name="CSSClass" class="rs_inp rs_80" value="<?php echo $this->escape($this->form->CSSClass); ?>" size="105" id="CSSClass" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><?php echo JText::_('RSFP_FORM_CSS_CLASS_DESC'); ?></td>
		</tr>
	</table>
</fieldset>