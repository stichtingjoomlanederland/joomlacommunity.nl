<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<form action="<?php echo JRoute::_('index.php?option=com_rsfirewall&view=updates'); ?>" method="post" name="adminForm" id="adminForm">	
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<p><?php echo JText::_('COM_RSFIREWALL_UPDATE_INSTRUCTIONS'); ?></p>
		<p><a href="https://www.rsjoomla.com/support/documentation/rsfirewall-user-guide/installing-and-uninstalling/updating-rsfirewall.html#joomla" class="btn btn-primary" target="_blank"><?php echo JText::_('COM_RSFIREWALL_UPDATE_CLICK_HERE_TO_READ'); ?></a></p>
	</div>
</form>