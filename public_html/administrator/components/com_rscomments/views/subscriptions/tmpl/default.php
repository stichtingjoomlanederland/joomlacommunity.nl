<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

$listOrder	= $this->escape($this->state->get('list.ordering', 'IdSubscription'));
$listDirn	= $this->escape($this->state->get('list.direction', 'DESC')); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rscomments&view=subscriptions'); ?>" method="post" name="adminForm" id="adminForm">
	<?php echo RSCommentsAdapterGrid::sidebar(); ?>
		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
		
		<table class="table table-striped table-hover">
			<caption id="captionTable" class="sr-only">
				<span id="orderedBy"><?php echo JText::_('JGLOBAL_SORTED_BY'); ?> </span>,
				<span id="filteredBy"><?php echo JText::_('JGLOBAL_FILTERED_BY'); ?></span>
			</caption>
			<thead>
				<tr>
					<th width="2%"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
					<th width="3%"><?php echo JHtml::_('searchtools.sort', JText::_('COM_RSCOMMENTS_SUBSCRIPTION_ID'), 'IdSubscription', $listDirn, $listOrder); ?></th>
					<th width="30%"><?php echo JHtml::_('searchtools.sort', JText::_('COM_RSCOMMENTS_SUBSCRIPTION_NAME'), 'name', $listDirn, $listOrder); ?></th>
					<th width="30%"><?php echo JHtml::_('searchtools.sort', JText::_('COM_RSCOMMENTS_SUBSCRIPTION_EMAIL'), 'email', $listDirn, $listOrder); ?></th>
					<th width="8%"><?php echo JHtml::_('searchtools.sort', JText::_('COM_RSCOMMENTS_COMMENT_COMPONENT'), 'option', $listDirn, $listOrder); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) { ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td><?php echo JHtml::_('grid.id', $i, $item->IdSubscription); ?></td>
					<td><?php echo $item->IdSubscription; ?></td>
					<td><?php echo $item->name; ?></td>
					<td><a href="mailto:<?php echo $item->email; ?>"><?php echo $item->email; ?></a></td>
					<td><?php echo RSCommentsHelperAdmin::component($item->option); ?></td>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo JHtml::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
</form>

<script>
function rsc_change_filter() {
	if (document.getElementById('filter_component_id')) {
		document.getElementById('filter_component_id').value='';
	}
}
</script>