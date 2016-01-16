window.addEvent('domready', function(){
	// Autocompleter
	if (isset($('frontend'))) {
		new RSAutocompleter.Request.HTML('rs_location', 'index.php?option=com_rseventspro&task=locations', {
			'postVar': 'rs_location',
			minLength: 1,
			maxChoices: 100,
			autoSubmit: false,
			cache: true,
			delay: 250,
			onRequest: function() {
			  $('rs_location').addClass('rs_location_loading');
			},
			onComplete: function() {
			  $('rs_location').removeClass('rs_location_loading');
			}
		  });
	} else {
		new RSAutocompleter.Request.HTML('rs_location', 'index.php?option=com_rseventspro&task=locations', {
			'postVar': 'rs_location',
			minLength: 1,
			maxChoices: 100,
			autoSubmit: false,
			cache: true,
			delay: 250,
			onRequest: function() {
			  $('rs_location').setStyles({
				'background':'url("components/com_rseventspro/assets/images/loading.gif") no-repeat scroll 99% center transparent'
			  });
			},
			onComplete: function() {
			  $('rs_location').setStyle('background','');
			}
		  });
	}
	
	// Z-index fix
	if(Browser.ie7) {
		var zIndexNumber = 1000;
		$$('fieldset').each(function(el,i){
			el.setStyle('z-index',zIndexNumber);
			zIndexNumber -= 10;
		});
	};
	
	$('rs_photo').addEvents({
		mouseenter: function(){     
			$('rs_add_photo').setStyle('display', 'block'); 
		},
		mouseleave: function(){      
			$('rs_add_photo').setStyle('display', 'none');
		}
	});

	$('rs_add_photo').addEvents({
		mouseenter: function(){     
			$('rs_add_photo').setStyle('display', 'block'); 
		},
		mouseleave: function(){      
			$('rs_add_photo').setStyle('display', 'block');
		}
	});

	if (isset($('rs_check_recurring'))) {
		$('rs_check_recurring').addEvent('click', function(event){
			if ($('rs_check_recurring').checked == true) {
				$('rs_li_6').reveal({duration: 'short'});
				$('rs_right_1').dissolve();
				$('rs_menu_item_1').removeClass('active');
				$('rs_menu_item_6').addClass('active');
				$('rs_right_6').reveal();
				$('apply_changes').checked = true;
			} else {
				$('rs_li_6').dissolve();			
				$('rs_right_6').dissolve();
				$('apply_changes').checked = false;
			}
		});
	}

  $('rs_check_registration').addEvent('click', function(event) {
		if ($('rs_check_registration').checked == true) {
			$('rs_li_7').reveal({duration: 'short'});
			$('rs_right_1').setStyle('display', 'none');
			$('rs_menu_item_1').removeClass('active');
			$('rs_menu_item_7').addClass('active');
			$('rs_right_7').reveal();
			$('rs_li_8').reveal({duration: 'short'});
			
			$$('li[id^=rs_li_t]').each(function (el){
				if (el.get('id') != 'rs_li_tc')
					$(el.get('id')).reveal({duration: 'short'});
			});
			
			if ($('rs_check_ticketsconfig').checked) {
				$('rs_li_tc').reveal({duration: 'short'});
			}
			
			if ($('rs_check_discounts').checked == true) {
				$('rs_li_9').reveal({duration: 'short'});
				$('rs_li_10').reveal({duration: 'short'});
				
				$$('li[id^=rs_li_c]').each(function (el){
					$(el.get('id')).reveal({duration: 'short'});
				});
			}
		} else {
			$('rs_li_7').dissolve();
			$('rs_right_7').setStyle('display', 'none');
			$('rs_li_8').dissolve();
			$('rs_right_8').setStyle('display', 'none');
			
			$$('li[id^=rs_li_t]').each(function (el){
				$(el.get('id')).dissolve();
			});
			
			$$('div[id^=rs_right_t]').each(function (el){
				$(el.get('id')).setStyle('display', 'none');
			});
			
			$('rs_li_9').dissolve();
			$('rs_right_9').setStyle('display', 'none');
			$('rs_li_10').dissolve();
			$('rs_right_10').setStyle('display', 'none');
			
			$$('li[id^=rs_li_c]').each(function (el){
				$(el.get('id')).dissolve();
			});
			
			$$('div[id^=rs_right_c]').each(function (el){
				$(el.get('id')).setStyle('display', 'none');
			});			
		}
	});

	$('rs_check_discounts').addEvent('click', function(event) {
		if ($('rs_check_discounts').checked == true) {
			$('rs_li_9').reveal({duration: 'short'});
			$('rs_right_7').setStyle('display', 'none');
			$('rs_menu_item_7').removeClass('active');
			$('rs_menu_item_9').addClass('active');
			$('rs_right_9').reveal();
			$('rs_li_10').reveal({duration: 'short'});
			
			$$('li[id^=rs_li_c]').each(function (el){
				$(el.get('id')).reveal({duration: 'short'});
			});
		} else {
			$('rs_li_9').dissolve();
			$('rs_right_9').setStyle('display', 'none');
			$('rs_li_10').dissolve();
			$('rs_right_10').setStyle('display', 'none');
			
			$$('li[id^=rs_li_c]').each(function (el){
				$(el.get('id')).dissolve();
			});
			
			$$('div[id^=rs_right_c]').each(function (el){
				$(el.get('id')).setStyle('display', 'none');
			});
		}
	});

	$('rs_check_ticketsconfig').addEvent('click', function(event) {
		
		if ($('overbooking').checked) {
			$('rs_check_ticketsconfig').checked = false;
			alert(Joomla.JText._('COM_RSEVENTSPRO_NO_OVERBOOKING_TICKETS_CONFIG'));
			return;
		}
		
		if ($('rs_check_ticketsconfig').checked == true) {
			$('rs_li_tc').reveal({duration: 'short'});
		} else {
			$('rs_li_tc').dissolve();
		}
	});

	if (isset($('rs_add_more'))) {
		$('rs_add_more').addEvent('click', function(event){
			pElement = new Element('p');
			inputElement = new Element('input',{
				'type' : 'file',
				'name' : 'files[]',
				'class': 'rs_inp rs_inp_file'
			});
		
			$('rs_files').adopt(pElement.adopt(inputElement));
		});
	}

	$$('#rs_event_menu li').each(function (el) {
		el.addEvent('click', function(e){
			$$('#rs_event_menu li a').each(function (ela){
				ela.removeClass('active');
			});
		
			currentid = el.get('id');
			thecurrentid = currentid.replace('rs_li_','');
			
			if (thecurrentid != 'tc' ) {
				$$('.rs_right').each(function (eld){
					eld.setStyle('display', 'none');
				});				
				
				$('rs_right_' + thecurrentid).reveal({duration: 'short'});
				$('rs_menu_item_' + thecurrentid).addClass('active');
			} else {
				$('rs_menu_item_' + thecurrentid).addClass('active');
			}
			
		});
	});
	
	if (isset($('rs_repeats'))) {
		repeatsFX = new Fx.Slide('rs_repeats', {
			duration: 1000,
			transition: Fx.Transitions.Pow.easeOut
		});
		
		repeatsFX.hide();
	}
});

