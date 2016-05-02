<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
$currentFolder = $this->current == $this->config->download_folder ? 'root_rs_files' : $this->currentRel; 
$canCreate = rsfilesHelper::permissions('CanCreate',$currentFolder);
$canUpload = rsfilesHelper::permissions('CanUpload',$currentFolder);
$canDelete = rsfilesHelper::permissions('CanDelete',$currentFolder); ?>

<div class="navbar navbar-info">
	<div class="navbar-inner rsf_navbar">
		<a class="btn btn-navbar" id="rsf_navbar_btn" data-toggle="collapse" data-target=".nav-collapse"><i class="rsicon-down"></i></a>
		<a class="brand visible-tablet visible-phone" href="javascript:void(0)"><?php echo JText::_('COM_RSFILES_NAVBAR'); ?></a>
		<div class="nav-collapse collapse">
			<div class="nav pull-left">
				<ul class="nav rsf_navbar_ul">
					<li>
						<a class="btn <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_NAVBAR_HOME')); ?>" href="<?php echo JRoute::_('index.php?option=com_rsfiles'.$this->itemid); ?>">
							<span class="rsicon-home"></span>
						</a>
					</li>
					
					<?php if ($this->config->show_search && !$this->briefcase) { ?>
					<li>
						<a class="btn <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_NAVBAR_SEARCH')); ?>" href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=search'.$this->itemid); ?>">
							<span class="rsicon-search"></span>
						</a>
					</li>
					<?php } ?>
					
					<?php if ($this->config->enable_upload && $canUpload) { ?>
					<li>
						<a class="btn <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_NAVBAR_UPLOAD')); ?>" href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=upload'.($this->current_folder ? '&folder='.rsfilesHelper::encode($this->current_folder) : '').$this->itemid); ?>">
							<span class="rsicon-upload"></span>
						</a>
					</li>
					<?php } ?>
					
					<?php if ($canCreate) { ?>
					<li>
						<a class="rs_modal btn <?php echo rsfilesHelper::tooltipClass(); ?>" rel="{handler: 'iframe', size: {x: 450, y: 200}}" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_NAVBAR_NEW_FOLDER')); ?>" href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=create&tmpl=component'.($this->current_folder ? '&folder='.rsfilesHelper::encode($this->current_folder) : '').$this->itemid); ?>">
							<span class="rsicon-create-folder"></span>
						</a>
					</li>
					<?php } ?>
					
					<?php if ($currentFolder != 'root_rs_files' && $canDelete) { ?>
					<li>
						<a class="btn <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_NAVBAR_DELETE')); ?>" href="<?php echo JRoute::_('index.php?option=com_rsfiles&task=rsfiles.delete'.($this->current_folder ? '&folder='.rsfilesHelper::encode($this->current_folder) : '').$this->itemid); ?>" onclick="if (!confirm('<?php echo JText::_('COM_RSFILES_DELETE_MESSAGE',true); ?>')) return false;">
							<span class="rsicon-delete"></span>
						</a>
					</li>
					<?php } ?>
					
					<?php if ($this->user->get('id') > 0 && $this->config->enable_briefcase && !empty($this->config->briefcase_folder) && (rsfilesHelper::briefcase('CanDownloadBriefcase') || rsfilesHelper::briefcase('CanUploadBriefcase') || rsfilesHelper::briefcase('CanDeleteBriefcase') || rsfilesHelper::briefcase('CanMaintainBriefcase'))) { ?>
					<li>
						<a class="btn <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_NAVBAR_BRIEFCASE')); ?>" href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=briefcase'.$this->itemid); ?>">
							<span class="rsicon-briefcase"></span>
						</a>
					</li>
					<?php } ?>
					
					<?php if ($this->config->show_bookmark) { ?>
					<li>
						<a class="btn <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_NAVBAR_BOOKMARK')); ?>" href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=bookmarks'.$this->itemid); ?>">
							<span class="rsicon-bookmark"></span>
						</a>
					</li>
					<?php } ?>
					
					<?php if($this->current != $this->dld_fld) { ?>
					<li>
						<a class="btn <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_NAVBAR_UP')); ?>" href="<?php echo JRoute::_('index.php?option=com_rsfiles'.$this->previous.$this->itemid); ?>">
							<span class="rsicon-up"></span>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
			<div class="nav pull-right">
				<ul class="nav">
					<?php if ($this->config->enable_rss) { ?>
					<li>
						<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&format=feed&type=rss'.($this->current_folder ? '&folder='.rsfilesHelper::encode($this->current_folder) : '').$this->itemid); ?>" class="btn <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_NAVBAR_RSS')); ?>">
							<span class="rsicon-rss"></span>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
</div>

<div class="alert" id="rsf_alert" style="display:none;">
	<button type="button" class="close" onclick="document.getElementById('rsf_alert').style.display = 'none';">&times;</button>
	<span id="rsf_message"></span>
</div>

<?php if ($this->config->file_path == 1) { ?>
	<ul class="breadcrumb">
		<?php if (empty($this->navigation)) { ?>
		<li class="active"><?php echo JText::_('COM_RSFILES_HOME'); ?></li>
		<?php } else { ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_rsfiles'.$this->itemid); ?>"><?php echo JText::_('COM_RSFILES_HOME'); ?></a>
		</li>
		<?php end($this->navigation); ?>
		<?php $last_item_key = key($this->navigation); ?>
		<?php foreach ($this->navigation as $key => $element) { ?>
		<?php if ($key != $last_item_key) { ?>
		<li>
			<span class="divider">/</span>
			<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&folder='.rsfilesHelper::encode($element->fullpath).$this->itemid); ?>"><?php echo $element->name; ?></a>
		</li>
		<?php } else { ?>
		<li class="active">
			<span class="divider">/</span>
			<?php echo $element->name; ?>
		</li>
		<?php } ?>
		<?php } ?>
		<?php } ?>
	</ul>
<?php } ?>