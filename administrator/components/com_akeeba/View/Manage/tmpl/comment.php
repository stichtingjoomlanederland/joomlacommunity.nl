<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var  \Akeeba\Backup\Admin\View\Manage\Html  $this */

?>
<form name="adminForm" id="adminForm" action="index.php" method="post" class="form-horizontal">
	<input type="hidden" name="option" value="com_akeeba" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="Manage" />
	<input type="hidden" name="id" value="<?php echo (int)$this->record['id']; ?>" />
	<input type="hidden" name="<?php echo \JFactory::getSession()->getFormToken(); ?>" value="1" />

	<div class="control-group">
		<label class="control-label" for="description">
			<?php echo \JText::_('COM_AKEEBA_BUADMIN_LABEL_DESCRIPTION'); ?>
		</label>
		<div class="controls">
			<input type="text" name="description" maxlength="255" size="50"
				value="<?php echo $this->escape($this->record['description']); ?>" class="input-xxlarge" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="comment">
			<?php echo \JText::_('COM_AKEEBA_BUADMIN_LABEL_DESCRIPTION'); ?>
		</label>
		<div class="controls">
			<?php echo JEditor::getInstance($this->container->platform->getConfig()->get('editor', 'tinymce'))->display('comment',  $this->record['comment'], '550', '400', '60', '20', array()); ?>
		</div>
	</div>
</form>