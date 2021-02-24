<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<ul id="rsepro-edit-menu" class="<?php echo RSEventsproAdapterGrid::nav(true); ?>">
	<li class="nav-item"><a rel="rs_icon" href="javascript:void(0);" class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> nav-link" <?php if ($this->config->modaltype == 1) { ?>data-toggle="modal" data-bs-toggle="modal" data-target="#rsepro-edit-event-photo" data-bs-target="#rsepro-edit-event-photo" onclick="rsepro_reset_frame();"<?php } ?>><?php echo $this->loadTemplate('icon'); ?></a></li>
	
	<?php if ($this->item->completed) { ?>
	<li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-target="#rsepro-edit-tabd" data-bs-target="#rsepro-edit-tabd" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_DASHBOARD'); ?> <span class="fa fa-tachometer"></span></a></li>
	<?php } ?>
	
	<li class="nav-item <?php if (!$this->tab) echo 'active'; ?>"><a class="nav-link<?php if (!$this->tab) echo ' active'; ?>" href="javascript:void(0);" data-target="#rsepro-edit-tab1" data-bs-target="#rsepro-edit-tab1" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_CREATE'); ?> <span class="fa fa-flag"></span></a></li>
	
	<li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-target="#rsepro-edit-tab2" data-bs-target="#rsepro-edit-tab2" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_CATEGORIES'); ?> <span class="fa fa-tag"></span></a></li>
	
	<li class="nav-item rsepro-hide"<?php echo $this->item->rsvp ? ' style="display:block;"' : ''; ?>><a class="nav-link" href="javascript:void(0);" data-target="#rsepro-edit-tabrsvp" data-bs-target="#rsepro-edit-tabrsvp" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_RSVP'); ?>  <span class="fa fa-calendar"></span></a></li>
	
	<li class="nav-item rsepro-hide"<?php echo $this->item->registration ? ' style="display:block;"' : ''; ?>><a class="nav-link" href="javascript:void(0);" data-target="#rsepro-edit-tab3" data-bs-target="#rsepro-edit-tab3" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_REGISTRATION'); ?>  <span class="fa fa-calendar"></span></a></li>
	
	<li class="nav-item rsepro-hide"<?php echo $this->item->registration ? ' style="display:block;"' : ''; ?>><a class="nav-link" href="javascript:void(0);" data-target="#rsepro-edit-tab4" data-bs-target="#rsepro-edit-tab4" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_NEWTICKET'); ?> <span class="fa fa-plus-circle"></span></a></li>
	
	<?php if ($this->tickets) { ?>
	<?php foreach ($this->tickets as $ticket) { ?>
	<li class="nav-item rsepro-ticket rsepro-hide"<?php echo $this->item->registration ? ' style="display:block;"' : ''; ?> id="ticket_<?php echo $ticket->id; ?>"><a class="nav-link" href="javascript:void(0);" data-target="#rsepro-edit-ticket<?php echo $ticket->id; ?>" data-bs-target="#rsepro-edit-ticket<?php echo $ticket->id; ?>" data-toggle="tab" data-bs-toggle="tab"><?php echo $ticket->name; ?> <span class="fa fa-ticket"></span></a></li>
	<?php }} ?>
	
	<?php JFactory::getApplication()->triggerEvent('onrsepro_addMenuOptionRegistration'); ?>
	
	<li class="nav-item rsepro-hide"<?php echo $this->item->discounts && $this->item->registration ? ' style="display:block;"' : ''; ?>><a class="nav-link" href="javascript:void(0);" data-target="#rsepro-edit-tab6" data-bs-target="#rsepro-edit-tab6" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_DISCOUNTS'); ?> <span class="fa fa-scissors"></span></a></li>
	
	<li class="nav-item rsepro-hide"<?php echo $this->item->discounts && $this->item->registration ? ' style="display:block;"' : ''; ?>><a class="nav-link" href="javascript:void(0);" data-target="#rsepro-edit-tab7" data-bs-target="#rsepro-edit-tab7" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_NEWCOUPON'); ?> <span class="fa fa-plus-circle"></span></a></li>
	
	<?php if ($this->coupons) { ?>
	<?php foreach ($this->coupons as $coupon) { ?>
	<li class="nav-item rsepro-hide"<?php echo $this->item->discounts && $this->item->registration ? ' style="display:block;"' : ''; ?>><a class="nav-link" href="javascript:void(0);" data-target="#rsepro-edit-coupon<?php echo $coupon->id; ?>" data-bs-target="#rsepro-edit-coupon<?php echo $coupon->id; ?>" data-toggle="tab" data-bs-toggle="tab"><?php echo $coupon->name; ?> <span class="fa fa-money"></span></a></li>
	<?php }} ?>
	
	<?php if (empty($this->item->parent) && (!empty($this->permissions['can_repeat_events']) || $this->admin)) { ?>
	<li class="nav-item rsepro-hide"<?php echo $this->item->recurring ? ' style="display:block;"' : ''; ?>><a class="nav-link" href="javascript:void(0);" data-target="#rsepro-edit-tab8" data-bs-target="#rsepro-edit-tab8" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_RECURRING'); ?> <span class="fa fa-repeat"></span></a></li>
	<?php } ?>
	
	<?php if (!empty($this->permissions['can_upload']) || $this->admin) { ?>
	<li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-target="#rsepro-edit-tab9" data-bs-target="#rsepro-edit-tab9" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_FILES'); ?> <span class="fa fa-file-o"></span></a></li>
	<?php } ?>
	
	<li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-target="#rsepro-edit-tab10" data-bs-target="#rsepro-edit-tab10" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_CONTACT'); ?> <span class="fa fa-user"></span></a></li>
	
	<li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-target="#rsepro-edit-tab11" data-bs-target="#rsepro-edit-tab11" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_META'); ?> <span class="fa fa-list"></span></a></li>
	
	<?php if (!empty($this->permissions['can_change_options']) || $this->admin) { ?>
	<li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-target="#rsepro-edit-tab12" data-bs-target="#rsepro-edit-tab12" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_FRONTEND'); ?> <span class="fa fa-home"></span></a></li>
	<?php } ?>
	
	<?php if (rseventsproHelper::isGallery()) { ?>
	<li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-target="#rsepro-edit-tab13" data-bs-target="#rsepro-edit-tab13" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_GALLERY'); ?> <span class="fa fa-picture-o"></span></a></li>
	<?php } ?>
	
	<?php if (rseventsproHelper::pdf('1.18')) { ?>
	<li class="nav-item rsepro-hide"<?php echo $this->item->registration ? ' style="display:block;"' : ''; ?>><a class="nav-link" href="javascript:void(0);" data-target="#rsepro-edit-invoice" data-bs-target="#rsepro-edit-invoice" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_INVOICE'); ?> <span class="fa fa-file-pdf-o"></span></a></li>
	<?php } ?>
	
	<?php JFactory::getApplication()->triggerEvent('onrsepro_addMenuOption'); ?>
</ul>