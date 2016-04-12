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
JText::script('COM_RSEVENTSPRO_LATE_FEE_BIGGER_ERROR'); ?>

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
	
	<form class="row-fluid tabbable tabs-left rsepro-edit-event" method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro&layout=edit&id='.rseventsproHelper::sef($this->item->id,$this->item->name), false); ?>" name="adminForm" id="adminForm" enctype="multipart/form-data">
		
		<?php echo $this->loadTemplate('navigation'); ?>
		
		<div class="tab-content">
			
			<!-- Start Information tab -->
			<div class="tab-pane active" id="rsepro-edit-tab1">
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
			
			<?php if (rseventsproHelper::pdf()) { ?>
			<!-- Start Ticket PDF tab -->
			<div class="tab-pane" id="rsepro-edit-tab5">
				<?php echo $this->loadTemplate('ticketpdf'); ?>
			</div>
			<!-- End Ticket PDF tab -->
			<?php } ?>
			
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
	
	<div id="rsepro-edit-event-photo" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3><?php echo JText::_('COM_RSEVENTSPRO_EVENT_PHOTO'); ?></h3>
		</div>
		<div class="modal-body">
			<div id="rsepro-image-loader" class="center" style="display:none;"><img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/load.gif" alt="" /></div>
			<iframe id="rsepro-image-frame" src="" width="100%"></iframe>
		</div>
		<div class="modal-footer">
			<label for="aspectratio" class="pull-left" style="display: none" id="aspectratiolabel">
				<input type="checkbox" id="aspectratio" name="aspectratio" value="1" style="margin:0;" /> <?php echo JText::_('COM_RSEVENTSPRO_FREE_ASPECT_RATIO'); ?>
			</label>
			<button class="btn btn-primary" type="button" id="rsepro-crop-icon-btn" style="display: none;"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CROP'); ?></button>
			<button class="btn btn-danger" type="button" id="rsepro-delete-icon-btn" style="display: none;"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE'); ?></button>
			<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
		</div>
	</div>
	
	<?php if (!empty($this->permissions['can_create_categories']) || $this->admin) { ?>
	<div id="rsepro-add-new-categ" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ADD_CATEGORY'); ?></h3>
		</div>
		<div class="modal-body form-horizontal">
			<div class="control-group">
				<div class="control-label">
					<label for="rsepro-new-category"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_CATEGORY_NAME'); ?></label>
				</div>
				<div class="controls">
					<input type="text" id="rsepro-new-category" name="category" placeholder="<?php echo JText::_('COM_RSEVENTSPRO_EVENT_ENTER_CATEGORY_NAME'); ?>" />
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label for="category-parent"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_CHOOSE_PARENT'); ?></label>
				</div>
				<div class="controls">
					<select id="category-parent" name="parent">
						<?php echo JHtml::_('select.options', JHtml::_('category.categories','com_rseventspro')); ?>
					</select>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<img id="rsepro-add-category-loader" src="<?php echo JUri::root(); ?>components/com_rseventspro/assets/images/loader.gif" alt="" class="pull-left" style="display: none;" />
			<button class="btn btn-primary rsepro-event-add-category"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ADD_CATEGORY_ADD'); ?></button>
			<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
		</div>
	</div>
	<?php } ?>
	
	<div id="rsepro-edit-event-file" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3><?php echo JText::_('COM_RSEVENTSPRO_EVENT_EDIT_FILE'); ?></h3>
		</div>
		<div class="modal-body form-horizontal">
			<div class="control-group">
				<div class="control-label">
					<label for="rsepro-file-name"><?php echo JText::_('COM_RSEVENTSPRO_FILE_NAME'); ?></label>
				</div>
				<div class="controls">
					<input type="text" id="rsepro-file-name" name="file_name" class="input-xlarge" />
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label><?php echo JText::_('COM_RSEVENTSPRO_FILE_PERMISSIONS'); ?></label>
				</div>
				<div class="controls">
					<legend><?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_ALL'); ?></legend>
					<label class="checkbox">
						<input id="fp0" name="fp0" type="checkbox" value="1" />
						<?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_BEFORE'); ?>
					</label>
					<label class="checkbox">
						<input id="fp1" name="fp1" type="checkbox" value="1" />
						<?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_DURING'); ?>
					</label>
					<label class="checkbox">
						<input id="fp2" name="fp2" type="checkbox" value="1" />
						<?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_AFTER'); ?>
					</label>
					
					<legend><?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_REGISTERED'); ?></legend>
					<label class="checkbox">
						<input id="fp3" name="fp3" type="checkbox" value="1" />
						<?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_BEFORE'); ?>
					</label>
					<label class="checkbox">
						<input id="fp4" name="fp4" type="checkbox" value="1" />
						<?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_DURING'); ?>
					</label>
					<label class="checkbox">
						<input id="fp5" name="fp5" type="checkbox" value="1" />
						<?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_AFTER'); ?>
					</label>
				</div>
			</div>
			<input type="hidden" name="rsepro-file-id" id="rsepro-file-id" value="" />
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" type="button" id="rsepro-save-file"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE'); ?></button>
			<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
		</div>
	</div>
	
</div>