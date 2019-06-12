<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form method="post" action="index.php?option=com_rsform&amp;task=forms.new.steptwo" name="adminForm" id="adminForm">
	<fieldset>
		<h3><?php echo JText::_('RSFP_NEW_FORM_STEP_1'); ?></h3>
		<p><?php echo JText::_('RSFP_NEW_FORM_STEP_1_DESC'); ?></p>
		<button class="btn pull-left btn-primary" type="submit"><?php echo JText::_('Next'); ?></button>
		<button class="btn pull-left" type="button" onclick="Joomla.submitbutton('forms.new.stepfinal');"><?php echo JText::_('RSFP_SKIP_WIZARD'); ?></button>
		<button class="btn pull-left btn-danger" type="button" onclick="Joomla.submitbutton('forms.cancel');"><?php echo JText::_('Cancel'); ?></button>
	</fieldset>
	
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="task" value="forms.new.steptwo" />
</form>

<?php JHtml::_('behavior.keepalive'); ?>