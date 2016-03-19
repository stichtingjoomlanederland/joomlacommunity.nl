<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="navbar navbar-info">
	<div class="navbar-inner rsf_navbar">
		<a class="btn btn-navbar" id="rsf_navbar_btn" data-toggle="collapse" data-target=".rsf_navbar .nav-collapse"><i class="rsicon-down"></i></a>
		<a class="brand visible-tablet visible-phone" href="javascript:void(0)"><?php echo JText::_('COM_RSFILES_NAVBAR'); ?></a>
		<div class="nav-collapse collapse">
			<div class="nav pull-left">
				<ul class="nav rsf_navbar_ul">
					
					<?php if (!$this->briefcase) { ?>
					<li>
						<a class="btn <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_NAVBAR_HOME')); ?>" href="<?php echo JRoute::_('index.php?option=com_rsfiles'.$this->itemid); ?>">
							<span class="rsicon-home"></span>
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
					
					<?php if ($this->config->show_search && !$this->briefcase) { ?>
					<li>
						<a class="btn <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_NAVBAR_SEARCH')); ?>" href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=search'.$this->itemid); ?>">
							<span class="rsicon-search"></span>
						</a>
					</li>
					<?php } ?>
					
					<li>
						<a class="btn <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_NAVBAR_DOWNLOAD')); ?>" href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=download'.($this->briefcase ? '&from=briefcase' : '').rsfilesHelper::getPath(true).($this->hash ? '&hash='.$this->hash : '').$this->itemid); ?>">
							<span class="rsicon-download"></span>
						</a>
					</li>
					
					<?php if ($this->candelete) { ?>
					<li>
						<a class="btn <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_NAVBAR_DELETE')); ?>" href="<?php echo JRoute::_('index.php?option=com_rsfiles&task=rsfiles.delete'.($this->briefcase ? '&from=briefcase' : '').rsfilesHelper::getPath(true).$this->itemid); ?>" onclick="if (!confirm('<?php echo JText::_('COM_RSFILES_DELETE_FILE_MESSAGE',true); ?>')) return false;">
							<span class="rsicon-delete"></span>
						</a>
					</li>
					<?php } ?>
					
					<?php if ($this->canedit) { ?>
					<li>
						<a class="btn <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_NAVBAR_EDIT')); ?>" href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=edit'.($this->briefcase ? '&from=briefcase' : '').rsfilesHelper::getPath(true).'&return='.base64_encode(JURI::getInstance()).$this->itemid); ?>">
							<span class="rsicon-edit"></span>
						</a>
					</li>
					<?php } ?>
					
					<li>
						<a class="btn <?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_NAVBAR_BACK')); ?>" href="<?php echo JRoute::_('index.php?option=com_rsfiles'.($this->briefcase ? '&layout=briefcase' : '').$this->previous.$this->itemid); ?>">
							<span class="rsicon-back"></span>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>