<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<?php if (!empty($this->fields['general'])) { ?>
<table border="0" cellspacing="0" cellpadding="8">
<?php foreach ($this->fields['general'] as $field) { ?>
	<tr id="id<?php echo $field->name; ?>">
		<td><?php echo $field->body; ?></td>
	</tr>
<?php } ?>
</table>
<div class="alert alert-error" id="rsformerror0" style="display:none;"></div>
<p style="overflow:hidden"><input type="button" value="<?php echo $this->componentId ? JText::_('Update') : JText::_('JSAVE'); ?>" name="componentSaveButton" onclick="processComponent('<?php echo $this->type_id; ?>')" class="btn btn-success pull-left" /> <button type="button" class="btn pull-right rsform_close"><?php echo JText::_('RSFP_CLOSE'); ?></button></p>
<?php } ?>
{rsfsep}
<?php if (!empty($this->fields['validations'])) { ?>
<table border="0" cellspacing="0" cellpadding="8">
<?php foreach ($this->fields['validations'] as $field) { ?>
	<tr id="id<?php echo $field->name; ?>"<?php echo ($field->name == 'VALIDATIONMULTIPLE' ? ' style="display:none"' : ''); ?>>
		<td><?php echo $field->body; ?></td>
	</tr>
<?php } ?>
</table>
<div class="alert alert-error" id="rsformerror1" style="display:none;"></div>
<p style="overflow:hidden"><input type="button" value="<?php echo $this->componentId ? JText::_('Update') : JText::_('JSAVE'); ?>" name="componentSaveButton" onclick="processComponent('<?php echo $this->type_id; ?>')" class="btn btn-success pull-left" /><button type="button" class="btn pull-right rsform_close"><?php echo JText::_('RSFP_CLOSE'); ?></button></p>
<?php } ?>
{rsfsep}
<?php if (!empty($this->fields['attributes'])) { ?>
<table border="0" cellspacing="0" cellpadding="8">
<?php foreach ($this->fields['attributes'] as $field) { ?>
	<tr id="id<?php echo $field->name; ?>">
		<td><?php echo $field->body; ?></td>
	</tr>
<?php } ?>
</table>
<div class="alert alert-error" id="rsformerror2" style="display:none;"></div>
<p style="overflow:hidden"><input type="button" value="<?php echo $this->componentId ? JText::_('Update') : JText::_('JSAVE'); ?>" name="componentSaveButton" onclick="processComponent('<?php echo $this->type_id; ?>')" class="btn btn-success pull-left" /> <button type="button" class="btn pull-right rsform_close"><?php echo JText::_('RSFP_CLOSE'); ?></button></p>
<?php } ?>