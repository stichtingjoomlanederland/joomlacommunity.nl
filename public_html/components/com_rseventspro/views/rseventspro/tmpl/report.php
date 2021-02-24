<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro'); ?>" name="adminForm" id="adminForm">
	<?php echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_REPORT_MESSAGE'), '<textarea name="jform[report]" id="jform_report" class="form-control span12" cols="40" rows="10"></textarea>'); ?>
	<?php echo RSEventsproAdapterGrid::renderField('', '<button type="submit" class="btn btn-primary" onclick="return rsepro_validate_report();">'.JText::_('COM_RSEVENTSPRO_GLOBAL_SEND').'</button> <button type="button" class="btn btn-danger" onclick="'.rseventsproHelper::modalClose(false, true).'">'.JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL').'</button>'); ?>

	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="jform[id]" value="<?php echo JFactory::getApplication()->input->getInt('id',0); ?>" />
	<input type="hidden" name="task" value="rseventspro.report" />
	<input type="hidden" name="tmpl" value="component" />
</form>