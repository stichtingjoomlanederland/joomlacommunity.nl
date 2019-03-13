<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
function rsform_close_window() {
	window.close();
}
</script>

<form action="index.php?option=com_rsform&amp;controller=files&amp;task=display" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div id="rsform_explorer">
	<button type="button" class="btn" onclick="rsform_close_window();"><?php echo JText::_('JCANCEL'); ?></button>
	<?php if ($this->canUpload) { ?>
		<table class="adminform">
		<tr>
			<th colspan="3"><?php echo JText::_('RSFP_UPLOAD_FILE'); ?></th>
		</tr>
		<tr>
			<td width="120"><label for="upload"><?php echo JText::_('RSFP_FILE'); ?>:</label></td>
			<td width="1%" nowrap="nowrap"><input class="input_box" id="upload" name="upload" type="file" size="57" /></td>
			<td><input class="btn" type="button" value="<?php echo JText::_('RSFP_UPLOAD_FILE'); ?>" onclick="Joomla.submitbutton('upload')" /></td>
		</tr>
		</table>
	<?php } else { ?>
		<div class="alert alert-error">
			<?php echo JText::_('RSFP_CANT_UPLOAD'); ?>
		</div>
	<?php } ?>
		
	<table class="adminlist table table-striped" id="articleList">
		<thead>
		<tr>
			<th><strong><?php echo JText::_('RSFP_CURRENT_LOCATION'); ?></strong>
				<?php foreach ($this->elements as $folder) { ?>
					<a href="index.php?option=com_rsform&amp;controller=files&amp;task=display&amp;folder=<?php echo urlencode($folder->fullpath); ?>&amp;tmpl=component"><?php echo $this->escape($folder->name); ?></a> <?php echo DIRECTORY_SEPARATOR; ?>
				<?php } ?>
			</th>
		</tr>
		</thead>
		<tr>
			<td><a class="folder" href="index.php?option=com_rsform&amp;controller=files&amp;task=display&amp;folder=<?php echo urlencode($this->previous); ?>&amp;tmpl=component">..<?php echo JHtml::image('com_rsform/admin/icon-16-up.gif', JText::_('BACK'), null, true); ?></a></td>
		</tr>
	<?php foreach ($this->folders as $folder) { ?>
		<tr>
			<td><a class="folder" href="index.php?option=com_rsform&amp;controller=files&amp;task=display&amp;folder=<?php echo urlencode($folder->fullpath); ?>&amp;tmpl=component"><?php echo $this->escape($folder->name); ?></a></td>
		</tr>
	<?php } ?>
	<?php foreach ($this->files as $file) { ?>
			<tr>
				<td><a class="file" href="javascript: void(0);" data-fullpath="<?php echo $this->escape($file->fullpath); ?>" onclick="window.opener.document.getElementById('UserEmailAttachFile').value = jQuery(this).data('fullpath'); rsform_close_window();"><?php echo $this->escape($file->name); ?></a></td>
			</tr>
	<?php } ?>
	</table>
		
	<?php echo JHtml::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="controller" value="files" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="folder" value="<?php echo $this->escape($this->current); ?>" />
	<input type="hidden" name="task" value="display" />
</div>
</form>