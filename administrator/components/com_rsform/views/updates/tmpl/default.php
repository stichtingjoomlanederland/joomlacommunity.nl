<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form action="<?php echo JRoute::_('index.php?option=com_rsform&view=updates'); ?>" method="post" name="adminForm" id="adminForm">	
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<p><?php echo JText::_('RSFP_UPDATE_INSTRUCTIONS'); ?></p>
		<p><a href="https://www.rsjoomla.com/support/documentation/rsform-pro/installing-and-uninstalling/updating-to-a-newer-version.html#joomla" class="btn btn-primary" target="_blank"><?php echo JText::_('RSFP_UPDATE_CLICK_HERE_TO_READ'); ?></a></p>
	</div>
</form>