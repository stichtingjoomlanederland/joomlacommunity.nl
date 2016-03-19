jQuery(document).ready(function() {
	if (jQuery('#rs_repeats_control').length) {
		if (parseInt(jQuery('#rs_repeats').prop('scrollHeight')) > 70)
			jQuery('#rs_repeats_control').css('display','');
	}
	
	if (jQuery('#numberinp').length) {
		jQuery('#numberinp').on('keyup', function() {
			jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
		});
	}
});

function rse_calculatetotal(tickets,type) {
	var params = 'task=total&idevent=' + parseInt(jQuery('#eventID').text());
	rse_root = typeof rsepro_root != 'undefined' ? rsepro_root : '';
	
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
	
	if (jQuery('#coupon').length) {
		params += '&coupon=' + jQuery('#coupon').val();
	}
	if (jQuery('#RSEProCoupon').length) {
		params += '&coupon=' + jQuery('#RSEProCoupon').val();
	}
	
	if (jQuery('select[name=payment]').length) {
		params += '&payment=' + jQuery('#payment').val();
	} else if (jQuery('input[name=payment]').length) {
		if (jQuery('input[name=payment]:checked').length)
			params += '&payment=' + jQuery('input[name=payment]:checked').val();
	}
	
	if (jQuery('select[name^="form[RSEProPayment]"]').length) {
		params += '&payment=' + jQuery('#RSEProPayment').val();
	} else if (jQuery('input[name="form[RSEProPayment]"]').length) {
		if (jQuery('input[name="form[RSEProPayment]"]:checked').length)
			params += '&payment=' + jQuery('input[name="form[RSEProPayment]"]:checked').val();
	}
	
	if (type == 'json') {
		params += '&type=json';
		jQuery.ajaxSetup({dataType: 'json'});
	}
	
	params += '&randomTime=' + Math.random();
	
	jQuery.ajax({
		url: rse_root + 'index.php?option=com_rseventspro',
		type: 'post',
		data: params
	}).done(function( response ) {
		if (type == 'json') {
			if (typeof response.discount != 'undefined') {
				jQuery('#rsepro-cart-discount').css('display','');
				jQuery('#rsepro-cart-discount').find('td:nth-child(2)').text('- ' + response.discount);
				jQuery('#rsepro-cart-discount').find('td:nth-child(1) span').html('<br /><small>' + response.discountname + '</small>');
			} else {
				jQuery('#rsepro-cart-discount').css('display','none');
				jQuery('#rsepro-cart-discount').find('td:nth-child(2)').text('');
				jQuery('#rsepro-cart-discount').find('td:nth-child(1) span').text('');
			}
			
			if (typeof response.earlybooking != 'undefined') {
				jQuery('#rsepro-cart-earlybooking').css('display','');
				jQuery('#rsepro-cart-earlybooking').find('td:nth-child(2)').text('- ' + response.earlybooking);
			} else {
				jQuery('#rsepro-cart-earlybooking').css('display','none');
				jQuery('#rsepro-cart-earlybooking').find('td:nth-child(2)').text('');
			}
			
			if (typeof response.latefee != 'undefined') {
				jQuery('#rsepro-cart-latefee').css('display','');
				jQuery('#rsepro-cart-latefee').find('td:nth-child(2)').text(response.latefee);
			} else {
				jQuery('#rsepro-cart-latefee').css('display','none');
				jQuery('#rsepro-cart-latefee').find('td:nth-child(2)').text('');
			}
			
			if (typeof response.tax != 'undefined') {
				jQuery('#rsepro-cart-tax').css('display','');
				jQuery('#rsepro-cart-tax').find('td:nth-child(2)').text(response.tax);
			} else {
				jQuery('#rsepro-cart-tax').css('display','none');
				jQuery('#rsepro-cart-tax').find('td:nth-child(2)').text('');
			}
			
			if (typeof response.total != 'undefined') {
				jQuery('#rsepro-cart-total').find('td:nth-child(2)').text(response.total);
			}
		} else {
			var start = response.indexOf('RS_DELIMITER0') + 13;
			var end = response.indexOf('RS_DELIMITER1');
			response = response.substring(start, end);
			response = response.split('|');
			
			if (response[0] != 0) {
				jQuery('#grandtotalcontainer').css('display','');
				jQuery('#grandtotal').html(response[0]);
			} else {
				jQuery('#grandtotalcontainer').css('display','none');
				jQuery('#grandtotal').text(0);
			}
			
			if (response[1] != '') {
				jQuery('#paymentinfocontainer').css('display','');
				jQuery('#paymentinfo').html(response[1]);
			} else {
				jQuery('#paymentinfocontainer').css('display','none');
				jQuery('#paymentinfo').text('');
			}
		}
	});
}

function rsepro_description_on(id) {
	jQuery('#rsehref'+id).css('display','none');
	jQuery('#rsedescription'+id).removeClass('rsepro_extra_off').addClass('rsepro_extra_on');
}

function rsepro_description_off(id) {
	jQuery('#rsehref'+id).css('display','inline');
	jQuery('#rsedescription'+id).removeClass('rsepro_extra_on').addClass('rsepro_extra_off');
}

function rs_add_option(theoption) {
	jQuery('#rseprosearch').val(theoption);
	jQuery('#rs_results').css('display','none');
}

function rs_add_filter() {
	if (jQuery('#rseprosearch').val() != '')
		document.adminForm.submit();
}

function rs_clear_filters() {
	jQuery('#rs_clear').val(1);
	document.adminForm.submit();
}

function rs_remove_filter(key) {
	jQuery('#rs_remove').val(key);
	document.adminForm.submit();
}

/**
 *	Verify coupon
 */
function rse_verify_coupon(ide, coupon) {
	if (coupon == '') {
		return false;
	}
	
	rse_root = typeof rsepro_root != 'undefined' ? rsepro_root : '';
	
	jQuery.ajax({
		url: rse_root + 'index.php?option=com_rseventspro',
		type: 'post',
		dataType: 'html',
		data: 'task=verify&id=' + ide + '&coupon=' + coupon
	}).done(function( response ) {
		var start = response.indexOf('RS_DELIMITER0') + 13;
		var end = response.indexOf('RS_DELIMITER1');
		response = response.substring(start, end);
		alert(response);
	});
}

/**
 *	Events pagination
 */
