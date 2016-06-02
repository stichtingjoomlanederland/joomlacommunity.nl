<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

$hasFiles = !empty($this->files);
?>
<form name="adminForm" id="adminForm" action="index.php" method="post" class="form-horizontal">
	<input type="hidden" name="option" value="com_akeeba" />
	<input type="hidden" name="view" value="Discover" />
	<?php if($hasFiles): ?>
	<input type="hidden" name="task" value="import" />
	<input type="hidden" name="directory" value="<?php echo $this->escape($this->directory); ?>" />
	<?php else: ?>
	<input type="hidden" name="task" value="default" />
	<?php endif; ?>
	<input type="hidden" name="<?php echo \JFactory::getSession()->getFormToken(); ?>" value="1" />
	
	<?php if($hasFiles): ?>
	<div class="well form-inline">
		<label for="directory2"><?php echo \JText::_('COM_AKEEBA_DISCOVER_LABEL_DIRECTORY'); ?></label>
		<input type="text" class="input-xxlarge" name="directory2" id="directory2" value="<?php echo $this->escape($this->directory); ?>" disabled="disabled" size="70" />
	</div>

	<div class="control-group">
		<label class="control-label" for="input01">
			<?php echo \JText::_('COM_AKEEBA_DISCOVER_LABEL_FILES'); ?>
		</label>
		<div class="controls">
			<select name="files[]" id="files" multiple="multiple" class="input-xxlarge">
			<?php foreach($this->files as $file): ?>
				<option value="<?php echo $this->escape(basename($file)); ?>"><?php echo $this->escape(basename($file)); ?></option>
			<?php endforeach; ?>
			</select>
			<p class="help-block"><?php echo \JText::_('COM_AKEEBA_DISCOVER_LABEL_SELECTFILES'); ?></p>
		</div>
	</div>
	
	<div class="form-actions">
		<button class="btn btn-large btn-primary" onclick="this.form.submit(); return false;">
			<span class="icon-box-add icon-white"></span>
			<?php echo \JText::_('COM_AKEEBA_DISCOVER_LABEL_IMPORT'); ?>
		</button>
	</div>
	<?php endif; ?>

	<?php if ( ! ($hasFiles)): ?>
	<div class="alert alert-warning">
		<span class="icon icon-warning-2"></span>
		<?php echo \JText::_('COM_AKEEBA_DISCOVER_ERROR_NOFILES'); ?>
	</div>
	<p>
		<button class="btn btn-warning" onclick="this.form.submit(); return false;">
			<span class="icon icon-leftarrow"></span>
			<?php echo \JText::_('COM_AKEEBA_DISCOVER_LABEL_GOBACK'); ?>
		</button>
	</p>
	<?php endif; ?>
</form>