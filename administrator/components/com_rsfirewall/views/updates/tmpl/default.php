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
		<p><strong><?php echo JText::_('COM_RSFIREWALL_UPDATE_CHECKING'); ?></strong></p>
		<iframe src="https://www.rsjoomla.com/index.php?option=com_rsmembership&amp;task=checkupdate&amp;sess=<?php echo $this->hash; ?>&amp;revision=<?php echo urlencode($this->version); ?>&amp;version=<?php echo urlencode($this->jversion); ?>&amp;tmpl=component" style="border:0px solid;width:100%;height:22px;" scrolling="no" frameborder="no"></iframe>
		<iframe src="https://www.rsjoomla.com/latest.html?tmpl=component" style="border:0px solid;width:100%;height:380px;" scrolling="no" frameborder="no"></iframe>
	</div>
</form>