function rspagination(tpl,limitstart,ide) {
	jQuery('#rs_loader').css('display','');
	
	if (tpl == 'day' || tpl == 'week')
		var params = 'view=calendar&layout=items&tpl='+tpl+'&format=raw&limitstart='+ limitstart;
	else
		var params = 'view=rseventspro&layout=items&tpl='+tpl+'&format=raw&limitstart='+ limitstart;
	
	if (parseInt(jQuery('#parent').text()) > 0) {
		params += '&parent=' + parseInt(jQuery('#parent').text());
	}
	
	if (jQuery('#date').text() != '') {
		params += '&date=' + jQuery('#date').text();
	}
	
	if (ide) {
		params += '&id=' + parseInt(ide);
	}
	
	params += '&Itemid=' + parseInt(jQuery('#Itemid').text());
	params += '&randomTime=' + Math.random();
	
	if (jQuery('#langcode').length && jQuery('#langcode').val().length > 0) {
		params += '&lang=' + jQuery('#langcode').text();
	}
	
	rse_root = typeof rsepro_root != 'undefined' ? rsepro_root : '';
	
	jQuery.ajax({
		url: rse_root + 'index.php?option=com_rseventspro',
		type: 'post',
		dataType: 'html',
		data: params
	}).done(function( response ) {
		var start = response.indexOf('RS_DELIMITER0') + 13;
		var end = response.indexOf('RS_DELIMITER1');
		response = response.substring(start, end);
		
		jQuery('#rs_events_container').append(response);
		jQuery('#rs_loader').css('display','none');
		
		if (jQuery('#rs_events_container li[class!="rsepro-month-year"]').length > 0 && (tpl == 'events' || tpl == 'locations' || tpl == 'subscribers' || tpl == 'day' || tpl == 'week')) {
			jQuery('#rs_events_container li[class!="rsepro-month-year"]').on('mouseenter', function() {
				jQuery(this).find('div.rs_options').css('display','');
			});
			
			jQuery('#rs_events_container li[class!="rsepro-month-year"]').on('mouseleave', function() {
				jQuery(this).find('div.rs_options').css('display','none');
			});
		}
		
		if ((jQuery('#rs_events_container').children('li[class!="rsepro-month-year"]').length) >= parseInt(jQuery('#total').text())) {
			jQuery('#rsepro_loadmore').css('display','none');
		}
	});
}

/**
 *	Deprecated
 *	Rate event
 */
function rsepro_feedback(val,id) {}

/**
 *	Get ticket information
 */
function rs_get_ticket(what) {
	if (jQuery(what).prop('id') == 'numberinp' || jQuery(what).prop('id') == 'number') {
		return;
	}
	
	jQuery('#rs_loader').css('display','');
	var rse_root = typeof rsepro_root != 'undefined' ? rsepro_root : '';
	var ticketId = jQuery('#RSEProTickets').length ? jQuery('#RSEProTickets').val() : jQuery('#ticket').val();
	
	jQuery.ajax({
		url: rse_root + 'index.php?option=com_rseventspro',
		type: 'post',
		dataType: 'html',
		data: 'task=tickets&id=' + ticketId + '&randomTime='+Math.random()
	}).done(function( response ) {
		var start = response.indexOf('RS_DELIMITER0') + 13;
		var end = response.indexOf('RS_DELIMITER1');
		response = response.substring(start, end);
		response = response.split('|');
		
		jQuery('#rs_loader').css('display','none');
		
		if (parseInt(response[0]) == 0) {
			jQuery('#numberinp').css('display','');
			jQuery('#number').css('display','none');
			jQuery('#numberinp').val(1);
			jQuery('#from').val(0);
		} else {
			jQuery('#numberinp').css('display','none');
			jQuery('#number').css('display','');
			jQuery('#from').val(1);
			
			jQuery('#number option').remove();
			for(i=1; i <= parseInt(response[0]); i++) {
				jQuery('#number').append(jQuery('<option>', { 'text': i, 'value': i }));
			}
		}
		
		jQuery('#tdescription').html(response[1]);
		
		if (!jQuery('#rsepro-cart-details').length){
			rse_calculatetotal();
		}
	});
}

/**
 *	Subscriber validation
 */
function svalidation() {
	ret = true;
	msg = new Array();
	
	if (jQuery('#name').val() == '') {
		ret = false; 
		jQuery('#name').addClass('invalid'); 
		msg.push(smessage[0]); 
	} else {
		jQuery('#name').removeClass('invalid');
	}
	
	if (jQuery('#email').val() == '') { 
		ret = false; 
		jQuery('#email').addClass('invalid');
		msg.push(smessage[1]); 
	} else { 
		jQuery('#email').removeClass('invalid'); 
	}
	
	if (jQuery('#hiddentickets').length && jQuery('#hiddentickets').html() == '') {
		ret = false; msg.push(smessage[3]); 
	}
	
	if (jQuery('#rsepro_selected_tickets').length && jQuery('#rsepro_selected_tickets').html() == '') { 
		ret = false; msg.push(smessage[3]); 
	}
	
	if (!rse_validateEmail(jQuery('#email').val())) { 
		ret = false; 
		jQuery('#email').addClass('invalid');
		msg.push(smessage[4]);
	} else { 
		jQuery('#email').removeClass('invalid');
	}
	
	if (ret) {
		return true;
	} else {
		alert(msg.join("\n"));
		return false;
	}
}

function rsepro_validate_subscription() {
	ret = true;
	msg = new Array();
	
	if (jQuery('#name').val() == '') {
		ret = false; 
		jQuery('#name').addClass('invalid'); 
		msg.push(smessage[0]); 
	} else {
		jQuery('#name').removeClass('invalid');
	}
	
	if (jQuery('#email').val() == '') { 
		ret = false; 
		jQuery('#email').addClass('invalid');
		msg.push(smessage[1]); 
	} else { 
		jQuery('#email').removeClass('invalid'); 
	}
	
	if (!rse_validateEmail(jQuery('#email').val())) { 
		ret = false; 
		jQuery('#email').addClass('invalid');
		msg.push(smessage[4]);
	} else { 
		jQuery('#email').removeClass('invalid');
	}
	
	if (jQuery('#rsepro-cart-details').length && jQuery('.rsepro-cart-ticket').length == 0) {
		ret = false; 
		msg.push(smessage[3]); 
	}
	
	if (ret) {
		return true;
	} else {
		alert(msg.join("\n"));
		return false;
	}
}

/**
 *	Email validation
 */	
function rse_validateEmail(email) {
	var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return regex.test(email);
}

/**
 *	Add ticket to subscription
 */
function rs_add_ticket() {
	var container		= jQuery('#tickets');
	var hidden_tickets	= jQuery('#hiddentickets');
	var ticket			= jQuery('#RSEProTickets').length ? jQuery('#RSEProTickets') : jQuery('#ticket');
	var ticket_number	= parseInt(jQuery('#from').val()) == 0 ? parseInt(jQuery('#numberinp').val()) : parseInt(jQuery('#number').val());
	var ticket_id		= ticket.val();
	var ticket_name		= ticket.find('option:selected').text();
	
	if (ticket_number == 0) ticket_number = 1;
	
	if (parseInt(jQuery('#from').val()) == 1) {
		var available_per_user = jQuery('#number option').length;
		
		if (jQuery('#tickets'+ticket_id).length) {
			if (parseInt(jQuery('#tickets'+ticket_id).val()) + ticket_number > available_per_user) {
				alert(smessage[6].replace('%d',available_per_user));
				return;
			}
		}
	}
	
	if (jQuery('#hiddentickets').length && typeof maxtickets != 'undefined' && typeof usedtickets != 'undefined') {
		var total = 0;
		jQuery('#hiddentickets input').each(function() {
			total += parseInt(jQuery(this).val());
		});
		
		total += ticket_number;
		totalAvailable = parseInt(maxtickets) - parseInt(usedtickets);
		
		if (total > totalAvailable) {
			alert(smessage[5]);
			return;
		}
	}
	
	if (jQuery('#tickets'+ticket_id).length == 0) {
		var input = jQuery('<input>', {
			type: 'hidden',
			name: 'tickets['+ticket_id+']',
			id:	  'tickets'+ticket_id,
			value: ticket_number
		});
		
		var span = jQuery('<span>', {
			id: 'content'+ticket_id,
			html: '<span id="ticketq'+ ticket_id +'">' + ticket_number + '</span>' + ' x ' + ticket_name + ' <a href="javascript:void(0);" onclick="rs_remove_ticket('+ ticket_id +')"> ('+smessage[2]+')</a><br/>'
		});
		
		hidden_tickets.append(input);
		container.append(span);
	} else {
		jQuery('#ticketq'+ticket_id).html(parseInt(jQuery('#ticketq'+ticket_id).text()) + parseInt(ticket_number));
		jQuery('#tickets'+ticket_id).val(parseInt(jQuery('#tickets'+ticket_id).val()) + parseInt(ticket_number));
	}
	
	rse_calculatetotal();
}

