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
		<?php echo $this->loadTemplate('navbar'); ?>
		
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
			<table class="rsf_files table table-striped">
				<thead>
					<tr>
						<th width="30%">
						<?php echo JHtml::_('grid.sort', 'COM_RSFILES_FILE_NAME', 'name', $this->listDirn, $this->listOrder); ?>
						</th>
						<?php if ($this->config->list_show_date) { ?><th width="12%"><?php echo JHtml::_('grid.sort', 'COM_RSFILES_FILE_DATE', 'date', $this->listDirn, $this->listOrder); ?></th></th><?php } ?>
						<th width="10%">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($this->items)) { ?>
					<?php foreach ($this->items as $i => $item) { ?>
					<?php $canDownload = rsfilesHelper::permissions('CanDownload',$item->fullpath); ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="rsfiles-download-info">
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
						</td>
						<?php if ($this->config->list_show_date) { ?><td><?php if ($item->type != 'folder') echo $item->dateadded; ?></td><?php } ?>
						<td>
							<?php if ($item->type != 'folder') { ?>
							<?php if ($canDownload && $this->config->direct_download) { ?>
							<a class="<?php echo rsfilesHelper::tooltipClass(); ?> <?php echo $download->enablemodal; ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_DOWNLOAD')); ?>" <?php echo $download->rel; ?> href="<?php echo $download->dlink; ?>">
							<?php } else { ?>
							<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=download&path='.rsfilesHelper::encode($item->fullpath).$this->itemid); ?>" class="<?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_DOWNLOAD')); ?>">
							<?php } ?>
								<i class="rsicon-download"></i>
							</a>
							
							<?php if ($this->config->show_details) { ?>
							<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=details&path='.rsfilesHelper::encode($item->fullpath).$this->itemid); ?>" class="<?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_DETAILS')); ?>">
								<i class="rsicon-details"></i>
							</a>
							<?php } ?>
							
							<?php if ($canDownload) { ?>
								<?php $properties	= rsfilesHelper::previewProperties($item->id, $item->fullpath); ?>
								<?php $extension	= $properties['extension']; ?>
								<?php $size			= $properties['size']; ?>
								<?php $handler		= $properties['handler']; ?>
								
								<?php if (in_array($extension, rsfilesHelper::previewExtensions()) && $item->show_preview) { ?>
								<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=preview&tmpl=component&path='.rsfilesHelper::encode($item->fullpath).$this->itemid); ?>" rel="{handler: '<?php echo $handler; ?>', <?php echo $size; ?>, iframeOptions : {allowfullscreen : true, webkitallowfullscreen: true, mozallowfullscreen: true}}" class="rs_modal <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_PREVIEW')); ?>">
									<i class="rsicon-search"></i>
								</a>
								<?php } ?>
							<?php } ?>
							
							<?php if ($canDownload && $this->config->show_bookmark && !$item->FileType) { ?>
							<a href="javascript:void(0);" class="<?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_NAVBAR_BOOKMARK_FILE')); ?>" onclick="rsf_bookmark('<?php echo JURI::root(); ?>','<?php echo $this->escape(addslashes(urldecode($item->fullpath))); ?>','<?php echo $this->briefcase ? 1 : 0; ?>','<?php echo $this->app->input->getInt('Itemid',0); ?>')">
								<i class="rsicon-bookmark-add"></i>
							</a>
							<?php } ?>
							
							<?php } ?>
						</td>
					</tr>
					<?php } ?>
					<?php } else { ?>
					<tr>
						<td colspan="3"><?php echo JText::_('COM_RSFILES_NO_FILES'); ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			
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