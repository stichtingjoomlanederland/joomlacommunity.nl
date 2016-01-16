<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<script type="text/javascript">
	window.addEvent('domready', function(){ 
		<?php if ($this->total > count($this->items)) { ?>
		$('rsepro_loadmore').addEvent('click', function(el) {
			var lstart = $('rseprocontainer').getElements('tr');
			rspagination('subscriptions',lstart.length);
		});
		<?php } ?>
		
		<?php if ($this->state->get('filter.event')) { ?>
		$('filter_event').removeProperty('onchange');
		document.getElementById('filter_event').onchange = function() {
			$('filter_ticket').value = '';
			document.adminForm.submit();
		}
		<?php } ?>
});
</script>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions'); ?>" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div class="span10">
		<?php echo $this->filterbar->show(); ?>
		<table class="table table-striped adminlist" id="locationsList">
			<thead>
				<th width="1%" align="center" class="small hidden-phone"><input type="checkbox" name="checkall-toggle" id="rscheckbox" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);"/></th>
				<th><?php echo JHtml::_('grid.sort', 'COM_RSEVENTSPRO_SUBSCRIBERS_HEAD_NAME', 'u.name', $listDirn, $listOrder); ?></th>
				<th width="15%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'COM_RSEVENTSPRO_SUBSCRIBERS_HEAD_EVENT', 'e.name', $listDirn, $listOrder); ?></th>
				<th width="15%" class="nowrap center hidden-phone"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBERS_HEAD_TICKETS'); ?></th>
				<th width="10%" class="nowrap center hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_RSEVENTSPRO_SUBSCRIBERS_HEAD_PAYMENT', 'u.gateway', $listDirn, $listOrder); ?></th>
				<th width="5%" class="nowrap center hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_RSEVENTSPRO_SUBSCRIBERS_HEAD_STATUS', 'u.state', $listDirn, $listOrder); ?></th>
				<th width="1%" class="nowrap center hidden-phone"><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'u.id', $listDirn, $listOrder); ?></th>
			</thead>
			<tbody id="rseprocontainer">
				<?php foreach ($this->items as $i => $item) { ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="nowrap has-context">
							<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=subscription.edit&id='.$item->id); ?>"><?php echo $item->name; ?></a> <br />
							<?php echo rseventsproHelper::date($item->date,null,true); ?> <br />
							<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=subscription.edit&id='.$item->id); ?>"><?php echo $item->email; ?></a> - <?php echo $this->getUser($item->idu); ?> - <?php echo $item->ip; ?>
						</td>
						<td class="center nowrap has-context">
							<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$item->ide); ?>"><?php echo $item->event; ?></a> <br />
							<?php if ($item->allday) { ?>
							<?php echo rseventsproHelper::date($item->start,rseventsproHelper::getConfig('global_date')); ?>
							<?php } else { ?>
							(<?php echo rseventsproHelper::date($item->start); ?> - <?php echo rseventsproHelper::date($item->end); ?>)
							<?php } ?>
						</td>
						<td class="center hidden-phone">
							<?php echo rseventsproHelper::getUserTickets($item->id, true); ?>
						</td>
						<td class="center hidden-phone">
							<?php echo rseventsproHelper::getPayment($item->gateway); ?>
						</td>
						<td class="center hidden-phone">
							<?php echo $this->getStatus($item->state); ?>
						</td>
						<td class="center hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="7">
					<?php if ($this->total > count($this->items)) { ?>
					<button type="button" class="rsepromore_inactive" id="rsepro_loadmore"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_MORE_RESULTS'); ?></button>
					<?php } ?>
				</td>
			</tr>
		</tfoot>
		</table>
	</div>
</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="total" id="total" value="<?php echo $this->total; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
</form>