/**
 *	Remove ticket from subscription
 */
function rs_remove_ticket(theid) {
	if (jQuery('#tickets'+theid).length) {
		jQuery('#content'+theid).remove();
		jQuery('#tickets'+theid).remove();
		rse_calculatetotal();
	}
}

/**
 *	Send message to guests validation
 */
function rs_send_guests() {
	var ret = true;
	
	if (jQuery('#subject').val() == '') {
		ret = false; 
		jQuery('#subject').addClass('invalid');
	} else { 
		jQuery('#subject').removeClass('invalid');
	}
	
	if (jQuery('#denied').prop('checked') == false && jQuery('#pending').prop('checked') == false && jQuery('#accepted').prop('checked') == false && !jQuery('#subscribers :selected').length) {
		ret = false;
		jQuery('#a_option').addClass('invalid');
		jQuery('#d_option').addClass('invalid');
		jQuery('#p_option').addClass('invalid');
		jQuery('#subscribers').addClass('invalid')
	} else {
		jQuery('#a_option').removeClass('invalid');
		jQuery('#d_option').removeClass('invalid');
		jQuery('#p_option').removeClass('invalid');
		jQuery('#subscribers').removeClass('invalid')
	}
	
	return ret;
}

/**
 *	Invite validation
 */
function rs_invite() {
	var ret = true;
	
	if (jQuery('#jform_from').val() == '') { 
		ret = false; 
		jQuery('#jform_from').addClass('invalid');
	} else { 
		jQuery('#jform_from').removeClass('invalid');
	}
	
	if (jQuery('#jform_from_name').val() == '') { 
		ret = false; 
		jQuery('#jform_from_name').addClass('invalid');
	} else { 
		jQuery('#jform_from_name').removeClass('invalid');
	}
	
	if (jQuery('#emails').val() == '') { 
		ret = false; 
		jQuery('#emails').addClass('invalid'); 
	} else { 
		jQuery('#emails').removeClass('invalid');
	}
	
	if (ret) {
		document.adminForm.submit();
	}
}

/**
 *	Get Gmail email addresses
 */
function rs_google_contacts(token) {
	jQuery.ajax({
		url: 'https://www.google.com/m8/feeds/contacts/default/full?alt=json&access_token=' + token.access_token,
		dataType: 'json'
	}).done(function( response ) {
		jQuery(response.feed.entry).each(function() {
			jQuery('#emails').append(jQuery(jQuery(this).prop('gd$email')).prop('address') + "\n");
		});
	});
}

/**
 *	Verify captcha
 */
function checkcaptcha() {
	rse_root = typeof rsepro_root != 'undefined' ? rsepro_root : '';
	var params = 'task=checkcaptcha';
	
	if (jQuery('#g-recaptcha-response').length != 0) {
		params += '&recaptcha=' + jQuery('#g-recaptcha-response').val();
	} else if (jQuery('#secret').length != 0) {
		params += '&secret=' + jQuery('#secret').val();
	}
	
	params += '&randomTime='+Math.random();
	
	if (jQuery('#g-recaptcha-response').length != 0) {
		if (jQuery('#g-recaptcha-response').val() == '') {
			jQuery('#g-recaptcha-response').prev().addClass('invalid');
			return;
		} else {
			jQuery('#g-recaptcha-response').prev().removeClass('invalid');
		}
	} else if (jQuery('#secret').length != 0) {
		if (jQuery('#secret').val() == '') {
			jQuery('#secret').addClass('invalid');
			return;
		} else {
			jQuery('#secret').removeClass('invalid');
		}
	}
	
	jQuery.ajax({
		url: rse_root + 'index.php?option=com_rseventspro',
		type: 'post',
		dataType: 'html',
		data: params
	}).done(function( response ) {
		var start = response.indexOf('RS_DELIMITER0') + 13;
		var end = response.indexOf('RS_DELIMITER1');
		response = response.substring(start, end);
		
		if (parseInt(response)) {
			if (jQuery('#g-recaptcha-response').length != 0) {
				jQuery('#g-recaptcha-response').prev().removeClass('invalid');
			} else if (jQuery('#secret').length != 0) {
				jQuery('#secret').removeClass('invalid');
			}
			
			rs_invite();
		} else  {
			if (jQuery('#g-recaptcha-response').length != 0) {
				jQuery('#g-recaptcha-response').prev().addClass('invalid');
				grecaptcha.reset();
			} else if (jQuery('#secret').length != 0) {
				jQuery('#secret').addClass('invalid');
				reloadCaptcha();
				jQuery('#secret').val('');
			}
		}
	});
}

/**
 *	Reload captcha
 */	
function reloadCaptcha() {
	jQuery('#captcha').prop('src', jQuery('#captcha').prop('src') + '?' + Math.random());
}

/**
 *	Add calendar filter
 */	
function rs_calendar_add_filter(name, search) {
	if (name != 0) {
		if (parseInt(search) == 1) {
			jQuery('#rsepro-filter-from > ul > li > a[rel=categories]').click();
			jQuery('#rsepro-filter-condition > ul > li > a[rel=is]').click();
			jQuery('#rsepro-filter').val(name);
			jQuery('.rsepro-filter-filters input[value=categories]').parent().parent().remove();
			jQuery('#rsepro-filter-btn').click();
		} else {
			jQuery('#filter_from').val('categories');
			jQuery('#filter_condition').val('is');
			jQuery('#rseprosearch').val(name);
			jQuery('#adminForm').submit();
		}
	} else {
		if (parseInt(search) == 1) {
			jQuery('input[name="filter_from[]"]').each(function() {
				if (jQuery(this).val() == 'categories') {
					jQuery(this).parents('li').remove();
				}
			});
		} else {
			jQuery('#filter_from').val('');
			jQuery('#filter_condition').val('');
			jQuery('#rseprosearch').val('');
		}
		jQuery('#adminForm').submit();
	}
}

/**
 *	Credit card validation
 */	
function cc_validate(card_message,ccv_message) {
	var ret = true;
	var message = '';
	var cc_number = jQuery('#cc_number');
	var cc_ccv = jQuery('#cc_ccv');
	var firstname = jQuery('#firstname');
	var lastname = jQuery('#lastname');
	
	if (cc_number.val().length < 13 || cc_number.val().length > 16) { 
		ret = false; 
		message += card_message + "\n"; 
		cc_number.addClass('invalid');
	} else { 
		cc_number.removeClass('invalid');
	}
	
	if (cc_ccv.val().length < 3 || cc_ccv.val().length > 4) { 
		ret = false; 
		message += ccv_message + "\n"; 
		cc_ccv.addClass('invalid'); 
	} else { 
		cc_ccv.removeClass('invalid');
	}
	
	if (firstname.val() == '') { 
		ret = false; 
		firstname.addClass('invalid');
	} else { 
		firstname.removeClass('invalid');
	}
	
	if (lastname.val() == '') { 
		ret = false; 
		lastname.addClass('invalid');
	} else { 
		lastname.removeClass('invalid');
	}
	
	if (message.length != 0) {
		alert(message);
	}
	
	return ret;
}
	
