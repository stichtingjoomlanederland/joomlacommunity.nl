jQuery(document).ready(function (){
	RSEventsPro.Event.init();
});

// Check for RSEventsPro variable
if (typeof RSEventsPro == 'undefined') {
	var RSEventsPro = {};
}

RSEventsPro.Event = {
	
	root: function () {
		return jQuery('#rsepro-root').val();
	},
	
	// Initialize the buttons
	init: function () {
		// Set the active tab
		jQuery('.rsepro-edit-event > ul > li > a').each(function(i, el) {
			if (i == jQuery('#tab').val() && i != 0) {
				el.click();
			}
		});
		
		// Update the tab
		jQuery('.rsepro-edit-event > ul > li > a').on('click', function() {
			var tabindex = jQuery(this).parent().index();
			
			if (tabindex != 0) {
				jQuery('#tab').val(tabindex);
			}
		});
		
		
		/*******************/
		/**** Recurring ****/
		/*******************/
		
		// Enable/Disable recurring
		jQuery('#jform_recurring').on('click', function() {
			RSEventsPro.Event.enableRecurring();
		});
		
		// Show options depending on the repeat type (daily, weekly, monthly, yearly)
		jQuery('#jform_repeat_type').on('change', function() {
			RSEventsPro.Event.repeatType();
		});
		
		jQuery('#jform_repeat_on_type').on('change', function() {
			RSEventsPro.Event.repeatOnType();
		});
		
		// Remove the 'repeat also on' dates
		jQuery('#rsepro-remove-repeat-dates').on('click', function() {
			RSEventsPro.Event.removeRecurringDates();
		});
		
		// Remove the 'exclude dates'
		jQuery('#rsepro-remove-exclude-dates').on('click', function() {
			RSEventsPro.Event.removeExcludeDates();
		});
		
		// Apply to all
		jQuery('#apply_changes').on('click', function() {
			RSEventsPro.Event.applyToAll();
		});
		
		jQuery('#rsepro-recurring-events-show').on('click', function() {
			var recEl = this;
			jQuery('#rsepro-recurring-events').slideToggle(400, function() {
				if (jQuery(recEl).find('i').hasClass('fa-arrow-down')) {
					jQuery(recEl).find('i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
				} else {
					jQuery(recEl).find('i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
				}
			});
		});
		
		jQuery('#jform_repeatalso, #jform_excludedates, #jform_repeat_interval, #jform_repeat_end, #repeat_days, #jform_repeat_on_day, #jform_repeat_on_type, #jform_repeat_on_day_order, #jform_repeat_on_day_type').on('change', function() {
			RSEventsPro.Event.repeats();
		});
		
		
		/*********************/
		/**** Registraion ****/
		/*********************/
		
		// Enable/Disable registraion
		jQuery('#jform_registration').on('click', function() {
			RSEventsPro.Event.enableRegistraion();
		});
		
		// Add ticket
		jQuery('.rsepro-add-ticket').on('click', function() {
			RSEventsPro.Event.addTicket();
		});
		
		// Remove tickets
		jQuery('.rsepro-remove-ticket').on('click', function(){
			RSEventsPro.Event.removeTicket(jQuery(this).data('id'));
		});
		
		// Overbooking
		jQuery('#jform_overbooking').on('change', function() {
			RSEventsPro.Event.overbooking();
		});
		
		// Max tickets
		jQuery('#jform_max_tickets').on('change', function() {
			RSEventsPro.Event.maxticketsamount();
		});
		
		// Tickets configuration
		jQuery('#jform_ticketsconfig').on('change', function() {
			RSEventsPro.Event.ticketsconfig();
		});
		
		
		
		/*******************/
		/**** Discounts ****/
		/*******************/
		
		// Enable/Disable discounts & coupons
		jQuery('#jform_discounts').on('click', function() {
			RSEventsPro.Event.enableCoupons();
		});
		
		// Add coupon
		jQuery('.rsepro-event-add-coupon').on('click', function() {
			RSEventsPro.Event.addCoupon();
		});
		
		// Remove coupons
		jQuery('.rsepro-event-remove-coupon').on('click', function(){
			RSEventsPro.Event.removeCoupon(jQuery(this).data('id'));
		});
		
		
		
		/***************/
		/**** Files ****/
		/***************/
		
		// Add more files
		jQuery('.rsepro-event-add-files').on('click', function() {
			RSEventsPro.Event.addFile();
		});
		
		
		
		/***************/
		/**** Event ****/
		/***************/
		
		// Save the event
		jQuery('.rsepro-event-update').on('click', function() {
			RSEventsPro.Event.save();
		});
		
		// Cancel the editing of an event
		jQuery('.rsepro-event-cancel').on('click', function() {
			RSEventsPro.Event.cancel();
		});
		
		// Make an event all day
		jQuery('#jform_allday').on('change', function() {
			RSEventsPro.Event.allDay();
		});
		
		// Add a new category
		jQuery('.rsepro-event-add-category').on('click', function() {
			RSEventsPro.Event.addCategory();
		});
		
		var locationTimeOut;
		// Select location
		jQuery('#rsepro-location').on('keyup', function() {
			clearTimeout(locationTimeOut);
			what = this;
			
			locationTimeOut = setTimeout(function() {
				RSEventsPro.Event.selectLocation(what);
			}, 1000);
		});
		
		// Cancel the add of a new location
		jQuery('#rsepro-cancel-location').on('click', function() {
			RSEventsPro.Event.cancelLocation();
		});
		
		// Save the new location
		jQuery('#rsepro-save-location').on('click', function() {
			RSEventsPro.Event.saveLocation();
		});
		
		// Remove file
		jQuery('.rsepro-remove-file').on('click', function() {
			RSEventsPro.Event.removeFile(jQuery(this).parent().prop('id'));
		});
		
		// Edit file
		jQuery('.rsepro-edit-file').on('click', function() {
			RSEventsPro.Event.editFile(jQuery(this).parent().prop('id'));
		});
		
		// Save file
		jQuery('#rsepro-save-file').on('click', function() {
			RSEventsPro.Event.saveFile();
		});
		
		// Generate coupon codes
		jQuery('.rsepro-coupon-generate').on('click', function() {
			RSEventsPro.Event.generateCoupons(jQuery(this).data('id'));
		});
		
		// Initialize functions
		RSEventsPro.Event.allDay();
		RSEventsPro.Event.overbooking();
		RSEventsPro.Event.maxticketsamount();
		RSEventsPro.Event.ticketsconfig();
		RSEventsPro.Event.repeatType();
		RSEventsPro.Event.repeatOnType();
		RSEventsPro.Event.applyToAll();
		RSEventsPro.Event.orderTickets();
	},
	
	// All day event
	allDay: function() {
		if (jQuery('#jform_allday').is(':checked')) {
			jQuery('#jform_start').val(jQuery('#jform_start').val().split(' ')[0]);
			jQuery('#jform_end').val('');
			jQuery('#rsepro-end-date-id').css('display','none');
			
			if (jQuery('#rsepro-time').val() == 1) {
				if (jQuery('#jform_start_dummy').length)
					jQuery('#jform_start_dummy').val(jQuery('#jform_start_dummy').val().split(' ')[0]);
			}
			
			jQuery('#jform_start_datetimepicker').datetimepicker('destroy');
			
			if (jQuery('#rsepro-time').val() == 1) {
				jQuery('#jform_start_datetimepicker').datetimepicker({
					pickTime: false,
					linkField: "jform_start",
					format: 'yyyy-MM-dd'
				});
			} else {
				jQuery('#jform_start_datetimepicker').datetimepicker({
					pickTime: false,
					format: 'yyyy-MM-dd'
				});
			}
		} else {
			jQuery('#rsepro-end-date-id').css('display','');
			jQuery('#jform_start_datetimepicker').datetimepicker('destroy');
			jQuery('#jform_end_datetimepicker').datetimepicker('destroy');
			if (jQuery('#rsepro-time').val() == 1) {
				if (jQuery('#rsepro-seconds').val() == 1) {
					jQuery("#jform_start_datetimepicker").datetimepicker({
						pickTime: true,
						pickSeconds: false,
						pick12HourFormat: true,
						linkField: "jform_start",
						format: "yyyy-MM-dd HH:mm PP"
					});
					jQuery("#jform_end_datetimepicker").datetimepicker({
						pickTime: true,
						pickSeconds: false,
						pick12HourFormat: true,
						linkField: "jform_end",
						format: "yyyy-MM-dd HH:mm PP"
					});
				} else {
					jQuery("#jform_start_datetimepicker").datetimepicker({
						pickTime: true,
						pick12HourFormat: true,
						linkField: "jform_start",
						format: "yyyy-MM-dd HH:mm:ss PP"
					});
					jQuery("#jform_end_datetimepicker").datetimepicker({
						pickTime: true,
						pick12HourFormat: true,
						linkField: "jform_end",
						format: "yyyy-MM-dd HH:mm:ss PP"
					});
				}
			} else {
				if (jQuery('#rsepro-seconds').val() == 1) {
					jQuery('#jform_start_datetimepicker').datetimepicker({
						pickTime: true,
						pickSeconds: false,
						format: 'yyyy-MM-dd hh:mm'
					});
					jQuery('#jform_end_datetimepicker').datetimepicker({
						pickTime: true,
						pickSeconds: false,
						format: 'yyyy-MM-dd hh:mm'
					});
				} else {
					jQuery('#jform_start_datetimepicker').datetimepicker({
						pickTime: true,
						format: 'yyyy-MM-dd hh:mm:ss'
					});
					jQuery('#jform_end_datetimepicker').datetimepicker({
						pickTime: true,
						format: 'yyyy-MM-dd hh:mm:ss'
					});
				}
			}
		}
	},
	
	// Overbooking amount
	overbooking: function() {
		if (jQuery('#jform_ticketsconfig').is(':checked') && jQuery('#jform_overbooking').is(':checked')) {
			jQuery('#jform_overbooking').prop('checked',false);
			alert(Joomla.JText._('COM_RSEVENTSPRO_NO_OVERBOOKING_TICKETS_CONFIG'));
			return;
		}
		
		if (jQuery('#jform_overbooking').is(':checked')) {
			jQuery('#rsepro-overbooking-amount').css('display','');
			jQuery('#jform_max_tickets').prop('disabled',true);
			jQuery('#jform_max_tickets').prop('checked',false);
			jQuery('#jform_max_tickets').parent().parent().addClass('muted');
		} else {
			jQuery('#rsepro-overbooking-amount').css('display','none');
			jQuery('#jform_max_tickets').prop('disabled',false);
			jQuery('#jform_max_tickets').parent().parent().removeClass('muted');
		}
	},
	
	// Max tickets amount
	maxticketsamount: function() {
		if (jQuery('#jform_max_tickets').is(':checked')) {
			jQuery('#rsepro-max-tickets-amount').css('display','');
			jQuery('#jform_overbooking').prop('disabled',true);
			jQuery('#jform_overbooking').prop('checked',false);
			jQuery('#jform_overbooking').parent().parent().addClass('muted');
		} else {
			jQuery('#rsepro-max-tickets-amount').css('display','none');
			jQuery('#jform_overbooking').prop('disabled',false);
			jQuery('#jform_overbooking').parent().parent().removeClass('muted');
		}
	},
	
	// Tickets configuration
	ticketsconfig: function() {
		if (jQuery('#jform_overbooking').is(':checked') && jQuery('#jform_ticketsconfig').is(':checked')) {
			jQuery('#jform_ticketsconfig').prop('checked',false);
			alert(Joomla.JText._('COM_RSEVENTSPRO_NO_OVERBOOKING_TICKETS_CONFIG'));
			return;
		}
		
		if (jQuery('#jform_ticketsconfig').is(':checked')) {
			jQuery('#rsepro-tickets-configuration').css('display','');
		} else {
			jQuery('#rsepro-tickets-configuration').css('display','none');
		}
	},
	
	// Add files
	addFile: function() {
		var container	= jQuery('#rsepro-event-files');
		var div1		= jQuery('<div>', { 'class' : 'control-group'});
		var div2		= jQuery('<div>', { 'class' : 'controls'});
		var input		= jQuery('<input>', { 'class' : 'input-large', 'name' : 'files[]', 'type' : 'file'});
		var a			= jQuery('<a>', { 'href' : 'javascript:void(0)'}).on('click', function() { RSEventsPro.Event.removeInputFile(this); });
		var i			= jQuery('<i>', { 'class' : 'fa fa-times'});
		
		div2.append(input);
		a.html(i);
		div2.append(a);
		div1.append(div2);
		container.append(div1);
	},
	
	// Remove the input files created
	removeInputFile: function(what) {
		jQuery(what).parent().parent().remove();
	},
	
	// Enable/Disable registraion
	enableRegistraion: function() {
		jQuery('ul li a[data-target="#rsepro-edit-tab3"]').parent().slideToggle();
		jQuery('ul li a[data-target="#rsepro-edit-tab4"]').parent().slideToggle();
		jQuery('ul li a[data-target="#rsepro-edit-tab5"]').parent().slideToggle();
		jQuery('ul li a[data-target^="#rsepro-edit-ticket"]').parent().slideToggle();
		
		if (jQuery('#jform_registration').is(':checked')) {
			if (jQuery('#jform_discounts').is(':checked')) {
				jQuery('ul li a[data-target="#rsepro-edit-tab6"]').parent().slideDown();
				jQuery('ul li a[data-target="#rsepro-edit-tab7"]').parent().slideDown();
				jQuery('ul li a[data-target^="#rsepro-edit-coupon"]').parent().slideDown();
			} else {
				jQuery('ul li a[data-target="#rsepro-edit-tab6"]').parent().slideUp();
				jQuery('ul li a[data-target="#rsepro-edit-tab7"]').parent().slideUp();
				jQuery('ul li a[data-target^="#rsepro-edit-coupon"]').parent().slideUp();
			}
		} else {
			jQuery('ul li a[data-target="#rsepro-edit-tab6"]').parent().slideUp();
			jQuery('ul li a[data-target="#rsepro-edit-tab7"]').parent().slideUp();
			jQuery('ul li a[data-target^="#rsepro-edit-coupon"]').parent().slideUp();
		}
	},
	
	// Enable/Disable coupons
	enableCoupons: function() {
		jQuery('ul li a[data-target="#rsepro-edit-tab6"]').parent().slideToggle();
		jQuery('ul li a[data-target="#rsepro-edit-tab7"]').parent().slideToggle();
		jQuery('ul li a[data-target^="#rsepro-edit-coupon"]').parent().slideToggle();
	},
	
	// Add event on the recurring checkbox
	enableRecurring: function() {
		jQuery('ul li a[data-target="#rsepro-edit-tab8"]').parent().slideToggle();
		
		if (jQuery('#jform_recurring').is(':checked')) {
			jQuery('#jform_recurring').parent().find('small').css('display','');
		} else {
			jQuery('#jform_recurring').parent().find('small').css('display','none');
		}
	},
	
	// Calculate the number of repeats the event will output
	repeats: function () {
		if (jQuery('#jform_recurring').is(':checked')) {
			if (((jQuery('#jform_repeat_interval').val() != '' || jQuery('#jform_repeat_interval').val() != 0) && jQuery('#jform_repeat_end').val() != '') || jQuery('#jform_repeatalso option').length) {
				
				var totalRepeats = 0;
				var params = new Array();
				
				params.push('task=repeats');
				params.push('interval=' + jQuery('#jform_repeat_interval').val());
				params.push('type=' + jQuery('#jform_repeat_type').val());
				params.push('start=' + jQuery('#jform_start').val());
				params.push('end=' + jQuery('#jform_repeat_end').val());
				params.push('repeat_on_type=' + jQuery('#jform_repeat_on_type').val());
				params.push('repeat_on_day=' + jQuery('#jform_repeat_on_day').val());
				params.push('repeat_on_day_order=' + jQuery('#jform_repeat_on_day_order').val());
				params.push('repeat_on_day_type=' + jQuery('#jform_repeat_on_day_type').val());
				
				jQuery('#repeat_days option:selected').each(function(){
					params.push('days[]=' + jQuery(this).val());
				});
				
				// Add repeat also dates
				if (jQuery('#jform_repeatalso option').length) {
					jQuery('#jform_repeatalso option').each(function() {
						params.push('also[]=' + jQuery(this).val());
					});
				}
				
				// Add exclude dates
				if (jQuery('#jform_excludedates option').length) {
					jQuery('#jform_excludedates option').each(function() {
						params.push('exclude[]=' + jQuery(this).val());
					});
				}
				
				jQuery.ajax({
					url: RSEventsPro.Event.root() + 'index.php?option=com_rseventspro',
					type: 'post',
					data: params.join('&')
				}).done(function( response ) {
					var start = response.indexOf('RS_DELIMITER0') + 13;
					var end = response.indexOf('RS_DELIMITER1');
					response = response.substring(start, end);
					
					totalRepeats += parseInt(response);
					
					jQuery('#rs_repeating_total').text(totalRepeats);
					jQuery('#rs_repeating_event_total').text(totalRepeats);
				});
			}
		}
	},
	
	// Add event to the repeat type
	repeatType: function() {
		value = jQuery('#jform_repeat_type').val();
		
		if (value == 1 || value == 2) {
			jQuery('#rsepro-repeat-days').css('display','');
		} else { 
			jQuery('#rsepro-repeat-days').css('display','none');
		}
	
		if (value == 3) {
			jQuery('#rsepro-repeat-interval').css('display','');
		} else {
			jQuery('#rsepro-repeat-interval').css('display','none');
		}
		
		RSEventsPro.Event.repeats();
	},
	
	repeatOnType: function() {
		value = jQuery('#jform_repeat_on_type').val();
		
		if (value == 0) {
			jQuery('#jform_repeat_on_day').css('display','none');
			jQuery('#repeat_on_day_order_container').css('display','none');
			jQuery('#repeat_on_day_type_container').css('display','none');
		} else if (value == 1) {
			jQuery('#jform_repeat_on_day').css('display','');
			jQuery('#repeat_on_day_order_container').css('display','none');
			jQuery('#repeat_on_day_type_container').css('display','none');
		} else {
			jQuery('#jform_repeat_on_day').css('display','none');
			jQuery('#repeat_on_day_order_container').css('display','');
			jQuery('#repeat_on_day_type_container').css('display','');
		}
	},
	
	// Add a new recurring date
	addRecurringDate: function() {
		value = jQuery('#repeat_date').val();
		
		if (value == '') {
			return;
		}
		
		if (jQuery('#jform_repeatalso option[value="' + value + '"]').length == 0) {
			jQuery('#jform_repeatalso').append(jQuery('<option>', { 'text': value, 'value': value }));
		}
		
		jQuery('#repeat_date').val('');
		RSEventsPro.Event.repeats();
	},
	
	// Add a new date to the exclude dates field
	addExcludeDate: function() {
		value = jQuery('#exclude_date').val();
		
		if (value == '') {
			return;
		}
		
		if (jQuery('#jform_excludedates option[value="' + value + '"]').length == 0) {
			jQuery('#jform_excludedates').append(jQuery('<option>', { 'text': value, 'value': value }));
		}
		
		jQuery('#exclude_date').val('');
		RSEventsPro.Event.repeats();
	},
	
	// Remove recurring dates
	removeRecurringDates: function() {
		jQuery('#jform_repeatalso option:selected').remove();
		RSEventsPro.Event.repeats();
	},
	
	// Remove exclude dates
	removeExcludeDates: function() {
		jQuery('#jform_excludedates option:selected').remove();
		RSEventsPro.Event.repeats();
	},
	
	// Apply to all
	applyToAll: function() {
		if (jQuery('#apply_changes').is(':checked')) {
			jQuery('#apply_to_all_info').css('display','none');
		} else {
			jQuery('#apply_to_all_info').css('display','');
		}
	},
	
	// Add a new category
	addCategory: function() {		
		category = jQuery('#rsepro-new-category').val();
		parent	 = jQuery('#category-parent').val();
		
		if (category == '') {
			jQuery('#rsepro-new-category').addClass('invalid');
			return;
		}
		
		jQuery('#rsepro-new-category').removeClass('invalid');
		jQuery('#rsepro-add-category-loader').css('display','');
		
		jQuery.ajax({
			url: RSEventsPro.Event.root() + 'index.php?option=com_rseventspro',
			type: 'post',
			dataType: 'json',
			data: {	
				'task': 'savedata',
				'type' : 'category',
				'jform[published]' : 1,
				'jform[title]' : category,
				'jform[parent_id]' : parent
			}
		}).done(function( response ) {
			var clone = jQuery('#category-parent option[value=1]').clone();
			
			jQuery('#categories option').remove();
			jQuery('#category-parent option').remove();
			jQuery(response).each(function (i,el){
				jQuery('#categories').append(jQuery('<option>', { 'text': el.text, 'value': el.value }));
				jQuery('#category-parent').append(jQuery('<option>', { 'text': el.text, 'value': el.value }));
			});
			
			jQuery('#category-parent').append(clone);
			jQuery('#categories').trigger('liszt:updated');
			jQuery('#rsepro-new-category').val('');
			jQuery('#rsepro-add-category-loader').css('display','none');
			jQuery('#rsepro-add-new-categ').modal('hide');
		});
	},
	
	// Select location
	selectLocation: function(what) {
		value = jQuery(what).val();
		
		jQuery('#rsepro-location').addClass('rsepro-loading');
		
		jQuery.ajax({
			url: RSEventsPro.Event.root() + 'index.php?option=com_rseventspro&task=locations',
			type: 'post',
			dataType: 'json',
			data: {
				'rs_location': value,
				'json': 1
			}
		}).done(function( response ) {
			jQuery('.rsepro-locations-container').css('visibility','visible');
			jQuery('.rsepro-location-container').css('visibility','hidden');
			jQuery('.rsepro-location-container').css('overflow','hidden');
			jQuery('#rsepro-locations li').remove();
			jQuery('#rsepro-location').removeClass('rsepro-loading');
			
			if (response.length == 0) {
				jQuery('.rsepro-locations-container').css('visibility','hidden');
				jQuery('.rsepro-location-container').css('visibility','visible');
				jQuery('.rsepro-location-container').css('overflow','visible');
				jQuery('#jform_location').val('');
			} else {
				jQuery(response).each(function (i,el){
					jQuery('#rsepro-locations').append(jQuery('<li>').html(el.name).on('click', function() {
						if (el.id == '-') {
							jQuery('.rsepro-locations-container').css('visibility','hidden');
							jQuery('.rsepro-location-container').css('visibility','visible');
							jQuery('.rsepro-location-container').css('overflow','visible');
							jQuery('#jform_location').val('');
						} else {
							jQuery('#rsepro-location').val(el.name);
							jQuery('#jform_location').val(el.id);
							jQuery('.rsepro-locations-container').css('visibility','hidden');
							jQuery('.rsepro-location-container').css('visibility','hidden');
							jQuery('.rsepro-location-container').css('overflow','hidden');
						}
					}));
				});
			}
		});		
	},
	
	// Cancel the creation of a new location
	cancelLocation: function() {
		jQuery('#location_address').val('');
		jQuery('.rsepro-locations-container').css('visibility','hidden');
		jQuery('.rsepro-location-container').css('visibility','hidden');
		jQuery('.rsepro-location-container').css('overflow','hidden');
	},
	
	// Save the new location
	saveLocation: function() {
		jQuery('#rsepro-save-location').prop('disabled', true);
		jQuery('#rsepro-cancel-location').prop('disabled', true);
		
		jQuery.ajax({
			url: RSEventsPro.Event.root() + 'index.php?option=com_rseventspro',
			type: 'post',
			data: {
				'task': 'savedata',
				'type': 'location',
				'jform[published]': '1',
				'jform[name]': jQuery('#rsepro-location').val(),
				'jform[address]': jQuery('#location_address').val(),
				'jform[description]': jQuery('#location_description').val(),
				'jform[coordinates]': jQuery('#location_coordinates').val()
			}
		}).done(function( response ) {
			if (response != 0) {
				jQuery('#rsepro-save-location').html('<i class="fa fa-check"></i> ' + Joomla.JText._('COM_RSEVENTSPRO_SAVED'));
				
				setTimeout( function() {
					jQuery('.rsepro-locations-container').css('visibility','hidden');
					jQuery('.rsepro-location-container').css('visibility','hidden');
					jQuery('.rsepro-location-container').css('overflow','hidden');
					jQuery('#jform_location').val(response);
					jQuery('#location_address').val('');
					jQuery('#location_description').val('');
					jQuery('#location_coordinates').val('');
					
					jQuery('#rsepro-save-location').html(Joomla.JText._('COM_RSEVENTSPRO_EVENT_LOCATION_ADD_LOCATION'));
					jQuery('#rsepro-save-location').prop('disabled', false);
					jQuery('#rsepro-cancel-location').prop('disabled', false);
				}, 1000);
			}
		});
	},
	
	// Remove file
	removeFile: function(id) {
		if (confirm(Joomla.JText._('COM_RSEVENTSPRO_EVENT_DELETE_FILE_CONFIRM'))) {
			jQuery('#rsepro-file-loader').css('display','');
			
			jQuery.ajax({
				url: RSEventsPro.Event.root() + 'index.php?option=com_rseventspro',
				type: 'post',
				data: {
					'task': 'event.removefile',
					'id': id
				}
			}).done(function(response ) {
				var start = response.indexOf('RS_DELIMITER0') + 13;
				var end = response.indexOf('RS_DELIMITER1');
				response = response.substring(start, end);
				
				if (parseInt(response) == 1) {
					jQuery('.rsepro-event-files li#'+id).remove();
				}
				
				jQuery('#rsepro-file-loader').css('display','none');
			});
		}
	},
	
	// Edit file
	editFile: function(id) {
		// Reset values
		jQuery('#rsepro-edit-event-file input[type="checkbox"]').prop('checked',false);
		jQuery('#rsepro-file-name').val('');
		jQuery('#rsepro-file-name').removeClass('invalid');
		jQuery('label[for="rsepro-file-name"]').removeClass('invalid');
		jQuery('#rsepro-file-loader').css('display','');
		
		jQuery.ajax({
			url: RSEventsPro.Event.root() + 'index.php?option=com_rseventspro',
			type: 'post',
			dataType: 'json',
			data: {
				'task': 'loadfile',
				'id': id
			}
		}).done(function(response ) {
			if (response.id) {
				jQuery('#rsepro-file-id').val(id);
				jQuery('#rsepro-file-name').val(response.name);
				
				for (i=0; i < response.permissions.length; i++) {
					if (response.permissions[i] == 1) {
						jQuery('#fp'+i).prop('checked', true)
					}
				}
				
				jQuery('#rsepro-file-loader').css('display','none');
				jQuery('#rsepro-edit-event-file').modal('show');
			}
		});
	},
	
	// Save file
	saveFile: function() {
		if (jQuery('#rsepro-file-name').val().length == 0) {
			jQuery('#rsepro-file-name').addClass('invalid');
			jQuery('label[for="rsepro-file-name"]').addClass('invalid');
		} else {
			jQuery('#rsepro-file-name').removeClass('invalid');
			jQuery('label[for="rsepro-file-name"]').removeClass('invalid');
		}
		
		jQuery.ajax({
			url: RSEventsPro.Event.root() + 'index.php?option=com_rseventspro',
			type: 'post',
			data: {
				task : 'event.savefile',
				name : jQuery('#rsepro-file-name').val(),
				id: jQuery('#rsepro-file-id').val(),
				fp0 : jQuery('#fp0').prop('checked') ? 1 : 0,
				fp1 : jQuery('#fp1').prop('checked') ? 1 : 0,
				fp2 : jQuery('#fp2').prop('checked') ? 1 : 0,
				fp3 : jQuery('#fp3').prop('checked') ? 1 : 0,
				fp4 : jQuery('#fp4').prop('checked') ? 1 : 0,
				fp5 : jQuery('#fp5').prop('checked') ? 1 : 0,
			}
		}).done(function(response ) {
			jQuery('.rsepro-event-files li#' + jQuery('#rsepro-file-id').val() + ' a.rsepro-edit-file').text(jQuery('#rsepro-file-name').val());
			jQuery('#rsepro-edit-event-file').modal('hide');
		});
		
	},
	
	// Add ticket
	addTicket: function() {
		var tName		= jQuery('#ticket_name').val();
		var tPrice		= jQuery('#ticket_price').val();
		var tSeats		= jQuery('#ticket_seats').val();
		var tUserSeats	= jQuery('#ticket_user_seats').val();
		var tDescr		= jQuery('#ticket_description').val();
		var params		= new Array();
		
		if (tName == '') {
			jQuery('#ticket_name').addClass('invalid');
			jQuery('label[for="ticket_name"]').addClass('invalid');
			return;
		} else {
			jQuery('#ticket_name').removeClass('invalid');
			jQuery('label[for="ticket_name"]').removeClass('invalid');
		}
		
		params.push('view=event');
		params.push('layout=edit');
		params.push('tpl=tickets');
		params.push('format=raw');
		params.push('type=ticket');
		params.push('id='+jQuery('#eventID').val());
		params.push('jform[ide]='+jQuery('#eventID').val());
		params.push('jform[name]='+tName);
		params.push('jform[price]='+tPrice);
		params.push('jform[seats]='+tSeats);
		params.push('jform[user_seats]='+tUserSeats);
		params.push('jform[description]='+tDescr);
		
		jQuery('#ticket_groups option:selected').each(function(){
			params.push('groups[]=' + jQuery(this).val());
		});
		
		jQuery('#rsepro-add-ticket-loader').css('display','');
		
		jQuery.ajax({
			url: RSEventsPro.Event.root() + 'index.php?option=com_rseventspro',
			type: 'post',
			dataType: 'json',
			data: params.join('&')
		}).done(function(response) {
			jQuery('<li style="display:block;" class="rsepro-ticket rsepro-hide" id="ticket_' + response.id + '"><a data-toggle="tab" data-target="#rsepro-edit-ticket' + response.id + '" href="javascript:void(0);">' + tName + '</a></li>').insertBefore(jQuery('ul li a[data-target="#rsepro-edit-tab6"]').parent());
			jQuery(response.html).insertBefore(jQuery('#rsepro-edit-tab6'));
			
			// Add custom js codes
			jQuery('#ticket_groups'+response.id).chosen();
			jQuery('#rsepro-edit-ticket' + response.id + ' .rsepro-remove-ticket').on('click', function(){
				RSEventsPro.Event.removeTicket(jQuery(this).data('id'));
			});
			jQuery('#rsepro-edit-ticket' + response.id + ' .rsepro-event-update').on('click', function() {
				RSEventsPro.Event.save();
			});
			jQuery('#rsepro-edit-ticket' + response.id + ' .rsepro-event-cancel').on('click', function() {
				RSEventsPro.Event.cancel();
			});
			
			// Go to the new ticket tab
			jQuery('.rsepro-edit-event > ul > li > a[data-target="#rsepro-edit-ticket' + response.id + '"]').click();
			
			RSEventsPro.Event.orderTickets();
			
			// Reset values
			jQuery('#rsepro-add-ticket-loader').css('display','none');
			jQuery('#ticket_name').val('');
			jQuery('#ticket_price').val('');
			jQuery('#ticket_seats').val('');
			jQuery('#ticket_user_seats').val('');
			jQuery('#ticket_description').val('');
			jQuery('#ticket_groups option').prop('selected',false);
			jQuery('#ticket_groups').trigger('liszt:updated');
		});
	},
	
	// Remove ticket
	removeTicket: function (id) {		
		if (confirm(Joomla.JText._('COM_RSEVENTSPRO_CONFIRM_DELETE_TICKET'))) {
			jQuery.ajax({
				url: RSEventsPro.Event.root() + 'index.php?option=com_rseventspro',
				type: 'post',
				data: {
					task : 'event.removeticket',
					id: id
				}
			}).done(function(response ) {
				var start = response.indexOf('RS_DELIMITER0') + 13;
				var end = response.indexOf('RS_DELIMITER1');
				response = response.substring(start, end);
				
				if (parseInt(response) == 1) {
					jQuery('ul li a[data-target="#rsepro-edit-ticket' + id + '"]').parent().remove();
					jQuery('#rsepro-edit-ticket'+id).remove();
					jQuery('ul li a[data-target="#rsepro-edit-tab3"]').click();
				}
			});
		}
	},
	
	// Add coupon
	addCoupon: function() {
		var cName		= jQuery('#coupon_name').val();
		var cCode		= jQuery('#coupon_code').val();
		var cStart		= jQuery('#coupon_start').val();
		var cEnd		= jQuery('#coupon_end').val();
		var cUsage		= jQuery('#coupon_usage').val();
		var cAmount		= jQuery('#coupon_discount').val();
		var cType		= jQuery('#coupon_type').val();
		var cAction		= jQuery('#coupon_action').val();
		var params		= new Array();
		
		if (cName == '') {
			jQuery('#coupon_name').addClass('invalid');
			jQuery('label[for="coupon_name"]').addClass('invalid');
			return;
		} else {
			jQuery('#coupon_name').removeClass('invalid');
			jQuery('label[for="coupon_name"]').removeClass('invalid');
		}
		
		if (cAmount == '' || cAmount == 0) {
			jQuery('#coupon_discount').addClass('invalid');
			jQuery('label[for="coupon_discount"]').addClass('invalid');
			return;
		} else {
			jQuery('#coupon_discount').removeClass('invalid');
			jQuery('label[for="coupon_discount"]').removeClass('invalid');
		}
		
		params.push('view=event');
		params.push('layout=edit');
		params.push('tpl=coupons');
		params.push('format=raw');
		params.push('type=coupon');
		params.push('id='+jQuery('#eventID').val());
		params.push('jform[ide]='+jQuery('#eventID').val());
		params.push('jform[name]='+cName);
		params.push('jform[from]='+cStart);
		params.push('jform[to]='+cEnd);
		params.push('jform[discount]='+cAmount);
		params.push('jform[type]='+cType);
		params.push('jform[action]='+cAction);
		params.push('jform[usage]='+cUsage);
		params.push('codes='+cCode);
		
		jQuery('#coupon_groups option:selected').each(function(){
			params.push('groups[]=' + jQuery(this).val());
		});
		
		jQuery('#rsepro-add-coupon-loader').css('display','');
		
		jQuery.ajax({
			url: RSEventsPro.Event.root() + 'index.php?option=com_rseventspro',
			type: 'post',
			dataType: 'json',
			data: params.join('&')
		}).done(function(response) {
			jQuery('<li style="display:block;" class="rsepro-hide"><a data-toggle="tab" data-target="#rsepro-edit-coupon' + response.id + '" href="javascript:void(0);">' + cName + '</a></li>').insertBefore(jQuery('ul li a[data-target="#rsepro-edit-tab8"]').parent());
			jQuery(response.html).insertBefore(jQuery('#rsepro-edit-tab8'));
			
			// Add custom js codes
			jQuery('#coupon_groups'+response.id).chosen();
			jQuery('#rsepro-edit-coupon' + response.id + ' .rsepro-coupon-generate').on('click', function() {
				RSEventsPro.Event.generateCoupons(jQuery(this).data('id'));
			});
			jQuery('#rsepro-edit-coupon' + response.id + ' .rsepro-event-remove-coupon').on('click', function(){
				RSEventsPro.Event.removeCoupon(jQuery(this).data('id'));
			});
			jQuery('#rsepro-edit-coupon' + response.id + ' .rsepro-event-update').on('click', function() {
				RSEventsPro.Event.save();
			});
			jQuery('#rsepro-edit-coupon' + response.id + ' .rsepro-event-cancel').on('click', function() {
				RSEventsPro.Event.cancel();
			});
			
			if (jQuery('#rsepro-time').val() == 1) {
				jQuery('#coupons_' + response.id + '_to_datetimepicker').datetimepicker({
					pickTime: true,
					pick12HourFormat: true,
					linkField: 'coupons_' + response.id + '_to',
					format: 'yyyy-MM-dd HH:mm:ss PP'
				});
				jQuery('#coupons_' + response.id + '_from_datetimepicker').datetimepicker({
					pickTime: true,
					pick12HourFormat: true,
					linkField: 'coupons_' + response.id + '_from',
					format: 'yyyy-MM-dd HH:mm:ss PP'
				});
			} else {
				jQuery('#coupons_' + response.id + '_from_datetimepicker').datetimepicker({
					pickTime: true,
					format: "yyyy-MM-dd hh:mm:ss"
				});
				jQuery('#coupons_' + response.id + '_to_datetimepicker').datetimepicker({
					pickTime: true,
					format: "yyyy-MM-dd hh:mm:ss"
				});
			}
			
			if ( typeof MooTools != 'undefined' ) {
				(function($) {
					$("div[id$='datetimepicker']").each(function (i,el) {
						if (typeof $(el)[0] != 'undefined') {
							$(el)[0].hide = null;
						}
					});
				})(jQuery);
			}
		
			
			// Go to the new coupon tab
			jQuery('.rsepro-edit-event > ul > li > a[data-target="#rsepro-edit-coupon' + response.id + '"]').click();
			
			// Reset values
			jQuery('#rsepro-add-coupon-loader').css('display','none');
			jQuery('#coupon_name').val('');
			jQuery('#coupon_code').val('');
			jQuery('#coupon_start').val('');
			jQuery('#coupon_end').val('');
			jQuery('#coupon_usage').val('');
			jQuery('#coupon_discount').val('');
			jQuery('#coupon_groups option').prop('selected',false);
			jQuery('#coupon_groups').trigger('liszt:updated');
		});
		
	},
	
	// Remove coupon
	removeCoupon: function(id) {
		if (confirm(Joomla.JText._('COM_RSEVENTSPRO_CONFIRM_DELETE_COUPON'))) {
			jQuery.ajax({
				url: RSEventsPro.Event.root() + 'index.php?option=com_rseventspro',
				type: 'post',
				data: {
					task : 'event.removecoupon',
					id: id
				}
			}).done(function(response ) {
				var start = response.indexOf('RS_DELIMITER0') + 13;
				var end = response.indexOf('RS_DELIMITER1');
				response = response.substring(start, end);
				
				if (parseInt(response) == 1) {
					jQuery('ul li a[data-target="#rsepro-edit-coupon' + id + '"]').parent().remove();
					jQuery('#rsepro-edit-coupon'+id).remove();
					jQuery('ul li a[data-target="#rsepro-edit-tab6"]').click();
				}
			});
		}
	},
	
	// Generate coupon codes
	generateCoupons: function(id) {
		var output = new Array();
		times = jQuery('#coupon_times'+id).val();
		times = times == 0 || times == '' ? 1 : parseInt(times);
		
		for (j=0; j < times; j++) {
			string = '';
			i = 0;
			
			while(i<5) {
				string += String.fromCharCode(65 + Math.round(Math.random() * 25));
				string += Math.floor(Math.random()*11);
				i++;
			}
			output.push(string);
		}
		
		jQuery('#coupon_code'+id).val(output.join("\n"));
	},
	
	// Validate form
	validate: function() {
		var ret = true;
		var msg = new Array();
		var tab1 = false, tab2 = false, tab3 = false, tab6 = false, tab10 = false;
		
		// Check for event name
		if (jQuery('#jform_name').val() == '') {
			jQuery('#jform_name').addClass('invalid');
			jQuery('label[for="jform_name"]').addClass('invalid');
			tab1 = true;
			msg.push(Joomla.JText._('COM_RSEVENTSPRO_NO_NAME_ERROR'));
			ret = false;
		} else {
			jQuery('#jform_name').removeClass('invalid');
			jQuery('label[for="jform_name"]').removeClass('invalid');
		}
		
		// Check for location
		if (jQuery('#jform_location').val() == '' || jQuery('#jform_location').val() == 0) {
			jQuery('#rsepro-location').addClass('invalid');
			jQuery('label[for="jform_location"]').addClass('invalid');
			msg.push(Joomla.JText._('COM_RSEVENTSPRO_NO_LOCATION_ERROR'));
			tab1 = true;
			ret = false;
		} else {
			jQuery('#rsepro-location').removeClass('invalid');
			jQuery('label[for="jform_location"]').removeClass('invalid');
		}
		
		// Check for category
		if (jQuery('#categories option:selected').length == 0) {
			jQuery('#categories').addClass('invalid');
			jQuery('#categories_chzn').addClass('invalid');
			msg.push(Joomla.JText._('COM_RSEVENTSPRO_NO_CATEGORY_ERROR'));
			ret = false;
			tab2 = true;
		} else {
			jQuery('#categories').removeClass('invalid');
			jQuery('#categories_chzn').removeClass('invalid');
		}
		
		// Check for start date
		if (jQuery('#jform_start').val() == '') {
			if (jQuery('#rsepro-time').val() == 1) {
				jQuery('#jform_start_dummy').addClass('invalid');
			} else {
				jQuery('#jform_start').addClass('invalid');
			}
			
			jQuery('label[for="jform_start"]').addClass('invalid');
			msg.push(Joomla.JText._('COM_RSEVENTSPRO_NO_START_ERROR'));
			ret = false;
			tab1 = true;
		} else {
			if (jQuery('#rsepro-time').val() == 1) {
				jQuery('#jform_start_dummy').removeClass('invalid');
			} else {
				jQuery('#jform_start').removeClass('invalid');
			}
			
			jQuery('label[for="jform_start"]').removeClass('invalid');
		}
		
		// Check for end date, only when All day event option is not enable
		if (!jQuery('#jform_allday').is(':checked')) {
			if (jQuery('#jform_end').val() == '') {
				if (jQuery('#rsepro-time').val() == 1) {
					jQuery('#jform_end_dummy').addClass('invalid');
				} else {
					jQuery('#jform_end').addClass('invalid');
				}
				
				jQuery('label[for="jform_end"]').addClass('invalid');
				msg.push(Joomla.JText._('COM_RSEVENTSPRO_NO_END_ERROR'));
				ret = false;
				tab1 = true;
			} else {
				if (jQuery('#rsepro-time').val() == 1) {
					jQuery('#jform_end_dummy').removeClass('invalid');
				} else {
					jQuery('#jform_end').removeClass('invalid');
				}
				
				jQuery('label[for="jform_end"]').removeClass('invalid');
			}
		}
		
		// Check for owner
		if (jQuery('#jform_owner').val() == '' || jQuery('#jform_owner').val() == 0) {
			jQuery('#jform_owner_name').addClass('invalid');
			jQuery('label[for="jform_owner"]').addClass('invalid');
			msg.push(Joomla.JText._('COM_RSEVENTSPRO_NO_OWNER_ERROR'));
			ret = false;
			tab10 = true;
		} else {
			jQuery('#jform_owner_name').removeClass('invalid');
			jQuery('label[for="jform_owner"]').removeClass('invalid');
		}
		
		// Check if the end date is after the start date
		if (!jQuery('#jform_allday').is(':checked')) {
			if (jQuery('#jform_start').val() != '' && jQuery('#jform_end').val() != '') {
				if (RSEventsPro.Event.convertDate(jQuery('#jform_start').val()) >= RSEventsPro.Event.convertDate(jQuery('#jform_end').val())) {
					if (jQuery('#rsepro-time').val() == 1) {
						jQuery('#jform_end_dummy').addClass('invalid');
					} else {
						jQuery('#jform_end').addClass('invalid');
					}
					jQuery('label[for="jform_end"]').addClass('invalid');
					msg.push(Joomla.JText._('COM_RSEVENTSPRO_END_BIGGER_ERROR'));
					ret = false;
					tab1 = true;
				} else {
					if (jQuery('#rsepro-time').val() == 1) {
						jQuery('#jform_end_dummy').removeClass('invalid');
					} else {
						jQuery('#jform_end').removeClass('invalid');
					}
					jQuery('label[for="jform_end"]').removeClass('invalid');
				}
			}
		}
		
		// Check if the end registration date occurs after the start registraion date
		if (jQuery('#jform_registration').is(':checked')) {
			if (jQuery('#jform_start_registration').val() != '' && jQuery('#jform_end_registration').val() != '' && RSEventsPro.Event.convertDate(jQuery('#jform_start_registration').val()) >= RSEventsPro.Event.convertDate(jQuery('#jform_end_registration').val())) {
				if (jQuery('#rsepro-time').val() == 1) {
					jQuery('#jform_end_registration_dummy').addClass('invalid');
				} else {
					jQuery('#jform_end_registration').addClass('invalid');
				}
				
				jQuery('label[for="jform_end_registration"]').addClass('invalid');
				msg.push(Joomla.JText._('COM_RSEVENTSPRO_END_REG_BIGGER_ERROR'));
				ret = false;
				tab3 = true;
			} else {
				if (jQuery('#rsepro-time').val() == 1) {
					jQuery('#jform_end_registration_dummy').removeClass('invalid');
				} else {
					jQuery('#jform_end_registration').removeClass('invalid');
				}
				
				jQuery('label[for="jform_end_registration"]').removeClass('invalid');
			}
		}
		
		if (jQuery('#jform_discounts').is(':checked')) {
			
			// Check the early fee discount
			if (parseInt(jQuery('#jform_early_fee').val()) != 0 && jQuery('#jform_early_fee_end').val() == '') {
				if (jQuery('#rsepro-time').val() == 1) {
					jQuery('#jform_early_fee_end_dummy').addClass('invalid');
				} else {
					jQuery('#jform_early_fee_end').addClass('invalid');
				}
				
				msg.push(Joomla.JText._('COM_RSEVENTSPRO_EARLY_FEE_ERROR'));
				ret = false;
				tab6 = true;
			} else {
				if (jQuery('#rsepro-time').val() == 1) {
					jQuery('#jform_early_fee_end_dummy').removeClass('invalid');
				} else {
					jQuery('#jform_early_fee_end').removeClass('invalid');
				}
			}
			
			// Check the late fee
			if (parseInt(jQuery('#jform_late_fee').val()) != 0 && jQuery('#jform_late_fee_start').val() == '') {
				if (jQuery('#rsepro-time').val() == 1) {
					jQuery('#jform_late_fee_start_dummy').addClass('invalid');
				} else {
					jQuery('#jform_late_fee_start').addClass('invalid');
				}
				
				msg.push(Joomla.JText._('COM_RSEVENTSPRO_LATE_FEE_ERROR'));
				ret = false;
				tab6 = true;
			} else {
				if (jQuery('#rsepro-time').val() == 1) {
					jQuery('#jform_late_fee_start_dummy').removeClass('invalid');
				} else {
					jQuery('#jform_late_fee_start').removeClass('invalid');
				}
			}
			
			// Check the early discount date and the late fee date
			if (parseInt(jQuery('#jform_early_fee').val()) != 0 && parseInt(jQuery('#jform_late_fee').val()) != 0 && jQuery('#jform_early_fee_end').val() != '' && jQuery('#jform_late_fee_start').val() != '' && RSEventsPro.Event.convertDate(jQuery('#jform_late_fee_start').val()) <= RSEventsPro.Event.convertDate(jQuery('#jform_early_fee_end').val())) {
				if (jQuery('#rsepro-time').val() == 1) {
					jQuery('#jform_late_fee_start_dummy').addClass('invalid');
				} else {
					jQuery('#jform_late_fee_start').addClass('invalid');
				}
				
				msg.push(Joomla.JText._('COM_RSEVENTSPRO_LATE_FEE_BIGGER_ERROR'));
				ret = false;
				tab6 = true;
			} else {
				if (jQuery('#rsepro-time').val() == 1) {
					jQuery('#jform_late_fee_start_dummy').removeClass('invalid');
				} else {
					jQuery('#jform_late_fee_start').removeClass('invalid');
				}
			}
		}
		
		if (tab1) {
			jQuery('ul li a[data-target="#rsepro-edit-tab1"]').addClass('invalid');
		} else {
			jQuery('ul li a[data-target="#rsepro-edit-tab1"]').removeClass('invalid');
		}
		
		if (tab2) {
			jQuery('ul li a[data-target="#rsepro-edit-tab2"]').addClass('invalid');
		} else {
			jQuery('ul li a[data-target="#rsepro-edit-tab2"]').removeClass('invalid');
		}
		
		if (tab3) {
			jQuery('ul li a[data-target="#rsepro-edit-tab3"]').addClass('invalid');
		} else {
			jQuery('ul li a[data-target="#rsepro-edit-tab3"]').removeClass('invalid');
		}
		
		if (tab6) {
			jQuery('ul li a[data-target="#rsepro-edit-tab6"]').addClass('invalid');
		} else {
			jQuery('ul li a[data-target="#rsepro-edit-tab6"]').removeClass('invalid');
		}
		
		if (tab10) {
			jQuery('ul li a[data-target="#rsepro-edit-tab10"]').addClass('invalid');
		} else {
			jQuery('ul li a[data-target="#rsepro-edit-tab10"]').removeClass('invalid');
		}
		
		// Set the error messages
		if (msg.length > 0) {
			jQuery('#rsepro-errors').css('display', '');
			jQuery('#rsepro-errors').html(msg.join('<br />'));
			jQuery('html, body').animate({ scrollTop: 0 }, 2000);
		} else {
			jQuery('#rsepro-errors').text('');
			jQuery('#rsepro-errors').css('display', 'none');
		}
		
		jQuery('#jform_repeatalso option').prop('selected',true)
		jQuery('#jform_excludedates option').prop('selected',true)
		
		return ret;
	},
	
	// Save event
	save: function(task) {
		if (RSEventsPro.Event.validate()) {
			task = typeof task == 'undefined' ? 'event.apply' : task;
			Joomla.submitform(task);
		}
	},
	
	// Cancel event
	cancel: function() {
		Joomla.submitbutton('event.cancel');
	},
	
	// Compare two dates
	convertDate: function(string) {
		var t = string.split(/[- :]/);
		return new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);
	},
	
	orderTickets: function() {
		jQuery('#rsepro-edit-menu').sortable({
			axis: 'y',
			items: 'li.rsepro-ticket',
			update: function(event, ui) {
				jQuery.ajax({
					url: RSEventsPro.Event.root() + 'index.php?option=com_rseventspro',
					type: 'post',
					data: 'task=event.ticketsorder&id=' + jQuery('#eventID').val() + '&' + jQuery(this).sortable('serialize')
				});
			}
		});

		jQuery('#rsepro-edit-menu li').disableSelection();
	}
}