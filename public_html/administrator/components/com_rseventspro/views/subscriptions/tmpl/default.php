<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');

$listOrder	= $this->escape($this->state->get('list.ordering','u.date'));
$listDirn	= $this->escape($this->state->get('list.direction','desc')); ?>

<?php if ($this->state->get('filter.event')) { ?>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#filter_event').off('change');
		jQuery('#filter_event').on('change', function() {
			jQuery('#filter_ticket').val('');
			document.adminForm.submit();
		});
	});
</script>
<?php } ?>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions'); ?>" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div id="j-sidebar-container" class="span2">
		<?php echo JHtmlSidebar::render(); ?>
	</div>
	<div id="j-main-container" class="span10 j-main-container">
		
		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
		
		<table class="table table-striped adminlist" id="locationsList">
			<thead>
				<th width="1%" align="center" class="small"><input type="checkbox" name="checkall-toggle" id="rscheckbox" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);"/></th>
				<th><?php echo JHtml::_('searchtools.sort', 'COM_RSEVENTSPRO_SUBSCRIBERS_HEAD_NAME', 'u.name', $listDirn, $listOrder); ?></th>
				<th width="15%" class="nowrap center"><?php echo JHtml::_('searchtools.sort', 'COM_RSEVENTSPRO_SUBSCRIBERS_HEAD_EVENT', 'e.name', $listDirn, $listOrder); ?></th>
				<th width="15%" class="nowrap center hidden-phone"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBERS_HEAD_TICKETS'); ?></th>
				<th width="15%" class="nowrap center hidden-phone"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBERS_HEAD_TOTAL'); ?></th>
				<th width="10%" class="nowrap center hidden-phone"><?php echo JHtml::_('searchtools.sort', 'COM_RSEVENTSPRO_SUBSCRIBERS_HEAD_PAYMENT', 'u.gateway', $listDirn, $listOrder); ?></th>
				<th width="5%" class="nowrap center hidden-phone"><?php echo JHtml::_('searchtools.sort', 'COM_RSEVENTSPRO_SUBSCRIBERS_HEAD_STATUS', 'u.state', $listDirn, $listOrder); ?></th>
				<th width="1%" class="nowrap center hidden-phone"><?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'u.id', $listDirn, $listOrder); ?></th>
			</thead>
			<tbody id="rseprocontainer">
				<?php foreach ($this->items as $i => $item) { ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="nowrap has-context">
							<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=subscription.edit&id='.$item->id); ?>"><?php echo $item->name; ?></a> <br />
							<?php echo rseventsproHelper::showdate($item->date,null,true); ?> <br />
							<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=subscription.edit&id='.$item->id); ?>"><?php echo $item->email; ?></a> - <?php echo $this->getUser($item->idu); ?> - <?php echo $item->ip; ?>
						</td>
						<td class="center nowrap has-context">
							<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$item->ide); ?>"><?php echo $item->event; ?></a> <br />
							<?php if ($item->allday) { ?>
							<?php echo rseventsproHelper::showdate($item->start,rseventsproHelper::getConfig('global_date')); ?>
							<?php } else { ?>
							(<?php echo rseventsproHelper::showdate($item->start); ?> - <?php echo rseventsproHelper::showdate($item->end); ?>)
							<?php } ?>
						</td>
						<td class="center hidden-phone">
							<?php echo rseventsproHelper::getUserTickets($item->id, true); ?>
						</td>
						<td class="center hidden-phone">
							<?php echo rseventsproHelper::currency(rseventsproHelper::total($item->id)); ?>
						</td>
						<td class="center hidden-phone">
							<?php $payment = rseventsproHelper::getPayment($item->gateway); ?>
							<?php echo $payment ? $payment : '-'; ?>
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
				<td colspan="8" style="text-align: center;">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		</table>
	</div>
</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
</form>