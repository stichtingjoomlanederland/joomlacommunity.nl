<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.modal');
JHTML::_('behavior.keepalive'); 
$current_path = !empty($this->current) ? '&path='.addslashes($this->current) : ''; ?>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'upload') {
		SqueezeBox.open('<?php echo JURI::root(); ?>administrator/index.php?option=com_rsfiles&view=files&layout=form&tmpl=component<?php echo $current_path; ?>',{handler: 'iframe',size: {x: 800, y: 600}});
		return false;
	} else if (task == 'synchronize'){
		if (confirm('<?php echo JText::_('COM_RSFILES_CONFIRM_SYNCHRONIZATION',true); ?>')) {
			RSFiles.Sync.steps  = [
				'checkFolders',
				'checkFiles',
				'checkDatabase'
			];
			RSFiles.Sync.start();
		} else return false;
	} else if (task == 'briefcase.add') {
		SqueezeBox.open('<?php echo JURI::root(); ?>administrator/index.php?option=com_users&view=users&layout=modal&tmpl=component&field=addusers', {
			handler : 'iframe',
			size: {x: 800, y: 600},
			iframeOptions: {
				onload : 'rsf_attachUsersModalEvents(this)'
			}
		});
		return false;
	} else {
		Joomla.submitform(task);
	}
}

function rsf_attachUsersModalEvents(what) {
	jQuery(what).contents().find('a.button-select').each(function() {
		jQuery(this).on('click', function() {
			jSelectUser_addusers(jQuery(this).data('user-value'), jQuery(this).data('user-name'));
		});
	});
}