function rs_edit_allday(what) {
	if (what.checked) {
		$('enddate').style.display = 'none';
		
		if ($('is12').value == 1) {
			$('start').value = $('start').value.split(' ')[0];
			$('start_dummy').value = $('start_dummy').value.split(' ')[0];
			$('end').value = '';
			$('end_dummy').value = '';
			
			Calendar.setup({
				inputField	:	"start_dummy",
				ifFormat	:	"%Y-%m-%d",
				button		:	"start_dummy_img",
				align		:	"Tl",
				showsTime	:	false,
				time24	:	false,
				onClose	:	function() { $('start').value = this.date.print("%Y-%m-%d"); this.hide(); },
				singleClick	:	true
			});
		} else {
			$('start').value = $('start').value.split(' ')[0];
			$('end').value = '';
			
			Calendar.setup({
				inputField	:	"start",
				ifFormat	:	"%Y-%m-%d",
				button		:	"start_img",
				align		:	"Tl",
				singleClick	:	true
			});
		}
	} else {
		$('enddate').style.display = '';
		
		if ($('is12').value == 1) {
			if ($('start').value.split(' ').length <= 1)
				$('start').value = $('start').value + ' 00:00:00';
			if ($('start_dummy').value.split(' ').length <= 1)
				$('start_dummy').value = $('start_dummy').value + ' 12:00:00 am';
			
			Calendar.setup({
				inputField	:	"start_dummy",
				ifFormat	:	"%Y-%m-%d %I:%M:%S %P",
				button		:	"start_dummy_img",
				align		:	"Tl",
				showsTime	:	true,
				time24	:	false,
				onClose	:	function() { $('start').value = this.date.print("%Y-%m-%d %H:%M:%S"); this.hide(); },
				singleClick	:	true
			});
			
		} else {
			if ($('start').value.split(' ').length <= 1)
				$('start').value = $('start').value + ' 00:00:00';
			
			Calendar.setup({
				inputField	:	"start",
				ifFormat	:	"%Y-%m-%d %H:%M:%S",
				button		:	"start_img",
				align		:	"Tl",
				singleClick	:	true
			});
		}
	}
}

function rs_show_repeats() {
	repeatsFX.toggle().chain(function() {
		if (repeatsFX.open) {
			$('repeatimg').removeClass('repeatimg_down').addClass('repeatimg_up');
		} else {
			$('repeatimg').removeClass('repeatimg_up').addClass('repeatimg_down');
		}
	});
}


