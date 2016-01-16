// Show the map in the settings view
function rseproMap() {
	if (jQuery('#mapContainer').css('display') == 'none') {
		jQuery('#mapContainer').css('display','');
		jQuery('#map_canvas').rsjoomlamap({
			coordinates: 'jform_google_maps_center',
			zoom: parseInt(jQuery('#jform_google_map_zoom').val()),
			markerDraggable: true
		});
	} else {
		jQuery('#mapContainer').css('display','none');
	}
}

// Load more data
function rspagination(view, limitstart, rscontainer) {
	var params = '';
	
	if (rscontainer)
		jQuery('#rsepro_loadmore_'+rscontainer).removeClass('rsepromore_inactive').addClass('rsepromore_active');
	else jQuery('#rsepro_loadmore').removeClass('rsepromore_inactive').addClass('rsepromore_active');

	if (rscontainer) {
		var total = 0;
		if (jQuery('#rseprocontainer_past').length)			total += jQuery('#rseprocontainer_past').children('tr').length - 1;
		if (jQuery('#rseprocontainer_ongoing').length)		total += jQuery('#rseprocontainer_ongoing').children('tr').length - 1;
		if (jQuery('#rseprocontainer_thisweek').length)		total += jQuery('#rseprocontainer_thisweek').children('tr').length - 1;
		if (jQuery('#rseprocontainer_thismonth').length)	total += jQuery('#rseprocontainer_thismonth').children('tr').length - 1;
		if (jQuery('#rseprocontainer_nextmonth').length)	total += jQuery('#rseprocontainer_nextmonth').children('tr').length - 1;
		if (jQuery('#rseprocontainer_upcoming').length)		total += jQuery('#rseprocontainer_upcoming').children('tr').length - 1;
		
		params = '&type=' + rscontainer + '&total=' + total;
	}
	
	jQuery.ajax({
		url: 'index.php?option=com_rseventspro',
		type: 'post',
		data: 'view=' + view + '&layout=items&lstart=' + limitstart + params
	}).done(function( response ) {
		var start = response.indexOf('RS_DELIMITER0') + 13;
		var end = response.indexOf('RS_DELIMITER1');
		response = response.substring(start, end);
		
		if (rscontainer) {
			jQuery('#rseprocontainer_'+rscontainer).append(response);
			jQuery('#rsepro_loadmore_'+rscontainer).removeClass('rsepromore_active').addClass('rsepromore_inactive');
			
			if ((jQuery('#rseprocontainer_'+rscontainer).children('tr').length - 1) >= jQuery('#total_'+rscontainer).val()) {
				jQuery('#'+rscontainer).css('display','none');
			}
		} else {
			jQuery('#rseprocontainer').append(response);
			jQuery('#rsepro_loadmore').removeClass('rsepromore_active').addClass('rsepromore_inactive');
			
			if ((jQuery('#rseprocontainer').children('tr').length) >= jQuery('#total').val()) {
				jQuery('#rsepro_loadmore').css('display','none');
			}
		}
		
		if (response.indexOf('hasTooltip') > -1) {
			jQuery('.hasTooltip').tooltip({"html": true,"container": "body"});
		} else {
			$$('.hasTip').each(function(el) {
				var title = el.get('title');
				if (title) {
					var parts = title.split('::', 2);
					el.store('tip:title', parts[0]);
					el.store('tip:text', parts[1]);
				}
			});
			var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});
		}
		
		if (typeof jQuery != 'undefined' && typeof jQuery.JSortableList != 'undefined') {
			if (view == 'locations') {
				var sortableList = new jQuery.JSortableList('#locationsList tbody', 'adminForm', 'asc', 'index.php?option=com_rseventspro&task=locations.saveOrderAjax&tmpl=component', '', '1');
			} else if (view == 'categories') {
				var sortableList = new jQuery.JSortableList('#categoriesList tbody', 'adminForm', 'asc', 'index.php?option=com_rseventspro&task=categories.saveOrderAjax&tmpl=component', '', '1');
			}
		}
	});
}

