<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ADD_FILES'); ?></legend>

<div id="rsepro-event-files">
	<div class="control-group">
		<div class="controls">
			<input type="file" class="input-large" name="files[]" />
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<input type="file" class="input-large" name="files[]" />
		</div>
	</div>
	
	<div class="control-group">
		<div class="controls">
			<input type="file" class="input-large" name="files[]" />
		</div>
	</div>
</div>

<?php if ($this->files) { ?>
<legend>
	<?php echo JText::_('COM_RSEVENTSPRO_EVENT_FILES'); ?> 
	<?php echo JHtml::image('com_rseventspro/loader.gif', '', array('id' => 'rsepro-file-loader', 'style' => 'display: none;'), true); ?> 
</legend>
<ul class="unstyled rsepro-event-files">
<?php foreach ($this->files as $file) { ?>
	<li id="<?php echo $file->id; ?>">
		<i class="fa fa-file-o"></i> 
		<a href="javascript:void(0)" class="rsepro-edit-file">
			<?php echo $file->name; ?>
		</a>
		<a href="javascript:void(0)" class="rsepro-remove-file">
			<i class="fa fa-times"></i>
		</a>
	</li>
<?php } ?>
</ul>
<?php } ?>

<div class="form-actions">
	<button class="btn rsepro-event-add-files" type="button"><span class="fa fa-plus"></span> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_ADD_MORE_FILES'); ?></button>
	<button class="btn btn-success rsepro-event-save" type="button"><?php echo JText::_('COM_RSEVENTSPRO_SAVE_EVENT'); ?></button>
	<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
	<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
</div>