/*
	Add a new ticket
*/
function rs_edit_addticket(unlimited, buttontext, error, remove, confirmmsg) {
	var ticket_name = $('ticket_name').value;
	var ticket_price = $('ticket_price').value;
	var ticket_seats = $('ticket_seats').value;
	var ticket_user_seats = $('ticket_user_seats').value;
	var ticket_description = $('ticket_description').value;
	var selectedarray = new Array();
	var eventID = $('eventID').value;
	
	if (ticket_name == '') {
		$('ticket_name').addClass('rse_error');
		alert(error);
		return;
	} else $('ticket_name').removeClass('rse_error');
	
	for (i=0; i < $('ticket_groups').options.length; i++) {
		if ($('ticket_groups').options[i].selected)
			selectedarray.push('groups[]=' + $('ticket_groups').options[i].value);
	}
	
	var groups = selectedarray.length > 0 ? '&' + selectedarray.join('&') : '';
	
	var req = new Request({
		method: 'post',
		url: 'index.php?option=com_rseventspro',
		onSuccess: function(responseText, responseXML) {
			var response = responseText;
			var start = response.indexOf('RS_DELIMITER0') + 13;
			var end = response.indexOf('RS_DELIMITER1');
			response = response.substring(start, end);
			
			// Create the li element
			theli = new Element('li', {
				id: 'rs_li_t'+response,
				'class': 'rs_display_none'
			});
			
			thea = new Element('a', {
				id: 'rs_menu_item_t'+response,
				'class': 'rs_title_3',
				href: 'javascript:void(0)',
				'text': ticket_name
			});
			
			thea.inject(theli);
			theli.inject($('rs_li_tc'), 'before');
			
			theli.reveal({duration: 'short'});
			$('rs_menu_item_8').removeClass('active');
			$('rs_menu_item_t'+response).addClass('active');
			
			
			// Create the tab div
			thediv = new Element('div', {
				id: 'rs_right_t'+response,
				'class': 'rs_right rs_display_none'
			});
			
			thefieldset = new Element('fieldset');
			thelegend = new Element('legend', { 'text': ticket_name });
			thebutton = new Element('button',{
				type: 'button',
				'class': 'rs_button rs_submit',
				text: buttontext,
				events: {
					click: function(){
						validateRSForm();
					}
				}
			});
			
			removebutton = new Element('button',{
				type: 'button',
				'class': 'rs_button rs_submit',
				text: remove,
				events: {
					click: function(){
						if (confirm(confirmmsg))
							rs_edit_removeticket(response);
					}
				}
			});
			
			thep1 	= new Element('p');
			thep2 	= new Element('p');
			thep3 	= new Element('p');
			thep4 	= new Element('p');
			thep5 	= new Element('p');
			thediv1	= new Element('div', { 'class': 'rs_period' });
			
			thelabel1 = new Element('label', {'for': 'ticket_name'+response, 'text' : $('name_label').get('text')});
			thelabel2 = new Element('label', {'for': 'ticket_price'+response, 'text' : $('price_label').get('text')});
			thelabel3 = new Element('label', {'for': 'ticket_seats'+response, 'text' : $('seats_label').get('text')});
			thelabel4 = new Element('label', {'for': 'ticket_user_seats'+response, 'text' : $('user_seats_label').get('text')});
			thelabel5 = new Element('label', {'for': 'ticket_description'+response, 'text' : $('description_label').get('text')});
			
			theinput1 = new Element('input', {
				id: 'ticket_name'+response,
				name: 'tickets['+response+'][name]',
				'class': 'rs_inp',
				value: ticket_name,
				type: 'text'
			});
			
			ticket_price = ticket_price == '' ? 0 : ticket_price;
			theinput2 = new Element('input', {
				id: 'ticket_price'+response,
				name: 'tickets['+response+'][price]',
				'class': 'rs_inp',
				value: ticket_price,
				type: 'text',
				events: {
					keyup: function(){
						$(this).set('value', $(this).get('value').replace(/[^0-9\.\,]/g, ''));
					}
				}
			});
			
			ticket_seats = ticket_seats == '' ? unlimited : ticket_seats;
			ticket_user_seats = ticket_user_seats == '' ? unlimited : ticket_user_seats;
			
			theinput3 = new Element('input', {
				id: 'ticket_seats'+response,
				name: 'tickets['+response+'][seats]',
				'class': 'rs_inp',
				value: ticket_seats,
				type: 'text',
				events: {
					blur: function(){
						if ($(this).get('value') == '') $(this).set('value', unlimited);
					},
					focus: function(){
						if ($(this).get('value') == unlimited) $(this).set('value', '');
					},
					keyup: function(){
						$(this).set('value', $(this).get('value').replace(/[^0-9]/g, ''));
					}
				}
			});
			
			theinput4 = new Element('input', {
				id: 'ticket_user_seats'+response,
				name: 'tickets['+response+'][user_seats]',
				'class': 'rs_inp',
				value: ticket_user_seats,
				type: 'text',
				events: {
					blur: function(){
						if ($(this).get('value') == '') $(this).set('value', unlimited);
					},
					focus: function(){
						if ($(this).get('value') == unlimited) $(this).set('value', '');
					},
					keyup: function(){
						$(this).set('value', $(this).get('value').replace(/[^0-9]/g, ''));
					}
				}
			});
			
			thetextarea = new Element('textarea', {
				id: 'ticket_description'+response,
				name: 'tickets['+response+'][description]',
				'class': 'rs_txt',
				text: ticket_description
			});
			
			thelabel6 	= new Element('label', {'for': 'ticket_groups'+response, 'text' : $('tgroups_label').get('text')});
			thediv2 	= new Element('div', {'class': 'rs_ticket_groups_'+response });
			
			var thegroupselect = new Element('select', {'id': 'ticket_groups'+response, 'class': 'rschosen', 'multiple': 'multiple', 'name': 'tickets['+response+'][groups][]'});
			for (i=0; i < $('ticket_groups').options.length; i++) {
				thegroupselect.options[thegroupselect.options.length] = new Option($('ticket_groups').options[i].text, $('ticket_groups').options[i].value);
				if ($('ticket_groups').options[i].selected)
					thegroupselect.options[i].selected = true;
			}
			
			thegroupselect.inject(thediv2);
			thelabel6.inject(thediv1);
			thediv2.inject(thediv1);
			
			thelabel1.inject(thep1);
			theinput1.inject(thep1);
			thelabel2.inject(thep2);
			theinput2.inject(thep2);
			thelabel3.inject(thep3);
			theinput3.inject(thep3);
			thelabel4.inject(thep4);
			theinput4.inject(thep4);
			thelabel5.inject(thep5);
			thetextarea.inject(thep5);
			
			thelegend.inject(thefieldset);
			thep1.inject(thefieldset);
			thep2.inject(thefieldset);
			thep3.inject(thefieldset);
			thep4.inject(thefieldset);
			thediv1.inject(thefieldset);
			thep5.inject(thefieldset);
			removebutton.inject(thefieldset);
			thebutton.inject(thefieldset);
			
			thefieldset.inject(thediv);
			
			thediv.inject($('new_tickets'));
			$('rs_right_8').dissolve();
			thediv.reveal();
			
			theli.addEvent('click', function(e){
				
				$$('#rs_event_menu li a').each(function (ela){
					ela.removeClass('active');
				});
				
				$$('.rs_right').each(function (eld){
					eld.setStyle('display', 'none');
				});
				
				$('rs_right_t' + response).reveal({duration: 'short'});
				$('rs_menu_item_t' + response).addClass('active');
			});
			
			// Reset fields
			$('ticket_name').value = '';
			$('ticket_price').value = '';
			$('ticket_seats').value = unlimited;
			$('ticket_user_seats').value = unlimited;
			$('ticket_description').value = '';
			
			for (i=0; i < $('ticket_groups').options.length; i++) {
				$('ticket_groups').options[i].selected = false;
			}
			
			if (typeof Chosen != 'undefined')
				$('ticket_groups').fireEvent('liszt:updated');
			else 
				jQuery('#ticket_groups').trigger('liszt:updated');
			
			if (typeof Chosen != 'undefined')
				new Chosen($('ticket_groups'+response));
			else 
				jQuery('#ticket_groups'+response).chosen();
			
			
		}
	});
	
	if (isset($('frontend')))
		req.send('task=rseventspro.saveticket&jform[ide]=' + eventID + '&jform[name]='+ ticket_name + '&jform[price]='+ ticket_price + '&jform[seats]=' + ticket_seats + '&jform[user_seats]='+ ticket_user_seats + '&jform[description]='+ ticket_description + groups + '&randomTime='+Math.random());
	else
		req.send('task=savedata&type=ticket&jform[ide]=' + eventID + '&jform[name]='+ ticket_name + '&jform[price]='+ ticket_price + '&jform[seats]=' + ticket_seats + '&jform[user_seats]='+ ticket_user_seats + '&jform[description]='+ ticket_description + groups);
}

