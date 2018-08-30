<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php?option=com_rsform" method="post" name="adminForm" id="adminForm">
	<p><?php echo JText::_('RSFP_COPY_COMPONENTS_WHERE'); ?></p>
	<?php echo $this->lists['forms']; ?>
	<p><button class="btn btn-primary" type="submit"><?php echo JText::_('RSFP_DO_COPY');?></button></p>
	
	<input type="hidden" name="formId" value="<?php echo $this->formId; ?>" />
	<input type="hidden" name="task" value="components.copy.process" />
	<?php foreach ($this->cids as $cid) { ?>
	<input type="hidden" name="cid[]" value="<?php echo $cid; ?>" />
	<?php } ?>
</form>