<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_rsfirewall&view=lists'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php $this->filterbar->show(); ?>
	
	<table class="adminlist table table-striped">
		<thead>
		<tr>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( '#' ); ?></th>
			<th width="1%" nowrap="nowrap"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
			<th width="1%" nowrap="nowrap"><?php echo JHtml::_('grid.sort', 'COM_RSFIREWALL_LIST_DATE', 'date', $listDirn, $listOrder); ?></th>
			<th><?php echo JHtml::_('grid.sort', 'COM_RSFIREWALL_IP_ADDRESS', 'ip', $listDirn, $listOrder); ?></th>
			<th><?php echo JHtml::_('grid.sort', 'COM_RSFIREWALL_LIST_REASON', 'reason', $listDirn, $listOrder); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JHtml::_('grid.sort', 'COM_RSFIREWALL_LIST_TYPE', 'type', $listDirn, $listOrder); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'published', $listDirn, $listOrder); ?></th>
		</tr>
		</thead>
	<?php foreach ($this->items as $i => $item) { ?>
		<tr class="row<?php echo $i % 2; ?>">
			<td width="1%" nowrap="nowrap"><?php echo $this->pagination->getRowOffset($i); ?></td>
			<td width="1%" nowrap="nowrap"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
			<td width="1%" nowrap="nowrap"><?php echo JHtml::_('date', $item->date); ?></td>
			<td class="has-context">
				<div class="pull-left">
					<a href="<?php echo JRoute::_('index.php?option=com_rsfirewall&task=list.edit&id='.(int) $item->id); ?>"><img src="components/com_rsfirewall/assets/images/flags/<?php echo $this->geoip->getCountryFlag($item->ip); ?>" /> <?php echo $this->geoip->show($item->ip); ?></a>
				</div>
				<div class="pull-left">
					<?php echo $this->dropdown->show($i, $item); ?>
				</div>
			</td>
			<td><?php echo $this->escape($item->reason); ?></td>
			<td width="1%" nowrap="nowrap" class="com-rsfirewall-list-type-<?php echo $item->type; ?>"><?php echo JText::_('COM_RSFIREWALL_LIST_TYPE_'.$item->type); ?></td>
			<td width="1%" nowrap="nowrap" align="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'lists.'); ?></td>
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