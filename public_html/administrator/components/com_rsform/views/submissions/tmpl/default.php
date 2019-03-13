<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2017 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
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
<?php
// Export Modal
$modalData = array(
	'selector'	=> 'exportModal',
	'params'	=> array('title' => JText::_('RSFP_CHOOSE_EXPORT_FORMAT')),
	'body'		=> $this->loadTemplate('modal_export')
);
echo JLayoutHelper::render('joomla.modal.main', $modalData);
?>

<form action="<?php echo JRoute::_('index.php?option=com_rsform&view=submissions'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<div id="rsform-btn-inline">
		<div class="btn-wrapper input-append">
			<input name="search" id="search" value="<?php echo $this->escape($this->filter); ?>" placeholder="<?php echo JText::_('RSFP_SEARCH'); ?>" type="text" />
				<button type="submit" class="btn">
					<span class="icon-search"></span>
				</button>
		</div>
		
		<div class="btn-wrapper">
			<button class="btn" type="button" onclick="resetForm();this.form.submit();"><?php echo JText::_( 'JCLEAR' ); ?></button>
		</div>
		
		<div class="btn-wrapper rsform-calendar-field">
			<?php echo $this->calendars['from']; ?>
		</div>
		
		<div class="btn-wrapper rsform-calendar-field">
			<?php echo $this->calendars['to']; ?>
		</div>
		
		<div class="hidden-phone btn-wrapper">
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
                        <?php echo $this->getHeaderLabel($header); ?>
					</label>
					<?php $i++; ?>
				<?php } ?>
					</div>
					<center><button class="btn btn-primary" type="button" onclick="Joomla.submitbutton('submissions.columns')"><?php echo JText::_('Submit'); ?></button></center>
				</div>
			</div>
		</div>
	</div>
	
	<div style="overflow: auto;">
	<table class="adminlist table table-striped" id="articleList">
		<thead>
		<tr>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('#'); ?></th>
			<th width="1%" nowrap="nowrap"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
			<?php foreach ($this->staticHeaders as $header) { ?>
			<th width="1%" nowrap="nowrap" <?php echo !$this->isHeaderEnabled($header, 1) ? 'style="display: none"' : ''; ?> class="title"><?php echo JHtml::_('grid.sort', JText::_('RSFP_'.$header), $header, $this->sortOrder, $this->sortColumn, 'submissions.manage'); ?></th>
			<?php } ?>
			<?php foreach ($this->headers as $header) { ?>
			<th <?php echo !$this->isHeaderEnabled($header, 0) ? 'style="display: none"' : ''; ?> class="title">
				<?php echo JHtml::_('grid.sort', $this->getHeaderLabel($header), $header, $this->sortOrder, $this->sortColumn, 'submissions.manage'); ?>
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
				<td width="1%" nowrap="nowrap"><?php echo JHtml::_('grid.id', $i, $submissionId); ?></td>
				<?php foreach ($this->staticHeaders as $header) { ?>
				<td width="1%" nowrap="nowrap" <?php echo !$this->isHeaderEnabled($header, 1) ? 'style="display: none"' : ''; ?>><?php echo $this->escape($submission[$header]); ?></td>
				<?php } ?>
				<?php foreach ($this->headers as $header) { ?>
				<td <?php echo !$this->isHeaderEnabled($header, 0) ? 'style="display: none"' : ''; ?>>
					<?php if (isset($submission['SubmissionValues'][$header]['Value'])) { ?>
						<?php if (in_array($header, $this->unescapedFields)) { ?>
							<?php echo $submission['SubmissionValues'][$header]['Value']; ?>
						<?php } else { 
							$escapedValue = $this->escape($submission['SubmissionValues'][$header]['Value']);
							if($this->form->TextareaNewLines && isset($this->specialFields['textareaFields']) && !empty($this->specialFields['textareaFields']) && in_array($header, $this->specialFields['textareaFields'])) { 
								$escapedValue = nl2br($escapedValue);
							}
							echo $escapedValue;
						?>
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
				<div class="pull-right">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
			</td>
		</tr>
	</tfoot>
	</table>
        <?php
        // Import Modal
        $modalData = array(
            'selector'	=> 'importModal',
            'params'	=> array('title' => JText::_('COM_RSFORM_IMPORT_SUBMISSIONS')),
            'body'		=> $this->loadTemplate('modal_import')
        );
        echo JLayoutHelper::render('joomla.modal.main', $modalData);
        ?>
	</div>

	<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortOrder; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="boxchecked" value="0" />
</form>

<?php JHtml::_('behavior.keepalive'); ?>