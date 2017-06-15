<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var  \Akeeba\Backup\Admin\View\Manage\Html  $this */

$urlIncludeFolders = addslashes(JUri::base() . 'index.php?option=com_akeeba&view=IncludeFolders&task=ajax');
$urlBrowser = addslashes(JUri::base() . 'index.php?option=com_akeeba&view=Browser&processfolder=1&tmpl=component&folder=');
$escapedOrder = addslashes($this->order);
$js = <<< JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
Joomla.orderTable = function () {
	table = document.getElementById("sortTable");
	direction = document.getElementById("directionTable");
	order = table.options[table.selectedIndex].value;
	if (order != '$escapedOrder')
	{
		dirn = 'asc';
	}
	else
	{
		dirn = direction.options[direction.selectedIndex].value;
	}
	Joomla.tableOrdering(order, dirn, '');
}

JS;

$this->getContainer()->template->addJSInline($js);

?>

<?php if($this->promptForBackupRestoration): ?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/Manage/howtorestore_modal'); ?>
<?php endif; ?>

<div class="alert alert-info">
	<button class="close" data-dismiss="alert">×</button>
	<h4 class="alert-heading"><?php echo \JText::_('COM_AKEEBA_BUADMIN_LABEL_HOWDOIRESTORE_LEGEND'); ?></h4>

	<?php echo \JText::sprintf('COM_AKEEBA_BUADMIN_LABEL_HOWDOIRESTORE_TEXT_PRO',
			'https://www.akeebabackup.com/videos/1212-akeeba-backup-core/1618-abtc04-restore-site-new-server.html',
			'index.php?option=com_akeeba&view=Transfer',
			'https://www.akeebabackup.com/latest-kickstart-core.zip'
			); ?>
</div>

