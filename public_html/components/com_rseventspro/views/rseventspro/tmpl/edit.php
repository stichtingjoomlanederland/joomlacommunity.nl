<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::_('behavior.keepalive');
JText::script('COM_RSEVENTSPRO_NO_OVERBOOKING_TICKETS_CONFIG');
JText::script('COM_RSEVENTSPRO_EVENT_LOCATION_ADD_LOCATION');
JText::script('COM_RSEVENTSPRO_EVENT_DELETE_FILE_CONFIRM');
JText::script('COM_RSEVENTSPRO_CONFIRM_DELETE_TICKET');
JText::script('COM_RSEVENTSPRO_CONFIRM_DELETE_COUPON');
JText::script('COM_RSEVENTSPRO_SAVED');
JText::script('COM_RSEVENTSPRO_NO_RESULTS');
JText::script('COM_RSEVENTSPRO_NO_NAME_ERROR');
JText::script('COM_RSEVENTSPRO_NO_LOCATION_ERROR');
JText::script('COM_RSEVENTSPRO_NO_CATEGORY_ERROR');
JText::script('COM_RSEVENTSPRO_NO_START_ERROR');
JText::script('COM_RSEVENTSPRO_NO_END_ERROR');
JText::script('COM_RSEVENTSPRO_NO_OWNER_ERROR');
JText::script('COM_RSEVENTSPRO_END_BIGGER_ERROR');
JText::script('COM_RSEVENTSPRO_END_REG_BIGGER_ERROR');
JText::script('COM_RSEVENTSPRO_EARLY_FEE_ERROR');
JText::script('COM_RSEVENTSPRO_LATE_FEE_ERROR');
JText::script('COM_RSEVENTSPRO_LATE_FEE_BIGGER_ERROR');
JText::script('COM_RSEVENTSPRO_END_REG_BIGGER_THAN_END_ERROR');
JText::script('COM_RSEVENTSPRO_CONSENT_INFO'); ?>

<script type="text/javascript">
	function rsepro_reset_frame() {
		jQuery('#rsepro-image-loader').css('display','');
		jQuery('#rsepro-image-frame').css('display','none');
		jQuery('#rsepro-image-frame').prop('src','<?php echo JRoute::_('index.php?option=com_rseventspro&layout=upload&tmpl=component&id='.$this->item->id,false); ?>');
		jQuery('#aspectratiolabel').css('display', 'none');
		jQuery('#rsepro-crop-icon-btn').css('display','none');
		jQuery('#rsepro-delete-icon-btn').css('display','none');
	}
	
	<?php if ($this->config->enable_google_maps && (!empty($this->permissions['can_add_locations']) || $this->admin)) { ?>
	var rseproediteventmap;
	jQuery(document).ready(function (){
		rseproediteventmap = jQuery('#rsepro-location-map').rsjoomlamap({
			address: 'location_address',
			coordinates: 'location_coordinates',
			zoom: <?php echo (int) $this->config->google_map_zoom ?>,
			center: '<?php echo $this->config->google_maps_center; ?>',
			markerDraggable: true
		});
	});
	<?php } ?>
	
	function rsepro_scroll(id) {
		if (jQuery(window).width() < 750) {
			window.setTimeout(function() {
				jQuery('html,body').animate({scrollTop: jQuery(id).offset().top},'slow');
			},300);
		}
	}

	jQuery(document).ready(function (){
		jQuery('.rsepro-edit-event > ul > li > a').each(function() {
			if (jQuery(this).attr('data-toggle') == 'tab') {
				jQuery(this).on('click', function() {
					rsepro_scroll(jQuery(this).attr('data-target'));
				});
			}
		});
	});
</script>

