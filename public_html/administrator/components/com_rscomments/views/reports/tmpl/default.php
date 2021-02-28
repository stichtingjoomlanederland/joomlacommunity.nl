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
	<?php echo RSCommentsAdapterGrid::sidebar(); ?>
		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
		
		<table class="table table-striped">
			<caption id="captionTable" class="sr-only">
				<span id="orderedBy"><?php echo JText::_('JGLOBAL_SORTED_BY'); ?> </span>,
				<span id="filteredBy"><?php echo JText::_('JGLOBAL_FILTERED_BY'); ?></span>
			</caption>
			<thead>
				<tr>
					<th width="2%" class="center text-center"><?php echo JHtml::_('grid.checkall'); ?></th>
					<th class="hidden-phone center text-center" width="1%"><?php echo JText::_('JGRID_HEADING_ID'); ?></th>
					<th nowrap="nowrap"><?php echo JText::_('COM_RSCOMMENTS_REPORTS_TEXT'); ?></th>
					<th width="10%" class="center text-center"><?php echo JHtml::_('searchtools.sort', JText::_('COM_RSCOMMENTS_REPORTS_NAME'), 'u.name', $listDirn, $listOrder); ?></th>
					<th width="10%" class="center text-center"><?php echo JHtml::_('searchtools.sort', JText::_('COM_RSCOMMENTS_REPORTS_IP'), 'r.ip', $listDirn, $listOrder); ?></th>
					<th width="10%" class="center text-center"><?php echo JHtml::_('searchtools.sort', JText::_('COM_RSCOMMENTS_REPORTS_DATE'), 'r.date', $listDirn, $listOrder); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) { ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center text-center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
					<td class="center text-center"><?php echo $item->id; ?></td>
					<td><?php echo $item->report; ?></td>
					<td class="center text-center"><?php echo empty($item->uid) ? JText::_('COM_RSCOMMENTS_GUEST') : $item->name; ?></td>
					<td class="center text-center"><?php echo $item->ip; ?></td>
					<td class="center text-center"><?php echo RSCommentsHelperAdmin::showDate($item->date); ?></td>
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
	
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
</form>