/*
	Add a new coupon
*/
function rs_edit_add_coupon(unlimited,buttontext,error,error1,remove,confirmmsg) {
	var coupon_name = $('coupon_name').value;
	var coupon_code = $('coupon_code').value;
	var coupon_start = $('coupon_start').value;
	var coupon_end = $('coupon_end').value;
	var coupon_usage = $('coupon_usage').value;
	var coupon_discount = $('coupon_discount').value;
	var coupon_type = $('coupon_type').value;
	var coupon_action = $('coupon_action').value;
	var eventID = $('eventID').value;
	var selectedarray = new Array();
	
	if (coupon_name == '')
	{
		$('coupon_name').addClass('rse_error');
		alert(error);
		return;
	} else $('coupon_name').removeClass('rse_error');
	
	if (coupon_discount == '')
	{
		$('coupon_discount').addClass('rse_error');
		alert(error1);
		return;
	} else $('coupon_discount').removeClass('rse_error');
	
	for (i=0; i < $('coupon_groups').options.length; i++) {
		if ($('coupon_groups').options[i].selected)
			selectedarray.push('groups[]=' + $('coupon_groups').options[i].value);
	}
		
	var groups = selectedarray.length > 0 ? '&' + selectedarray.join('&') : '';
	
	var req = new Request({
		url: 'index.php?option=com_rseventspro',
		method: 'post',
		onSuccess: function(responseText, responseXML) {
			var response = responseText;
			var start = response.indexOf('RS_DELIMITER0') + 13;
			var end = response.indexOf('RS_DELIMITER1');
			response = response.substring(start, end);
			
			// Create the li element
			theli = new Element('li', {
				id: 'rs_li_c'+response,
				'class': 'rs_display_none'
			});
			
			thea = new Element('a', {
				id: 'rs_menu_item_c'+response,
				'class': 'rs_title_3',
				href: 'javascript:void(0)',
				'text': coupon_name
			});
			
			thea.inject(theli);
			
			if (isset($('rs_li_6')))
				theli.inject($('rs_li_6'), 'before');
			else 
				theli.inject($('rs_li_3'), 'after');
			
			theli.reveal({duration: 'short'});
			$('rs_menu_item_10').removeClass('active');
			$('rs_menu_item_c'+response).addClass('active');
			
			// Create the tab div
			thediv = new Element('div', {
				id: 'rs_right_c'+response,
				'class': 'rs_right rs_display_none'
			});
			
			thefieldset = new Element('fieldset');
			thelegend = new Element('legend', { 'text': coupon_name });
			thebutton = new Element('button',{
				type: 'button',
				'class': 'rs_button rs_submit',
				text: buttontext,
				events: {
					click: function(){
						validateRSForm();
					}
				}
			});
			
			removebutton = new Element('button',{
				type: 'button',
				'class': 'rs_button rs_submit',
				text: remove,
				events: {
					click: function(){
						if (confirm(confirmmsg))
							rs_edit_remove_coupon(response);
					}
				}
			});
			
			// Create the main elements
			thep1 = new Element('p');
			thep2 = new Element('p');
			thediv1 = new Element('div', { 'class': 'rs_period' });
			thep3 = new Element('p');
			thediv2 = new Element('div', { 'class': 'rs_period' });
			thediv3 = new Element('div', { 'class': 'rs_period' });
			
			// Create the first element block
			thelabel1 = new Element('label', {'for': 'coupon_name'+response, 'text' : $('cname_label').get('text')});
			theinput1 = new Element('input', {
				id: 'coupon_name'+response,
				name: 'coupons['+response+'][name]',
				'class': 'rs_inp',
				value: coupon_name,
				type: 'text'
			});
			
			thelabel1.inject(thep1);
			theinput1.inject(thep1);
			
			// Create the second element block
			thelabel2 = new Element('label', {'for': 'coupon_code'+response, 'text' : $('ccode_label').get('text')});
			thetextarea = new Element('textarea', {
				id: 'coupon_code'+response,
				name: 'coupons['+response+'][code]',
				'class': 'rs_txt rs_txt_small',
				text: coupon_code
			});
			thespan1 = new Element('span', {'class': 'rs_currency', 'text' : $('cgenerate').get('text')})
			theinput2 = new Element('input', {
				id: 'coupon_times'+response,
				name: 'coupon_times'+response,
				'class': 'rs_inp_smaller',
				value: $('coupon_times').value,
				type: 'text',
				events: {
					keyup: function(){
						$(this).set('value', $(this).get('value').replace(/[^0-9]/g, ''));
					}
				}
			});
			thespan2 = new Element('span', {'class': 'rs_currency', 'text' : $('ccoupons').get('text')});
			if (isset($('frontend')) && $('frontend').value == 1) thespan2.setStyle('margin-right','11px')
			thea1 = new Element('a', {
				href: 'javascript:void(0)',
				'class': 'rs_generate_submit',
				text: $('coupon_href').get('text'),
				events: {
					click: function(){
						rs_generate('coupon_code'+response,$('coupon_times'+response).value);
					}
				}
			});
			
			thelabel2.inject(thep2);
			thetextarea.inject(thep2);
			thespan1.inject(thep2);
			theinput2.inject(thep2);
			thespan2.inject(thep2);
			thea1.inject(thep2);
			
			// Create the third element block
			thediv4 = new Element('div', { 'class': 'rs_calendar' });
			thelabel3 = new Element('label', {'for': 'coupon_start'+response, 'text' : $('cstart_label').get('text')});
			thediv5 = new Element('div', { 'class': 'rs_starting' });
			
			if (parseInt($('is12').value) == 1)
			{
				theinput3 = new Element('input', {
					id: 'coupon_start'+response+'_dummy',
					name: 'couponsfrom_dummy',
					'class': 'rs_inp',
					type: 'text',
					value: $('coupon_start_dummy').value,
					readonly: 'readonly'
				});
				thea2 = new Element('a', {
					href: 'javascript:void(0)',
					'class': 'rs_calendar_icon',
					id: 'rs_starting_calendar'
				});
				theimg1 = new Element('img', {
					alt: 'calendar',
					src: $('coupon_start_dummy_img').get('src'),
					id: 'couponsstart'+response+'_dummy_img'
				});
				hidden1 = new Element('input', {
					id: 'coupon_start'+response,
					name: 'coupons['+response+'][from]',
					'class': 'rs_inp',
					type: 'hidden',
					value: coupon_start
				});
			} else 
			{
				theinput3 = new Element('input', {
					id: 'coupon_start'+response,
					name: 'coupons['+response+'][from]',
					'class': 'rs_inp',
					type: 'text',
					value: coupon_start
				});
				thea2 = new Element('a', {
					href: 'javascript:void(0)',
					'class': 'rs_calendar_icon',
					id: 'rs_starting_calendar'
				});		
				theimg1 = new Element('img', {
					alt: 'calendar',
					src: $('coupon_start_img').get('src'),
					id: 'couponsstart'+response+'_img'
				});
			}
			
			theimg1.inject(thea2);
			theinput3.inject(thediv5);
			thea2.inject(thediv5);
			if (parseInt($('is12').value) == 1) hidden1.inject(thediv5);
			thelabel3.inject(thediv4);
			thediv5.inject(thediv4);
			thediv4.inject(thediv1);
			
			if (!isset($('frontend')))
			{
				thespan3 = new Element('span', {'class': 'rs_to', 'text' : $('coupon_end_text').get('text')});
				thespan3.inject(thediv1);
			}
			
			
			thediv6 = new Element('div', { 'class': 'rs_calendar' });
			
			if (!isset($('frontend')))
			{
				thelabel4 = new Element('label');
				thelabel4.appendChild(document.createTextNode('\u00A0'));
			} else 
			{
				thelabel4 = new Element('label', {'text' : $('coupon_end_text').get('text')});
			}
			
			thediv7 = new Element('div', { 'class': 'rs_starting', id: 'rs_ending_calendar' });
			
			if (parseInt($('is12').value) == 1)
			{
				theinput4 = new Element('input', {
					id: 'coupon_end'+response+'_dummy',
					name: 'couponsto_dummy',
					'class': 'rs_inp',
					type: 'text',
					value: $('coupon_end_dummy').value,
					readonly: 'readonly'
				});
				thea3 = new Element('a', {
					href: 'javascript:void(0)',
					'class': 'rs_calendar_icon',
					id: 'rs_starting_calendar'
				});
				theimg2 = new Element('img', {
					alt: 'calendar',
					src: $('coupon_end_dummy_img').get('src'),
					id: 'couponsend'+response+'_img'
				});
				hidden2 = new Element('input', {
					id: 'coupon_end'+response,
					name: 'coupons['+response+'][to]',
					'class': 'rs_inp',
					type: 'hidden',
					value: coupon_end
				});		
			} else
			{
				theinput4 = new Element('input', {
					id: 'coupon_end'+response,
					name: 'coupons['+response+'][to]',
					'class': 'rs_inp',
					type: 'text',
					value: coupon_end
				});
				thea3 = new Element('a', {
					href: 'javascript:void(0)',
					'class': 'rs_calendar_icon',
					id: 'rs_starting_calendar'
				});
				theimg2 = new Element('img', {
					alt: 'calendar',
					src: $('coupon_end_img').get('src'),
					id: 'couponsend'+response+'_img'
				});
			}
			
			theimg2.inject(thea3);
			theinput4.inject(thediv7);
			thea3.inject(thediv7);
			if (parseInt($('is12').value) == 1) hidden2.inject(thediv7);
			thelabel4.inject(thediv6);
			thediv7.inject(thediv6);
			thediv6.inject(thediv1);
			
			
			thelabel5 = new Element('label', {'for': 'coupon_usage'+response, 'text' : $('cusage_label').get('text')});
			theinput5 = new Element('input', {
				id: 'coupon_usage'+response,
				name: 'coupons['+response+'][usage]',
				'class': 'rs_inp',
				type: 'text',
				value: coupon_usage,
				events: {
					blur: function(){
						if ($(this).get('value') == '') $(this).set('value', unlimited);
					},
					focus: function(){
						if ($(this).get('value') == unlimited) $(this).set('value', '');
					},
					keyup: function(){
						$(this).set('value', $(this).get('value').replace(/[^0-9]/g, ''));
					}
				}
			});
			
			thelabel5.inject(thep3);
			theinput5.inject(thep3);
			
			thelabel6 = new Element('label', {'for': 'coupon_discount'+response, 'text' : $('cdiscount_label').get('text')});
			theinput6 = new Element('input', {
				id: 'coupon_discount'+response,
				name: 'coupons['+response+'][discount]',
				'class': 'rs_inp_smaller',
				type: 'text',
				value: coupon_discount
			});
			thespan4 = new Element('span', { 'class': 'rs_currency' });
			theselect1 = new Element('select', {
				id: 'coupon_type'+response,
				name: 'coupons['+response+'][type]',
				'class': 'rs_sel rs_sel_smaller',
				size: 1
			});
			
			
			for(i=0;i<$('coupon_type').options.length;i++)
			{
				thevalue = $($('coupon_type').options[i]).get('value');
				thetext = $($('coupon_type').options[i]).get('text');
				
				if (thevalue == coupon_type)
					theselect1.adopt( new Element('option', { value: thevalue, text: thetext, selected: 'selected' }) );
				else
					theselect1.adopt( new Element('option', { value: thevalue, text: thetext }) );
			}
			
			thespan5 = new Element('span', { text : $('coupon_end_text').get('text') });
			thespan5.set('style','margin-right:20px;');
			
			theselect2 = new Element('select', {
				id: 'coupon_action'+response,
				name: 'coupons['+response+'][action]',
				'class': 'rs_sel',
				size: 1
			});
			
			for(i=0;i<$('coupon_action').options.length;i++)
			{
				thevalue = $($('coupon_action').options[i]).get('value');
				thetext = $($('coupon_action').options[i]).get('text');
				
				if (thevalue == coupon_action)
					theselect2.adopt( new Element('option', { value: thevalue, text: thetext, selected: 'selected' }) );
				else
					theselect2.adopt( new Element('option', { value: thevalue, text: thetext }) );
			}
			
			thelabel6.inject(thediv2);
			theinput6.inject(thediv2);
			theselect1.inject(thespan4);
			thespan4.inject(thediv2);
			thespan5.inject(thediv2);
			theselect2.inject(thediv2);
			
			thelabel7 = new Element('label', {'for': 'coupon_groups'+response, 'text' : $('cgroups_label').get('text')});
			thediv8 = new Element('div', {'class': 'rs_coupon_groups_'+response });
			
			var thegroupselect = new Element('select', {'id': 'coupon_groups'+response, 'class': 'rschosen', 'multiple': 'multiple', 'name': 'coupons['+response+'][groups][]'});
			for (i=0; i < $('coupon_groups').options.length; i++) {
				thegroupselect.options[thegroupselect.options.length] = new Option($('coupon_groups').options[i].text, $('coupon_groups').options[i].value);
				if ($('coupon_groups').options[i].selected)
					thegroupselect.options[i].selected = true;
			}
			
			thegroupselect.inject(thediv8);
			thelabel7.inject(thediv3);
			thediv8.inject(thediv3);
			
			thelegend.inject(thefieldset);
			thep1.inject(thefieldset);
			thep2.inject(thefieldset);
			thediv1.inject(thefieldset);
			thep3.inject(thefieldset);
			thediv2.inject(thefieldset);
			thediv3.inject(thefieldset);
			thefieldset.inject(thediv);
			removebutton.inject(thediv);
			thebutton.inject(thediv);
			
			thediv.inject($('new_coupons'));
			$('rs_right_10').dissolve();
			thediv.reveal();
			
			theli.addEvent('click', function(e){
				
				$$('#rs_event_menu li a').each(function (ela){
					ela.removeClass('active');
				});
				
				$$('.rs_right').each(function (eld){
					eld.setStyle('display', 'none');
				});
				
				$('rs_right_c' + response).reveal({duration: 'short'});
				$('rs_menu_item_c' + response).addClass('active');
			});
			
			if (parseInt($('is12').value) == 1)
			{
				window.addEvent('domready', function() {Calendar.setup({
					inputField     :    'coupon_start'+response+'_dummy',
					ifFormat       :    '%Y-%m-%d %I:%M:%S %P',
					button         :    'couponsstart'+response+'_dummy_img',
					align          :    'Tl',
					showsTime	   :	true,
					time24		   :	false,
					onClose		   :	function() { $('coupon_start'+response).value = this.date.print("%Y-%m-%d %H:%M:%S"); this.hide(); },
					singleClick    :    true
				});});
				
				window.addEvent('domready', function() {Calendar.setup({
					inputField     :    'coupon_end'+response+'_dummy',
					ifFormat       :    '%Y-%m-%d %I:%M:%S %P',
					button         :    'couponsend'+response+'_dummy_img',
					align          :    'Tl',
					showsTime	   :	true,
					time24		   :	false,
					onClose		   :	function() { $('coupon_end'+response).value = this.date.print("%Y-%m-%d %H:%M:%S"); this.hide(); },
					singleClick    :    true
				});});
			} else
			{	
				window.addEvent('domready', function() {Calendar.setup({
					inputField     :    'coupon_start'+response,
					ifFormat       :    '%Y-%m-%d %H:%M:%S',
					button         :    'couponsstart'+response+'_img',
					align          :    'Tl',
					singleClick    :    true
				});});
				
				window.addEvent('domready', function() {Calendar.setup({
					inputField     :    'coupon_end'+response,
					ifFormat       :    '%Y-%m-%d %H:%M:%S',
					button         :    'couponsend'+response+'_img',
					align          :    'Tl',
					singleClick    :    true
				});});
			}
			
			//Reset fields
			$('coupon_name').value = '';
			$('coupon_code').value = '';
			$('coupon_times').value = 3;
			if (parseInt($('is12').value) == 1) $('coupon_start_dummy').value = '';
			$('coupon_start').value = '';
			if (parseInt($('is12').value) == 1) $('coupon_end_dummy').value = '';
			$('coupon_end').value = '';
			$('coupon_usage').value = unlimited;
			$('coupon_discount').value = '';
			$('coupon_type').value = 0;
			$('coupon_action').value = 0;
			
			for (i=0; i < $('coupon_groups').options.length; i++) {
				$('coupon_groups').options[i].selected = false;
			}
			
			if (typeof Chosen != 'undefined')
				$('coupon_groups').fireEvent('liszt:updated');
			else 
				jQuery('#coupon_groups').trigger('liszt:updated');
			
			if (typeof Chosen != 'undefined')
				new Chosen($('coupon_groups'+response));
			else 
				jQuery('#coupon_groups'+response).chosen();
		}
	});
	
	if (isset($('frontend')))
		req.send('task=rseventspro.savecoupon&jform[name]=' + coupon_name + '&jform[from]=' + coupon_start + '&jform[to]=' + coupon_end + '&jform[discount]=' +  coupon_discount + '&jform[type]=' + coupon_type + '&jform[action]=' + coupon_action + groups + '&jform[ide]=' + eventID + '&codes=' + coupon_code + '&randomTime='+Math.random());
	else
		req.send('task=savedata&type=coupon&jform[name]=' + coupon_name + '&jform[from]=' + coupon_start + '&jform[to]=' + coupon_end + '&jform[discount]=' +  coupon_discount + '&jform[type]=' + coupon_type + '&jform[action]=' + coupon_action + groups + '&jform[ide]=' + eventID + '&codes=' + coupon_code + '&randomTime='+Math.random());
}

