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
	<div class="row-fluid">
		<div id="j-sidebar-container" class="span2">
			<?php echo JHtmlSidebar::render(); ?>
		</div>
		<div id="j-main-container" class="span10 j-main-container">
			
			<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
			
			<table class="table table-striped table-hover">
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
						<td align="center"><?php echo JHtml::_('grid.id', $i, $item->IdSubscription); ?></td>
						<td><?php echo $item->IdSubscription; ?></td>
						<td><?php echo $item->name; ?></td>
						<td><a href="mailto:<?php echo $item->email; ?>"><?php echo $item->email; ?></a></td>
						<td align="center"><?php echo RSCommentsHelperAdmin::component($item->option); ?></td>
					</tr>
				<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="5"><?php echo $this->pagination->getListFooter(); ?></td>
					</tr>
				</tfoot>
			</table>

			<?php echo JHtml::_( 'form.token' ); ?>
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="filter[component_id]" id="rsc_filter_component_id" value="<?php echo $this->state->get('filter.component_id'); ?>" />
		</div>
	</div>
</form>