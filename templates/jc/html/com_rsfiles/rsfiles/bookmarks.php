<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
JText::script('COM_RSFILES_PLEASE_SELECT_FILES'); ?>

<div class="rsfiles-layout">
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
					</ul>
				</div>
			</div>
		</div>
	</div>

	<form action="<?php echo JRoute::_('index.php?option=com_rsfiles'); ?>" method="post" name="adminForm" id="adminForm">
		<div style="text-align: right;">
			<button type="submit" class="btn btn-info" onclick="return rsf_download_bookmarks(1)"><i class="rsicon-download"></i> <?php echo JText::_('COM_RSFILES_DOWNLOAD_ALL'); ?></button> 
			<button type="submit" class="btn btn-info" onclick="return rsf_download_bookmarks();"><i class="rsicon-download"></i> <?php echo JText::_('COM_RSFILES_DOWNLOAD_SELECTED'); ?></button>
		</div>
		<br />
		<table class="table table-striped">
			<thead>
				<tr>
					<th width="1%" align="center" class="center"><input type="checkbox" name="checkall-toggle" id="rscheckbox" value="" onclick="Joomla.checkAll(this);"/></th>
					<th><?php echo JText::_('COM_RSFILES_FILE_NAME'); ?></th>
					<th width="2%" class="center" align="center"><?php echo JText::_('COM_RSFILES_DELETE'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($this->items)) { ?>
				<?php foreach ($this->items as $i => $item) { ?>
				<tr>
					<td class="center" align="center"><?php echo JHtml::_('grid.id', $i, $item->path); ?></td>
					<td><a href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=download'.($item->root == 'briefcase_folder' ? '&from=briefcase' : '').'&path='.rsfilesHelper::encode($item->path)); ?>"><?php echo $item->name; ?><a></td>
					<td class="center" align="center"><a href="<?php echo JRoute::_('index.php?option=com_rsfiles&task=rsfiles.removebookmark&path='.rsfilesHelper::encode($item->path)); ?>"><i class="rsicon-delete"></i></a></td>
				</tr>
				<?php } ?>
				<?php } ?>
			</tbody>
		</table>
		
		<?php echo JHTML::_( 'form.token' ); ?>
		<input type="hidden" name="task" value="rsfiles.downloadbookmarks" />
		<input type="hidden" name="boxchecked" value="0" />
	</form>
</div>