/*
	Generate coupon codes
*/
function rs_generate(theid,thenumber) {
	var output = new Array();
	thenumber = parseInt(thenumber) == 0 ? 1 : parseInt(thenumber);
	
	for (j=0;j<thenumber;j++) {
		string = '';
		i = 0;
		while(i<5) {
			string += String.fromCharCode(65 + Math.round(Math.random() * 25));
			string += Math.floor(Math.random()*11);
			i++;
		}
		output.push(string);
	}
	
	$(theid).value = output.join("\n");
}

/*
	Add a selected date for repeating
*/
function rs_add_date() {
	to		= $('repeatalso');
	from	= $('repeat_date');
	add		= true;
	
	if (from.value == '') return;
	
	for (i=0; i < to.length; i++)
		if (to[i].value == from.value)
			add = false;
	
	if (add)
		to.options[to.options.length] = new Option( from.value, from.value, false, false);
	
	createRepeats();
}

/*
	Remove a date from repeating
*/
function rs_remove_dates() {
	from = $('repeatalso');
	
	if (from.options.selectedIndex == -1) return false;
	for(i=from.length-1; i>=0;i--)
		if (from.options[i].selected)
			from.remove(from.options[i].index);
	
	createRepeats();
}

/*
	Check what interval is on
*/
function rs_check_repeat(val) {
	if (val == 1 || val == 2)
		$('rs_repeat_days').style.display = '';
	else $('rs_repeat_days').style.display = 'none';
	
	createRepeats();
}

