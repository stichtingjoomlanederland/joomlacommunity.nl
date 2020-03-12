<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<select name="ExportRows">
	<option value="0" <?php echo $this->exportAll ? 'selected="selected"' : ''; ?>><?php echo JText::_('RSFP_EXPORT_ALL_ROWS'); ?></option>
	<option value="<?php echo implode(',', $this->exportSelected); ?>" <?php echo !$this->exportAll ? 'selected="selected"' : ''; ?>><?php echo JText::_('RSFP_EXPORT_SELECTED_ROWS'); ?> (<?php echo $this->exportSelectedCount; ?>)</option>
	<option value="-1"><?php echo JText::_('RSFP_EXPORT_FILTERED_ROWS'); ?> (<?php echo $this->exportFilteredCount; ?>)</option>
</select>

<table class="adminlist table table-striped">
	<tr>
		<td><input type="checkbox" onclick="toggleCheckColumns();" id="checkColumns" /></td>
		<td colspan="2"><label for="checkColumns"><strong><?php echo JText::_('RSFP_CHECK_ALL'); ?></strong></label></td>
	</tr>
	<thead>
	<tr>
		<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_EXPORT'); ?></th>
		<th class="title"><?php echo JText::_('RSFP_EXPORT_SUBMISSION_INFO'); ?></th>
		<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_EXPORT_COLUMN_ORDER'); ?></th>
	</tr>
	</thead>
	<?php $k = 0; ?>
	<?php $i = 1; ?>
	<?php foreach ($this->staticHeaders as $header) { ?>
		<tr class="row<?php echo $k; ?>">
			<td><input type="checkbox" onchange="updateCSVPreview();" name="ExportSubmission[<?php echo $header; ?>]" id="header<?php echo $i; ?>" value="<?php echo $header; ?>" <?php echo $this->isHeaderEnabled($header, 1) ? 'checked="checked"' : ''; ?> /></td>
			<td><label for="header<?php echo $i; ?>"><?php echo JText::_('RSFP_'.$header); ?></label></td>
			<td><input type="text" onkeyup="updateCSVPreview();" style="text-align: center" name="ExportOrder[<?php echo $header; ?>]" value="<?php echo $i; ?>" size="3"/></td>
		</tr>
		<?php $i++; ?>
		<?php $k=1-$k; ?>
	<?php } ?>
	<thead>
	<tr>
		<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_EXPORT'); ?></th>
		<th class="title"><?php echo JText::_('RSFP_EXPORT_COMPONENTS'); ?></th>
		<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_EXPORT_COLUMN_ORDER'); ?></th>
	</tr>
	</thead>
	<?php foreach ($this->headers as $header) { ?>
		<tr class="row<?php echo $k; ?>">
			<td><input type="checkbox" onchange="updateCSVPreview();" name="ExportComponent[<?php echo $header; ?>]" id="header<?php echo $i; ?>" value="<?php echo $header; ?>" <?php echo $this->isHeaderEnabled($header, 0) ? 'checked="checked"' : ''; ?> /></td>
			<td><label for="header<?php echo $i; ?>">
					<?php echo $this->getHeaderLabel($header); ?>
				</label></td>
			<td><input type="text" onkeyup="updateCSVPreview();" style="text-align: center" name="ExportOrder[<?php echo $header; ?>]" value="<?php echo $i; ?>" size="3" /></td>
		</tr>
		<?php $i++; ?>
		<?php $k=1-$k; ?>
	<?php } ?>
</table>

<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('submissions.export.task');" name="Export"><?php echo JText::_('RSFP_EXPORT');?></button>