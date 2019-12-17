<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JText::script('ERROR');
JText::script('RSFP_CONDITION_PLEASE_ADD_OPTIONS');
JText::script('RSFP_CONDITION_IS');
JText::script('RSFP_CONDITION_IS_NOT');
JText::script('COM_RSFORM_CONDITION_PLEASE_SELECT_AT_LEAST_ONE_FIELD');
JText::script('COM_RSFORM_CONDITION_PLEASE_ADD_AT_LEAST_ONE_CONDITION');

JHtml::_('formbehavior.chosen');
?>
<script type="text/javascript">
if (window.opener && window.opener.showConditions) {
	window.opener.showConditions(<?php echo $this->formId; ?>);
}
	
<?php if ($this->close) { ?>
window.close();
<?php } ?>

RSFormPro.addCondition = function() {
	<?php if (!$this->optionFields) { ?>
	Joomla.renderMessages({'error': [Joomla.JText._('RSFP_CONDITION_PLEASE_ADD_OPTIONS')]});
	<?php } else { ?>
	var newCondition = document.createElement('p');

	var spacer = document.createElement('span');
	spacer.setAttribute('class', 'rsform_spacer');
	spacer.innerHTML = '&nbsp;&nbsp;&nbsp;';

	var spacer2 = document.createElement('span');
	spacer2.setAttribute('class', 'rsform_spacer');
	spacer2.innerHTML = '&nbsp;&nbsp;&nbsp;';

	var spacer3 = document.createElement('span');
	spacer3.setAttribute('class', 'rsform_spacer');
	spacer3.innerHTML = '&nbsp;&nbsp;&nbsp;';
	
	// fields
	var fields = document.createElement('select');
	fields.name = 'detail_component_id[]';
	fields.setAttribute('name', 'detail_component_id[]');
	fields.onchange = RSFormPro.conditionChangeField;

	var option;
	<?php foreach ($this->optionFields as $field) { ?>
	option 		    = document.createElement('option');
	option.value 	= '<?php echo $field->ComponentId; ?>';
	option.text 	= <?php echo json_encode($field->ComponentName); ?>;
	fields.options.add(option);
	<?php } ?>
	
	// operator
	var operator = document.createElement('select');
	operator.setAttribute('class', 'input-small');
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
	var selected_values = RSFormPro.getConditionValues(<?php echo $this->optionFields[0]->ComponentId; ?>);
	if (selected_values !== false)
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
	var removeBtn = document.createElement('button');
	removeBtn.setAttribute('type', 'button');
	removeBtn.setAttribute('class', 'btn btn-danger btn-mini');
	removeBtn.onclick = function() {
		this.parentNode.parentNode.removeChild(this.parentNode);
	};

	var removeIcon = document.createElement('i');
	removeIcon.setAttribute('class', 'rsficon rsficon-remove');

	removeBtn.appendChild(removeIcon);

	// Append all elements
	newCondition.appendChild(fields);
	newCondition.appendChild(spacer);
	newCondition.appendChild(operator);
	newCondition.appendChild(spacer2);
	newCondition.appendChild(values);
	newCondition.appendChild(spacer3);
	newCondition.appendChild(removeBtn);

	document.getElementById('rsform_conditions').appendChild(newCondition);
	<?php } ?>
};

RSFormPro.getConditionValues = function(id) {
	var fields = [];
	
<?php foreach ($this->optionFields as $field) { ?>
	fields['<?php echo $field->ComponentId; ?>'] = [];
	<?php foreach ($field->items as $item) { ?>
    fields['<?php echo $field->ComponentId; ?>'].push({'value': <?php echo json_encode($item->value); ?>, 'text': <?php echo json_encode($item->label); ?>});
	<?php } ?>
<?php } ?>

	return typeof fields[id] !== 'undefined' ? fields[id] : false;
};

RSFormPro.conditionChangeField = function() {
	var children = this.parentNode.childNodes;

	for (var i = 0; i < children.length; i++) {
		if (children[i].nodeName === 'SELECT' && children[i].getAttribute('name') === 'value[]') {
			children[i].options.length = 0;

			var selected_values = RSFormPro.getConditionValues(this.value);
			if (selected_values !== false) {
				for (var j = 0; j < selected_values.length; j++) {
					var option = document.createElement('option');
					option.value = selected_values[j].value;
					option.text = selected_values[j].text;
					children[i].options.add(option);
				}
			}

			break;
		}
	}
};

Joomla.submitbutton = function(task) {
	if (task === 'apply' || task === 'save')
	{
		if (document.getElementById('component_id').value === '')
		{
			Joomla.renderMessages({'error': [Joomla.JText._('COM_RSFORM_CONDITION_PLEASE_SELECT_AT_LEAST_ONE_FIELD')]});

			return false;
		}

		if (document.getElementsByName('detail_component_id[]').length === 0)
		{
			Joomla.renderMessages({'error': [Joomla.JText._('COM_RSFORM_CONDITION_PLEASE_ADD_AT_LEAST_ONE_CONDITION')]});

			return false;
		}
	}

	Joomla.submitform(task);
}
</script>

<?php if (!RSFormProHelper::getConfig('global.disable_multilanguage')) { ?>
    <p><?php echo JText::sprintf('RSFP_YOU_ARE_EDITING_CONDITIONS_IN', $this->escape($this->lang)); ?></p>
<?php } ?>
<form name="adminForm" id="adminForm" method="post" action="index.php">
	<div id="rsform_conditions">
	<p>
		<button class="btn btn-success pull-left" onclick="Joomla.submitbutton('apply');" type="button"><?php echo JText::_('JAPPLY'); ?></button>
		<button class="btn btn-success pull-left" onclick="Joomla.submitbutton('save');" type="button"><?php echo JText::_('JSAVE'); ?></button>
		<button class="btn pull-left" onclick="window.close();" type="button"><?php echo JText::_('JCANCEL'); ?></button>
	</p>
	<p><br /><br /></p>
	<span class="rsform_clear_both"></span>
	<p>
		<?php echo JText::sprintf('RSFP_SHOW_FIELD_IF_THE_FOLLOWING_MATCH', $this->lists['action'], $this->lists['block'], $this->lists['allfields'], $this->lists['condition']); ?> <a class="btn btn-primary" href="javascript: void(0);" onclick="RSFormPro.addCondition();"><i class="rsficon rsficon-plus"></i></a>
	</p>
	<?php if ($this->condition->details) { ?>
		<?php foreach ($this->condition->details as $detail) { ?>
		<p>
			<?php echo JHtml::_('select.genericlist', $this->optionFields, 'detail_component_id[]', '', 'ComponentId', 'ComponentName', $detail->component_id); ?>
			<span class="rsform_spacer">&nbsp;</span>
			<?php echo JHtml::_('select.genericlist', $this->operators, 'operator[]', 'class="input-small"', 'value', 'text', $detail->operator); ?>
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
			<button type="button" class="btn btn-danger btn-mini" onclick="this.parentNode.parentNode.removeChild(this.parentNode);"><i class="rsficon rsficon-remove"></i></button>
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
window.addEventListener('DOMContentLoaded', function() {
	var detail_component_ids = document.getElementsByName('detail_component_id[]');
	for (var i = 0; i < detail_component_ids.length; i++) {
		detail_component_ids[i].onchange = RSFormPro.conditionChangeField;
	}
});
</script>