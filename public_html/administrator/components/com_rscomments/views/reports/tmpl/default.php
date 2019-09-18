<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access'); 

$listOrder	= $this->escape($this->state->get('list.ordering','r.date'));
$listDirn	= $this->escape($this->state->get('list.direction','DESC')); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rscomments&view=reports&id='.JFactory::getApplication()->input->getInt('id',0)); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div id="j-sidebar-container" class="span2">
			<?php echo JHtmlSidebar::render(); ?>
		</div>
		<div id="j-main-container" class="span10 j-main-container">
			
			<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
			
			<table class="table table-striped">
				<thead>
					<tr>
						<th width="2%" class="center" align="center"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
						<th class="hidden-phone center" align="center" width="1%"><?php echo JText::_('JGRID_HEADING_ID'); ?></th>
						<th nowrap="nowrap"><?php echo JText::_('COM_RSCOMMENTS_REPORTS_TEXT'); ?></th>
						<th width="10%" class="center" align="center"><?php echo JHtml::_('searchtools.sort', JText::_('COM_RSCOMMENTS_REPORTS_NAME'), 'u.name', $listDirn, $listOrder); ?></th>
						<th width="10%" class="center" align="center"><?php echo JHtml::_('searchtools.sort', JText::_('COM_RSCOMMENTS_REPORTS_IP'), 'r.ip', $listDirn, $listOrder); ?></th>
						<th width="10%" class="center" align="center"><?php echo JHtml::_('searchtools.sort', JText::_('COM_RSCOMMENTS_REPORTS_DATE'), 'r.date', $listDirn, $listOrder); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item) { ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center" align="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
						<td class="center" align="center"><?php echo $item->id; ?></td>
						<td><?php echo $item->report; ?></td>
						<td class="center" align="center"><?php echo empty($item->uid) ? JText::_('COM_RSCOMMENTS_GUEST') : $item->name; ?></td>
						<td class="center" align="center"><?php echo $item->ip; ?></td>
						<td class="center" align="center"><?php echo RSCommentsHelperAdmin::showDate($item->date); ?></td>
					</tr>
				<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="6" align="center"><?php echo $this->pagination->getListFooter(); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
</form>