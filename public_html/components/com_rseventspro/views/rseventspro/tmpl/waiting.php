<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
JText::script('COM_RSEVENTSPRO_WAITING_MESSAGE_NAME');
JText::script('COM_RSEVENTSPRO_WAITING_MESSAGE_EMAIL');
JText::script('COM_RSEVENTSPRO_WAITING_INVALID_EMAIL_ADDRESS');
JText::script('COM_RSEVENTSPRO_CONSENT_INFO'); ?>

<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=waiting'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal" autocomplete="off">

	<div class="alert alert-info"><?php echo JText::_('COM_RSEVENTSPRO_WAITING_LIST_INFO'); ?></div>
	
	<div class="form-horizontal rsepro-horizontal">
		<?php echo RSEventsproAdapterGrid::renderField('<label for="name">'.JText::_('COM_RSEVENTSPRO_WAITING_NAME').'</label>','<input type="text" name="name" id="name" value="'.JFactory::getUser()->get('name').'" size="40" class="form-control" />'); ?>
		<?php echo RSEventsproAdapterGrid::renderField('<label for="name">'.JText::_('COM_RSEVENTSPRO_WAITING_EMAIL').'</label>','<input type="text" name="email" id="email" value="'.JFactory::getUser()->get('email').'" size="40" class="form-control" />'); ?>
		<?php if ($this->config->consent) { ?>
		<div class="control-group">
			<div class="controls">
				<label class="checkbox inline">
					<input type="checkbox" name="consent" id="consent" value="1" /> <?php echo JText::_('COM_RSEVENTSPRO_CONSENT'); ?>
				</label>
			</div>
		</div>
		<?php } ?>
	</div>

	<div class="control-group">
		<div class="controls">
			<button type="submit" class="button btn btn-primary" onclick="return rsepro_validate_waitinglist();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE'); ?></button> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?> 
			<?php echo rseventsproHelper::redirect(false,JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'),rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id))); ?>
		</div>
	</div>
	
	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="task" value="rseventspro.waiting" />
	<input type="hidden" name="id" value="<?php echo $this->event->id; ?>" />
	<input type="hidden" name="tmpl" value="component" />
</form>