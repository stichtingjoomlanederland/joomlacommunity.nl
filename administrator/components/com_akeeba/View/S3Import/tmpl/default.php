<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="com_akeeba" />
	<input type="hidden" name="view" value="S3Import" />
	<input type="hidden" name="task" value="display" />
	
	<input type="hidden" id="ak_s3import_folder" name="folder" value="<?php echo $this->escape($this->root); ?>" />

	<div class="row-fluid">
		<div class="span12 form-inline">
			<input type="text" size="40" name="s3access" id="s3access" value="<?php echo $this->escape($this->s3access); ?>" placeholder="<?php echo \JText::_('COM_AKEEBA_CONFIG_S3ACCESSKEY_TITLE'); ?>" />
			<input type="password" size="40" name="s3secret" id="s3secret" value="<?php echo $this->escape($this->s3secret); ?>" placeholder="<?php echo \JText::_('COM_AKEEBA_CONFIG_S3SECRETKEY_TITLE'); ?>" />

			<?php if(empty($this->buckets)): ?>
			<button class="btn btn-primary" type="submit" onclick="ak_s3import_resetroot()">
				<span class="icon-connection icon-white"></span>
				<?php echo \JText::_('COM_AKEEBA_S3IMPORT_LABEL_CONNECT'); ?>
			</button>
			<?php else: ?>
			<?php echo $this->bucketSelect; ?>

			<button class="btn btn-primary" type="submit" onclick="ak_s3import_resetroot()">
				<span class="icon-folder-open icon-white"></span>
				<?php echo \JText::_('COM_AKEEBA_S3IMPORT_LABEL_CHANGEBUCKET'); ?>
			</button>
			<?php endif; ?>
		</div>
	</div>
	
	<div class="row-fluid">
		<div id="ak_crumbs_container">
			<ul class="breadcrumb">
				<li>
					<a href="javascript:ak_s3import_chdir('');">&lt;root&gt;</a>
					<span class="divider">/</span>
				</li>

				<?php if(!empty($this->crumbs)): ?>
					<?php $runningCrumb = ''; $i = 0; ?>
					<?php foreach($this->crumbs as $crumb): ?>
					<?php $runningCrumb .= $crumb.'/'; $i++; ?>
					<li>
						<a href="javascript:ak_s3import_chdir('<?php echo addslashes($runningCrumb); ?>');">
							<?php echo $this->escape($crumb); ?>

						</a>
						<?php if($i < count($this->crumbs)): ?>
						<span class="divider">/</span>
						<?php endif; ?>
					</li>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	
	<div>
		<fieldset id="ak_folder_container">
			<legend><?php echo \JText::_('COM_AKEEBA_FILEFILTERS_LABEL_DIRS'); ?></legend>
			<div id="folders">
				<?php if(!empty($this->contents['folders'])): ?>
				<?php foreach($this->contents['folders'] as $name => $record): ?>
				<div class="folder-container" onclick="ak_s3import_chdir('<?php echo addslashes($record['prefix']); ?>')">
					<span class="folder-icon-container">
						<span class="icon icon-folder-close"></span>
					</span>
					<span class="folder-name">
						<?php echo $this->escape(rtrim($name,'/')); ?>

					</span>
				</div>
				<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</fieldset>

		<fieldset id="ak_files_container">
			<legend><?php echo \JText::_('COM_AKEEBA_FILEFILTERS_LABEL_FILES'); ?></legend>
			<div id="files">
				<?php if(!empty($this->contents['files'])): ?>
				<?php foreach($this->contents['files'] as $name => $record): ?>
				<div class="file-container" onclick="window.location = 'index.php?option=com_akeeba&view=S3Import&task=dltoserver&part=-1&frag=-1&layout=downloading&file=<?php echo $this->escape($name); ?>'">
					<span class="file-icon-container">
						<span class="icon icon-file"></span>
					</span>
					<span class="file-name file-clickable">
						<?php echo $this->escape(basename($record['name'])); ?>

					</span>
				</div>
				<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</fieldset>
	</div>
</form>

<script type="text/javascript">
akeeba.jQuery(document).ready(function($){
	// Work around Safari which ignores autocomplete=off (FOR CRYING OUT LOUD!)
	setTimeout(function(){
		$('#s3access').val('<?php echo addslashes($this->s3access); ?>');
		$('#s3secret').val('<?php echo addslashes($this->s3secret); ?>');
	}, 500);
});
</script>