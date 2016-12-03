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
		
		<?php if ((!$this->maintenance && $this->briefcase_root != $this->config->briefcase_folder) || ($this->maintenance && $this->current != $this->config->briefcase_folder)) { ?>
		<div class="well">
			<div class="span8">
				<?php if ($this->config->show_folder_desc == 1 && !empty($this->fdescription)) { ?>
				<?php echo $this->fdescription; ?>
				<?php } ?>
			</div>
			
			<?php if(($this->maintenance && !empty($this->folder)) || !$this->maintenance) { ?>
			<div class="span4">
				<b class="rsf_briefcase_name"><?php echo JText::_('COM_RSFILES_BRIEFCASE_FILES'); ?></b> <?php echo $this->curentfilesno; ?> / <?php echo $this->maxfilesno; ?> <br />
				<b class="rsf_briefcase_name"><?php echo JText::_('COM_RSFILES_BRIEFCASE_QUOTA'); ?></b> <?php echo $this->currentquota; ?> / <?php echo $this->maxfilessize; ?> Mb
				<?php $percentage = (ceil($this->currentquota/$this->maxfilessize*100) <= 100 ? ceil($this->currentquota/$this->maxfilessize*100) : '100'); ?>
				<?php $class = $percentage >= 90 ? 'progress-danger' : ''; ?>
				<div class="progress <?php echo $class; ?>">
					<div class="bar" style="width: <?php echo $percentage; ?>%;">&nbsp;</div>
				</div>
			</div>
			<?php } ?>
			<div class="clearfix"></div>
		</div>
		<?php } ?>
		
		<table class="rsf_files table table-striped">
			<thead>
				<tr>
					<th width="30%"><?php echo JText::_('COM_RSFILES_FILE_NAME'); ?></th>
					<?php if ($this->config->list_show_date) { ?><th width="10%"><?php echo JText::_('COM_RSFILES_FILE_DATE'); ?></th><?php } ?>
					<th width="10%">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($this->items)) { ?>
				<?php foreach ($this->items as $i => $item) { ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="rsfiles-download-info">
						<?php $thumbnail = rsfilesHelper::thumbnail($item); ?>
						<?php if ($item->type != 'folder') { ?>
							<?php $download = rsfilesHelper::downloadlink($item,$item->fullpath); ?>
							<?php if ($this->download && $this->config->direct_download) { ?>
							<a class="rsfiles-file <?php echo $download->enablemodal.$thumbnail->class; ?>" <?php echo $download->rel; ?> href="<?php echo $download->dlink; ?>" title="<?php echo $thumbnail->image; ?>">
							<?php } else { ?>
							<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=download&from=briefcase&path='.rsfilesHelper::encode($item->fullpath).$this->itemid); ?>" class="rsfiles-file <?php echo $thumbnail->class; ?>" title="<?php echo $thumbnail->image; ?>">
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
							<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=briefcase&folder='.rsfilesHelper::encode($item->fullpath).$this->itemid); ?>" class="<?php echo $thumbnail->class; ?>" title="<?php echo $thumbnail->image; ?>">
								<i class="rsicon-folder"></i> <?php echo (!empty($item->filename) ? $item->filename : $item->name); ?>
							</a>
						<?php } ?>
					</td>
					<?php if ($this->config->list_show_date) { ?><td><?php if ($item->type != 'folder') echo $item->dateadded; ?></td><?php } ?>
					<td>
						<?php if ($item->type != 'folder') { ?>
						<?php if ($this->download && $this->config->direct_download) { ?>
						<a class="<?php echo rsfilesHelper::tooltipClass(); ?> <?php echo $download->enablemodal; ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_DOWNLOAD')); ?>" <?php echo $download->rel; ?> href="<?php echo $download->dlink; ?>">
						<?php } else { ?>
						<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=download&from=briefcase&path='.rsfilesHelper::encode($item->fullpath).$this->itemid); ?>" class="<?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_DOWNLOAD')); ?>">
						<?php } ?>
							<i class="rsicon-download"></i>
						</a>
						
						<?php if ($this->config->show_details) { ?>
						<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=details&from=briefcase&path='.rsfilesHelper::encode($item->fullpath).$this->itemid); ?>" class="<?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_DETAILS')); ?>">
							<i class="rsicon-details"></i>
						</a>
						<?php } ?>
						
						<?php $properties	= rsfilesHelper::previewProperties($item->id, $item->fullpath); ?>
						<?php $extension	= $properties['extension']; ?>
						<?php $size			= $properties['size']; ?>
						<?php $handler		= $properties['handler']; ?>
						
						<?php if (in_array(strtolower($extension), rsfilesHelper::previewExtensions()) && $item->show_preview) { ?>
						<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=preview&from=briefcase&tmpl=component&path='.rsfilesHelper::encode($item->fullpath).$this->itemid); ?>" rel="{handler: '<?php echo $handler; ?>', <?php echo $size; ?>}" class="rs_modal <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_PREVIEW')); ?>">
							<i class="rsicon-search"></i>
						</a>
						<?php } ?>
						
						<?php if (($this->download || $this->maintenance) && $this->config->show_bookmark && !$item->FileType) { ?>
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