<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="rsfiles-layout">
<div class="row-fluid">
	<div class="span12">
		<table class="rsf_files table table-striped table-condensed">
			<?php if ($this->params->get('header',1)) { ?>
			<thead>
				<tr>
					<th width="30%"><?php echo JText::_('COM_RSFILES_FILE_NAME'); ?></th>
					<?php if ($this->params->get('date')) { ?><th width="10%"><?php echo JText::_('COM_RSFILES_FILE_DATE'); ?></th><?php } ?>
					<th width="8%">&nbsp;</th>
				</tr>
			</thead>
			<?php } ?>
			<tbody>
				<?php if (!empty($this->items)) { ?>
				<?php foreach ($this->items as $i => $item) { ?>
				<?php $canDownload = rsfilesHelper::permissions('CanDownload',$item->fullpath); ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="rsfiles-download-info">
						<?php if ($item->type != 'folder') { ?>
							<?php $download = rsfilesHelper::downloadlink($item,$item->fullpath); ?>
							<?php if ($canDownload && $this->config->direct_download) { ?>
							<a class="rsfiles-file <?php echo $download->enablemodal; ?>" <?php echo $download->rel; ?> href="<?php echo $download->dlink; ?>">
							<?php } else { ?>
							<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=download&path='.rsfilesHelper::encode($item->fullpath).$this->itemid); ?>" class="rsfiles-file">
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
							
							<?php if ($this->params->get('version') && !empty($item->fileversion)) { ?><span class="badge <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_FILE_VERSION')); ?>"><i class="rsicon-version"></i> <?php echo $item->fileversion; ?></span><?php } ?>
							<?php if ($this->params->get('license') && !empty($item->filelicense)) { ?><span class="badge <?php echo rsfilesHelper::tooltipClass(); ?> badge-license" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_FILE_LICENSE')); ?>"><i class="rsicon-license"></i> <?php echo $item->filelicense; ?></span><?php } ?>
							<?php if ($this->params->get('size') && !empty($item->size)) { ?><span class="badge <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_FILE_SIZE')); ?>"><i class="rsicon-file"></i> <?php echo $item->size; ?></span><?php } ?>
							
						<?php } else { ?>
							<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&folder='.rsfilesHelper::encode($item->fullpath).$this->itemid); ?>">
								<i class="rsicon-folder"></i> <?php echo (!empty($item->filename) ? $item->filename : $item->name); ?>
							</a>
						<?php } ?>
					</td>
					<?php if ($this->params->get('date')) { ?><td><?php if ($item->type != 'folder') echo $item->dateadded; ?></td><?php } ?>
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
						<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=preview&tmpl=component&path='.rsfilesHelper::encode($item->fullpath).$this->itemid); ?>" rel="{handler: '<?php echo $handler; ?>', <?php echo $size; ?>}" class="rs_modal <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_PREVIEW')); ?>">
							<i class="rsicon-search"></i>
						</a>
						<?php } ?>
						<?php } ?>
						
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
</div>