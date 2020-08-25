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

	<div class="alert alert-info">
		<?php echo JText::_('COM_RSEVENTSPRO_WAITING_LIST_INFO'); ?>
	</div>
	<div class="control-group">
		<div class="control-label">
			<label for="name"><?php echo JText::_('COM_RSEVENTSPRO_WAITING_NAME'); ?></label>
		</div>
		<div class="controls">
			<input type="text" name="name" id="name" value="<?php echo JFactory::getUser()->get('name'); ?>" size="40" class="input-large" />
		</div>
	</div>
	
	<div class="control-group">
		<div class="control-label">
			<label for="email"><?php echo JText::_('COM_RSEVENTSPRO_WAITING_EMAIL'); ?></label>
		</div>
		<div class="controls">
			<input type="text" name="email" id="email" value="<?php echo JFactory::getUser()->get('email'); ?>" size="40" class="input-large" />
		</div>
	</div>
	
	<?php if (rseventsproHelper::getConfig('consent','int','1')) { ?>
	<div class="control-group">
		<div class="controls">
			<label class="checkbox inline">
				<input type="checkbox" name="consent" id="consent" value="1" /> <?php echo JText::_('COM_RSEVENTSPRO_CONSENT'); ?>
			</label>
		</div>
	</div>
	<?php } ?>
	
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