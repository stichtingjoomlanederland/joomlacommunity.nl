<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="container-fluid form-horizontal">
	<div class="control-group">
		<div class="control-label">
			<label for="rsepro-file-name"><?php echo JText::_('COM_RSEVENTSPRO_FILE_NAME'); ?></label>
		</div>
		<div class="controls">
			<input type="text" id="rsepro-file-name" name="file_name" class="input-xlarge" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<label><?php echo JText::_('COM_RSEVENTSPRO_FILE_PERMISSIONS'); ?></label>
		</div>
		<div class="controls">
			<legend><?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_ALL'); ?></legend>
			<label class="checkbox">
				<input id="fp0" name="fp0" type="checkbox" value="1" />
				<?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_BEFORE'); ?>
			</label>
			<label class="checkbox">
				<input id="fp1" name="fp1" type="checkbox" value="1" />
				<?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_DURING'); ?>
			</label>
			<label class="checkbox">
				<input id="fp2" name="fp2" type="checkbox" value="1" />
				<?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_AFTER'); ?>
			</label>
			
			<legend><?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_REGISTERED'); ?></legend>
			<label class="checkbox">
				<input id="fp3" name="fp3" type="checkbox" value="1" />
				<?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_BEFORE'); ?>
			</label>
			<label class="checkbox">
				<input id="fp4" name="fp4" type="checkbox" value="1" />
				<?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_DURING'); ?>
			</label>
			<label class="checkbox">
				<input id="fp5" name="fp5" type="checkbox" value="1" />
				<?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_AFTER'); ?>
			</label>
		</div>
	</div>
</div>
<input type="hidden" name="rsepro-file-id" id="rsepro-file-id" value="" />