/**
 *	Allow only numeric values
 */	
function rs_check_card(what) {
	what.value = what.value.replace(/[^0-9]+/g, '');
}

/**
 *	Credit card validation
 */	
function rs_cc_form() {
	var has_error  = false;
	var cc_number  = jQuery('#cc_number');
	var csc_number = jQuery('#cc_csc');
	var cc_fname   = jQuery('#cc_fname');
	var cc_lname   = jQuery('#cc_lname');
	
	if (cc_number.val().length < 14 || cc_number.val().length > 19) {
		cc_number.addClass('invalid');
		has_error = true;
	} else {
		cc_number.removeClass('invalid');
	}	
	
	if (csc_number.val().length < 3) {
		csc_number.addClass('invalid');
		has_error = true;
	} else {
		csc_number.removeClass('invalid');
	}
	
	if (cc_fname.val().length == 0) {
		cc_fname.addClass('invalid');
		has_error = true;
	} else {
		cc_fname.removeClass('invalid');
	}
	
	if (cc_lname.val().length == 0) {
		cc_lname.addClass('invalid');
		has_error = true;
	} else {
		cc_lname.removeClass('invalid');
	}
	
	return has_error ? false : true;
}

/**
 *	Calendar month change
 */	
function rs_calendar(root,month,year,module) {
	jQuery('#rscalendarmonth'+module).css('display','none');
	jQuery('#rscalendar'+module).css('display','');
	
	jQuery.ajax({
		url: root + 'index.php?option=com_rseventspro',
		type: 'post',
		dataType: 'html',
		data: 'view=calendar&layout=module&format=raw&month=' + month + '&year=' + year + '&mid=' + module + '&randomTime='+Math.random()
	}).done(function( response ) {
		var start = response.indexOf('RS_DELIMITER0') + 13;
		var end = response.indexOf('RS_DELIMITER1');
		response = response.substring(start, end);
		
		jQuery('#rs_calendar_module'+module).html(response);
		jQuery('#rscalendarmonth'+module).css('display','');
		jQuery('#rscalendar'+module).css('display','none');
		
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
	});
}

/**
 *	Add selected location
 */	
function rs_add_loc() {}

/**
 *	Show more details
 */	
function show_more() {
	jQuery('#less').css('display','');
	jQuery('#more').css('display','none');
	jQuery('#rs_repeats').css('height','auto');
}

/**
 *	Show less details
 */		
function show_less() {
	jQuery('#less').css('display','none');
	jQuery('#more').css('display','');
	jQuery('#rs_repeats').css('height','70px');
}

function rsepro_add_ticket(id, place, tname, tprice) {
	if (window.dialogArguments) {
		var thedocument = window.dialogArguments;
	} else {
		var thedocument = window.opener || window.parent;
	}
	
	var seat_container			= jQuery('#rsepro_seat_'+id+place);
	var selected_container		= thedocument.jQuery('#rsepro_selected_tickets');
	var selected_view_container	= thedocument.jQuery('#rsepro_selected_tickets_view');
	
	available_per_user = eval('ticket_limit_'+id);
	selected = thedocument.jQuery('#rsepro_selected_tickets input[name^="tickets['+id+']"]').length;
	
	// Check if we are allowed to buy multiple tickets 
	if (thedocument.multitickets == 0) {
		var ticketids = new Array();
		
		// Get tickets
		thedocument.jQuery('#rsepro_selected_tickets input[name^="tickets["]').each(function() {
			theid = jQuery(this).prop('name').replace('tickets[','').replace('][]','');
			if (ticketids.indexOf(theid) == -1) {
				ticketids.push(theid);
			}
		});
		
		// Get free tickets
		thedocument.jQuery('#rsepro_selected_tickets input[name^="unlimited["]').each(function() {
			theid = jQuery(this).prop('name').replace('unlimited[','').replace(']','');
			if (ticketids.indexOf(theid) == -1) {
				ticketids.push(theid);
			}
		});
		
		if (ticketids.length > 0 && ticketids.indexOf(id) == -1) {
			if (!thedocument.jQuery('#rsepro_selected_tickets input[name^="unlimited["]').length) {
				jQuery('input[id^="rsepro_unlimited_"]').each(function() {
					jQuery(this).val('');
				});
			}
			
			alert(thedocument.smessage[7]);
			return;
		}
	}
	
	// We are dealing with unlimited tickets
	if (place == 0) {
		if (thedocument.jQuery('#ticket'+id+place).length) {
			if (jQuery('#rsepro_unlimited_'+id).val() == 0 || jQuery('#rsepro_unlimited_'+id).val() == '') {
				thedocument.jQuery('#ticket'+id+place).remove();
			} else {
				if (typeof thedocument.maxtickets != 'undefined' && typeof thedocument.usedtickets != 'undefined') {
					var maxticketsAvailable = thedocument.maxtickets - thedocument.usedtickets;
					var thetotal = parseInt(jQuery('.rsepro_selected').length);
					jQuery('input[id^="rsepro_unlimited_"]').each(function() {
						if (jQuery(this).val() != '')
							thetotal += parseInt(jQuery(this).val());
					});
					
					if (thetotal > maxticketsAvailable) {
						alert(thedocument.smessage[5]);
						return;
					}
				}
				
				// Check for tickets quantity limit
				if (jQuery('#rsepro_unlimited_'+id).val() > available_per_user) {
					jQuery('#rsepro_unlimited_'+id).val(available_per_user);
					alert(thedocument.smessage[6].replace('%d',available_per_user));
					return;
				}
				
				thedocument.jQuery('#ticket'+id+place).val(jQuery('#rsepro_unlimited_'+id).val());
			}
		} else {
			if (jQuery('#rsepro_unlimited_'+id).val() != 0 || jQuery('#rsepro_unlimited_'+id).val() != '') {
				
				if (typeof thedocument.maxtickets != 'undefined' && typeof thedocument.usedtickets != 'undefined') {
					var maxticketsAvailable = thedocument.maxtickets - thedocument.usedtickets;
					var thetotal = parseInt(jQuery('.rsepro_selected').length);
					jQuery('input[id^="rsepro_unlimited_"]').each(function() {
						if (jQuery(this).val() != '')
							thetotal += parseInt(jQuery(this).val());
					});
					
					if (thetotal > maxticketsAvailable) {
						alert(thedocument.smessage[5]);
						return;
					}
				}
				
				// Check for tickets quantity limit
				if (jQuery('#rsepro_unlimited_'+id).val() > available_per_user) {
					alert(thedocument.smessage[6].replace('%d',available_per_user));
					jQuery('#rsepro_unlimited_'+id).val(available_per_user);
					return;
				}
				
				if (jQuery('#rsepro_unlimited_'+id).val() > 0) {
					var input = jQuery('<input>', {
						type: 'hidden',
						name: 'unlimited['+id+']',
						id: 'ticket'+id+place
					}).val(jQuery('#rsepro_unlimited_'+id).val());
					selected_container.append(input);
				}
			}
		}
	} else {
		if (seat_container.hasClass('rsepro_selected')) {
			// Deselect ticket
			seat_container.removeClass('rsepro_selected')
			
			if (thedocument.jQuery('#ticket'+id+place).length) {
				thedocument.jQuery('#ticket'+id+place).remove();
			}
			
		} else {
			
			if (typeof thedocument.maxtickets != 'undefined' && typeof thedocument.usedtickets != 'undefined') {
				var maxticketsAvailable = thedocument.maxtickets - thedocument.usedtickets;
				var thetotal = parseInt(jQuery('.rsepro_selected').length);
				jQuery('input[id^="rsepro_unlimited_"]').each(function() {
					if (jQuery(this).val() != '')
						thetotal += parseInt(jQuery(this).val());
				});
				
				if (thetotal >= maxticketsAvailable) {
					alert(thedocument.smessage[5]);
					return;
				}
			}
			
			// Check for tickets quantity limit
			if (selected + 1 > available_per_user) {
				alert(thedocument.smessage[6].replace('%d',available_per_user));
				return;
			}
			
			seat_container.addClass('rsepro_selected');
			
			
			var input = jQuery('<input>', {
				type: 'hidden',
				name: 'tickets['+id+'][]',
				id: 'ticket'+id+place
			}).val(place);
			selected_container.append(input);
		}
	}
	
	if (thedocument.jQuery('#content'+id).length == 0) {
		if (place == 0) {
			if (jQuery('#rsepro_unlimited_'+id).val() > 0)
				selected_view_container.append('<span id="content'+id+'"><span id="rsepro_quantity'+id+'">'+ jQuery('#rsepro_unlimited_'+id).val() +'</span> x ' + decodeURIComponent(tname) + ' (' + tprice + ') <br /> </span>');
		} else {
			selected_view_container.append('<span id="content'+id+'"><span id="rsepro_quantity'+id+'">'+ thedocument.jQuery('input[name^="tickets['+id+'][]"]').length +'</span> x ' + decodeURIComponent(tname) + ' (' + tprice + ') <span id="rsepro_seats'+id+'"></span><br /> </span>');
		}
	} else {
		if (place == 0) {
			if (jQuery('#rsepro_unlimited_'+id).val() == 0)
				thedocument.jQuery('#content'+id).remove();
			else 
				thedocument.jQuery('#rsepro_quantity'+id).text(jQuery('#rsepro_unlimited_'+id).val());
		} else {
			if (thedocument.jQuery('input[name^="tickets['+id+'][]"]').length == 0)
				thedocument.jQuery('#content'+id).remove();
			else 
				thedocument.jQuery('#rsepro_quantity'+id).text(thedocument.jQuery('input[name^="tickets['+id+'][]"]').length);
		}
	}
	
	if (thedocument.jQuery('#rsepro_seats' + id).length) {
		var seats = [];
		for (var t = 0; t < thedocument.jQuery('input[name^="tickets['+id+'][]"]').length; t++) {
			seats.push(jQuery(thedocument.jQuery('input[name^="tickets['+id+'][]"]')[t]).val());
		}
		thedocument.jQuery('#rsepro_seats' + id).html(Joomla.JText._('COM_RSEVENTSPRO_SEATS').replace('%s', seats.join(', ')));
	}
	
	var total = 0;

	thedocument.jQuery('span[id^="rsepro_quantity"]').each(function() {
		total += parseInt(jQuery(this).text());
	});
	
	if (total > 0)
		thedocument.jQuery('#rsepro_cart').html(total + ' ' + Joomla.JText._('COM_RSEVENTSPRO_TICKETS'));
	else
		thedocument.jQuery('#rsepro_cart').html(Joomla.JText._('COM_RSEVENTSPRO_SELECT_TICKETS'));
	
	thedocument.rsepro_update_total();
}