function change_root(val) {
	document.location = '<?php echo JURI::root(); ?>administrator/index.php?option=com_rsfiles&task=files.root&root='+val;
	return;
}
</script>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rsfiles&view=files'); ?>" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
			<div id="com-rsfiles-sync-progress" style="display:none;">
				<img src="<?php echo JURI::root(); ?>administrator/components/com_rsfiles/assets/images/icons/loader.gif" alt="" />
				<div id="com-rsfiles-sync-text"></div>
			</div>
			<?php echo $this->filterbar->show(); ?>
			
			<div class="alert" style="display: none;" id="com-rsfiles-alert"></div>
			<div class="well">
				<div class="rsf_navigation">
					<?php echo $this->navigation; ?>
				</div>
				
				<?php if ($this->root != 'briefcase' || $this->current != $this->config->briefcase_folder) { ?>
				<div class="input-prepend rsf_new_folder">
					<input type="text" id="newfolder"  name="newfolder" class="input-large" value="" size="30" />
					<button type="button" class="btn btn-info button" onclick="rsf_create();"><?php echo JText::_('COM_RSFILES_CREATE'); ?></button>
				</div>
				<?php } ?>
			</div>
			
			<table class="table table-striped adminlist">
			<thead>
				<th width="1%" align="center" class="hidden-phone center"><input type="checkbox" name="checkall-toggle" id="rscheckbox" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);"/></th>
				<th><?php echo JText::_('COM_RSFILES_FILES'); ?></th>
				<th class="center hidden-phone" align="center" width="1%"><?php echo JText::_('COM_RSFILES_FILES_VERSION'); ?></th>
				<th class="center hidden-phone" align="center" width="10%"><?php echo JText::_('COM_RSFILES_FILES_HITS_LIMIT'); ?></th>
				<th class="center hidden-phone" align="center" width="6%"><?php echo JText::_('COM_RSFILES_FILES_LICENSE'); ?></th>
				
				<?php if (!$this->briefcase) { ?>
				<th class="center hidden-phone" align="center" width="1%"><?php echo JText::_('COM_RSFILES_FILES_REPORTS'); ?></th>
				<th class="center hidden-phone" align="center" width="1%"><?php echo JText::_('COM_RSFILES_FILES_STATISTICS'); ?></th>
				<?php } ?>
				
				<th class="center hidden-phone" align="center" width="1%"><?php echo JText::_('COM_RSFILES_FILES_EDIT'); ?></th>
				<th class="center hidden-phone" align="center" width="1%"><?php echo JText::_('JSTATUS'); ?></th>
			</thead>
			<tbody id="rsfiles_files">
				<?php foreach ($this->items as $i => $item) { ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->fullpath); ?>
						</td>
						
						<?php if ($item->type == 'folder') { ?>
						<td class="nowrap has-context">
							<img src="<?php echo JURI::root().'administrator/components/com_rsfiles/assets/images/icons/folder.png';?>" style="vertical-align:middle;" alt="" /> 
							<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&view=files&folder='.$item->fullpath); ?>">
								<?php echo $item->name; ?>
							</a>  
							<?php echo !empty($item->filename) ? '<span class="rsf_fname"><i>('.$item->filename.')</i></span>' : '';?>
						</td>
						<td class="center hidden-phone" align="center"></td>
						<td class="center hidden-phone" align="center"></td>
						<td class="center hidden-phone" align="center"></td>
						
						<?php if (!$this->briefcase) { ?>
						<td class="center hidden-phone" align="center"></td>
						<td class="center hidden-phone" align="center"></td>
						<?php } ?>
						
						<td class="center hidden-phone" align="center">
							<?php $folderUrl = $item->id ? JRoute::_('index.php?option=com_rsfiles&task=file.edit&IdFile='.$item->id) : JRoute::_('index.php?option=com_rsfiles&task=file.edit&cid='.$item->fullpath); ?>
							<a href="<?php echo $folderUrl; ?>">
								<img src="<?php echo JURI::root().'administrator/components/com_rsfiles/assets/images/icons/edit.png'; ?>" style="vertical-align:middle;" alt="" />
							</a>
						</td>
						<?php } ?>
						
						<?php if($item->type == 'file' || $item->type == 'external') { ?>
						<td class="nowrap has-context">
							<?php $fileUrl = $item->id ? JRoute::_('index.php?option=com_rsfiles&task=file.edit&IdFile='.$item->id) : JRoute::_('index.php?option=com_rsfiles&task=file.edit&cid='.$item->fullpath); ?>
							<img src="<?php echo JURI::root();?>administrator/components/com_rsfiles/assets/images/icons/<?php echo $item->type == 'file' ? 'file' : 'remote' ?>.png" style="vertical-align:middle;" alt="" /> 
							<a href="<?php echo $fileUrl; ?>">
								<?php echo $item->name; ?>
							</a>
							<?php if (!empty($item->filename)) { ?><span class="rsf_fname"><i>(<?php echo $item->filename;?>)</i></span><?php } ?>
						</td>
						
						<td class="center hidden-phone" align="center"><?php echo $item->fileversion; ?></td>
						<td class="center hidden-phone" align="center"><?php echo !empty($item->DownloadLimit) ? $item->Downloads.' / '.$item->DownloadLimit : ''; ?></td>
						<td class="center hidden-phone" align="center"><?php echo $item->filelicense;?></td>
						
						<?php if (!$this->briefcase) { ?>
						<td class="center hidden-phone" align="center">
							<a class="<?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_REPORTS'),$item->reports); ?>" href="<?php echo JRoute::_('index.php?option=com_rsfiles&view=reports&id='.$item->fullpath); ?>">
								<img src="<?php echo JURI::root(); ?>administrator/components/com_rsfiles/assets/images/icons/info.png" alt="<?php echo JText::_('COM_RSFILES_REPORTS'); ?>" />
							</a>
						</td>
						
						<td class="center hidden-phone" align="center">
							<?php if($item->stats) { ?>
							<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&view=statistics&layout=view&id='.$item->id); ?>" target="_blank" title="<?php echo rsfilesHelper::tooltipText(JText::sprintf('COM_RSFILES_STATISTICS_VIEW_FOR',$item->name)); ?>" class="<?php echo rsfilesHelper::tooltipClass(); ?>">
								<img src="<?php echo JURI::root();?>administrator/components/com_rsfiles/assets/images/icons/statistics16.png" />
							</a> 
							<?php } else { ?> 
							<a href="javascript:void(0)" onclick="statistics(<?php echo $i; ?>);" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_STATISTICS_NOT_ENABLED')); ?>" class="<?php echo rsfilesHelper::tooltipClass(); ?>">
								<img src="<?php echo JURI::root();?>administrator/components/com_rsfiles/assets/images/icons/no-statistics16.png" />
							</a>
							<?php } ?>
						</td>
						<?php } ?>
						
						<td class="center hidden-phone" align="center">
							<a href="<?php echo $fileUrl; ?>">
								<img src="<?php echo JURI::root().'administrator/components/com_rsfiles/assets/images/icons/edit.png'; ?>" style="vertical-align:middle;" alt="" />
							</a>
						</td>
						<?php } ?>
						
						<td class="center hidden-phone" align="center">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'files.'); ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="9" align="center"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
		</table>
		</div>
	</div>
	
	<?php if (!$this->briefcase) { ?>
	<div class="modal hide fade" id="batchfiles">
		<?php echo $this->loadTemplate('batch'); ?>
	</div>
	<?php } ?>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="path" id="path" value="<?php echo $this->current; ?>" />
	<input type="hidden" name="folder" value="<?php echo $this->folder; ?>" />
</form>