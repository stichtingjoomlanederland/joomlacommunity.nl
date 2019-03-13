<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JText::script('RSFP_CONDITION_PLEASE_ADD_OPTIONS');
JText::script('RSFP_CONDITION_IS');
JText::script('RSFP_CONDITION_IS_NOT');
?>
<script type="text/javascript">
if (window.opener && window.opener.showConditions)
	window.opener.showConditions(<?php echo $this->formId; ?>);
	
<?php if ($this->close) { ?>
window.close();
<?php } ?>

function rsform_add_condition() {
	<?php if (!$this->optionFields) { ?>
	alert(Joomla.JText._('RSFP_CONDITION_PLEASE_ADD_OPTIONS'));
	<?php } else { ?>
	var new_condition = jQuery('<p>');
	
	var spacer = jQuery('<span>', {'class': 'rsform_spacer'}).html('&nbsp;&nbsp;&nbsp;');
	var spacer2 = jQuery('<span>', {'class': 'rsform_spacer'}).html('&nbsp;&nbsp;&nbsp;');
	var spacer3 = jQuery('<span>', {'class': 'rsform_spacer'}).html('&nbsp;&nbsp;&nbsp;');
	
	// fields
	var fields = document.createElement('select');
	fields.name = 'detail_component_id[]';
	fields.setAttribute('name', 'detail_component_id[]');
	fields.onchange = rsform_change_field;

	var option;
	<?php foreach ($this->optionFields as $field) { ?>
	option 		    = document.createElement('option');
	option.value 	= '<?php echo $field->ComponentId; ?>';
	option.text 	= '<?php echo addslashes($field->ComponentName); ?>';
	fields.options.add(option);
	<?php } ?>
	
	// operator
	var operator = document.createElement('select');
	operator.name = 'operator[]';

	option 		    = document.createElement('option');
	option.value 	= 'is';
	option.text 	= Joomla.JText._('RSFP_CONDITION_IS');
	operator.options.add(option);

	option 		    = document.createElement('option');
	option.value 	= 'is_not';
	option.text 	= Joomla.JText._('RSFP_CONDITION_IS_NOT');
	operator.options.add(option);
	
	// values
	var values = document.createElement('select');
	values.name = 'value[]';
	selected_values = rsform_get_field_value(<?php echo $this->optionFields[0]->ComponentId; ?>);
	if (selected_values != false)
	{
		for (var i=0; i<selected_values.length; i++)
		{
			option 		    = document.createElement('option');
			option.value	= selected_values[i].value;
			option.text		= selected_values[i].text;
			values.options.add(option);
		}
	}
	
	// remove button
	var remove = jQuery('<a>', {
		'href': 'javascript:void(0);'
	}).append('<a class="btn btn-danger btn-mini" href="javascript:void(0);"><i class="rsficon rsficon-remove"></i></a>').click(function() {
		jQuery(this).parent('p').remove();
	});
	
	new_condition.append(fields, spacer, operator, spacer2, values, spacer3, remove);
	
	jQuery('#rsform_conditions').append(new_condition);
	<?php } ?>
}

function rsform_get_field_value(id) {
	var fields = [];
	
<?php foreach ($this->optionFields as $field) { ?>
	fields['<?php echo $field->ComponentId; ?>'] = [];
	<?php foreach ($field->items as $item) { ?>
    fields['<?php echo $field->ComponentId; ?>'].push({'value': <?php echo json_encode($item->value); ?>, 'text': <?php echo json_encode($item->label); ?>});
	<?php } ?>
<?php } ?>

	return typeof(fields[id]) != 'undefined' ? fields[id] : false;
}

function rsform_change_field() {
	values = jQuery(this).parent().children('select')[2];
	values.options.length = 0;
	
	selected_values = rsform_get_field_value(this.value);
	if (selected_values != false)
	{
		for (var i=0; i<selected_values.length; i++)
		{
			var option 		= document.createElement('option');
			option.value	= selected_values[i].value;
			option.text		= selected_values[i].text;
			values.options.add(option);
		}
	}
}
</script>

<style type="text/css">
.rsform_spacer {
	margin-right: 3px;
}
</style>

<?php if (!RSFormProHelper::getConfig('global.disable_multilanguage')) { ?>
    <p><?php echo JText::sprintf('RSFP_YOU_ARE_EDITING_CONDITIONS_IN', $this->escape($this->lang)); ?></p>
<?php } ?>
<form name="adminForm" id="adminForm" method="post" action="index.php">
	<div id="rsform_conditions">
	<p>
		<button class="btn btn-success pull-left" onclick="Joomla.submitform('apply');" type="button"><?php echo JText::_('JAPPLY'); ?></button>
		<button class="btn btn-success pull-left" onclick="Joomla.submitform('save');" type="button"><?php echo JText::_('JSAVE'); ?></button>
		<button class="btn pull-left" onclick="window.close();" type="button"><?php echo JText::_('JCANCEL'); ?></button>
	</p>
	<p><br /><br /></p>
	<span class="rsform_clear_both"></span>
	<p>
		<?php echo JText::sprintf('RSFP_SHOW_FIELD_IF_THE_FOLLOWING_MATCH', $this->lists['action'], $this->lists['block'], $this->lists['allfields'], $this->lists['condition']); ?> <a class="btn btn-primary" href="javascript: void(0);" onclick="rsform_add_condition();"><i class="rsficon rsficon-plus"></i></a>
	</p>
	<?php if ($this->condition->details) { ?>
		<?php foreach ($this->condition->details as $detail) { ?>
		<p>
			<?php echo JHtml::_('select.genericlist', $this->optionFields, 'detail_component_id[]', '', 'ComponentId', 'ComponentName', $detail->component_id); ?>
			<span class="rsform_spacer">&nbsp;</span>
			<?php echo JHtml::_('select.genericlist', $this->operators, 'operator[]', '', 'value', 'text', $detail->operator); ?>
			<span class="rsform_spacer">&nbsp;</span>
			<select name="value[]">
			<?php foreach ($this->optionFields as $field) { ?>
                <?php if ($field->ComponentId != $detail->component_id) continue; ?>
                <?php foreach ($field->items as $item) { ?>
                    <option <?php if ($item->value == $detail->value) { ?>selected="selected"<?php } ?> value="<?php echo $this->escape($item->value); ?>"><?php echo $this->escape($item->label); ?></option>
                <?php } ?>
			<?php } ?>
			</select>
			<span class="rsform_spacer">&nbsp;</span>
			<a class="btn btn-danger btn-mini" href="javascript:void(0);" onclick="jQuery(this).parent('p').remove();"><i class="rsficon rsficon-remove"></i></a>
		</p>
		<?php } ?>
	<?php } ?>
	</div>
	
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="controller" value="conditions" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="formId" value="<?php echo $this->formId; ?>" />
	<input type="hidden" name="form_id" value="<?php echo $this->formId; ?>" />
	<input type="hidden" name="cid" value="<?php echo (int) $this->condition->id; ?>" />
	<input type="hidden" name="id" value="<?php echo (int) $this->condition->id; ?>" />
	<input type="hidden" name="lang_code" value="<?php echo $this->escape($this->lang); ?>" />
</form>

<script type="text/javascript">
var detail_component_ids = document.getElementsByName('detail_component_id[]');
for (var i=0; i<detail_component_ids.length; i++)
	detail_component_ids[i].onchange = rsform_change_field;
</script>