<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<form action="<?php echo JRoute::_('index.php?option=com_rsfirewall');?>" method="post" name="adminForm" id="adminForm">
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
	<div id="com-rsfirewall-main-content">
	<?php if ($this->supported) { ?>
	<div id="com-rsfirewall-dbcheck-messages">
		<div class="alert alert-info"><p><?php echo JText::_('COM_RSFIREWALL_ONLY_TABLES_WITH_MYISAM_STORAGE_ENGINE_DESC');?></p></div>
	</div>
	<p id="com-rsfirewall-scan-in-progress" class="com-rsfirewall-hidden"><?php echo JText::_('COM_RSFIREWALL_SCAN_IS_IN_PROGRESS'); ?></p>
	<p><button type="button" class="btn btn-primary" id="com-rsfirewall-start-button" onclick="RSFirewallStartCheck();"><?php echo JText::_('COM_RSFIREWALL_CHECK_DB'); ?></button></p>

	<div class="com-rsfirewall-content-box">
		<div class="com-rsfirewall-content-box-header">
			<h3><span class="com-rsfirewall-icon-16-database"></span><?php echo JText::_('COM_RSFIREWALL_SERVER_DATABASE'); ?></h3>
		</div>
		<div id="com-rsfirewall-database" class="com-rsfirewall-content-box-content com-rsfirewall-hidden">
			<div class="com-rsfirewall-progress" id="com-rsfirewall-database-progress"><div class="com-rsfirewall-bar" style="width: 0%;">0%</div></div>
			<table id="com-rsfirewall-database-table">
				<thead>
					<tr>
						<th width="20%" nowrap="nowrap"><?php echo JText::_('COM_RSFIREWALL_TABLE_NAME'); ?></th>
						<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_RSFIREWALL_TABLE_ENGINE'); ?></th>
						<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_RSFIREWALL_TABLE_COLLATION'); ?></th>
						<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_RSFIREWALL_TABLE_ROWS'); ?></th>
						<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_RSFIREWALL_TABLE_DATA'); ?></th>
						<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_RSFIREWALL_TABLE_INDEX'); ?></th>
						<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_RSFIREWALL_TABLE_OVERHEAD'); ?></th>
						<th><?php echo JText::_('COM_RSFIREWALL_RESULT'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->tables as $i => $table) { ?>
					<tr class="com-rsfirewall-table-row <?php if ($i % 2) { ?>alt-row<?php } ?> com-rsfirewall-hidden">
						<td width="20%" nowrap="nowrap"><?php echo $this->escape($table->Name); ?></td>
						<td width="1%" nowrap="nowrap"><?php echo $this->escape($table->Engine); ?></td>
						<td width="1%" nowrap="nowrap"><?php echo $this->escape($table->Collation); ?></td>
						<td width="1%" nowrap="nowrap"><?php echo (int) $table->Rows; ?></td>
						<td width="1%" nowrap="nowrap"><?php echo $this->_convert($table->Data_length); ?></td>
						<td width="1%" nowrap="nowrap"><?php echo $this->_convert($table->Index_length); ?></td>
						<td width="1%" nowrap="nowrap">
							<?php if ($table->Data_free > 0) { ?>
								<?php if (strtolower($table->Engine) == 'myisam') { ?>
								<b class="com-rsfirewall-level-high"><?php echo $this->_convert($table->Data_free); ?></b>
								<?php } else { ?>
								<em><?php echo JText::_('COM_RSFIREWALL_NOT_SUPPORTED'); ?></em>
								<?php } ?>
							<?php } else { ?>
								<?php echo $this->_convert($table->Data_free); ?>
							<?php } ?>
						</td>
						<td id="result<?php echo $i; ?>"></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
			
	<script type="text/javascript">
	RSFirewall.requestTimeOut.Seconds = <?php echo $this->request_timeout;?>;
	// DB Check
	function RSFirewallStartCheck() {
		RSFirewall.$('#com-rsfirewall-start-button').remove();
		RSFirewall.Database.Check.unhide('#com-rsfirewall-scan-in-progress').hide().fadeIn('slow');
		
		RSFirewall.Database.Check.prefix = 'com-rsfirewall-database';
		RSFirewall.Database.Check.tables = [];
		<?php krsort($this->tables); ?>
		<?php foreach ($this->tables as $table) { ?>
		RSFirewall.Database.Check.tables.push('<?php echo addslashes($table->Name); ?>');
		<?php } ?>
		RSFirewall.Database.Check.tablesNum = RSFirewall.Database.Check.tables.length;
		
		RSFirewall.Database.Check.stopCheck = function() {
			RSFirewall.$('#com-rsfirewall-database-progress').fadeOut('fast', function(){RSFirewall.$(this).remove()});
			RSFirewall.$('#com-rsfirewall-scan-in-progress').remove();
		}
		
		RSFirewall.Database.Check.startCheck();
	}
	</script>
	<?php } else { ?>
	<div class="alert alert-info"><?php echo JText::_('COM_RSFIREWALL_DB_CHECK_UNSUPPORTED'); ?></div>
	<?php } ?>
	</div>
</div>
</form>