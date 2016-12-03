<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.calendar');
?>
<script type="text/javascript">
function submitbutton(task)
{
	if (task == 'submissions.resend')
	{
		if (document.adminForm.boxchecked.value == 0)
			alert('<?php echo addslashes(JText::sprintf('RSFP_PLEASE_MAKE_SELECTION_TO', JText::_('RSFP_RESEND'))); ?>');
		else
			submitform(task);
	}
	else
		submitform(task);
}

Joomla.submitbutton = submitbutton;

function toggleCheckColumns()
{
	var tocheck = document.getElementById('checkColumns').checked;
	var staticcolumns = document.getElementsByName('staticcolumns[]');
	for (i=0; i<staticcolumns.length; i++)
		staticcolumns[i].checked = tocheck;
		
	var columns = document.getElementsByName('columns[]');
	for (i=0; i<columns.length; i++)
		columns[i].checked = tocheck;
}

function resetForm()
{
	document.getElementById('Language').selectedIndex = 0;
	document.getElementById('Language').value = '';
	document.getElementById('search').value   = '';
	document.getElementById('search').value   = '';
	document.getElementById('dateFrom').value = '';
	document.getElementById('dateTo').value   = '';
}
</script>

<style type="text/css">
.input-append {
	display: inline;
}

.rs_inp {
	margin-bottom: 9px !important;
}

#columnsDiv label {
	display: block;
}
</style>

<form action="<?php echo JRoute::_('index.php?option=com_rsform&view=submissions'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminform">
		<tr>
			<td width="100%">
				<div class="pull-left">
					<?php echo JText::_('RSFP_VIEW_SUBMISSIONS_FOR'); ?> <?php echo $this->lists['forms']; ?>
					<?php echo JText::_('RSFP_SHOW_SUBMISSIONS_LANGUAGE'); ?> <?php echo $this->lists['Languages']; ?>
					<?php echo JText::_('RSFP_SEARCH'); ?>
					<input type="text" class="rs_inp rs_10" name="search" id="search" value="<?php echo $this->escape($this->filter); ?>" class="text_area" onchange="document.adminForm.submit();" />
					<span class="hidden-phone">
					<?php echo JText::_('RSFP_DATE'); ?>
					<?php echo $this->calendars['from']; ?>
					<?php echo JText::_('RSFP_TO'); ?>
					<?php echo $this->calendars['to']; ?>
					</span>
				</div>
				<button class="pull-left btn btn-success" type="button" onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button class="pull-left btn btn-danger" type="button" onclick="resetForm();this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<div class="hidden-phone">
					<button class="btn" type="button" onclick="toggleCustomizeColumns();"><?php echo JText::_('RSFP_CUSTOMIZE_COLUMNS'); ?></button>
					<div id="columnsContainer">
						<div id="columnsDiv">
							<label for="checkColumns" class="checkbox"><input type="checkbox" onclick="toggleCheckColumns();" id="checkColumns" /> <strong><?php echo JText::_('RSFP_CHECK_ALL'); ?></strong></label>
							<div id="columnsInnerDiv">
							<?php $i = 0; ?>
						<?php foreach ($this->staticHeaders as $header) { ?>
							 <label for="column<?php echo $i; ?>" class="checkbox"><input type="checkbox" <?php echo $this->isHeaderEnabled($header, 1) ? 'checked="checked"' : ''; ?> name="staticcolumns[]" value="<?php echo $this->escape($header); ?>" id="column<?php echo $i; ?>" /><?php echo JText::_('RSFP_'.$header); ?></label>
							<?php $i++; ?>
						<?php } ?>
						<?php foreach ($this->headers as $header) { ?>
							<label for="column<?php echo $i; ?>" class="checkbox">
							<input type="checkbox" <?php echo $this->isHeaderEnabled($header, 0) ? 'checked="checked"' : ''; ?> name="columns[]" value="<?php echo $this->escape($header); ?>" id="column<?php echo $i; ?>" /> 
							<?php if ($header == '_STATUS') echo JText::_('RSFP_PAYMENT_STATUS'); elseif ($header == '_ANZ_STATUS') echo JText::_('RSFP_ANZ_STATUS'); else echo $header; ?>
							</label>
							<?php $i++; ?>
						<?php } ?>
							</div>
							<center><button class="btn btn-primary" type="button" onclick="submitbutton('submissions.columns')"><?php echo JText::_('Submit'); ?></button></center>
						</div>
					</div>
				</div>
			</td>
		</tr>
	</table>
	
	<div style="overflow: auto;">
	<table class="adminlist table table-striped" id="articleList">
		<thead>
		<tr>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('#'); ?></th>
			<th width="1%" nowrap="nowrap"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
			<?php foreach ($this->staticHeaders as $header) { ?>
			<th width="1%" nowrap="nowrap" <?php echo !$this->isHeaderEnabled($header, 1) ? 'style="display: none"' : ''; ?> class="title"><?php echo JHTML::_('grid.sort', JText::_('RSFP_'.$header), $header, $this->sortOrder, $this->sortColumn, 'submissions.manage'); ?></th>
			<?php } ?>
			<?php foreach ($this->headers as $header) { ?>
			<th <?php echo !$this->isHeaderEnabled($header, 0) ? 'style="display: none"' : ''; ?> class="title">
				<?php if ($header == '_STATUS') $htitle = JText::_('RSFP_PAYMENT_STATUS'); elseif ($header == '_ANZ_STATUS') $htitle = JText::_('RSFP_ANZ_STATUS'); else $htitle = $header; ?>
				<?php echo JHTML::_('grid.sort', $htitle, $header, $this->sortOrder, $this->sortColumn, 'submissions.manage'); ?>
			</th>
			<?php } ?>
		</tr>
		</thead>
		<?php
		$i = 0;
		$k = 0;
		foreach ($this->submissions as $submissionId => $submission) { ?>
			<tr class="row<?php echo $k; ?>">
				<td width="1%" nowrap="nowrap" align="center"><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.id', $i, $submissionId); ?></td>
				<?php foreach ($this->staticHeaders as $header) { ?>
				<td width="1%" nowrap="nowrap" <?php echo !$this->isHeaderEnabled($header, 1) ? 'style="display: none"' : ''; ?>><?php echo $this->escape($submission[$header]); ?></td>
				<?php } ?>
				<?php foreach ($this->headers as $header) { ?>
				<td <?php echo !$this->isHeaderEnabled($header, 0) ? 'style="display: none"' : ''; ?>>
					<?php if (isset($submission['SubmissionValues'][$header]['Value'])) { ?>
						<?php if (in_array($header, $this->unescapedFields)) { ?>
							<?php echo $submission['SubmissionValues'][$header]['Value']; ?>
						<?php } else { ?>
							<?php echo $this->escape($submission['SubmissionValues'][$header]['Value']); ?>
						<?php } ?>
					<?php } else { ?>
					&nbsp;
					<?php } ?>
				</td>
				<?php } ?>
			</tr>
		<?php
			$i++;
			$k=1-$k;
		}
		?>
	</table>
	</div>
	
	<table class="adminlist table table-striped" id="articleList">
	<tfoot>
		<tr>
			<td>
				<div class="pull-left">
					<?php echo $this->pagination->getListFooter(); ?>
				</div>
				<?php if (RSFormProHelper::isJ('3.0')) { ?>
				<div class="pull-right">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
				<?php } ?>
			</td>
		</tr>
	</tfoot>
	</table>

	<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortOrder; ?>" />
	<input type="hidden" name="formId" value="<?php echo $this->formId; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="boxchecked" value="0" />
</form>

<?php JHTML::_('behavior.keepalive'); ?>