/*
	Check function
*/
function rs_check() {
	setTimeout('createRepeats()',1500);
}

/*
	Get the number of repeats a event can have
*/
function createRepeats() {
	var total = 0;
	
	if ($('repeat_end').value != '') {
		var days = new Array();
		var selectedDays = $('repeat_days').getSelected();

		selectedDays.each(function (el){
			days.push('days[]=' + el.value);
		});
		
		var daysstring = days.length > 0 ? '&' + days.join('&') : '';
		
		var repeatsRequest = new Request({
			method: 'post',
			url: 'index.php?option=com_rseventspro',
			data: 'task=repeats&interval=' + $('repeat_interval').value + '&type=' + $('repeat_type').value + '&start=' + $('start').value + '&end=' + $('repeat_end').value + daysstring,
			onSuccess: function(responseText){
				var response = responseText;
				var start = response.indexOf('RS_DELIMITER0') + 13;
				var end = response.indexOf('RS_DELIMITER1');
				response = response.substring(start, end);
				
				total += parseInt(response);
				
				if ($('repeatalso').options.length > 0)
					total += $('repeatalso').options.length;
			
				$('rs_repeating_event_total').innerHTML = total;
				$('rs_repeating_total').innerHTML = total;
			}
		});
		
		repeatsRequest.send();
	}
}

