<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form method="post" action="index.php?option=com_rsform&amp;task=forms.new.stepfinal" name="adminForm" id="adminForm">
	<fieldset>
		<h3><?php echo JText::_('RSFP_NEW_FORM_STEP_3'); ?></h3>
		<p><?php echo JText::_('RSFP_NEW_FORM_STEP_3_DESC'); ?></p>
		
		<table class="admintable">
			<tr>
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_WHAT_PREDEFINED_FORM'); ?></td>
				<td><?php echo $this->lists['predefinedForms']; ?></td>
			</tr>
		</table>
		
		<button class="btn btn-success" type="submit"><?php echo JText::_('RSFP_FINISH'); ?></button>
	</fieldset>
	
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="task" value="forms.new.stepfinal" />
</form>

<?php JHtml::_('behavior.keepalive'); ?>