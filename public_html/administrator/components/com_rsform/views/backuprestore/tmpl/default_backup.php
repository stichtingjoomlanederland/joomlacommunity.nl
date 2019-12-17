<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<div id="formsList">
	<table class="adminlist table table-striped">
	<thead>
	<tr>
		<th width="1"><input type="checkbox" name="toggle" id="toggleAll" value="" onclick="Joomla.checkAll(this);" /></th>
		<th class="title"><?php echo JText::_('RSFP_FORM_TITLE'); ?></th>
		<th class="title"><?php echo JText::_('RSFP_FORM_NAME'); ?></th>
		<th class="title" nowrap="nowrap" width="1%"><?php echo JText::_('RSFP_FORM_ID'); ?></th>
		<th class="title" nowrap="nowrap" width="1%"><?php echo JText::_('RSFP_SUBMISSIONS'); ?></th>
	</tr>
	</thead>
	<?php
	foreach ($this->forms as $i => $row)
	{
		?>
		<tr>
			<td><?php echo JHtml::_('grid.id', $i, $row->FormId); ?></td>
			<td><label for="cb<?php echo $i; ?>"><?php echo !empty($row->FormTitle) ? strip_tags($row->FormTitle) : '<em>' . JText::_('RSFP_FORM_DEFAULT_TITLE') . '</em>'; ?></label></td>
			<td><?php echo $this->escape($row->FormName); ?></td>
			<td width="1%"><?php echo $row->FormId; ?></td>
			<td width="1%"><?php echo $row->_allSubmissions; ?></td>
		</tr>
		<?php
	}
	?>
	</table>
	<?php
	$this->field->startFieldset();
	
	$field = $this->form->getField('submissions');
	$this->field->showField($field->label, $field->input);

	$field = $this->form->getField('name');
	$this->field->showField($field->label, $field->input);
	
	$this->field->showField('', '<button id="backupButton" class="btn btn-primary" type="button" onclick="Joomla.submitbutton(\'backup.start\')">'.JText::_('RSFP_BACKUP_GENERATE').'</button>', array('class' => 'form-actions'));
	
	$this->field->endFieldset();
	?>
</div>

<div class="progressWrapper" style="display: none;"><div class="progressBar" id="progressBar">0%</div></div>

<script type="text/javascript">
RSFormPro.Backup.requestTimeOut.Seconds = <?php echo (float) $this->config->get('request_timeout');?>;
<?php foreach ($this->forms as $row) { ?>
RSFormPro.Backup.submissionsCount[<?php echo $row->FormId; ?>] = <?php echo $row->_allSubmissions; ?>;
<?php } ?>
</script>