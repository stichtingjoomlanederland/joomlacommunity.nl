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
	<?php if ($field->name == 'FILESIZE' && is_callable('ini_get')) { ?>
	<tr id="idFILESIZEDIAGNOSE1">
		<td><button type="button" class="btn btn-warning" onclick="document.getElementById('idFILESIZEDIAGNOSE2').style.display = ''; document.getElementById('idFILESIZEDIAGNOSE1').style.display = 'none';"><?php echo JText::_('RSFP_COMP_FIELD_FILESIZEDIAGNOSE'); ?></button></td>
	</tr>
	<tr id="idFILESIZEDIAGNOSE2" style="display: none;">
		<td><?php echo JText::sprintf('RSFP_COMP_FIELD_FILESIZEDIAGNOSE_MSG',
			ini_get('file_uploads') ? '<span class="rsform_ok">'.JText::_('RSFP_COMP_FVALUE_YES').'</span>' : '<strong class="rsform_notok">'.JText::_('RSFP_COMP_FVALUE_NO').'</strong>',
			ini_get('upload_tmp_dir') ? '<span class="rsform_ok">'.JText::_('RSFP_COMP_FVALUE_YES').'</span>' : '<strong class="rsform_notok">'.JText::_('RSFP_COMP_FVALUE_NO').'</strong>',
			ini_get('max_file_uploads') ? '<span class="rsform_ok">'.ini_get('max_file_uploads').'</span>' : '<strong class="rsform_notok">'.ini_get('max_file_uploads').'</strong>',
			ini_get('upload_max_filesize') ? '<span class="rsform_ok">'.ini_get('upload_max_filesize').'</span>' : '<strong class="rsform_notok">'.ini_get('upload_max_filesize').'</strong>',
			(int) ini_get('post_max_size') >= (int) ini_get('upload_max_filesize') ? '<span class="rsform_ok">'.ini_get('post_max_size').'</span>' : '<strong class="rsform_notok">'.ini_get('post_max_size').'</strong>',
			(int) ini_get('memory_limit') >= (int) ini_get('post_max_size') ? '<span class="rsform_ok">'.ini_get('memory_limit').'</span>' : '<strong class="rsform_notok">'.ini_get('memory_limit').'</strong>'
			) ?></td>
	</tr>
	<?php } ?>
<?php } ?>
</table>
<div class="alert alert-error" id="rsformerror2" style="display:none;"></div>
<p style="overflow:hidden"><input type="button" value="<?php echo $this->componentId ? JText::_('Update') : JText::_('JSAVE'); ?>" name="componentSaveButton" onclick="processComponent('<?php echo $this->type_id; ?>')" class="btn btn-success pull-left" /> <button type="button" class="btn pull-right rsform_close"><?php echo JText::_('RSFP_CLOSE'); ?></button></p>
<?php } ?>