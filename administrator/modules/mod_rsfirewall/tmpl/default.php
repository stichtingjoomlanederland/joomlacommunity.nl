<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
JText::script('MOD_RSFIREWALL_YOU_ARE_RUNNING_LATEST_VERSION');
JText::script('MOD_RSFIREWALL_UPDATE_IS_AVAILABLE_RSFIREWALL');
JText::script('MOD_RSFIREWALL_UPDATE_IS_AVAILABLE_JOOMLA');
?>
<div id="mod_rsfirewall_container">
	<div>
		<span class="com-rsfirewall-icon-16-grade"></span>
		<strong class="mod-rsfirewall-float-left mod-rsfirewall-eq-width"><?php echo JText::_('MOD_RSFIREWALL_GRADE'); ?></strong>
		<span class="com-rsfirewall-icon-16-spacer"></span><span class="mod-rsfirewall-float-left" style="color: <?php echo $color; ?>;"><?php echo $grade > 0 ? JText::sprintf('MOD_RSFIREWALL_YOUR_GRADE_IS', $grade) : JText::_('MOD_RSFIREWALL_GRADE_NOT_RUN'); ?></span>
	</div>
	<div>
		<span class="com-rsfirewall-icon-16-firewall"></span>
		<strong class="mod-rsfirewall-float-left mod-rsfirewall-eq-width">RSFirewall!</strong>
		<span id="mod-rsfirewall-firewall-version">
			<span class="com-rsfirewall-icon-16-loading"></span>
		</span>
	</div>
	<div>
		<span class="com-rsfirewall-icon-16-joomla"></span>
		<strong class="mod-rsfirewall-float-left mod-rsfirewall-eq-width">Joomla!</strong>
		<span id="mod-rsfirewall-joomla-version">
			<span class="com-rsfirewall-icon-16-loading"></span>
		</span>
	</div>
	
	<?php if ($logs) { ?>
	<p><?php echo JText::sprintf('MOD_RSFIREWALL_LAST_MESSAGES_FROM_SYSTEM_LOG', $logNum, JRoute::_('index.php?option=com_rsfirewall&view=logs')); ?></p>
	<table class="adminlist table table-striped">
	<thead>
		<tr>
			<th nowrap="nowrap"><?php echo JText::_('MOD_RSFIREWALL_ALERT_LEVEL'); ?></th>
			<th nowrap="nowrap"><?php echo JText::_('MOD_RSFIREWALL_DATE_EVENT'); ?></th>
			<th nowrap="nowrap"><?php echo JText::_('MOD_RSFIREWALL_IP_ADDRESS'); ?></th>
			<th nowrap="nowrap"><?php echo JText::_('MOD_RSFIREWALL_PAGE'); ?></th>
			<th nowrap="nowrap"><?php echo JText::_('MOD_RSFIREWALL_ALERT_DESCRIPTION'); ?></th>
		</tr>
	</thead>
	<?php foreach ($logs as $i => $log) { ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td class="com-rsfirewall-level-<?php echo $log->level; ?>"><?php echo JText::_('MOD_RSFIREWALL_LEVEL_'.$log->level); ?></td>
		<td><?php echo JHTML::_('date', $log->date, 'Y-m-d H:i:s'); ?></td>
		<td><img src="components/com_rsfirewall/assets/images/flags/<?php echo $geoip->getCountryFlag($log->ip); ?>" /> <?php echo $geoip->show($log->ip); ?></td>
		<td class="mod-rsfirewall-break-word"><?php echo htmlentities($log->page, ENT_COMPAT, 'utf-8'); ?></td>
		<td><?php echo JText::_('COM_RSFIREWALL_EVENT_'.$log->code); ?></td>
	</tr>
	<?php } ?>
	</table>
	<?php } ?>
</div>