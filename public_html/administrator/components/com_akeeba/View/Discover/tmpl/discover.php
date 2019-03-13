<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

$hasFiles = !empty($this->files);
?>
<form name="adminForm" id="adminForm" action="index.php" method="post" class="akeeba-form--horizontal">
	<?php if($hasFiles): ?>
	<div class="akeeba-panel--information akeeba-form--horizontal">
        <div class="akeeba-form-group">
            <label for="directory2"><?php echo \JText::_('COM_AKEEBA_DISCOVER_LABEL_DIRECTORY'); ?></label>
            <input type="text" name="directory2" id="directory2" value="<?php echo $this->escape($this->directory); ?>" disabled="disabled" size="70" />
        </div>
	</div>

	<div class="akeeba-form-group">
		<label for="files">
			<?php echo \JText::_('COM_AKEEBA_DISCOVER_LABEL_FILES'); ?>
		</label>
        <select name="files[]" id="files" multiple="multiple" class="input-xxlarge">
			<?php foreach($this->files as $file): ?>
                <option value="<?php echo $this->escape(basename($file)); ?>"><?php echo $this->escape(basename($file)); ?></option>
			<?php endforeach; ?>
        </select>
        <p class="akeeba-help-text">
            <?php echo \JText::_('COM_AKEEBA_DISCOVER_LABEL_SELECTFILES'); ?>
        </p>
	</div>

    <div class="akeeba-form-group--pull-right">
        <div class="akeeba-form-group--actions">
            <button class="akeeba-btn--primary" onclick="this.form.submit(); return false;">
                <span class="akion-ios-upload"></span>
			    <?php echo \JText::_('COM_AKEEBA_DISCOVER_LABEL_IMPORT'); ?>
            </button>
        </div>
    </div>
	<?php endif; ?>

	<?php if ( ! ($hasFiles)): ?>
	<div class="akeeba-panel--warning">
		<?php echo \JText::_('COM_AKEEBA_DISCOVER_ERROR_NOFILES'); ?>
	</div>
	<p>
		<button class="akeeba-btn--orange" onclick="this.form.submit(); return false;">
			<span class="akion-arrow-left-a"></span>
			<?php echo \JText::_('COM_AKEEBA_DISCOVER_LABEL_GOBACK'); ?>
		</button>
	</p>
	<?php endif; ?>

    <div class="akeeba-hidden-fields-container">
        <input type="hidden" name="option" value="com_akeeba" />
        <input type="hidden" name="view" value="Discover" />
		<?php if($hasFiles): ?>
            <input type="hidden" name="task" value="import" />
            <input type="hidden" name="directory" value="<?php echo $this->escape($this->directory); ?>" />
		<?php else: ?>
            <input type="hidden" name="task" value="default" />
		<?php endif; ?>
        <input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1" />
    </div>

</form>
