<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rscomments&view=groups'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
			<table class="table table-striped adminlist">
				<thead>
					<tr>
						<th width="1%" align="center"><input type="checkbox" name="checkall-toggle" id="rscheckbox" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);"/></th>
						<th><?php echo JText::_('COM_RSCOMMENTS_GROUP_NAME'); ?></th>
						<th width="1%"><?php echo JText::_('JGRID_HEADING_ID'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item) { ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td><?php echo JHtml::_('grid.id', $i, $item->IdGroup); ?></td>
						<td><a href="<?php echo JRoute::_('index.php?option=com_rscomments&task=group.edit&IdGroup='.$item->IdGroup); ?>"><?php echo $item->GroupName; ?></a></td>
						<td><?php echo $item->IdGroup; ?></td>
					</tr>
				<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3" align="center"><?php echo $this->pagination->getListFooter(); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
</form>