<div id="j-main-container">
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<input type="hidden" name="option" id="option" value="com_akeeba"/>
		<input type="hidden" name="view" id="view" value="Manage"/>
		<input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
		<input type="hidden" name="task" id="task" value="default"/>
		<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->escape($this->order); ?>"/>
		<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->escape($this->order_Dir); ?>"/>
		<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>

		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<input type="text" name="description" placeholder="<?php echo \JText::_('COM_AKEEBA_BUADMIN_LABEL_DESCRIPTION'); ?>"
					   id="filter_description"
					   value="<?php echo $this->escape($this->fltDescription); ?>"
					   title="<?php echo \JText::_('COM_AKEEBA_BUADMIN_LABEL_DESCRIPTION'); ?>"/>
			</div>
			<div class="btn-group pull-left hidden-phone">
				<button class="btn" type="submit" title="<?php echo \JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
					<span  class="icon-search"></span>
				</button>
				<button class="btn" type="button"
						onclick="document.getElementById('filter_description').value='';this.form.submit();"
						title="<?php echo \JText::_('JSEARCH_FILTER_CLEAR'); ?>">
					<span class="icon-remove"></span>
				</button>
			</div>

			<div class="filter-search btn-group pull-left hidden-phone">
				<?php echo \JHtml::_('calendar', $this->fltFrom, 'from', 'from', '%Y-%m-%d', array('class' => 'input-small')); ?>
			</div>
			<div class="filter-search btn-group pull-left hidden-phone">
				<?php echo \JHtml::_('calendar', $this->fltTo, 'to', 'to', '%Y-%m-%d', array('class' => 'input-small')); ?>
			</div>
			<div class="btn-group pull-left hidden-phone">
				<button class="btn" type="button" onclick="this.form.submit(); return false;"
						title="<?php echo \JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
					<span class="icon-search"></span>
				</button>
			</div>
			<div class="btn-group pull-left hidden-phone">
				<?php echo \JHtml::_('select.genericlist', $this->profilesList, 'profile', 'onchange="document.forms.adminForm.submit()" class="advancedSelect"', 'value', 'text', $this->fltProfile); ?>
			</div>

			<div class="btn-group pull-right">
				<label for="limit"
					   class="element-invisible"><?php echo \JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
				<?php echo $this->pagination->getLimitBox(); ?>

			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable"
					   class="element-invisible"><?php echo \JText::_('JFIELD_ORDERING_DESC'); ?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value="">
						<?php echo \JText::_('JFIELD_ORDERING_DESC'); ?>
					</option>
					<option value="asc" <?php echo ($this->order_Dir == 'asc') ? 'selected="selected"' : ""; ?>>
						<?php echo \JText::_('JGLOBAL_ORDER_ASCENDING'); ?>
					</option>
					<option value="desc" <?php echo ($this->order_Dir == 'desc') ? 'selected="selected"' : ""; ?>>
						<?php echo \JText::_('JGLOBAL_ORDER_DESCENDING'); ?>
					</option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo \JText::_('JGLOBAL_SORT_BY'); ?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo \JText::_('JGLOBAL_SORT_BY'); ?></option>
					<?php echo \JHtml::_('select.options', $this->sortFields, 'value', 'text', $this->order); ?>
				</select>
			</div>
		</div>

		<table class="table table-striped" id="itemsList">
		<thead>
			<tr>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
				</th>
				<th width="20" class="hidden-phone">
					<?php echo \JHtml::_('grid.sort', 'COM_AKEEBA_BUADMIN_LABEL_ID', 'id', $this->order_Dir, $this->order, 'default'); ?>
				</th>
				<th>
					<?php echo \JHtml::_('grid.sort', 'COM_AKEEBA_BUADMIN_LABEL_DESCRIPTION', 'description', $this->order_Dir, $this->order, 'default'); ?>
				</th>
				<th  class="hidden-phone">
					<?php echo \JHtml::_('grid.sort', 'COM_AKEEBA_BUADMIN_LABEL_PROFILEID', 'profile_id', $this->order_Dir, $this->order, 'default'); ?>
				</th>
				<th width="5%">
					<?php echo \JText::_('COM_AKEEBA_BUADMIN_LABEL_DURATION'); ?>
				</th>
				<th width="40">
					<?php echo \JText::_('COM_AKEEBA_BUADMIN_LABEL_STATUS'); ?>
				</th>
				<th width="80" class="hidden-phone">
					<?php echo \JText::_('COM_AKEEBA_BUADMIN_LABEL_SIZE'); ?>
				</th>
				<th class="hidden-phone">
					<?php echo \JText::_('COM_AKEEBA_BUADMIN_LABEL_MANAGEANDDL'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="11" class="center">
					<?php echo $this->pagination->getListFooter(); ?>

				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php if(empty($this->list)): ?>
			<tr>
				<td colspan="11" class="center">
					<?php echo \JText::_('COM_AKEEBA_BACKUP_STATUS_NONE'); ?>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ( ! (empty($this->list))): ?>
			<?php $id = 1; $i = 0; ?>
			<?php foreach($this->list as $record): ?>
				<?php
				$id = 1 - $id;
				list($originDescription, $originIcon) = $this->getOriginInformation($record);
				list($startTime, $duration, $timeZoneText) = $this->getTimeInformation($record);
				list($statusClass, $statusIcon) = $this->getStatusInformation($record);
				$profileName = $this->getProfileName($record);
				?>
				<tr class="row<?php echo $id; ?>">
					<td><?php echo \JHtml::_('grid.id', ++$i, $record['id']); ?></td>
					<td class="hidden-phone">
						<?php echo $this->escape($record['id']); ?>

					</td>
					<td>
						<span class="fa fa-fw <?php echo $originIcon; ?> akeebaCommentPopover" rel="popover"
							  title="<?php echo \JText::_('COM_AKEEBA_BUADMIN_LABEL_ORIGIN'); ?>"
							  data-content="<?php echo $this->escape($originDescription); ?>"></span>
						<?php if ( ! (empty($record['comment']))): ?>
						<span class="icon icon-question-sign akeebaCommentPopover" rel="popover"
							  data-content="<?php echo $this->escape($record['comment']); ?>"></span>
						<?php endif; ?>
						<a href="<?php echo $this->escape(JUri::base()); ?>index.php?option=com_akeeba&view=Manage&task=showcomment&id=<?php echo $this->escape((int)$record['id']); ?>">
							<?php echo $this->escape(empty($record['description']) ? JText::_('COM_AKEEBA_BUADMIN_LABEL_NODESCRIPTION') : $record['description']); ?>

						</a>
						<br/>
						<div class="akeeba-buadmin-startdate" title="<?php echo \JText::_('COM_AKEEBA_BUADMIN_LABEL_START'); ?>">
							<small>
								<span class="fa fa-fw fa-calendar"></span>
								<?php echo $this->escape($startTime); ?> <?php echo $this->escape($timeZoneText); ?>
							</small>
						</div>
					</td>
					<td class="hidden-phone">
						#<?php echo $this->escape((int)$record['profile_id']); ?>. <?php echo $this->escape($profileName); ?>

						<br/>
						<small>
							<em><?php echo $this->escape($this->translateBackupType($record['type'])); ?></em>
						</small>
					</td>
					<td>
						<?php echo $this->escape($duration); ?>

					</td>
					<td>
						<span class="label <?php echo $statusClass; ?> akeebaCommentPopover" rel="popover"
							  title="<?php echo \JText::_('COM_AKEEBA_BUADMIN_LABEL_STATUS'); ?>"
							  data-content="<?php echo \JText::_('COM_AKEEBA_BUADMIN_LABEL_STATUS_' . $record['meta']); ?>">
							<span class="fa fa-fw <?php echo $statusIcon; ?>"></span>
						</span>
					</td>
					<td class="hidden-phone">
						<?php if($record['meta'] == 'ok'): ?>
							<?php echo $this->escape($this->formatFilesize($record['size'])); ?>

						<?php elseif($record['total_size'] > 0): ?>
							<i><?php echo $this->formatFilesize($record['total_size']); ?></i>
						<?php else: ?>
							&mdash;
						<?php endif; ?>
					</td>
					<td class="hidden-phone">
						<?php echo $this->loadAnyTemplate('admin:com_akeeba/Manage/manage_column', [
							'record' => &$record
						]); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
		</table>
	</form>
</div>