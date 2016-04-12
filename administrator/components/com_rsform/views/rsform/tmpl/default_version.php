<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<div class="dashboard-container">
	<div class="dashboard-info">
		<img src="components/com_rsform/assets/images/rsformpro.png" align="middle" alt="RSForm! Pro" />
		<table class="dashboard-table">
			<tr>
				<td nowrap="nowrap"><strong><?php echo JText::_('COM_RSFORM_PRODUCT_VERSION') ?>: </strong></td>
				<td nowrap="nowrap">RSForm! Pro <?php echo $this->version; ?></td>
			</tr>
			<tr>
				<td nowrap="nowrap"><strong><?php echo JText::_('COM_RSFORM_COPYRIGHT_NAME') ?>: </strong></td>
				<td nowrap="nowrap">&copy; 2007 - <?php echo gmdate('Y'); ?> <a href="http://www.rsjoomla.com" target="_blank">RSJoomla!</a></td>
			</tr>
			<tr>
				<td nowrap="nowrap"><strong><?php echo JText::_('COM_RSFORM_LICENSE_NAME') ?>: </strong></td>
				<td nowrap="nowrap"><a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GNU/GPL</a> Commercial</a></td>
			</tr>
			<tr>
				<td nowrap="nowrap"><strong><?php echo JText::_('COM_RSFORM_UPDATE_CODE') ?>: </strong></td>
				<?php if (strlen($this->code) == 20) { ?>
				<td nowrap="nowrap" class="correct-code"><?php echo $this->escape($this->code); ?></td>
				<?php } elseif ($this->code) { ?>
				<td nowrap="nowrap" class="incorrect-code"><?php echo $this->escape($this->code); ?>
				<br />
				<strong><a href="http://www.rsjoomla.com/support/documentation/view-article/767-where-do-i-find-my-license-code-.html" target="_blank"><?php echo JText::_('COM_RSFORM_WHERE_DO_I_FIND_THIS'); ?></a></strong>
				</td>
				<?php } else { ?>
				<td nowrap="nowrap" class="missing-code"><a href="<?php echo JRoute::_('index.php?option=com_rsform&view=configuration'); ?>"><?php echo JText::_('COM_RSFORM_PLEASE_ENTER_YOUR_CODE_IN_THE_CONFIGURATION'); ?></a>
				<br />
				<strong><a href="http://www.rsjoomla.com/support/documentation/view-article/767-where-do-i-find-my-license-code-.html" target="_blank"><?php echo JText::_('COM_RSFORM_WHERE_DO_I_FIND_THIS'); ?></a></strong>
				</td>
				<?php } ?>
			</tr>
		</table>
	</div>
</div>