/*
	Delete files
*/
function rs_edit_remove_file(fileid) {
	var fileRequest = new Request({
		url: 'index.php?option=com_rseventspro',
		method: 'post',
		onSuccess: function(responseText){
			var response = responseText;
			var start = response.indexOf('RS_DELIMITER0') + 13;
			var end = response.indexOf('RS_DELIMITER1');
			response = response.substring(start, end);
				
			if (parseInt(response) == 1)
				$$('#rs_list_files li#'+fileid).dispose();
		}
	});
	
	if (isset($('frontend')))
		fileRequest.send('task=rseventspro.removefile&id=' + fileid + '&randomTime='+Math.random());
	else
		fileRequest.send('task=event.removefile&id=' + fileid + '&randomTime='+Math.random());
}

/*
	Save category
*/
function rs_edit_save_category() {
	var name = $('category').value;
	var parent = $('parent').value;
	
	if (name == '') {
		$('category').addClass('rse_error');
		return false;
	}
	
	var categoryRequest = new Request({
		url: 'index.php?option=com_rseventspro',
		method: 'post',
		onSuccess: function(responseText){
			if (parseInt(responseText) != 0) {
				var select = $('categories');
				select.options[select.options.length] = new Option(name, responseText);
				
				if (typeof Chosen != 'undefined')
					$('categories').fireEvent('liszt:updated');
				else 
					jQuery('#categories').trigger('liszt:updated');
				
				$('category').value = '';
				hm('box');
			}
		}
	});
	
	if (isset($('frontend')))
		categoryRequest.send('task=rseventspro.savecategory&jform[published]=1&jform[title]=' + name + '&jform[parent]=' + parent + '&randomTime='+Math.random());
	else
		categoryRequest.send('task=savedata&type=category&jform[published]=1&jform[title]=' + name + '&jform[parent_id]=' + parent + '&randomTime='+Math.random());
}

