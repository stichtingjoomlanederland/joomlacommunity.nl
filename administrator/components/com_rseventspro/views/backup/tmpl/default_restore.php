<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

//keep session alive while editing
JHtml::_('behavior.keepalive'); ?>

<div class="row-fluid">
	<div class="alert alert-info">
		<h3><?php echo JText::_('COM_RSEVENTSPRO_BACKUP_NOTE'); ?></h3>
		<ul>
			<li><?php echo JText::_('COM_RSEVENTSPRO_RESTORE_NOTE_1'); ?></li>
			<li><?php echo JText::_('COM_RSEVENTSPRO_RESTORE_NOTE_2'); ?></li>
		</ul>
	</div>
	
	<div class="control-group">
		<div class="control-label">
			<label for="rsepro-restore-file"><?php echo JText::_('COM_RSEVENTSPRO_RESTORE_FILE'); ?></label>
		</div>
		<div class="controls">
			<input type="file" id="rsepro-restore-file" name="restore" class="input-large" />
			<input type="hidden" name="local" id="local" value="" />
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<label class="btn">
				<input type="checkbox" name="overwrite" id="rsepro-overwrite" value="1" />
				<?php echo JText::_('COM_RSEVENTSPRO_OVERWRITE_DATA'); ?>
			</label>
			<button type="button" class="btn btn-primary" id="rsepro-restore-btn" onclick="Joomla.submitbutton('extract');"><?php echo JText::_('COM_RSEVENTSPRO_BACKUP_RESTORE'); ?></button>
		</div>
	</div>
</div>