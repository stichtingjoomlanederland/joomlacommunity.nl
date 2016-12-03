<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro'); ?>" name="adminForm" id="adminForm">
	<div><?php echo JText::_('COM_RSEVENTSPRO_REPORT_MESSAGE'); ?></div>
	<textarea name="jform[report]" id="jform_report" class="input-large" cols="40" rows="10"></textarea>
	<br /><br />
	<div style="text-align: right;">
		<button type="submit" class="btn btn-primary button" onclick="return rsepro_validate_report();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SEND'); ?></button>
		<button type="button" class="btn button" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
	</div>

	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="jform[id]" value="<?php echo JFactory::getApplication()->input->getInt('id',0); ?>" />
	<input type="hidden" name="task" value="rseventspro.report" />
	<input type="hidden" name="tmpl" value="component" />
</form>