function addRule(err1, err2, msg) {
	if (jQuery('#status').val() == 1 && jQuery('#rule').val() == 1 || jQuery('#status').val() == 2 && jQuery('#rule').val() == 2) {
		alert(err1);
		return;
	}
	
	if (jQuery('#rule').val() == 4 && jQuery('#mid').val() == 0) {
		alert(err2);
		return;
	}
	
	jQuery('#loader').css('display','');
	params = '&status=' + jQuery('#status').val() + '&interval=' + jQuery('#interval').val() + '&rule=' + jQuery('#rule').val() + '&mid=' + jQuery('#mid').val();
	
	jQuery.ajax({
		url: 'index.php?option=com_rseventspro',
		type: 'post',
		data: 'task=saverule&payment=' + jQuery('#payment').val() + params
	}).done(function( response ) {
		var start = response.indexOf('RS_DELIMITER0') + 13;
		var end = response.indexOf('RS_DELIMITER1');
		response = response.substring(start, end);
		
		if (response) {
			count = jQuery('#rseprocontainer > tr').length;
			
			// Create the table row
			var thetr = jQuery('<tr>').addClass('row'+(count % 2));
			
			// Create the first table column
			var thetd1 = jQuery('<td>').addClass('center hidden-phone');
			var theinp = jQuery('<input>', { 'type': 'checkbox', 'name': 'cid[]', 'id': 'cb'+count, 'value': response } ).on('click', function() { Joomla.isChecked(this.checked); })
			thetd1.append(theinp);
			
			// Create the second table column
			var thetd2 = jQuery('<td>').addClass('nowrap has-context');
			var message = jQuery('#message1').html() + ' <b>' + jQuery('#payment option:selected').text() + '</b> ' + jQuery('#message2').html() + ' <b>' + jQuery('#status option:selected').text() + '</b> ' + jQuery('#message3').html() + ' <b>' + jQuery('#interval').val() + '</b> ' + jQuery('#message4').html() + ' <b>' + jQuery('#rule option:selected').text() + '</b>';
			
			if (jQuery('#rule').val() == 4)
				message += ' <b>('+ jQuery('#email').html() +')</b>';
			
			thetd2.html(message);
			
			// Create the third table column
			var thetd3 = jQuery('<td>').addClass('center hidden-phone').html(response);
			
			thetr.append(thetd1);
			thetr.append(thetd2);
			thetr.append(thetd3);
			jQuery('#rseprocontainer').append(thetr);
			
			jQuery('#mid').val(0);
			jQuery('#email').html(msg);
			jQuery('#loader').css('display','none');
		}
	});
}

function rsepro_change_list(val) {
	var hasControlGroup = jQuery('#jform_params_archived').parent().parent().hasClass('control-group');
	if (hasControlGroup) {
		jQuery('#jform_params_archived').parent().parent().css('display','none');
		jQuery('#jform_params_days').parent().parent().css('display','none');
		
		if (val == 'all')
			jQuery('#jform_params_archived').parent().parent().css('display','');
		else if (val == 'future') {
				jQuery('#jform_params_days').parent().parent().css('display','');
			}
		else if (val == 'featured')
			jQuery('#jform_params_archived').parent().parent().css('display','');
	} else {
		jQuery('#jform_params_archived').parent().css('display','none');
		jQuery('#jform_params_days').parent().css('display','none');
		
		if (val == 'all')
			jQuery('#jform_params_archived').parent().css('display','');
		else if (val == 'future') {
			jQuery('#jform_params_days').parent().css('display','');
		}
		else if (val == 'featured')
			jQuery('#jform_params_archived').parent().css('display','');
	}
}

