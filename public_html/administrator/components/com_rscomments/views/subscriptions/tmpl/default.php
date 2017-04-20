<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.modal');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo JRoute::_('index.php?option=com_rscomments&view=subscriptions'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>

			<?php if ($this->state->get('subscriptions.filter.component') == 'com_content' || 
					$this->state->get('subscriptions.filter.component') == 'com_rsblog' || 
					$this->state->get('subscriptions.filter.component') == 'com_k2' || 
					$this->state->get('subscriptions.filter.component') == 'com_flexicontent') { ?>
					<div class="row-fluid hidden-phone"><div class="span12 offset0"><a class="modal btn btn-info" style="display:block;" rel="{handler: 'iframe', size: {x: 600, y: 475}}" href="index.php?option=com_rscomments&view=components&component=<?php echo $this->state->get('subscriptions.filter.component');?>&tmpl=component"><?php echo (!empty($this->article) ? $this->escape($this->article) : JText::_('COM_RSCOMMENTS_SELECT_ARTICLE')); ?></a></div></div>
			<?php } ?>
		</div>
		<div id="j-main-container" class="span10">
		<?php $this->filterbar->show(); ?>
		<div class="clearfix"></div>
		<div id="editcell1">
			<table class="table table-striped table-hover adminlist" id="sortTable" width="100%">
				<thead>
					<tr>
						<th width="2%"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
						<th width="1%"><?php echo JText::_('COM_RSCOMMENTS_SUBSCRIPTION_ID'); ?></th>
						<th width="30%"><?php echo JHtml::_('grid.sort', JText::_('COM_RSCOMMENTS_SUBSCRIPTION_NAME'), 'name', $listDirn, $listOrder); ?></th>
						<th width="30%"><?php echo JHtml::_('grid.sort', JText::_('COM_RSCOMMENTS_SUBSCRIPTION_EMAIL'), 'email', $listDirn, $listOrder); ?></th>
						<th width="8%"><?php echo JHtml::_('grid.sort', JText::_('COM_RSCOMMENTS_COMMENT_COMPONENT'), 'option', $listDirn, $listOrder); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item) { ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td align="center"><?php echo JHtml::_('grid.id', $i, $item->IdSubscription); ?></td>
						<td><?php echo $item->IdSubscription; ?></td>
						<td><?php echo $item->name; ?></td>
						<td><a href="mailto:<?php echo $item->email; ?>"><?php echo $item->email; ?></a></td>
						<td align="center"><?php echo RSCommentsHelper::component($item->option); ?></td>
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
		<input type="hidden" name="filter_component_id" id="rsc_filter_component_id" value="<?php echo $this->state->get('subscriptions.filter.component_id'); ?>" />
		</div> <!-- .span10 -->
	</div> <!-- .row-fluid -->
</form>