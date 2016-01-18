<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.keepalive'); 
JText::script('COM_RSFILES_PROCESS_START');
JText::script('COM_RSFILES_START_UPLOAD'); ?>

<style type="text/css">
.form-horizontal .control-group {
	margin-bottom: 5px;
}

legend + .control-group {
	margin-top: 5px;
}
</style>

<script type="text/javascript">
jQuery(document).ready(function(){
	var upload_files = false;
	jQuery('#com-rsfiles-upload-field').change(function(e) {
		upload_files = true;
	});

	jQuery('#com-rsfiles-upload-files').click(function () {
		if (upload_files === false) {
			jQuery('#com-rsfiles-upload-results li').remove();
			jQuery('#com-rsfiles-upload-results').prepend(jQuery('<li>', {'class': 'rs_error' }).html('<span class="icon-delete com-rsfiles-close-message"></span> <?php echo JText::_('COM_RSFILES_NO_FILES_SELECTED',true);?>'));
			jQuery('.com-rsfiles-close-message').on('click',function() {
				jQuery(this).parent('li').hide('fast');
			});
			
			return false;
		}
	});
	
	// when closing the window refresh the parent page to see the files
	jQuery('#rsfl_close_btn').click(function (){ window.parent.location.reload(); window.parent.SqueezeBox.close()});
});
</script>

<form id="rsfl_upload_form" action="<?php echo JRoute::_('index.php?option=com_rsfiles&task=files.upload'); ?>" method="POST" id="adminForm" name="adminForm" enctype="multipart/form-data" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="span<?php echo $this->briefcase ? 12 : 6; ?> rsspan<?php echo $this->briefcase ? 12 : 6; ?> <?php if (!$this->briefcase) echo 'rslft'; ?>">
			<?php echo JHtml::_('rsfieldset.start', 'adminform',JText::_('COM_RSFILES_FILE_ATTRIBUTES')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('published'), $this->form->getInput('published')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('DateAdded'), $this->form->getInput('DateAdded')); ?>
			<?php if (!$this->briefcase) echo JHtml::_('rsfieldset.element', $this->form->getLabel('FileStatistics'), $this->form->getInput('FileStatistics')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('FileVersion'), $this->form->getInput('FileVersion')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('IdLicense'), $this->form->getInput('IdLicense')); ?>
			<?php if (!$this->briefcase) echo JHtml::_('rsfieldset.element', $this->form->getLabel('DownloadMethod'), $this->form->getInput('DownloadMethod')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('DownloadLimit'), $this->form->getInput('DownloadLimit')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('show_preview'), $this->form->getInput('show_preview')); ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
		</div>
		
		<?php if (!$this->briefcase) { ?>
		<div class="span6 rsspan6 rsrgt">
			<?php echo JHtml::_('rsfieldset.start', 'adminform',JText::_('COM_RSFILES_FILE_PERMISSIONS')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('CanEdit'), $this->form->getInput('CanEdit')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('CanDelete'), $this->form->getInput('CanDelete')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('CanView'), $this->form->getInput('CanView')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('CanDownload'), $this->form->getInput('CanDownload')); ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
		</div>
		<?php } ?>
	</div>
	<div class="clr"></div>
	<div class="row-fluid">
		<div class="span12">
			<fieldset>
				<legend><?php echo JText::sprintf('COM_RSFILES_UPLOAD_PATH_LABEL',$this->path); ?></legend>
				<div class="rsleft">
					<span class="btn fileinput-button">
						<span class="icon-plus icon-white"></span>
						<span id="com-rsfiles-add-files"><?php echo JText::_('COM_RSFILES_ADD_FILES'); ?></span>
						<span id="com-rsfiles-no-files" style="display: none;"><?php echo JText::_('COM_RSFILES_READY_FOR_UPLOAD', true);?></span>
						<input id="com-rsfiles-upload-field" name="file" type="file" class="input-large" size="20" <?php if (!$this->single) { ?>multiple="multiple"<?php } ?> />
					</span>
					
					<label for="overwrite" class="btn" id="rsf_overwrite">
						<input type="checkbox" name="overwrite" id="overwrite" value="1" /> <span class="rsf_inline"><?php echo JText::_('COM_RSFILES_OVERWRITE'); ?></span>
					</label>
					
					<button class="btn btn-primary" type="button" id="com-rsfiles-upload-files">
						<span class="icon-upload icon-white"></span>
						<span id="com-rsfiles-upload-text"><?php echo JText::_('COM_RSFILES_START_UPLOAD'); ?></span>
					</button>
					
					<button class="btn btn-danger" type="reset" id="com-rsfiles-cancel-upload">
						<span class="icon-close icon-white"></span>
						<span><?php echo JText::_('COM_RSFILES_CANCEL'); ?></span>
					</button>
				</div>
				<div id="com-rsfiles-progress" class="com-rsfiles-progress" style="display:none;"><div style="width: 1%;" id="com-rsfiles-bar" class="com-rsfiles-bar">0%</div></div>
				<div class="clearfix"></div>
				<ul id="com-rsfiles-upload-results"></ul>
			</fieldset>
		</div>
	</div>

	<input type="hidden" value="<?php echo $this->path; ?>" name="FilePath" id="path" />
	<input type="hidden" id="chunk" name="chunk" value="<?php echo rsfilesHelper::getChunkSize(); ?>" />
	<span id="siteroot" style="display:none;"><?php echo JURI::root(); ?></span>
	<span id="singleupload" style="display:none;"><?php echo $this->escape($this->singleupload); ?></span>
</form>