/*
	Save locations
*/
function rs_edit_save_location() {
	var name = $('rs_location').value;
	var address = $('location_address').value;
	var description = $('location_description').value;
	var coordinates = $('location_coordinates').value;
	
	if (name == '') {
		$('rs_location').addClass('rse_error');
		return false;
	}
	
	var locationRequest = new Request({
		url: 'index.php?option=com_rseventspro',
		method: 'post',
		onSuccess: function(responseText){
			if (parseInt(responseText) != 0) {
				$('location').value = parseInt(responseText);
				$('location_address').value = '';
				$('location_description').value = '';
				$('location_coordinates').value = '';
				$('rs_location_window').setStyle('display','none');
			}
		}
	});
	
	if (isset($('frontend')))
		locationRequest.send('task=rseventspro.savelocation&jform[published]=1&jform[name]=' + encodeURIComponent(name) + '&jform[address]=' + encodeURIComponent(address) + '&jform[description]=' + encodeURIComponent(description) + '&jform[coordinates]=' + coordinates);
	else
		locationRequest.send('task=savedata&type=location&jform[published]=1&jform[name]=' + encodeURIComponent(name) + '&jform[address]=' + encodeURIComponent(address) + '&jform[description]=' + encodeURIComponent(description) + '&jform[coordinates]=' + coordinates);
}

/*
	Remove ticket
*/
function rs_edit_removeticket(theid) {
	var ticketRequest = new Request({
		url: 'index.php?option=com_rseventspro',
		method: 'post',
		onSuccess: function(responseText) {
			var response = responseText;
			var start = response.indexOf('RS_DELIMITER0') + 13;
			var end = response.indexOf('RS_DELIMITER1');
			response = response.substring(start, end);
			
			if (parseInt(response) == 1) {
				$('rs_li_t'+theid).dissolve();
				$('rs_right_t'+theid).dispose();
				$('rs_li_t'+theid).dispose();
				
				$('rs_menu_item_7').addClass('active');
				$('rs_li_7').addClass('active');
				$('rs_right_7').reveal();
			}
		}
	});
	
	if (isset($('frontend')))
		ticketRequest.send('task=rseventspro.removeticket&id=' + theid + '&randomTime='+Math.random());
	else
		ticketRequest.send('task=event.removeticket&id=' + theid + '&randomTime='+Math.random());
}

/*
	Remove coupon
*/
function rs_edit_remove_coupon(theid) {
	var couponRequest = new Request({
		url: 'index.php?option=com_rseventspro',
		method: 'post',
		onSuccess: function(responseText){
			var response = responseText;
			var start = response.indexOf('RS_DELIMITER0') + 13;
			var end = response.indexOf('RS_DELIMITER1');
			response = response.substring(start, end);
			
			if (parseInt(response) == 1) {
				$('rs_li_c'+theid).dissolve();
				$('rs_right_c'+theid).dispose();
				$('rs_li_c'+theid).dispose();
				
				$('rs_menu_item_9').addClass('active');
				$('rs_li_9').addClass('active');
				$('rs_right_9').reveal();
			}
		}
	});
	
	if (isset($('frontend')))
		couponRequest.send('task=rseventspro.removecoupon&id=' + theid + '&randomTime='+Math.random());
	else
		couponRequest.send('task=event.removecoupon&id=' + theid + '&randomTime='+Math.random());
}