function rsepro_reset_tickets(text) {
	if (window.dialogArguments) {
		var thedocument = window.dialogArguments;
	} else {
		var thedocument = window.opener || window.parent;
	}
	
	jQuery('.rsepro_selected').removeClass('rsepro_selected');
	jQuery('input[id^="rsepro_unlimited"]').each(function() {
		jQuery(this).val('');
	});
	
	thedocument.jQuery('#rsepro_selected_tickets_view').html('');
	thedocument.jQuery('#rsepro_selected_tickets').html('')
	thedocument.jQuery('#rsepro_cart').html(text);
	thedocument.rsepro_update_total();
}

function rsepro_update_total() {
	tickets = '&dummy=1';

	jQuery('span[id^="rsepro_quantity"]').each(function() {
		tickets += '&tickets['+ parseInt(jQuery(this).prop('id').replace('rsepro_quantity','')) + ']='+parseInt(jQuery(this).text());
	});
	
	rse_calculatetotal(tickets);
}

function ajaxValidationRSEventsPro(task, formId, data) {
	if (task == 'beforeSend') {
		data.params.push('cid=' + encodeURIComponent(document.getElementById('eventID').innerHTML));
	}
}

function rsepro_validate_report() {
	if (jQuery('#jform_report').val() == '') {
		jQuery('#jform_report').addClass('invalid');
		return false;
	}
	
	jQuery('#jform_report').removeClass('invalid');
	return true;
}

function rsepro_confirm_subscriber(id,token) {
	rse_root = typeof rsepro_root != 'undefined' ? rsepro_root : '';
	jQuery('#subscriptionConfirm').css('display','');
	
	jQuery.ajax({
		url: rse_root + 'index.php?option=com_rseventspro',
		type: 'post',
		dataType: 'html',
		data: 'task=rseventspro.confirmsubscriber&id=' + id + '&' + token + '=1&randomTime='+Math.random()
	}).done(function( response ) {
		var start = response.indexOf('RS_DELIMITER0') + 13;
		var end = response.indexOf('RS_DELIMITER1');
		response = response.substring(start, end);
		
		if (response == '1') {
			jQuery('#confirm'+id).html(Joomla.JText._('COM_RSEVENTSPRO_SUBSCRIBER_CONFIRMED'));
		}
	});
}

function rsepro_add_single_ticket(what) {
	var rse_root	= typeof rsepro_root != 'undefined' ? rsepro_root : '';
	var ticket_id	= jQuery('#ticket').val();
	
	jQuery.ajax({
		url: rse_root + 'index.php?option=com_rseventspro',
		type: 'post',
		dataType: 'json',
		data: 'task=singleticket&id=' + ticket_id
	}).done(function( response ) {
		if (typeof response.seats != 'undefined') {
			if (jQuery(what).prop('id') != 'numberinp' && jQuery(what).prop('id') !== 'number') {
				if (parseInt(response.seats) == 0) {
					jQuery('#numberinp').css('display','');
					jQuery('#number').css('display','none');
					jQuery('#numberinp').val(1);
					jQuery('#from').val(0);
				} else {
					jQuery('#numberinp').css('display','none');
					jQuery('#number').css('display','');
					jQuery('#from').val(1);
					
					jQuery('#number option').remove();
					for(i=1; i <= parseInt(response.seats); i++) {
						jQuery('#number').append(jQuery('<option>', { 'text': i, 'value': i }));
					}
				}
			}
		}
		
		var quantity = parseInt(jQuery('#from').val()) == 0 ? parseInt(jQuery('#numberinp').val()) : parseInt(jQuery('#number').val());
		var total	 = quantity * response.tprice;
		total = number_format(total, response.payment_decimals, response.payment_decimal, response.payment_thousands);
		
		jQuery('.rsepro-cart-ticket').remove();
		
		var tr	= jQuery('<tr>').addClass('rsepro-cart-ticket');
		var td1	= jQuery('<td>').html('<span>' + quantity + '</span> x ' + response.name + ' ( ' + response.price + ' )<br /> <small>' + response.description + '</small>');
		var td2	= jQuery('<td>').text(response.mask.replace('{price}',total));
		var td3	= jQuery('<td>');
		
		tr.append(td1);
		tr.append(td2);
		tr.append(td3);
		tr.insertBefore(jQuery('#rsepro-cart-discount'));
		
		rse_calculatetotal('&tickets['+ ticket_id + ']=' + parseInt(quantity),'json');
	});
}

