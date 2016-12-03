<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

$listOrder 	= $this->escape($this->state->get('list.ordering'));
$listDirn 	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'ordering';
$ordering	= $listOrder == 'ordering';
if ($saveOrder && $this->isJ30) {
	$saveOrderingUrl = 'index.php?option=com_rsfirewall&task=feeds.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_rsfirewall&view=feeds'); ?>" method="post" name="adminForm" id="adminForm">	
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php $this->filterbar->show(); ?>
	
	<table class="adminlist table table-striped" id="articleList">
		<thead>
		<tr>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( '#' ); ?></th>
			<th width="1%" nowrap="nowrap"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
			<th><?php echo JHtml::_('grid.sort', 'COM_RSFIREWALL_FEED_URL', 'url', $listDirn, $listOrder); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JHtml::_('grid.sort', 'COM_RSFIREWALL_FEED_LIMIT', 'limit', $listDirn, $listOrder); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'published', $listDirn, $listOrder); ?></th>
			<?php if ($this->isJ30) { ?>
			<th width="1%" class="nowrap center">
			<?php } else { ?>
			<th width="100">
			<?php } ?>
				<?php if ($this->isJ30) { ?>
					<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
				<?php } else { ?>
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
					<?php if ($saveOrder) { ?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'feeds.saveorder'); ?>
					<?php } ?>
				<?php } ?>
			</th>
		</tr>
		</thead>
	<?php foreach ($this->items as $i => $item) { ?>
		<tr class="row<?php echo $i % 2; ?>">
			<td width="1%" nowrap="nowrap"><?php echo $this->pagination->getRowOffset($i); ?></td>
			<td width="1%" nowrap="nowrap"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
			<td class="has-context">
				<div class="pull-left">
					<a href="<?php echo JRoute::_('index.php?option=com_rsfirewall&task=feed.edit&id='.(int) $item->id); ?>"><?php echo $this->escape($item->url); ?></a>
				</div>
				<div class="pull-left">
					<?php echo $this->dropdown->show($i, $item); ?>
				</div>
			</td>
			<td width="1%" nowrap="nowrap"><?php echo $this->escape($item->limit); ?></td>
			<td width="1%" nowrap="nowrap" align="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'feeds.'); ?></td>
			<td class="order center">
				<?php if ($this->isJ30) { ?>
					<?php
					$disableClassName = '';
					$disabledLabel	  = '';

					if (!$saveOrder) {
						$disabledLabel    = JText::_('JORDERINGDISABLED');
						$disableClassName = 'inactive tip-top';
					} ?>
					<span class="sortable-handler hasTooltip <?php echo $disableClassName; ?>" title="<?php echo $disabledLabel; ?>">
						<i class="icon-menu"></i>
					</span>
					<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order" />
				<?php } else { ?>
					<?php if ($saveOrder) { ?>
						<?php if ($listDirn == 'asc') { ?>
							<span><?php echo $this->pagination->orderUpIcon($i, true, 'feeds.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
							<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'feeds.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
						<?php } elseif ($listDirn == 'desc') { ?>
							<span><?php echo $this->pagination->orderUpIcon($i, true, 'feeds.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
							<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'feeds.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
						<?php } ?>
					<?php } ?>
					<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
				<?php } ?>
			</td>
		</tr>
	<?php } ?>
	<tfoot>
		<tr>
			<td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
	</tfoot>
	</table>
	
	<div>
		<?php echo JHtml::_( 'form.token' ); ?>
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="task" value="" />
		<?php if (!$this->isJ30) { ?>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php } ?>
	</div>
	</div>
</form>