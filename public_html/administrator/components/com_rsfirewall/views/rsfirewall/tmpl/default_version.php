<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<div class="dashboard-container">
	<div class="dashboard-info">
		<img src="components/com_rsfirewall/assets/images/rsfirewall.png" align="middle" alt="RSFirewall!" />
		<table class="dashboard-table">
			<tr>
				<td nowrap="nowrap"><strong><?php echo JText::_('COM_RSFIREWALL_PRODUCT_VERSION') ?>: </strong></td>
				<td nowrap="nowrap">RSFirewall! <?php echo $this->version; ?></td>
			</tr>
			<tr>
				<td nowrap="nowrap"><strong><?php echo JText::_('COM_RSFIREWALL_COPYRIGHT_NAME') ?>: </strong></td>
				<td nowrap="nowrap">&copy; 2009 - 2016 <a href="https://www.rsjoomla.com" target="_blank">RSJoomla!</a></td>
			</tr>
			<tr>
				<td nowrap="nowrap"><strong><?php echo JText::_('COM_RSFIREWALL_LICENSE_NAME') ?>: </strong></td>
				<td nowrap="nowrap"><a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GNU/GPL</a> Commercial</a></td>
			</tr>
			<tr>
				<td nowrap="nowrap"><strong><?php echo JText::_('COM_RSFIREWALL_UPDATE_CODE') ?>: </strong></td>
				<?php if (strlen($this->code) == 20) { ?>
				<td nowrap="nowrap" class="correct-code"><?php echo $this->escape($this->code); ?></td>
				<?php } elseif ($this->code) { ?>
				<td nowrap="nowrap" class="incorrect-code"><?php echo $this->escape($this->code); ?></td>
				<?php } else { ?>
				<td nowrap="nowrap" class="missing-code"><a href="<?php echo JRoute::_('index.php?option=com_rsfirewall&view=configuration'); ?>"><?php echo JText::_('COM_RSFIREWALL_PLEASE_ENTER_YOUR_CODE_IN_THE_CONFIGURATION'); ?></a></td>
				<?php } ?>
			</tr>
		</table>
	</div>
</div>