<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive'); ?>

<form action="index.php?option=com_rsform" method="post" name="adminForm" id="adminForm">

	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<table class="adminlist table table-striped" id="articleList">
			<thead>
				<tr>
					<th width="1%" nowrap="nowrap"><?php echo JText::_('#'); ?></th>
					<th width="1%" nowrap="nowrap"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
					<th width="1%" nowrap="nowrap"><?php echo JText::_('JSTATUS'); ?></th>
					<th class="title"><?php echo JHtml::_('grid.sort', JText::_('RSFP_FORM_TITLE'), 'FormTitle', $this->sortOrder, $this->sortColumn, 'directory.manage'); ?></th>
					<th class="title"><?php echo JHtml::_('grid.sort', JText::_('RSFP_FORM_NAME'), 'FormName', $this->sortOrder, $this->sortColumn, 'directory.manage'); ?></th>
					<th width="1%" nowrap="nowrap" class="title"><?php echo JHtml::_('grid.sort', JText::_('RSFP_FORM_ID'), 'FormId', $this->sortOrder, $this->sortColumn, 'directory.manage'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($this->forms as $i => $row) { ?>
				<?php $row->FormTitle = strip_tags($row->FormTitle); ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td width="1%" nowrap="nowrap"><?php echo $this->pagination->getRowOffset($i); ?></td>
					<td width="1%" nowrap="nowrap"><?php echo JHtml::_('grid.id', $i, $row->FormId); ?></td>
					<td width="1%" nowrap="nowrap">
						<?php if ($this->getStatus($row->FormId)) { ?>
							<span class="badge badge-success"><?php echo JText::_('RSFP_SUBM_DIR_ENABLED'); ?></span>
						<?php } else { ?>
							<span class="badge badge-important"><?php echo JText::_('RSFP_SUBM_DIR_DISABLED'); ?></span>
						<?php } ?>
					</td>
					<td><a href="index.php?option=com_rsform&amp;view=directory&amp;layout=edit&amp;formId=<?php echo $row->FormId; ?>"><?php echo !empty($row->FormTitle) ? $row->FormTitle : '<em>no title</em>'; ?></a></td>
					<td><?php echo $row->FormName; ?></td>
					<td width="1%" nowrap="nowrap"><?php echo $row->FormId; ?></td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
		</table>
	</div>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="task" value="directory.manage" />
	
	<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortOrder; ?>" />
</form>