function rsepro_add_multiple_tickets() {
	var quantity	= parseInt(jQuery('#from').val()) == 0 ? parseInt(jQuery('#numberinp').val()) : parseInt(jQuery('#number').val());
	var ticket_id	= jQuery('#ticket').val();
	var rse_root	= typeof rsepro_root != 'undefined' ? rsepro_root : '';
	
	if (quantity == 0) {
		quantity = 1;
	}
	
	if (parseInt(jQuery('#from').val()) == 1) {
		var available_per_user = jQuery('#number option').length;
		
		if (jQuery('#tickets'+ticket_id).length) {
			if (parseInt(jQuery('#tickets'+ticket_id).val()) + quantity > available_per_user) {
				alert(smessage[6].replace('%d',available_per_user));
				return;
			}
		}
	}
	
	if (jQuery('#rsepro-cart-details').length && typeof maxtickets != 'undefined' && typeof usedtickets != 'undefined') {
		var total = 0;
		jQuery('.rsepro-cart-ticket input').each(function() {
			total += parseInt(jQuery(this).val());
		});
		
		total += quantity;
		totalAvailable = parseInt(maxtickets) - parseInt(usedtickets);
		
		if (total > totalAvailable) {
			alert(smessage[5]);
			return;
		}
	}
	
	jQuery.ajax({
		url: rse_root + 'index.php?option=com_rseventspro',
		type: 'post',
		dataType: 'json',
		data: 'task=ticket&id=' + ticket_id
	}).done(function( response ) {
		if (response) {
			if (jQuery('#tickets'+ticket_id).length == 0) {
				
				var total	 = quantity * response.tprice;
				total = number_format(total, response.payment_decimals, response.payment_decimal, response.payment_thousands);
				
				var tr	= jQuery('<tr>').addClass('rsepro-cart-ticket');
				var td1	= jQuery('<td>').html('<span>' + quantity + '</span> x ' + response.name + ' ( ' + response.price + ' ) <br /> <small>' + response.description + '</small>');
				var td2	= jQuery('<td>').text(response.mask.replace('{price}',total));
				var td3	= jQuery('<td>');
				
				var remove = jQuery('<a>', {
					href: 'javascript:void(0)'
				}).text('(X)').on('click', function() {
					jQuery(this).parent().parent().remove();
					rsepro_multi_total();
				});
				
				var input = jQuery('<input>', {
					type: 'hidden',
					name: 'tickets['+ticket_id+']',
					id:	  'tickets'+ticket_id,
					value: quantity
				});
				
				td1.append(input);
				td3.append(remove);
				tr.append(td1);
				tr.append(td2);
				tr.append(td3);
				tr.insertBefore(jQuery('#rsepro-cart-discount'));
				
			} else {
				var ticketsQ = parseInt(jQuery('#tickets'+ticket_id).val()) + parseInt(quantity);
				jQuery('#tickets'+ticket_id).parent().find('span').text(ticketsQ);
				jQuery('#tickets'+ticket_id).val(ticketsQ);
				
				var total	 = ticketsQ * response.tprice;
				total = number_format(total, response.payment_decimals, response.payment_decimal, response.payment_thousands);
				jQuery('#tickets'+ticket_id).parent().parent().find('td:nth-child(2)').html(response.mask.replace('{price}',total));
			}
			
			var tickets = new Array();
			jQuery('#rsepro-cart-details input').each(function() {
				tickets.push(jQuery(this).prop('name')+ '=' + jQuery(this).val());
			});
			
			rse_calculatetotal('&'+tickets.join('&'),'json');
		}
	});
}

