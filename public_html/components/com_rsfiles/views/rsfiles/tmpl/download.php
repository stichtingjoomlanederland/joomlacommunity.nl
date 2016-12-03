<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="page-header">
	<h1><?php echo $this->file->fname; ?></h1>
</div>

<div class="rsfiles-layout">
<div class="row-fluid">
	<div class="span12">
		<?php echo $this->loadTemplate('navbar'); ?>
		<div class="clearfix"></div>
		
		<?php if ($this->candownload) { ?>
		<div class="well">
			<p><?php echo JText::_('COM_RSFILES_DOWNLOAD_DIRECTLY'); ?> :</p>
			<p>
				<a class="btn btn-primary btn-large <?php echo $this->download->enablemodal; ?>" <?php echo $this->download->rel; ?> href="<?php echo $this->download->dlink; ?>">
					<i class="rsicon-download"></i> <?php echo JText::_('COM_RSFILES_DOWNLOAD'); ?>
				</a>
			</p>
			
			<?php if (!empty($this->mirrors)) { ?>
			<p><?php echo JText::_('COM_RSFILES_DOWNLOAD_FROM_MIRRORS'); ?> :</p>
			<?php foreach ($this->mirrors as $mirror) { ?>
				<a class="btn btn-info" target="_blank" href="<?php echo $mirror->MirrorURL; ?>"><?php echo $mirror->MirrorName; ?></a>
			<?php } ?>
			<?php } ?>
		</div>
		<?php } else { ?>
			<div class="well">
				<i class="rsicon-report"></i> <?php echo JText::_('COM_RSFILES_DOWNLOAD_PERMISSION_ERROR'); ?>
			</div>
		<?php } ?>
		
		<div class="clearfix"></div>
		
		<?php if (!empty($this->screenshots)) { ?>
		<?php foreach ($this->screenshots as $screenshot) { ?>
		<ul class="thumbnails">
		<?php foreach ($screenshot as $path) { ?>
			<li class="span3">
				<a href="<?php echo JURI::root(); ?>components/com_rsfiles/images/screenshots/<?php echo $path; ?>" class="thumbnail rs_modal" rel="{handler: 'image'}">
					<img src="<?php echo JURI::root(); ?>components/com_rsfiles/images/screenshots/<?php echo $path; ?>" alt="" style="width:160px; height:120px;" />
				</a>
			</li>
		<?php } ?>
		</ul>
		<?php } ?>
		<?php } ?>
		
		<div class="clearfix"></div>
		
		<?php if (rsfilesHelper::gallery()) {
			$gallery = RSMediaGalleryIntegration::getInstance();
			if (!empty($this->file->ScreenshotsTags)) {
				echo $gallery->display($this->file->ScreenshotsTags, array('thumb_width' => '160', 'thumb_height' => '120'), 'default');
			}
		} ?>
		
		<?php if ($this->config->show_file_size || $this->config->show_date_added || $this->config->show_date_updated || $this->config->show_license || $this->config->show_file_version || $this->config->show_number_of_downloads) { ?>
		<h3 class="page-header"><?php echo JText::_('COM_RSFILES_DETAILS'); ?></h3>
		<div class="muted">
			<div class="pull-left">
				<?php if ($this->config->show_file_size && !empty($this->file->filesize)) { ?>
				<i class="rsicon-file"></i> <?php echo JText::_('COM_RSFILES_FILE_SIZE'); ?>: <?php echo $this->file->filesize; ?> <br />
				<?php } ?>
				
				<?php if ($this->config->show_date_added && !empty($this->file->dateadded)) { ?>
				<i class="rsicon-calendar"></i> <?php echo JText::_('COM_RSFILES_FILE_DATE_ADDED'); ?>: <?php echo $this->file->dateadded; ?> <br />
				<?php } ?>
				
				<?php if ($this->config->show_date_updated && !empty($this->file->lastmodified)) { ?>
				<i class="rsicon-calendar"></i> <?php echo JText::_('COM_RSFILES_FILE_LAST_MODIFIED'); ?>: <?php echo $this->file->lastmodified; ?> <br />
				<?php } ?>
			</div>
			
			<div class="pull-right">
				<?php if ($this->config->show_license && !empty($this->file->filelicense)) { ?>
				<i class="rsicon-license"></i> <?php echo JText::_('COM_RSFILES_FILE_LICENSE'); ?>: <a class="rs_modal" rel="{handler: 'iframe'}" href="<?php echo JRoute::_($this->file->filelicense,false); ?>"><?php echo $this->file->LicenseName; ?></a> <br />
				<?php } ?>
				
				<?php if ($this->config->show_file_version && !empty($this->file->fileversion)) { ?>
				<i class="rsicon-version"></i> <?php echo JText::_('COM_RSFILES_FILE_VERSION'); ?>: <?php echo $this->file->fileversion; ?> <br />
				<?php } ?>
				
				<?php if ($this->config->show_hits && !empty($this->file->hits) && !$this->briefcase) { ?>
				<i class="rsicon-hits"></i> <?php echo JText::_('COM_RSFILES_FILE_HITS'); ?>: <?php echo $this->file->hits; ?> <br />
				<?php } ?>
				
				<?php if ($this->config->show_number_of_downloads && !empty($this->file->downloads) && !$this->briefcase) { ?>
				<i class="rsicon-download"></i> <?php echo JText::_('COM_RSFILES_FILE_DOWNLOADS'); ?>: <?php echo $this->file->downloads; ?> <br />
				<?php } ?>
			</div>
		</div>
		<div class="clearfix"></div>
		<?php } ?>
		
		<?php if ($this->config->show_descriptions && !empty($this->file->FileDescription)) { ?>
		<h3 class="page-header"><?php echo JText::_('COM_RSFILES_DESCRIPTION'); ?></h3>
		<?php echo $this->file->FileDescription; ?>
		<?php } ?>
		
	</div>
</div>
</div>