<div id="rsepro-edit-container">
	
	<div id="rsepro-errors" class="alert alert-danger" style="display: none;"></div>
	
	<form class="row-fluid tabbable tabs-left rsepro-edit-event" method="post" action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=edit&id='.rseventsproHelper::sef($this->item->id,$this->item->name), false); ?>" name="adminForm" id="adminForm" enctype="multipart/form-data">
		
		<?php echo $this->loadTemplate('navigation'); ?>
		
		<div class="tab-content">
			
			<!-- Start Information tab -->
			<div class="tab-pane <?php if (!$this->tab) echo 'active'; ?>" id="rsepro-edit-tab1">
				<?php echo $this->loadTemplate('info'); ?>
			</div>
			<!-- End Information tab -->

			<!-- Start Categories & Tags tab -->
			<div class="tab-pane" id="rsepro-edit-tab2">
				<?php echo $this->loadTemplate('categories'); ?>
			</div>
			<!-- End Categories & Tags tab -->

			<?php if (!empty($this->permissions['can_upload']) || $this->admin) { ?>
			<!-- Start Event Files tab -->
			<div class="tab-pane" id="rsepro-edit-tab9">
				<?php echo $this->loadTemplate('files'); ?>
			</div>
			<!-- End Event Files tab -->
			<?php } ?>
			
			<!-- Start Contact tab -->
			<div class="tab-pane" id="rsepro-edit-tab10">
				<?php echo $this->loadTemplate('contact'); ?>
			</div>
			<!-- End Contact tab -->
			
			<!-- Start Metadata tab -->
			<div class="tab-pane" id="rsepro-edit-tab11">
				<?php echo $this->loadTemplate('meta'); ?>
			</div>
			<!-- End Metadata tab -->
			
			<?php if (!empty($this->permissions['can_change_options']) || $this->admin) { ?>
			<!-- Start Frontend Options tab -->
			<div class="tab-pane" id="rsepro-edit-tab12">
				<?php echo $this->loadTemplate('frontend'); ?>
			</div>
			<!-- End Frontend Options tab -->
			<?php } ?>
			
			<?php if (rseventsproHelper::isGallery()) { ?>
			<!-- Start Gallery tab -->
			<div class="tab-pane" id="rsepro-edit-tab13">
				<?php echo $this->loadTemplate('gallery'); ?>
			</div>
			<!-- End Gallery tab -->
			<?php } ?>
			
			<!-- Start Registration tab -->
			<div class="tab-pane" id="rsepro-edit-tab3">
				<?php echo $this->loadTemplate('registration'); ?>
			</div>
			<!-- End Registration tab -->
			
			<!-- Start New ticket tab -->
			<div class="tab-pane" id="rsepro-edit-tab4">
				<?php echo $this->loadTemplate('ticket'); ?>
			</div>
			<!-- End New ticket tab -->
			
			<?php echo $this->loadTemplate('tickets'); ?>
			
			<!-- Start Discounts tab -->
			<div class="tab-pane" id="rsepro-edit-tab6">
				<?php echo $this->loadTemplate('discounts'); ?>
			</div>
			<!-- End Discounts tab -->
			
			<!-- Start New coupon tab -->
			<div class="tab-pane" id="rsepro-edit-tab7">
				<?php echo $this->loadTemplate('coupon'); ?>
			</div>
			<!-- End New coupon tab -->
			
			<?php echo $this->loadTemplate('coupons'); ?>
			
			<?php if (empty($this->item->parent) && (!empty($this->permissions['can_repeat_events']) || $this->admin)) { ?>
			<!-- Start Recurring tab -->
			<div class="tab-pane" id="rsepro-edit-tab8">
				<?php echo $this->loadTemplate('recurring'); ?>
			</div>
			<!-- End Recurring tab -->
			<?php } ?>
			
			<!-- Start RSVP tab -->
			<div class="tab-pane" id="rsepro-edit-tabrsvp">
				<?php echo $this->loadTemplate('rsvp'); ?>
			</div>
			<!-- End RSVP tab -->
			
			<?php if ($this->item->completed) { ?>
			<!-- Start Dashboard tab -->
			<div class="tab-pane" id="rsepro-edit-tabd">
				<?php echo $this->loadTemplate('dashboard'); ?>
			</div>
			<!-- End Dashboard tab -->
			<?php } ?>
			
		</div>
		
		<div>
			<?php echo JHTML::_('form.token')."\n"; ?>
			<input type="hidden" name="task" id="task" value="rseventspro.save" />
			<input type="hidden" name="tab" value="<?php echo $this->tab; ?>" id="tab" />
			<input type="hidden" name="jform[form]" value="<?php echo $this->item->form; ?>" id="form"/>
			<input type="hidden" name="jform[id]" id="eventID" value="<?php echo $this->item->id; ?>" />
			<input type="hidden" id="rsepro-root" value="<?php echo JUri::base(); ?>" />
			<input type="hidden" name="time" id="rsepro-time" value="<?php echo $this->config->time_format; ?>" />
			<input type="hidden" name="seconds" id="rsepro-seconds" value="<?php echo $this->config->hideseconds; ?>" />
		</div>
	</form>
	
	<?php echo JHtml::_('bootstrap.renderModal', 'rsepro-edit-event-photo', array('title' => JText::_('COM_RSEVENTSPRO_EVENT_PHOTO'), 'footer' => $this->loadTemplate('modal_icon_footer'), 'bodyHeight' => 70), $this->loadTemplate('modal_icon')); ?>
	<?php echo JHtml::_('bootstrap.renderModal', 'rsepro-edit-event-file', array('title' => JText::_('COM_RSEVENTSPRO_EVENT_EDIT_FILE'), 'footer' => $this->loadTemplate('modal_file_footer'), 'bodyHeight' => 70), $this->loadTemplate('modal_file')); ?>
	
	<?php if (!empty($this->permissions['can_add_speaker']) || $this->admin) { ?>
	<?php echo JHtml::_('bootstrap.renderModal', 'rsepro-add-new-speaker', array('title' => JText::_('COM_RSEVENTSPRO_EVENT_ADD_SPEAKER'), 'footer' => $this->loadTemplate('modal_speaker_footer'), 'bodyHeight' => 70, 'url' => rseventsproHelper::route('index.php?option=com_rseventspro&view=rseventspro&layout=edit&id='.$this->item->id.'&tpl=modal_speaker', false)),''); ?>
	<?php } ?>
	
	<?php if (!empty($this->permissions['can_create_categories']) || $this->admin) { ?>
	<?php echo JHtml::_('bootstrap.renderModal', 'rsepro-add-new-categ', array('title' => JText::_('COM_RSEVENTSPRO_EVENT_ADD_CATEGORY'), 'footer' => $this->loadTemplate('modal_category_footer'), 'bodyHeight' => 70), $this->loadTemplate('modal_category')); ?>
	<?php } ?>
	
	<?php JFactory::getApplication()->triggerEvent('rsepro_eventNewFieldModal', array(array('view' => $this))); ?>
	
</div>