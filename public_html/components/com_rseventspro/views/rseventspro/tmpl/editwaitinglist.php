<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<h1><?php echo JText::_('COM_RSEVENTSPRO_EDIT_WAITINGLIST'); ?></h1>

<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editwaitinglist'); ?>" method="post" name="waitinglistForm" id="waitinglistForm" class="form-horizontal rsepro-horizontal form-validate">
	<?php echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_WAITINGLIST_NAME'), '<input type="text" id="jform_name" name="jform[name]" value="'.$this->escape($this->row->name).'" class="form-control" required />'); ?>
	<?php echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_WAITINGLIST_EMAIL'), '<input type="email" id="jform_email" name="jform[email]" value="'.$this->escape($this->row->email).'" class="form-control" required />'); ?>
	
	<div class="form-actions">
		<button type="submit" class="btn btn-primary"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE'); ?></button>
		<a class="btn btn-danger" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=waitinglist&id='.rseventsproHelper::sef($this->row->ide,$this->row->eventName),false); ?>"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></a>
	</div>
	
	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="task" value="rseventspro.savewaitinglist" />
	<input type="hidden" name="jform[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
</form>