function rsepro_add_simple_ticket(id, name, price, quantity) {
	var selected_container		= window.parent.jQuery('#rsepro_simple_tickets');
	var selected_view_container	= window.parent.jQuery('#rsepro_selected_tickets_view');
	var eventID					= parseInt(jQuery('#eventID').text());
	
	// Set the event id
	if (window.parent.jQuery('#jform_ide').val() == 0) {
		window.parent.jQuery('#jform_ide').val(eventID);
	} else if (window.parent.jQuery('#jform_ide').val() != eventID) {
		jQuery('input[name="simple_tickets[]"]').val('');
		alert(Joomla.JText._('COM_RSEVENTSPRO_SUBSCRIBER_PLEASE_SELECT_TICKET_FROM_EVENT'));
		return;
	}
	
	// Remove ticket if the quantity is 0
	if (parseInt(quantity) == 0) {
		window.parent.jQuery('#content'+id).remove();
		window.parent.jQuery('#ticket'+id).remove();
		return;
	}
	
	if (window.parent.jQuery('#content'+id).length == 0) {
		if (id == 0) {
			selected_view_container.append('<span id="content'+id+'"><span id="rsepro_quantity'+id+'">'+ parseInt(quantity) +'</span> x ' + name + ' <br /> </span>');
		} else {
			selected_view_container.append('<span id="content'+id+'"><span id="rsepro_quantity'+id+'">'+ parseInt(quantity) +'</span> x ' + name + ' (' + price + ') <br /> </span>');
		}
		
		var input = jQuery('<input>', { 'type': 'hidden', 'id': 'ticket'+id, 'value': parseInt(quantity) });
		
		if (id == 0)
			input.prop('name', 'tickets[ev'+eventID+']');
		else
			input.prop('name', 'tickets['+id+']');
		
		
		selected_container.append(input);
	} else {
		window.parent.jQuery('#rsepro_quantity'+id).text(parseInt(quantity));
		window.parent.jQuery('#ticket'+id).val(parseInt(quantity));
	}
}

function rsepro_add_ticket(id, place, tname, tprice) {
	var seat_container			= jQuery('#rsepro_seat_'+id+place);
	var selected_container		= window.parent.jQuery('#rsepro_selected_tickets');
	var selected_view_container	= window.parent.jQuery('#rsepro_selected_tickets_view');
	var eventID					= parseInt(jQuery('#eventID').text());
	
	// Set the event id
	if (window.parent.jQuery('#jform_ide').val() == 0) {
		window.parent.jQuery('#jform_ide').val(eventID);
	} else if (window.parent.jQuery('#jform_ide').val() != eventID) {
		alert(Joomla.JText._('COM_RSEVENTSPRO_SUBSCRIBER_PLEASE_SELECT_TICKET_FROM_EVENT'));
		return;
	}
	
	if (place == 0) {
		if (jQuery('#rsepro_unlimited_'+id).val() == 0 || jQuery('#rsepro_unlimited_'+id).val() == '') {
			window.parent.jQuery('#ticket'+id+place).remove();
			window.parent.jQuery('#content'+id).remove();
			return;
		}
	}
	
	// We are dealing with unlimited tickets
	if (place == 0) {
		if (window.parent.jQuery('#ticket'+id+place).length) {
			window.parent.jQuery('#ticket'+id+place).val(jQuery('#rsepro_unlimited_'+id).val());
		} else {
			input = jQuery('<input>', {'type': 'hidden', 'name': 'unlimited['+id+']', 'id': 'ticket'+id+place, 'value': jQuery('#rsepro_unlimited_'+id).val()});
			selected_container.append(input);
		}
	} else {
		if (seat_container.hasClass('rsepro_selected')) {
			// Deselect ticket
			seat_container.removeClass('rsepro_selected')
			
			if (window.parent.jQuery('#ticket'+id+place).length) {
				window.parent.jQuery('#ticket'+id+place).remove();
			}
		} else {
			seat_container.addClass('rsepro_selected');
			input = jQuery('<input>', {'type': 'hidden', 'name': 'tickets['+id+'][]', 'id': 'ticket'+id+place, 'value': place});
			selected_container.append(input);
		}
	}
	
	if (window.parent.jQuery('#content'+id).length == 0) {
		if (place == 0) {
			selected_view_container.append('<span id="content'+id+'"><span id="rsepro_quantity'+id+'">'+ jQuery('#rsepro_unlimited_'+id).val() +'</span> x ' + tname + ' (' + tprice + ') <br /> </span>');
		} else {
			selected_view_container.append('<span id="content'+id+'"><span id="rsepro_quantity'+id+'">'+ window.parent.jQuery('input[name^="tickets['+id+'][]"]').length +'</span> x ' + tname + ' (' + tprice + ') <br /> </span>');
		}
	} else {
		if (place == 0) {
			window.parent.jQuery('#rsepro_quantity'+id).text(jQuery('#rsepro_unlimited_'+id).val());
		} else {
			if (window.parent.jQuery('input[name^="tickets['+id+'][]"]').length == 0)
				window.parent.jQuery('#content'+id).remove();
			else 
				window.parent.jQuery('#rsepro_quantity'+id).text(window.parent.jQuery('input[name^="tickets['+id+'][]"]').length);
		}
	}
}

