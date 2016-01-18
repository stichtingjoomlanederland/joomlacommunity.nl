<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<?php if ($this->params->get('show_page_heading') != 0) { ?>
<div class="page-header">
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
</div>
<?php } ?>

<div class="rsfiles-layout">
<div class="row-fluid">
	<div class="span12">
		<?php if (($this->config->show_pagination_position == 0 || $this->config->show_pagination_position == 2) && $this->pagination->{rsfilesHelper::isJ3() ? 'pagesTotal' : 'pages.total'} > 1) { ?>
		<div class="pagination">
			<p class="counter pull-right">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
		<?php } ?>
		
		<?php if ($this->config->show_folder_desc == 1 && !empty($this->fdescription)) { ?>
			<div class="well"><?php echo $this->fdescription; ?></div>
		<?php } ?>
		
		<form action="<?php echo htmlentities(JURI::getInstance(), ENT_COMPAT, 'UTF-8'); ?>" method="post" id="adminForm" name="adminForm">
			<div class="rsf_files">
					<?php if (!empty($this->items)) { ?>
               		<?php foreach ($this->items as $i => $item) { ?>
					<?php $canDownload = rsfilesHelper::permissions('CanDownload',$item->fullpath); ?>
					<div class="col-md-6">
						<div class="rsfiles-download-info">
							<?php $thumbnail = rsfilesHelper::thumbnail($item); ?>
							<?php if ($item->type != 'folder') { ?>
								<?php $download = rsfilesHelper::downloadlink($item,$item->fullpath); ?>
								<?php if ($canDownload && $this->config->direct_download) { ?>
								<a class="rsfiles-file <?php echo $download->enablemodal.$thumbnail->class; ?>" <?php echo $download->rel; ?> href="<?php echo $download->dlink; ?>" title="<?php echo $thumbnail->image; ?>">
								<?php } else { ?>
								<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=download&path='.rsfilesHelper::encode($item->fullpath).$this->itemid); ?>" class="rsfiles-file <?php echo $thumbnail->class; ?>" title="<?php echo $thumbnail->image; ?>">
								<?php } ?>
									<i class="rsicon-file"></i> <?php echo (!empty($item->filename) ? $item->filename : $item->name); ?>
								</a>
								
								<br />
								
								<?php if ($item->isnew) { ?>
								<span class="badge badge-info"><?php echo JText::_('COM_RSFILES_NEW'); ?></span>
								<?php } ?>
								
								<?php if ($item->popular) { ?>
									<span class="badge badge-success"><?php echo JText::_('COM_RSFILES_POPULAR'); ?></span>
								<?php } ?>
								
								<?php if ($this->config->list_show_version && !empty($item->fileversion)) { ?><span class="badge <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_FILE_VERSION')); ?>"><i class="rsicon-version"></i> <?php echo $item->fileversion; ?></span><?php } ?>
								<?php if ($this->config->list_show_license && !empty($item->filelicense)) { ?><span class="badge <?php echo rsfilesHelper::tooltipClass(); ?> badge-license" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_FILE_LICENSE')); ?>"><i class="rsicon-license"></i> <?php echo $item->filelicense; ?></span><?php } ?>
								<?php if ($this->config->list_show_size && !empty($item->size)) { ?><span class="badge <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_FILE_SIZE')); ?>"><i class="rsicon-file"></i> <?php echo $item->size; ?></span><?php } ?>

							<?php } else { ?>
								<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&folder='.rsfilesHelper::encode($item->fullpath).$this->itemid); ?>" class="<?php echo $thumbnail->class; ?>" title="<?php echo $thumbnail->image; ?>">
									<i class="rsicon-folder"></i> <?php echo (!empty($item->filename) ? $item->filename : $item->name); ?>
								</a>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
					<?php } else { ?>
					<tr>
						<td colspan="3"><?php echo JText::_('COM_RSFILES_NO_FILES'); ?></td>
					</tr>
					<?php } ?>
			</div>
			
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->listOrder); ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->listDirn); ?>" />
		</form>
		
		<?php if (($this->config->show_pagination_position == 1 || $this->config->show_pagination_position == 2) && $this->pagination->{rsfilesHelper::isJ3() ? 'pagesTotal' : 'pages.total'} > 1) { ?>
		<div class="pagination">
			<p class="counter pull-right">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
		<?php } ?>
	</div>
</div>
</div>