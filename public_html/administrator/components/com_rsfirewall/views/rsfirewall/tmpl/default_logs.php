<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<table class="adminlist table table-striped">
	<thead>
	<tr>
		<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_RSFIREWALL_ALERT_LEVEL'); ?></th>
		<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_RSFIREWALL_LOG_DATE_EVENT'); ?></th>
		<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_RSFIREWALL_LOG_IP_ADDRESS'); ?></th>
		<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_RSFIREWALL_LOG_USER_ID'); ?></th>
		<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_RSFIREWALL_LOG_USERNAME'); ?></th>
		<th><?php echo JText::_('COM_RSFIREWALL_LOG_PAGE'); ?></th>
		<th><?php echo JText::_('COM_RSFIREWALL_LOG_REFERER'); ?></th>
	</tr>
	</thead>
	<?php foreach ($this->lastLogs as $i => $item) { ?>
		<tr class="row<?php echo $i % 2; ?>">
			<td width="1%" nowrap="nowrap" class="com-rsfirewall-level-<?php echo $item->level; ?>"><?php echo JText::_('COM_RSFIREWALL_LEVEL_'.$item->level); ?></td>
			<td width="1%" nowrap="nowrap"><?php echo $this->showDate($item->date); ?></td>
			<td width="1%" nowrap="nowrap"><img src="components/com_rsfirewall/assets/images/flags/<?php echo $this->geoip->getCountryFlag($item->ip); ?>" /> <?php echo $this->geoip->show($item->ip); ?></td>
			<td width="1%" nowrap="nowrap"><?php echo (int) $item->user_id; ?></td>
			<td width="1%" nowrap="nowrap"><?php echo $this->escape($item->username); ?></td>
			<td class="com-rsfirewall-break-word"><?php echo $this->escape($item->page); ?></td>
			<td class="com-rsfirewall-break-word"><?php echo $item->referer ? $this->escape($item->referer) : '<em>'.JText::_('COM_RSFIREWALL_NO_REFERER').'</em>'; ?></td>
		</tr>
	<?php } ?>
</table>