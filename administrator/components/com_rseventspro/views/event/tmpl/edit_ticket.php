<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_NEWTICKET'); ?></legend>

<div class="control-group">
	<div class="control-label">
		<label for="ticket_name"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_NAME'); ?></label>
	</div>
	<div class="controls">
		<input type="text" value="" class="span10" name="ticket_name" id="ticket_name" />
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="ticket_price"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_PRICE'); ?></label>
	</div>
	<div class="controls">
		<input type="text" value="" class="span10" name="ticket_price" id="ticket_price" onkeyup="this.value=this.value.replace(/[^0-9\.\,]/g, '');" />
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="ticket_seats"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_SEATS'); ?></label>
	</div>
	<div class="controls">
		<input type="text" value="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED'); ?>" onfocus="if (this.value=='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>') this.value=''" onblur="if (this.value=='') this.value='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>'" onkeyup="this.value=this.value.replace(/[^0-9]/g, '');" class="span10" name="ticket_seats" id="ticket_seats" />
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="ticket_user_seats"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_USER_SEATS'); ?></label>
	</div>
	<div class="controls">
		<input type="text"  value="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED'); ?>" onfocus="if (this.value=='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>') this.value=''" onblur="if (this.value=='') this.value='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>'" onkeyup="this.value=this.value.replace(/[^0-9]/g, '');" class="span10" name="ticket_user_seats" id="ticket_user_seats" />
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="ticket_groups"><?php echo JText::_('COM_RSEVENTSPRO_TICKET_GROUPS_INFO'); ?></label>
	</div>
	<div class="controls">
		<select class="rsepro-chosen" name="ticket_groups[]" id="ticket_groups" multiple="multiple">
			<?php echo JHtml::_('select.options', $this->eventClass->groups()); ?>
		</select>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="ticket_description"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_DESCRIPTION'); ?></label>
	</div>
	<div class="controls">
		<textarea class="span10" name="ticket_description" id="ticket_description" rows="5"></textarea>
	</div>
</div>

<div class="form-actions">
	<img id="rsepro-add-ticket-loader" src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/loader.gif" alt="" style="display: none;" />
	<button class="btn rsepro-add-ticket" type="button"><span class="fa fa-plus"></span> <?php echo JText::_('COM_RSEVENTSPRO_ADD_TICKET'); ?></button>
	<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
	<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
</div>