function rsepro_reset_tickets(text) {
	jQuery('.rsepro_selected').removeClass('rsepro_selected');
	jQuery('input[id^="rsepro_unlimited"]').val('');
	jQuery('input[name="simple_tickets[]"]').val('');
	
	window.parent.jQuery('#jform_ide').val(0);
	window.parent.jQuery('#rsepro_selected_tickets_view').text('');
	window.parent.jQuery('#rsepro_selected_tickets').text('');
	window.parent.jQuery('#rsepro_simple_tickets').text('');
}

function rsepro_update_total() {
	tickets = '&dummy=1';

	jQuery('span[id^="rsepro_quantity"]').each(function () {
		tickets += '&tickets['+ parseInt(jQuery(this).prop('id').replace('rsepro_quantity','')) + ']='+parseInt(jQuery(this).text());
	});
	
	rse_calculatetotal(tickets);
}

function rse_calculatetotal(tickets) {
	var params = 'task=total';
	
	if (typeof tickets != 'undefined') {
		params += tickets;
	} else {
		var ticketId = jQuery('#RSEProTickets').length ? jQuery('#RSEProTickets').val() : jQuery('#ticket').val();
		if (jQuery('#from').val() == 0) {
			var numberOfTickets = jQuery('#numberinp').val();
		} else {
			var numberOfTickets = jQuery('#number').val();
		}
		
		// Multiple tickets
		if (jQuery('#hiddentickets').length) {
			var ticketsstring = '';
			jQuery('#hiddentickets input').each(function () {
				ticketsstring += '&' + jQuery(this).prop('name') + '=' + jQuery(this).val();
			});
			
			params += ticketsstring;
		} else {
			params += '&tickets['+ticketId+']='+numberOfTickets;
		}
	}
	
	params += '&randomTime=' + Math.random();
	
	jQuery.ajax({
		url: 'index.php?option=com_rseventspro',
		type: 'post',
		data: params
	}).done(function( response ) {
		var start = response.indexOf('RS_DELIMITER0') + 13;
		var end = response.indexOf('RS_DELIMITER1');
		response = response.substring(start, end);
		jQuery('#grandtotal').text(response);
	});
}

function rsepro_confirm_subscriber(id,token) {
	jQuery('#subscriptionConfirm').css('display','');
	
	jQuery.ajax({
		url: 'index.php?option=com_rseventspro',
		type: 'post',
		data: 'task=subscriptions.confirmsubscriber&id=' + id + '&' + token + '=1&randomTime='+Math.random()
	}).done(function( response ) {
		var start = response.indexOf('RS_DELIMITER0') + 13;
		var end = response.indexOf('RS_DELIMITER1');
		response = response.substring(start, end);
		
		if (response == '1') {
			jQuery('#confirm'+id).text(Joomla.JText._('COM_RSEVENTSPRO_SUBSCRIBER_CONFIRMED'));
		}
	});
}

function rsepro_select_all(id, fid) {
	if (jQuery('#'+id).prop('checked')) {
		jQuery('#'+fid+' input').prop('checked',true);
	} else {
		jQuery('#'+fid+' input').prop('checked',false);
	}
}

