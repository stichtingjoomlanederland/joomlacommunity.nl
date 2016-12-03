<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

JText::script('COM_RSFIREWALL_BLOCK');
JText::script('COM_RSFIREWALL_UNBLOCK');
JText::script('COM_RSFIREWALL_LOG_ERROR');
JText::script('COM_RSFIREWALL_LOG_WARNING');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<script type="text/javascript">
Joomla.submitbutton = function(pressbutton) {
	if (pressbutton == 'logs.download') {
		document.location.href = 'index.php?option=com_rsfirewall&task=logs.download&<?php echo JSession::getFormToken(); ?>=1';
		return false;
	}
	Joomla.submitform(pressbutton);
};
</script>
<form action="<?php echo JRoute::_('index.php?option=com_rsfirewall&view=logs'); ?>" method="post" name="adminForm" id="adminForm">	
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php $this->filterbar->show(); ?>
	<div class="com-rsfirewall-log-message"></div>
	<table class="adminlist table table-striped">
		<thead>
		<tr>
			<th width="1%" nowrap="nowrap"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
			<th width="1%" nowrap="nowrap"></th>
			<th width="1%" nowrap="nowrap" class="hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_RSFIREWALL_ALERT_LEVEL', 'logs.level', $listDirn, $listOrder); ?></th>
			<th width="1%" nowrap="nowrap" class="hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_RSFIREWALL_LOG_DATE_EVENT', 'logs.date', $listDirn, $listOrder); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JHtml::_('grid.sort', 'COM_RSFIREWALL_LOG_IP_ADDRESS', 'logs.ip', $listDirn, $listOrder); ?></th>
			<th width="1%" nowrap="nowrap" class="hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_RSFIREWALL_LOG_USER_ID', 'logs.user_id', $listDirn, $listOrder); ?></th>
			<th width="1%" nowrap="nowrap" class="hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_RSFIREWALL_LOG_USERNAME', 'logs.username', $listDirn, $listOrder); ?></th>
			<th><?php echo JHtml::_('grid.sort', 'COM_RSFIREWALL_LOG_PAGE', 'logs.page', $listDirn, $listOrder); ?></th>
			<th class="hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_RSFIREWALL_LOG_REFERER', 'logs.referer', $listDirn, $listOrder); ?></th>
			<th><?php echo JText::_('COM_RSFIREWALL_LOG_DESCRIPTION'); ?></th>
		</tr>
		</thead>
	<?php foreach ($this->items as $i => $item) { ?>
		<tr class="row<?php echo $i % 2; ?> rsf-entry" id="rsf-log-<?php echo $item->id;?>">
			<td width="1%" nowrap="nowrap"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
			<td width="1%" nowrap="nowrap" class="rsf-status"><?php 
				if (!is_null($item->type)) {
					echo $item->type ? JText::_('COM_RSFIREWALL_WHITELISTED') : '<button type="button" onclick="RSFirewall.Status.Change('.$item->id.','.$item->listId.', \'unblockajax\', this)" class="btn btn-small">'.JText::_('COM_RSFIREWALL_UNBLOCK').'</button>';
				} else {
				?>
					<button type="button" onclick="RSFirewall.Status.Change(<?php echo $item->id; ?>, null, 'blockajax', this);" class="btn btn-danger btn-small"><?php echo JText::_('COM_RSFIREWALL_BLOCK'); ?></button>
				<?php
				}
			?></td>
			<td width="1%" nowrap="nowrap" class="hidden-phone com-rsfirewall-level-<?php echo $item->level; ?>"><?php echo JText::_('COM_RSFIREWALL_LEVEL_'.$item->level); ?></td>
			<td width="1%" nowrap="nowrap" class="hidden-phone"><?php echo $this->showDate($item->date); ?></td>
			<td width="1%" nowrap="nowrap"><img src="components/com_rsfirewall/assets/images/flags/<?php echo $this->geoip->getCountryFlag($item->ip); ?>" /> <?php echo $this->geoip->show($item->ip); ?></td>
			<td width="1%" nowrap="nowrap" class="hidden-phone"><?php echo (int) $item->user_id; ?></td>
			<td width="1%" nowrap="nowrap" class="hidden-phone"><?php echo $this->escape($item->username); ?></td>
			<td class="com-rsfirewall-break-word"><?php echo $this->escape($item->page); ?></td>
			<td class="hidden-phone com-rsfirewall-break-word"><?php echo $item->referer ? $this->escape($item->referer) : '<em>'.JText::_('COM_RSFIREWALL_NO_REFERER').'</em>'; ?></td>
			<td class="com-rsfirewall-break-word">
				<?php echo JText::_('COM_RSFIREWALL_EVENT_'.$item->code); ?>
				<?php if (!empty($item->debug_variables)) { ?>
					<button type="button" class="btn btn-small" onclick="RSFirewall.$(this).parent().find('.com-rsfirewall-hidden').removeClass('com-rsfirewall-hidden'); RSFirewall.$(this).remove();"><?php echo JText::_('COM_RSFIREWALL_SHOW'); ?></button>
					<div class="com-rsfirewall-hidden">
						<p><b><?php echo JText::_('COM_RSFIREWALL_LOG_DEBUG_VARIABLES'); ?></b></p>
						<?php echo nl2br($this->escape($item->debug_variables)); ?>
					</div>
				<?php } ?>
			</td>
		</tr>
	<?php } ?>
	<tfoot>
		<tr>
			<td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td>
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

<script type="text/javascript">
RSFirewall.Status.errorContainer = RSFirewall.$('.com-rsfirewall-log-message');
</script>