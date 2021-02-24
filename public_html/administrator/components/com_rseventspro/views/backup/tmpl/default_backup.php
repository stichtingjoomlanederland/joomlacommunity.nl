<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

//keep session alive while editing
JHtml::_('behavior.keepalive'); ?>
	
<div class="alert alert-info">
	<h3><?php echo JText::_('COM_RSEVENTSPRO_BACKUP_NOTE'); ?></h3>
	<ul>
		<li><?php echo JText::_('COM_RSEVENTSPRO_BACKUP_NOTE_1'); ?></li>
		<li><?php echo JText::_('COM_RSEVENTSPRO_BACKUP_NOTE_2'); ?></li>
	</ul>
	<button type="button" class="btn btn-primary" id="rsepro-backup-btn" onclick="rsepro_backup(0);"><?php echo JText::_('COM_RSEVENTSPRO_START_BACKUP'); ?></button>
</div>

<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo JText::_('COM_RSEVENTSPRO_BACKUP_FILE'); ?></th>
			<th class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JText::_('COM_RSEVENTSPRO_BACKUP_DATE'); ?></th>
			<th class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"></th>
		</tr>
	</thead>
	<tbody id="rsepro-local-backups">
		<?php foreach ($this->files as $file) { ?>
		<tr>
			<td><a href="<?php echo $file->url; ?>"><?php echo $file->name; ?></a></td>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo $file->date; ?></td>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
				<button type="button" class="btn btn-secondary" onclick="rsepro_backup_restore('<?php echo $file->name; ?>',1);"><?php echo JText::_('COM_RSEVENTSPRO_BACKUP_OVERWRITE_RESTORE'); ?></button>
				<button type="button" class="btn btn-secondary" onclick="rsepro_backup_restore('<?php echo $file->name; ?>',0);"><?php echo JText::_('COM_RSEVENTSPRO_BACKUP_RESTORE'); ?></button>
				<button type="button" class="btn btn-danger" onclick="rsepro_backup_delete('<?php echo $file->name; ?>', this);"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE_BTN'); ?></button>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>