function rsepro_backup(step) {
	var progress		= jQuery('#rsepro-backup');
	var progress_bar	= progress.find('.bar');
	var progress_label	= progress.find('.progress-label');
	var button			= jQuery('#rsepro-backup-btn');
	
	progress.addClass('progress-striped active');
	progress_bar.removeClass('bar-success');
	
	if (step == 0) {
		progress_bar.css('width', 0);
	}
	
	button.prop('disabled', true);
	
	jQuery.ajax({
		url: 'index.php?option=com_rseventspro',
		type: 'post',
		dataType: 'json',
		data: 'task=backup&step=' + step
	}).done(function( response ) {
		progress_bar.css('width', response.percentage + '%'); 
		progress_label.html(response.percentage + '% ');
		
		if (response.percentage < 100) {
			rsepro_backup(response.nextstep);
		} else {
			progress.removeClass('progress-striped active');
			progress_bar.addClass('bar-success');
			button.prop('disabled', false);
			
			var tr  = jQuery('<tr>');
			var td1 = jQuery('<td>');
			var td2 = jQuery('<td>');
			var td3 = jQuery('<td>', {class: 'center', align: 'center'});
			var a   = jQuery('<a>', { href: response.download }).html(response.name);
			var b1	= jQuery('<button>', {class: 'btn', type: 'button'}).html(Joomla.JText._('COM_RSEVENTSPRO_BACKUP_OVERWRITE_RESTORE'));
			var b2	= jQuery('<button>', {class: 'btn', type: 'button'}).html(Joomla.JText._('COM_RSEVENTSPRO_BACKUP_RESTORE'));
			var b3	= jQuery('<button>', {class: 'btn btn-danger', type: 'button'}).html(Joomla.JText._('COM_RSEVENTSPRO_GLOBAL_DELETE_BTN'));
			
			b1.on('click', function() {
				rsepro_backup_restore(response.name,0);
			});
			
			b2.on('click', function() {
				rsepro_backup_restore(response.name,1);
			});
			
			b3.on('click', function() {
				rsepro_backup_delete(response.name, b3);
			});
			
			td1.html(a);
			td2.html(response.date);
			td3.append(b1);
			td3.append('&nbsp;');
			td3.append(b2);
			td3.append('&nbsp;');
			td3.append(b3);
			
			tr.append(td1);
			tr.append(td2);
			tr.append(td3);
			
			jQuery('#rsepro-local-backups').prepend(tr);
			
			window.location = response.download;
		}
	});
}

function rsepro_backup_delete(file, what) {
	jQuery.ajax({
		url: 'index.php?option=com_rseventspro',
		type: 'post',
		dataType: 'json',
		data: 'task=backupdelete&file=' + file
	}).done(function( response ) {
		if (response.success == true) {
			jQuery(what).parents('tr').remove();
		}
	});
}

function rsepro_backup_restore(file, overwrite) {
	jQuery('#backuprestore > li > a[href="#restore"]').click();
	jQuery('#backuprestore dt.restore').click();
	
	if (overwrite) {
		jQuery('#rsepro-overwrite').prop('checked', true);
		jQuery('#rsepro-overwrite').parents('label').addClass('disabled');
	}
	
	jQuery('#local').val(file);
	jQuery('#rsepro-restore-btn').click();
	jQuery('#rsepro-restore-btn').prop('disabled', true);
}

function rsepro_restore(hash, step, offset, count) {
	var progress		= jQuery('#rsepro-backup');
	var progress_bar	= progress.find('.bar');
	var progress_label	= progress.find('.progress-label');
	var button 			= jQuery('#rsepro-restore-btn');
	
	progress.addClass('progress-striped active');
	progress_bar.removeClass('bar-success');
	
	if (step == 0) {
		progress_bar.css('width', 0);
	}
	
	button.prop('disabled', true);
	
	if (rsepro_restore_overwrite) {
		jQuery('#rsepro-overwrite').prop('checked', true);
		jQuery('#rsepro-overwrite').prop('disabled', true);
		jQuery('#rsepro-overwrite').parents('label').addClass('disabled');
	}
	
	jQuery.ajax({
		url: 'index.php?option=com_rseventspro',
		type: 'post',
		dataType: 'json',
		data: 'task=restore&hash='+ hash +'&step=' + step + '&offset=' + offset + '&count=' + count
	}).done(function( response ) {
		progress_bar.css('width', response.percentage + '%'); 
		progress_label.html(response.percentage + '% ');
		
		if (response.percentage < 100) {
			rsepro_restore(hash, response.step, response.offset, response.count);
		} else {
			button.prop('disabled', false);
			progress.removeClass('progress-striped active');
			progress_bar.addClass('bar-success');
			
			if (rsepro_restore_overwrite) {
				jQuery('#rsepro-overwrite').prop('disabled', false);
				jQuery('#rsepro-overwrite').parents('label').removeClass('disabled');
			}
		}
	});
}

function rsepro_generate_string() {
	var i = 0; var string = '';

	while(i<5) {
		string += String.fromCharCode(65 + Math.round(Math.random() * 25));
		string += Math.floor(Math.random()*11);
		i++;
	}
	
	jQuery('#jform_code').val(string);
}

function rsepro_discount_assignment() {
	if (jQuery('#jform_apply_to').val() == '1') {
		jQuery('#events').css('display','none');
	} else {
		jQuery('#events').css('display','');
	}
}

/****** DEPRECATED ******/
function rs_stop() {}
function rs_search() {}
function rs_add_loc() {}