function rsepro_add_ticket_seats(id, place) {
	if (window.dialogArguments) {
		var thedocument = window.dialogArguments;
	} else {
		var thedocument = window.opener || window.parent;
	}
	
	var rse_root				= typeof rsepro_root != 'undefined' ? rsepro_root : '';
	var seat_container			= jQuery('#rsepro_seat_'+id+place);
	
	available_per_user = eval('ticket_limit_'+id);
	selected = thedocument.jQuery('#rsepro-cart-details input[name^="tickets['+id+']"]').length;
	
	// Check if we are allowed to buy multiple tickets 
	if (thedocument.multitickets == 0) {
		var ticketids = new Array();
		
		// Get tickets
		thedocument.jQuery('#rsepro-cart-details input[name^="tickets["]').each(function() {
			theid = jQuery(this).prop('name').replace('tickets[','').replace('][]','');
			if (ticketids.indexOf(theid) == -1) {
				ticketids.push(theid);
			}
		});
		
		// Get free tickets
		thedocument.jQuery('#rsepro-cart-details input[name^="unlimited["]').each(function() {
			theid = jQuery(this).prop('name').replace('unlimited[','').replace(']','');
			if (ticketids.indexOf(theid) == -1) {
				ticketids.push(theid);
			}
		});
		
		if (ticketids.length > 0 && ticketids.indexOf(id) == -1) {
			if (!thedocument.jQuery('#rsepro-cart-details input[name^="unlimited["]').length) {
				jQuery('input[id^="rsepro_unlimited_"]').each(function() {
					jQuery(this).val('');
				});
			}
			
			alert(thedocument.smessage[7]);
			return;
		}
	}
	
	jQuery.ajax({
		url: rse_root + 'index.php?option=com_rseventspro',
		type: 'post',
		dataType: 'json',
		data: 'task=ticket&id=' + id
	}).done(function( response ) {
		// Are we dealing with unlimited tickets ?
		if (place == 0) {
			if (thedocument.jQuery('#ticket'+id+place).length) {
				if (jQuery('#rsepro_unlimited_'+id).val() == 0 || jQuery('#rsepro_unlimited_'+id).val() == '') {
					thedocument.jQuery('#ticket'+id+place).parent().parent().remove();
				} else {
					if (typeof thedocument.maxtickets != 'undefined' && typeof thedocument.usedtickets != 'undefined') {
						var maxticketsAvailable = thedocument.maxtickets - thedocument.usedtickets;
						var thetotal = parseInt(jQuery('.rsepro_selected').length);
						jQuery('input[id^="rsepro_unlimited_"]').each(function() {
							if (jQuery(this).val() != '')
								thetotal += parseInt(jQuery(this).val());
						});
						
						if (thetotal > maxticketsAvailable) {
							alert(thedocument.smessage[5]);
							return;
						}
					}
					
					// Check for tickets quantity limit
					if (jQuery('#rsepro_unlimited_'+id).val() > available_per_user) {
						jQuery('#rsepro_unlimited_'+id).val(available_per_user);
						alert(thedocument.smessage[6].replace('%d',available_per_user));
						return;
					}
					
					thedocument.jQuery('#ticket'+id+place).val(jQuery('#rsepro_unlimited_'+id).val());
					thedocument.jQuery('#ticket'+id+place).parent().find('span').text(jQuery('#rsepro_unlimited_'+id).val());
					
					var total	 = parseInt(jQuery('#rsepro_unlimited_'+id).val()) * response.tprice;
					total = number_format(total, response.payment_decimals, response.payment_decimal, response.payment_thousands);
					thedocument.jQuery('#ticket'+id+place).parent().parent().find('td:nth-child(2)').html(response.mask.replace('{price}',total));
				}
			} else {
				if (jQuery('#rsepro_unlimited_'+id).val() != 0 || jQuery('#rsepro_unlimited_'+id).val() != '') {
					
					if (typeof thedocument.maxtickets != 'undefined' && typeof thedocument.usedtickets != 'undefined') {
						var maxticketsAvailable = thedocument.maxtickets - thedocument.usedtickets;
						var thetotal = parseInt(jQuery('.rsepro_selected').length);
						jQuery('input[id^="rsepro_unlimited_"]').each(function() {
							if (jQuery(this).val() != '')
								thetotal += parseInt(jQuery(this).val());
						});
						
						if (thetotal > maxticketsAvailable) {
							alert(thedocument.smessage[5]);
							return;
						}
					}
					
					// Check for tickets quantity limit
					if (jQuery('#rsepro_unlimited_'+id).val() > available_per_user) {
						alert(thedocument.smessage[6].replace('%d',available_per_user));
						jQuery('#rsepro_unlimited_'+id).val(available_per_user);
						return;
					}
					
					if (jQuery('#rsepro_unlimited_'+id).val() > 0) {
						var total	 = parseInt(jQuery('#rsepro_unlimited_'+id).val()) * response.tprice;
						total = number_format(total, response.payment_decimals, response.payment_decimal, response.payment_thousands);
						
						var tr	= thedocument.jQuery('<tr>').addClass('rsepro-cart-ticket');
						var td1	= thedocument.jQuery('<td>').html('<span>' + jQuery('#rsepro_unlimited_'+id).val() + '</span> x ' + response.name + ' ( ' + response.price + ' ) <br /> <small>' + response.description + '</small>');
						var td2	= thedocument.jQuery('<td>').text(response.mask.replace('{price}',total));
						var td3	= thedocument.jQuery('<td>');
						
						var remove = thedocument.jQuery('<a>', {
							href: 'javascript:void(0)'
						}).text('(X)').on('click', function() {
							jQuery(this).parent().parent().remove();
							thedocument.rsepro_multi_seats_total();
						});
						
						var input = thedocument.jQuery('<input>', {
							type: 'hidden',
							name: 'unlimited['+id+']',
							id: 'ticket'+id+place
						}).val(jQuery('#rsepro_unlimited_'+id).val());
						
						td1.append(input);
						td3.append(remove);
						tr.append(td1);
						tr.append(td2);
						tr.append(td3);
						
						tr.insertBefore(thedocument.jQuery('#rsepro-cart-discount'));
					}
				}
			}
		} else {
			if (seat_container.hasClass('rsepro_selected')) {
				// Deselect ticket
				seat_container.removeClass('rsepro_selected')
				
				if (thedocument.jQuery('#rsepro-seat-'+id).length) {
					if (thedocument.jQuery('#ticket'+id+place).length) {
						thedocument.jQuery('#ticket'+id+place).remove();
						var quantity = parseInt(thedocument.jQuery('#rsepro-seat-'+id+' td:nth-child(1)').find('span').text()) - 1;
						var total = quantity * response.tprice;
						total = number_format(total, response.payment_decimals, response.payment_decimal, response.payment_thousands);
						thedocument.jQuery('#rsepro-seat-'+id+' td:nth-child(1)').find('span').text(quantity);
						thedocument.jQuery('#rsepro-seat-'+id+' td:nth-child(2)').text(total);
						thedocument.rsepro_multi_seats_total();
					}
					
					if (thedocument.jQuery('#rsepro-seat-'+id+' input').length == 0) {
						thedocument.jQuery('#rsepro-seat-'+id).remove();
						thedocument.rsepro_multi_seats_total();
					}
				}
			} else {
				
				if (typeof thedocument.maxtickets != 'undefined' && typeof thedocument.usedtickets != 'undefined') {
					var maxticketsAvailable = thedocument.maxtickets - thedocument.usedtickets;
					var thetotal = parseInt(jQuery('.rsepro_selected').length);
					jQuery('input[id^="rsepro_unlimited_"]').each(function() {
						if (jQuery(this).val() != '')
							thetotal += parseInt(jQuery(this).val());
					});
					
					if (thetotal >= maxticketsAvailable) {
						alert(thedocument.smessage[5]);
						return;
					}
				}
				
				// Check for tickets quantity limit
				if (selected + 1 > available_per_user) {
					alert(thedocument.smessage[6].replace('%d',available_per_user));
					return;
				}
				
				seat_container.addClass('rsepro_selected');
				
				if (thedocument.jQuery('#rsepro-seat-'+id).length) {
					var quantity = parseInt(thedocument.jQuery('#rsepro-seat-'+id+' td:nth-child(1)').find('span').text()) + 1;
					var total = quantity * response.tprice;
					total = number_format(total, response.payment_decimals, response.payment_decimal, response.payment_thousands);
					thedocument.jQuery('#rsepro-seat-'+id+' td:nth-child(1)').find('span').text(quantity);
					thedocument.jQuery('#rsepro-seat-'+id+' td:nth-child(2)').text(total);
					
					var input = thedocument.jQuery('<input>', {
						type: 'hidden',
						name: 'tickets['+id+'][]',
						id: 'ticket'+id+place
					}).val(place);
					thedocument.jQuery('#rsepro-seat-'+id+' td:nth-child(1)').append(input);
					
				} else {
					var total	 = 1 * response.tprice;
					total = number_format(total, response.payment_decimals, response.payment_decimal, response.payment_thousands);
					
					var tr	= thedocument.jQuery('<tr>', { id: 'rsepro-seat-'+id}).addClass('rsepro-cart-ticket');
					var td1	= thedocument.jQuery('<td>').html('<span>1</span> x ' + response.name + ' ( ' + response.price + ' ) <span id="rsepro-seats-'+id+'"></span> <br /> <small>' + response.description + '</small>');
					var td2	= thedocument.jQuery('<td>').text(response.mask.replace('{price}',total));
					var td3	= thedocument.jQuery('<td>');
					
					var remove = thedocument.jQuery('<a>', {
						href: 'javascript:void(0)'
					}).text('(X)').on('click', function() {
						jQuery(this).parent().parent().remove();
						thedocument.rsepro_multi_seats_total();
					});
					
					var input = thedocument.jQuery('<input>', {
						type: 'hidden',
						name: 'tickets['+id+'][]',
						id: 'ticket'+id+place
					}).val(place);
					
					td1.append(input);
					td3.append(remove);
					tr.append(td1);
					tr.append(td2);
					tr.append(td3);
					tr.insertBefore(thedocument.jQuery('#rsepro-cart-discount'));
				}
			}
			
			if (thedocument.jQuery('#rsepro-seats-'+id).length) {
				var seats = [];
				for (var t = 0; t < thedocument.jQuery('input[name^="tickets['+id+'][]"]').length; t++) {
					seats.push(jQuery(thedocument.jQuery('input[name^="tickets['+id+'][]"]')[t]).val());
				}
				
				thedocument.jQuery('#rsepro-seats-'+id).html(Joomla.JText._('COM_RSEVENTSPRO_SEATS').replace('%s', seats.join(', ')));
			}
		}
	});
	
	thedocument.rsepro_multi_seats_total();
}

function rsepro_single_total() {
	var quantity	= parseInt(jQuery('#from').val()) == 0 ? parseInt(jQuery('#numberinp').val()) : parseInt(jQuery('#number').val());
	var ticket_id	= jQuery('#ticket').val();
	
	setTimeout(function () {
		rse_calculatetotal('&tickets['+ ticket_id + ']=' + parseInt(quantity),'json');
	},1000);
}

function rsepro_multi_total() {
	var tickets = new Array();
	
	tickets.push('dummy=1');
	jQuery('#rsepro-cart-details input').each(function() {
		tickets.push(jQuery(this).prop('name')+ '=' + jQuery(this).val());
	});
	
	rse_calculatetotal('&'+tickets.join('&'),'json');
}

function rsepro_multi_seats_total() {
	setTimeout(function () {
		var tickets = new Array();
		tickets.push('dummy=1');
	
		jQuery('#rsepro-cart-details input[name^="unlimited"]').each(function() {
			tickets.push('tickets['+jQuery(this).prop('name').replace('unlimited[','').replace(']','')+']='+jQuery(this).val())
		});
		
		jQuery('#rsepro-cart-details tr[id^="rsepro-seat-"]').each(function() {
			tickets.push('tickets['+jQuery(this).prop('id').replace('rsepro-seat-','')+']='+jQuery(this).find('input').length);
		});
		
		rse_calculatetotal('&'+tickets.join('&'),'json');
	},1000);
}

function rsepro_reset_tickets_seats() {
	if (window.dialogArguments) {
		var thedocument = window.dialogArguments;
	} else {
		var thedocument = window.opener || window.parent;
	}
	
	jQuery('.rsepro_selected').removeClass('rsepro_selected');
	jQuery('input[id^="rsepro_unlimited"]').each(function() {
		jQuery(this).val('');
	});
	
	thedocument.jQuery('#rsepro-cart-details .rsepro-cart-ticket').remove();
	thedocument.rsepro_multi_seats_total();
}

function number_format(number, decimals, dec_point, thousands_sep) {
  //  discuss at: http://phpjs.org/functions/number_format/
  // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: davook
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Theriault
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: Michael White (http://getsprink.com)
  // bugfixed by: Benjamin Lupton
  // bugfixed by: Allan Jensen (http://www.winternet.no)
  // bugfixed by: Howard Yeend
  // bugfixed by: Diogo Resende
  // bugfixed by: Rival
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  //  revised by: Luke Smith (http://lucassmith.name)
  //    input by: Kheang Hok Chin (http://www.distantia.ca/)
  //    input by: Jay Klehr
  //    input by: Amir Habibi (http://www.residence-mixte.com/)
  //    input by: Amirouche
  //   example 1: number_format(1234.56);
  //   returns 1: '1,235'
  //   example 2: number_format(1234.56, 2, ',', ' ');
  //   returns 2: '1 234,56'
  //   example 3: number_format(1234.5678, 2, '.', '');
  //   returns 3: '1234.57'
  //   example 4: number_format(67, 2, ',', '.');
  //   returns 4: '67,00'
  //   example 5: number_format(1000);
  //   returns 5: '1,000'
  //   example 6: number_format(67.311, 2);
  //   returns 6: '67.31'
  //   example 7: number_format(1000.55, 1);
  //   returns 7: '1,000.6'
  //   example 8: number_format(67000, 5, ',', '.');
  //   returns 8: '67.000,00000'
  //   example 9: number_format(0.9, 0);
  //   returns 9: '1'
  //  example 10: number_format('1.20', 2);
  //  returns 10: '1.20'
  //  example 11: number_format('1.20', 4);
  //  returns 11: '1.2000'
  //  example 12: number_format('1.2000', 3);
  //  returns 12: '1.200'
  //  example 13: number_format('1 000,50', 2, '.', ' ');
  //  returns 13: '100 050.00'
  //  example 14: number_format(1e-8, 8, '.', '');
  //  returns 14: '0.00000001'

  number = (number + '')
    .replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
      var k = Math.pow(10, prec);
      return '' + (Math.round(n * k) / k)
        .toFixed(prec);
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
    .split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '')
    .length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1)
      .join('0');
  }
  return s.join(dec);
}

var rs_tooltip = function(){
	var id = 'rs_tt';
	var top = 3;
	var left = 3;
	var maxw = 400;
	var speed = 10;
	var timer = 20;
	var endalpha = 95;
	var alpha = 0;
	var tt,t,c,b,h;
	var ie = document.all ? true : false;
	return{
		show:function(v,w){
			if(tt == null){
				tt = document.createElement('div');
				tt.setAttribute('id',id);
				t = document.createElement('div');
				t.setAttribute('id',id + 'top');
				c = document.createElement('div');
				c.setAttribute('id',id + 'cont');
				b = document.createElement('div');
				b.setAttribute('id',id + 'bot');
				tt.appendChild(t);
				tt.appendChild(c);
				tt.appendChild(b);
				document.body.appendChild(tt);
				tt.style.opacity = 0;
				tt.style.filter = 'alpha(opacity=0)';
				document.onmousemove = this.pos;
			}
			tt.style.display = 'block';
			c.innerHTML = document.getElementById(v).innerHTML;
			tt.style.width = w ? w + 'px' : 'auto';
			if(!w && ie){
				t.style.display = 'none';
				b.style.display = 'none';
				tt.style.width = tt.offsetWidth;
				t.style.display = 'block';
				b.style.display = 'block';
			}
			if(tt.offsetWidth > maxw){tt.style.width = maxw + 'px'}
			h = parseInt(tt.offsetHeight) + top;
			clearInterval(tt.timer);
			tt.timer = setInterval(function(){rs_tooltip.fade(1)},timer);
		},
		pos:function(e){
			var u = ie ? event.clientY + document.documentElement.scrollTop : e.pageY;
			var l = ie ? event.clientX + document.documentElement.scrollLeft : e.pageX;
			tt.style.top = (u - h) + 'px';
			tt.style.left = (l + left) + 'px';
		},
		fade:function(d){
			var a = alpha;
			if((a != endalpha && d == 1) || (a != 0 && d == -1)){
				var i = speed;
				if(endalpha - a < speed && d == 1){
					i = endalpha - a;
				}else if(alpha < speed && d == -1){
					i = a;
				}
				alpha = a + (i * d);
				tt.style.opacity = alpha * .01;
				tt.style.filter = 'alpha(opacity=' + alpha + ')';
			}else{
				clearInterval(tt.timer);
				if(d == -1){tt.style.display = 'none'}
			}
		},
		hide:function(){
			clearInterval(tt.timer);
			tt.timer = setInterval(function(){rs_tooltip.fade(-1)},timer);
		}
	};
}();

function rsmAddEvent(obj, evType, fn) {
	if (obj.addEventListener) {
		obj.addEventListener(evType, fn, false);
		return true;
	} else if (obj.attachEvent){
		var r = obj.attachEvent("on"+evType, fn);
		return r;
	} else {
		return false;
	}
}