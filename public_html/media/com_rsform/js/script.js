var RSFormProCalendars = {};
var RSFormProPrices = {};
var ajaxExtraValidationScript = {};
var RSClickedSubmitElement = false;

if (typeof RSFormPro != 'object') {
	var RSFormPro = {};
}

RSFormPro.Forms = {};
RSFormPro.Editors = {};
RSFormPro.scrollToError = false;
RSFormPro.usePositioning = false;

/* Handle HTML5 form fields validation for the forms without AjaxValidation enabled */
RSFormPro.setHTML5Validation = function (formId, isDisabledSubmit, errorClasses, totalPages) {
	var parentErrorClass = errorClasses.parent;
	var fieldErrorClass = errorClasses.field;
	var submitElement = RSFormPro.getElementByType(formId, 'submit');
	var i, j;
	for (i = 0; i < submitElement.length; i++) {
		if (RSFormProUtils.hasClass(submitElement[i],'rsform-submit-button')) {
			RSFormProUtils.addEvent(submitElement[i], 'click', (function (event) {
				errorElements = RSFormPro.HTML5.validation(formId);
				if (errorElements.length) {
					var errorPagesFound = [];
					for (j = 0; j < errorElements.length; j++) {
						RSFormProUtils.addClass(errorElements[j], 'rsform-error');

						if (document.getElementById('component' + errorElements[j].componentId)) {
							document.getElementById('component' + errorElements[j].componentId).className = 'formError';
						}
						if (parentErrorClass) {
							try {
								var block = RSFormPro.getBlock(formId, RSFormProUtils.getAlias(errorElements[j].field.getAttribute('id')));

								RSFormProUtils.addClass(block[0], parentErrorClass);
							} catch (err) {}
						}

						if (totalPages > 0) {
							errorPagesFound.push(errorElements[j].page);
						}
					}

					if (totalPages > 0 && errorPagesFound.length) {
						var firstErrorPage = Math.min.apply(null, errorPagesFound);
						RSFormPro.Pages.change(formId, firstErrorPage, totalPages, false, errorClasses)
					}

					if (RSFormPro.scrollToError){
						RSFormPro.gotoErrorElement(formId);
					}

					if (isDisabledSubmit) {
						for (j = 0; j < submitElement.length; j++) {
							submitElement[j].disabled = false;
						}
					}

					event.preventDefault();
				} else {
					if (isDisabledSubmit) {
						// if the submit button or any other element has the id submit the form.submit() function is overwritten
						if (typeof this.form.submit != 'function') {
							document.createElement('form').submit.call(this.form)
						} else {
							this.form.submit();
						}
					}
				}
			}));
		}
	}
};

/* Disable the submit button when form is submitted functions */
RSFormPro.setDisabledSubmit = function(formId, ajaxValidation){
	if (!ajaxValidation) {
		var i, j;
		var submitElement = RSFormPro.getElementByType(formId, 'submit');
		for (i = 0; i < submitElement.length; i++) {
			if (RSFormProUtils.hasClass(submitElement[i],'rsform-submit-button')) {
				RSFormProUtils.addEvent(submitElement[i],'click', (function(event) {
					for (j = 0; j < submitElement.length; j++) {
						submitElement[j].disabled = true;
					}
				}));
			}
		}
	}
};
/* Thank You Message in Popup Window functions */

RSFormPro.showThankYouPopup = function (thankYouContainer){
	var content = thankYouContainer.innerHTML;
	var gotoUrl = thankYouContainer.querySelector('#rsfp-thankyou-popup-return-link').value;

	// remove the html part from the layout
	thankYouContainer.parentNode.removeChild( thankYouContainer );

	document.body.className = document.body.className+' rsfp_popup_activated';
	document.body.innerHTML += '<div class="rsfp_thankyou_popup_outer" onclick="RSFormPro.accessLink(event,\''+gotoUrl+'\')"><div class="rsfp_thankyou_popup_inner" id="rsfp_thankyou_popup_inner"><div class="rsfp_thankou_popup_close_btn">&times;</div>'+content+'</div></div>';

	var popupWindowHeight = document.getElementById('rsfp_thankyou_popup_inner').offsetHeight;
	var windowHeight = window.innerHeight;

	var marginTop = (windowHeight - popupWindowHeight) / 2;
	document.getElementById('rsfp_thankyou_popup_inner').style.marginTop = marginTop+'px';
};
RSFormPro.accessLink = function(event,link) {
	var clickedElementClass = event.target.className;
	if (clickedElementClass == 'rsfp_thankyou_popup_outer' || clickedElementClass == 'rsfp_thankou_popup_close_btn') {
		if (link.length > 0) {
			document.location = link;
		} else {
			document.location.reload();
		}
	}
};

/* Scroll to first error element */
RSFormPro.gotoErrorElement = function(formId){
	var form = RSFormPro.getForm(formId);
	var errorElements = form.getElementsByClassName('formError');
	if (errorElements.length) {
		var block = RSFormPro.findAncestor(errorElements[0], 'rsform-block');
		if (block) {
			RSFormPro.scrollToElement(block);
		} else {
			RSFormPro.scrollToElement(errorElements[0]);
		}
	}
};

RSFormPro.findAncestor = function(el, cls){
	while(el = el.parentElement) {
		var elementClasses = el.className;
		elementClasses = elementClasses.split(' ');
		if (elementClasses.indexOf(cls) >= 0) {
			return el;
		}
	}

	return false;
};

/* ScrollTo functions */
RSFormPro.scrollToElement = function(element){
	var to = element.offsetTop;
	var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
	var documentView = window.innerHeight + scrollTop;

	// this is more specific for the modern browsers
	if (typeof element.getBoundingClientRect == 'function') {
		to = element.getBoundingClientRect().top + scrollTop;
	}

	if (to < scrollTop || to > documentView) {
		RSFormPro.scrollTo(to, 300);
	}
};

RSFormPro.scrollTo = function(to, duration) {
	if (duration <= 0) return;
	var elementScrollTop = window.pageYOffset ?  window.pageYOffset : document.documentElement.scrollTop;
	var difference = to - elementScrollTop;
	var perTick = difference / duration * 10;
	
	setTimeout(function() {
		var limitControl;
		limitControl = window.pageYOffset ?  window.pageYOffset : document.documentElement.scrollTop;
		limitControl = limitControl + perTick;
		window.scrollTo(0, limitControl);
		// if the element has been reached or the scroll is at the bottom of the page no need to continue
		if (limitControl == to) return;

		RSFormPro.scrollTo(to, duration - 10);
	}, 10);
};

/* Field specific functions */

RSFormPro.refreshCaptcha = function(componentId, captchaPath) {
	if (!captchaPath) {
		captchaPath = 'index.php?option=com_rsform&task=captcha&format=image&componentId=' + componentId;
	}
	document.getElementById('captcha' + componentId).src = captchaPath + '&' + Math.random();
	document.getElementById('captchaTxt' + componentId).value = '';
	document.getElementById('captchaTxt' + componentId).focus();
};

RSFormPro.initGeoLocation = function(term, id, mapid,  map, marker, geocoder, type) {
	var content = document.getElementById('rsform_geolocation'+id);
	var address	= document.getElementById(mapid).clientWidth;

	document.getElementById('rsform_geolocation'+id).style.width = address+'px';
	document.getElementById('rsform_geolocation'+id).style.display = 'none';
	document.getElementById('rsform_geolocation'+id).innerHTML = '';

	if (term != '') {
		geocoder.geocode( {'address': term }, function(results, status) {
			if (status == 'OK') {
				for (var i=0; i<results.length; i++) {
					var item	= results[i];
					var theli	= document.createElement('li');
					var thea	= document.createElement('a');

					thea.setAttribute('href','javascript:void(0)');
					thea.innerHTML = item.formatted_address;

					RSFormProUtils.addEvent(thea,'click', (function() {
						var mapValue = type ? item.formatted_address : item.geometry.location.lat().toFixed(5) + ',' + item.geometry.location.lng().toFixed(5);
						var mapId	 = mapid;
						var location = new google.maps.LatLng(item.geometry.location.lat().toFixed(5), item.geometry.location.lng().toFixed(5));

						return function() {
							document.getElementById(mapId).value = mapValue;
							marker.setPosition(location);
							map.setCenter(location);
							document.getElementById('rsform_geolocation'+id).style.display = 'none';
						}
					})());

					theli.appendChild(thea);
					content.appendChild(theli);
				}

				document.getElementById('rsform_geolocation'+id).style.display = '';
			}
		});
	}
};

RSFormPro.disableInvalidDates = function(fieldName) {
	var theDate = new Date(),
		day,
		index;

	for (day = 1; day <= 31; day++) {
		var year 	= parseInt(document.getElementById(fieldName + 'y').value);
		var month 	= parseInt(document.getElementById(fieldName + 'm').value) - 1;

		index = day - 1;
		if (document.getElementById(fieldName + 'd').options[0].value == '') {
			index++;
		}
		document.getElementById(fieldName + 'd').options[index].disabled = false;
		if (!isNaN(year) && !isNaN(month)) {
			if (typeof theDate.__msh_oldSetFullYear == 'function') {
				theDate.__msh_oldSetFullYear(year, month, day);
			} else {
				theDate.setFullYear(year, month, day);
			}

			if (theDate.getDate() != day || theDate.getMonth() != month) {
				document.getElementById(fieldName + 'd').options[index].disabled = true;
			}
		}
	}
	if (document.getElementById(fieldName + 'd').options[document.getElementById(fieldName + 'd').selectedIndex].disabled == true) {
		for (day = 31; day >= 28; day--) {
			index = day - 1;
			if (document.getElementById(fieldName + 'd').options[0].value == '') {
				index++;
			}
			if (document.getElementById(fieldName + 'd').options[index].disabled == false) {
				document.getElementById(fieldName + 'd').value = day;
				break;
			}
		}
	}
};

/* Number functions */

RSFormPro.formatNumber = function(number, decimals, dec_point, thousands_sep) {
	var n = number, prec = decimals;
	n = !isFinite(+n) ? 0 : +n;
	prec = !isFinite(+prec) ? 0 : Math.abs(prec);
	var sep = (typeof thousands_sep == "undefined") ? ',' : thousands_sep;
	var dec = (typeof dec_point == "undefined") ? '.' : dec_point;

	var s = (prec > 0) ? n.toFixed(prec) : Math.round(n).toFixed(prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;

	var abs = Math.abs(n).toFixed(prec);
	var _, i;

	if (abs >= 1000) {
		_ = abs.split(/\D/);
		i = _[0].length % 3 || 3;

		_[0] = s.slice(0,i + (n < 0)) +
			_[0].slice(i).replace(/(\d{3})/g, sep+'$1');

		s = _.join(dec);
	} else {
		s = s.replace('.', dec);
	}

	return s;
};

RSFormPro.toNumber = function(number, decimal, thousands, decimals) {
	// Remove decimals first
	if (parseInt(decimals) > 0 && number.lastIndexOf(decimal) > -1) {
		var index = number.lastIndexOf(decimal);
		number = number.substring(0, index) + 'DECIMALS' + number.substring(index + 1);
	}

	// Remove thousands separator
	if (number.indexOf(thousands) > -1) {
		number = number.split(thousands).join('');
	}

	number = number.split('DECIMALS').join('.');
	return parseFloat(number);
};

/* Form and element functions */

RSFormPro.getForm = function(formId) {
	if (typeof RSFormPro.Forms[formId] == 'undefined') {
		var formIds = document.getElementsByName('form[formId]');
		for (var i=0; i<formIds.length; i++)
		{
			if (parseInt(formIds[i].value) != parseInt(formId))
				continue;

			var form = formIds[i].parentNode;
			if (form.tagName == 'FORM' || form.nodeName == 'FORM') {
				RSFormPro.Forms[formId] = form;
				return form;
			}

			while (form.parentNode)
			{
				form = form.parentNode;
				if (form.tagName == 'FORM' || form.nodeName == 'FORM') {
					RSFormPro.Forms[formId] = form;
					return form;
				}
			}
		}
	}

	return RSFormPro.Forms[formId];
};

RSFormPro.getValue = function(formId, name) {
	var form	= RSFormPro.getForm(formId);
	var values	= [];

	if (typeof form != 'undefined')
	{
		for (var i = 0; i < form.elements.length; i++)
		{
			var element = form.elements[i];
			var tagName = element.tagName || element.nodeName;

			switch (tagName)
			{
				case 'INPUT':
					if (element.type)
						switch (element.type.toUpperCase())
						{
							case 'TEXT':
							case 'NUMBER':
							case 'HIDDEN':
								if (!element.name || element.name != 'form[' + name + ']') continue;
								
								if (typeof RSFormPro.YUICalendar != 'undefined' &&
									typeof RSFormPro.YUICalendar.calendars[formId] != 'undefined' &&
									typeof RSFormPro.YUICalendar.calendars[formId][name] != 'undefined')
									{
										var selectedDate = new Date(RSFormPro.YUICalendar.calendars[formId][name].getSelectedDates()[0]);
										return (selectedDate.getTime() / 1000).toString();
									}
								
								if (typeof RSFormPro.jQueryCalendar != 'undefined' &&
									typeof RSFormPro.jQueryCalendar.calendars[formId] != 'undefined' &&
									typeof RSFormPro.jQueryCalendar.calendars[formId][name] != 'undefined')
									{
										var selectedDate = Date.parseDate(RSFormPro.jQueryCalendar.calendars[formId][name].currentDate, RSFormPro.jQueryCalendar.calendars[formId][name].hiddenFormat);
										return (selectedDate.getTime() / 1000).toString();
									}
								
								return element.value;
								break;

							case 'RADIO':
								if (!element.name || element.name != 'form[' + name + ']') continue;

								if (element.checked == true) {
									values.push(element.value);
								}
								break;

							case 'CHECKBOX':
								if (!element.name || element.name != 'form[' + name + '][]') continue;
								if (element.checked == true) {
									values.push(element.value);
								}
								break;
						}
					break;

				case 'SELECT':
					if (!element.name || element.name != 'form[' + name + '][]') continue;

					if (element.options)
						for (var o=0; o<element.options.length; o++)
							if (element.options[o].selected)
							{
								values.push(element.options[o].value);
							}
					break;
			}
		}
	}

	return values;
};

RSFormPro.getElementByType = function(formId, type) {
	var form	= RSFormPro.getForm(formId);
	type = type.toUpperCase();
	var elements	= [];

	if (typeof form != 'undefined')
	{
		for (var i = 0; i < form.elements.length; i++)
		{
			var element = form.elements[i];
			var tagName = element.tagName || element.nodeName;
			switch (tagName)
			{
				case 'INPUT':
				case 'BUTTON':
					if (element.type.toUpperCase() == type) {
						elements.push(element);
					}
					break;

				case 'SELECT':
				case 'TEXTAREA':
					if (type == 'SELECT' || type == 'TEXTAREA') {
						elements.push(element);
					}
					break;
			}
		}
	}

	return elements;
};

RSFormPro.resettingValues = false;

RSFormPro.resetValues = function(items) {
	if (RSFormPro.resettingValues)
	{
		return;
	}

	RSFormPro.resettingValues = true;
	var element, tagName;
	try
	{
		for (var i = 0; i < items.length; i++)
		{
			element = items[i];
			tagName = element.tagName || element.nodeName;

			switch (tagName)
			{
				case 'INPUT':
					if (element.type)
					{
						switch (element.type.toUpperCase())
						{
							case 'CHECKBOX':
							case 'RADIO':
								element.checked = element.defaultChecked;
								
								RSFormPro.triggerEvent(element, 'change');
							break;
							
							case 'NUMBER':
							case 'TEXT':
								element.value = element.defaultValue;
								
								RSFormPro.triggerEvent(element, 'change');
								RSFormPro.triggerEvent(element, 'input');

                                if (element.id && element.id.indexOf('rs-range-slider') == 0 && typeof jQuery != 'undefined')
                                {
                                    jQuery(element).data('ionRangeSlider').reset();
                                }
							break;
						}
					}
				break;
				
				case 'TEXTAREA':
					element.value = element.defaultValue;
					
					RSFormPro.triggerEvent(element, 'change');
					RSFormPro.triggerEvent(element, 'input');
				break;

				case 'SELECT':
					if (element.options)
					{
						for (var o = 0; o < element.options.length; o++)
						{
							element.options[o].selected = element.options[o].defaultSelected;
						}
					}
					
					RSFormPro.triggerEvent(element, 'change');
				break;
			}
		}
	}
	catch (err) {}

	RSFormPro.resettingValues = false;
};

RSFormPro.triggerEvent = function(element, type) {
	try {
		var event;
		if (document.createEvent) {
			event = document.createEvent("HTMLEvents");
			event.initEvent(type, true, true);
		} else {
			event = document.createEventObject();
			event.eventType = type;
		}

		event.eventName = type;

		if (document.createEvent) {
			element.dispatchEvent(event);
		} else {
			element.fireEvent("on" + event.eventType, event);
		}
	} catch (e) {}
};

RSFormPro.isChecked = function(formId, name, value) {
	var isChecked 	= false;
	var form 		= RSFormPro.getForm(formId);

	if (typeof form != 'undefined')
	{
		primary_loop:
			for (var i=0; i<form.elements.length; i++)
			{
				var element = form.elements[i];
				var tagName = element.tagName || element.nodeName;

				switch (tagName)
				{
					case 'INPUT':
						if (element.type)
							switch (element.type.toUpperCase())
							{
								case 'RADIO':
									if (!element.name || element.name != 'form[' + name + ']') continue;
									if (element.checked == true && element.value == value)
									{
										isChecked = true;
										break primary_loop;
									}
									break;

								case 'CHECKBOX':
									if (!element.name || element.name != 'form[' + name + '][]') continue;
									if (element.checked == true && element.value == value)
									{
										isChecked = true;
										break primary_loop;
									}
									break;
							}
						break;

					case 'SELECT':
						if (!element.name || element.name != 'form[' + name + '][]') continue;

						if (element.options)
							for (var o=0; o<element.options.length; o++)
								if (element.options[o].selected && element.options[o].value == value)
								{
									isChecked = true;
									break primary_loop;
								}
						break;
				}
			}
	}

	return isChecked;
};

RSFormPro.getBlock = function(formId, block) {
	var form 	 = RSFormPro.getForm(formId);
	var possible = false;
	var blocks, current_block;

	if (typeof form != 'undefined') {
		if (blocks = getElementsByClassName('rsform-block')) {
			for (i=0; i<blocks.length; i++) {
				var classes = blocks[i].className.split(' ');
				for (c=0; c<classes.length; c++) {
					if (classes[c] == 'rsform-block-' + block) {
						if (blocks[i].parentNode) {
							current_block = blocks[i];
							if (current_block == form)
								return [ blocks[i] ];

							while (current_block.parentNode) {
								current_block = current_block.parentNode;
								if (current_block == form)
									return [ blocks[i] ];
							}
						}

						possible = [ blocks[i] ];
					}
				}
			}
		}
	}

	return possible;
};

RSFormPro.getFieldsByName = function(formId, name) {
	var form 	= RSFormPro.getForm(formId);
	var results = [];
	var pushed  = false;

	if (typeof form != 'undefined') {
		for (var i=0; i<form.elements.length; i++) {
			var element = form.elements[i];
			pushed = false;
			if (element.name && (
					// single fields
					element.name == 'form[' + name + ']' ||
					// multiple fields
					element.name == 'form[' + name + '][]' ||
					// new field - Birthday field
					element.name == 'form[' + name + '][d]' ||
					element.name == 'form[' + name + '][m]' ||
					element.name == 'form[' + name + '][y]'
				)) {
				results.push(element);
				pushed = true;
			}

			if (pushed) {
				// for calendar
				if (element.id && element.id.indexOf('txtcal') > -1) {
					var suffix = element.id.replace('txtcal', '');
					if (document.getElementById('btn' + suffix))
					{
						results.push(document.getElementById('btn' + suffix));
					}
				}
				// for labels (radio, checkbox)
				var labels = form.getElementsByTagName('label');
				for (var l=0; l<labels.length; l++) {
					if (labels[l].htmlFor && labels[l].htmlFor == element.id)
						results.push(labels[l]);
				}
			}
		}
	}

	return results;
};

RSFormPro.showCounter = function(element, id) {
	var current = element.value.length;
	var result;
	if (element.maxLength > 0)
	{
		result = current + '/' + element.maxLength;
	}
	else
	{
		result = current;
	}
	
	document.getElementById('rsfp-counter-' + id).innerText = result;
};

RSFormPro.limitSelections = function(formId, field, max) {
    RSFormProUtils.addEvent(window, 'load', function() {
        var fields = RSFormPro.getFieldsByName(formId, field);
        var objects = [];
        var i;
        var tagName;

        if (!fields || !fields.length) {
            return;
        }

        for (i = 0; i < fields.length; i++) {
            tagName = fields[i].tagName || fields[i].nodeName;
            tagName = tagName.toUpperCase();

            if (tagName === 'INPUT' && fields[i].type && fields[i].type.toUpperCase() === 'CHECKBOX' && !fields[i].disabled) {
                objects.push(fields[i]);
            }
        }

        if (!objects.length) {
            return;
        }

        function limitSelections() {
            var values = RSFormProUtils.getChecked(objects);
            RSFormProUtils.remAttr(objects, 'disabled');
            if (values && values.length > 0 && values.length >= max) {
                RSFormProUtils.setAttr(RSFormProUtils.getUnchecked(objects), 'disabled', true);
            }
		}

        for (i = 0; i < objects.length; i++) {
            RSFormProUtils.addEvent(objects[i], 'change', limitSelections);
        }

        limitSelections();
    });
};

/*HTML5 simulators*/

RSFormPro.HTML5 = {
	validation: function(formId){
		var form = RSFormPro.getForm(formId);
		var errorElements = [];
		var html5types = ['number', 'email', 'range', 'url', 'tel'];

		var checkValidityExists = true;
		var page = 0;
		if (form.elements.length) {
			for (i=0; i<form.elements.length; i++) {
				if (!checkValidityExists) {
					break;
				}

				// if the multiple form is present and also validation we will need to find in which page the element is located
				if (form.elements[i].type == 'button') {
					var onclick = form.elements[i].getAttribute('onclick');
					if (typeof onclick == 'string' && onclick.indexOf('rsfp_changePage') >= 0){
						var countCommas = 0;
						var pos = onclick.indexOf(',');
						while (pos > -1) {
							++countCommas;
							pos = onclick.indexOf(',', ++pos);
						}


						if (countCommas > 2) {
							page++;
						}
					}
				}
				if (html5types.indexOf(form.elements[i].type) >= 0) {
					if ( typeof(form.elements[i].checkValidity) == "function" && checkValidityExists) {
						if (!form.elements[i].checkValidity()) {
							var elementObj = {
								field: form.elements[i],
								page: page
							};
							// try to get the componentId
							var componentId = RSFormPro.HTML5.getComponentId(formId, form.elements[i].getAttribute('id'));
							if (componentId) {
								elementObj.componentId = componentId;
							}

							errorElements.push(elementObj);
						}
					} else {
						checkValidityExists = false;
					}
				}
			}
		}
		return errorElements;
	},

	componentIds: {},

	getComponentId: function(formId, elementAlias) {
		if (typeof RSFormPro.HTML5.componentIds[formId] == 'undefined') {
			RSFormPro.HTML5.componentIds[formId] = {};
		}

		if (typeof RSFormPro.HTML5.componentIds[formId][elementAlias] == 'undefined') {
			var block = RSFormPro.getBlock(formId, RSFormProUtils.getAlias(elementAlias));
			var componentIdBlock = RSFormProUtils.getElementsByClassName('formNoError', 'span', block[0]);
			if (componentIdBlock.length) {
				var componentId = componentIdBlock[0].getAttribute('id');
				RSFormPro.HTML5.componentIds[formId][elementAlias] = componentId.replace('component', '');
			} else {
				RSFormPro.HTML5.componentIds[formId][elementAlias] = false;
			}
		}

		return RSFormPro.HTML5.componentIds[formId][elementAlias];
	}
};

/* Pagination functions */

RSFormPro.Pages = {
	change: function(formId, page, totalPages, validate, errorClasses) {
		// callback functions if next page is clicked
		var direction = RSFormPro.Pages.checkDirection(formId, page);
		if (direction == 'next') {
			RSFormPro.callbacks.runCallback(formId, 'nextPage');
		}
		RSFormPro.callbacks.runCallback(formId, 'changePage');
		
		var thePage;

		if (validate) {
			var form = RSFormPro.getForm(formId);
			if (!RSFormPro.Ajax.validate(form, page, errorClasses, totalPages)) {
				return false;
			}
		}

		for (var i = 0; i <= totalPages; i++) {
			thePage = document.getElementById('rsform_' + formId + '_page_' + i);
			if (thePage) {
				rsfp_hidePage(thePage);
			}
		}

		thePage = document.getElementById('rsform_' + formId + '_page_' + page);
		if (thePage) {
			rsfp_showPage(thePage);
			try {
				var func = window["rsfp_showProgress_" + formId];
				if (typeof func == "function") {
					func(page);
				}
			}
			catch (err) {}
		}
	},
	hide: function(thePage) {
		RSFormProUtils.addClass(thePage, 'formHidden');
	},
	show: function(thePage) {
		RSFormProUtils.removeClass(thePage, 'formHidden');
	},
	
	checkDirection : function(formId, page) {
		var base = RSFormPro.Pages;
		if (typeof base.history[formId] == 'undefined') {
			base.history[formId] = page;
			return 'next';
		} else {
			var direction;
			if (base.history[formId] <= page ) { 
				direction = 'next';
			} else {
				direction = 'prev';
			}
			base.history[formId] = page;
			
			return direction;
		}
	},	
	history: {}
};

/* Condition functions */
RSFormPro.Conditions = {
	add: function(formId, name, fnCondition) {
		var form = RSFormPro.getForm(formId);

		if (typeof form != 'undefined') {
			for (var i = 0; i < form.elements.length; i++) {
				var element = form.elements[i];
				if (element.name && (element.name == 'form[' + name + ']' || element.name == 'form[' + name + '][]')) {
					RSFormProUtils.addEvent(element, 'change', function() {
						fnCondition();
					});
				}
			}
		}
	},
	run: function(condition) {
		var formId = condition.form_id,
            conditions = [],
            items = [],
			detail, isChecked, displayValue, match;

		if (typeof condition.details === 'object')
		{
			for (var i = 0; i < condition.details.length; i++)
			{
				detail = condition.details[i];
                isChecked = RSFormPro.isChecked(formId, detail.ComponentName, detail.value);
                conditions.push(isChecked === (detail.operator === 'is'));
			}

			if (parseInt(condition.block) === 1)
			{
				items = RSFormPro.getBlock(formId, RSFormProUtils.getAlias(condition.ComponentName));
			}
			else
			{
				items = RSFormPro.getFieldsByName(formId, condition.ComponentName);
			}

			if (items.length > 0)
			{
				if (condition.condition === 'all')
				{
                    // && conditions need all elements of the Array to be true -> no false in Array
                    match = conditions.indexOf(false) === -1;
				}
				else
				{
                    // || conditions need only one element to be true -> one true in array
					match = conditions.indexOf(true) > -1;
				}

                if (match)
                {
                    displayValue = condition.action === 'show' ? '' : 'none';
                }
                else
                {
                    displayValue = condition.action === 'show' ? 'none' : '';
                }

				RSFormProUtils.setDisplay(items, displayValue);
                if (displayValue === 'none')
				{
					RSFormPro.resetValues(RSFormPro.getFieldsByName(formId, condition.ComponentName));
				}
			}
		}
	},
	runAll: function(formId) {
		var func = window["rsfp_runAllConditions" + formId];
		if (typeof func == "function") {
			func();
		}
	},
	addReset: function(formId) {
		var resetElements = RSFormPro.getElementByType(formId, 'reset');
		if (resetElements.length > 0)
		{
			for (var i = 0; i < resetElements.length; i++)
			{
				RSFormProUtils.addEvent(resetElements[i], 'click', function() {
					window.setTimeout(function() {
						RSFormPro.Conditions.runAll(formId);
					}, 1);
				});
			}
		}
	}
};

/* Calculation functions */

RSFormPro.Calculations = {
	toDays: function(seconds) {
		return Math.round(parseFloat(seconds) / 86400);
	},
	toHours: function(seconds) {
		return Math.round(parseFloat(seconds) / 86400 * 24);
	},
	addEvents: function(formId, fields) {
		RSFormProUtils.addEvent(window, 'load', function(){
			RSFormPro.Calculations._addEvents(formId, fields);
		});
	},
	_addEvents: function(formId, fields) {
		var func 		= window["rsfp_Calculations" + formId];
		var thefields	= fields ? fields : RSFormProPrices;
		var event 		= 'change';
		
		var resetElements = RSFormPro.getElementByType(formId, 'reset');
		if (resetElements.length > 0)
		{
			for (var i = 0; i < resetElements.length; i++)
			{
				RSFormProUtils.addEvent(resetElements[i], 'click', function() {
					if (typeof func == "function") {
						window.setTimeout(func, 1);
					}
				});
			}
		}

		for (var field in thefields) {
			if (!thefields.hasOwnProperty(field)) {
				continue;
			}
			field = field.replace(formId+'_','');
			objects = RSFormPro.getFieldsByName(formId,field);
			for(i = 0; i < objects.length;i++) {
				tagName = objects[i].tagName || objects[i].nodeName;

				if (tagName == 'INPUT' || tagName == 'SELECT') {
					event = 'change';
					if (tagName == 'INPUT' && typeof objects[i].type == 'string') {
						switch (objects[i].type.toUpperCase())
						{
							default:
								event = 'change';
							break;
							
							case 'NUMBER':
							case 'TEXT':
								event = 'input';
								
								if (typeof RSFormPro.jQueryCalendar != 'undefined' &&
									typeof RSFormPro.jQueryCalendar.calendars[formId] != 'undefined' &&
									typeof RSFormPro.jQueryCalendar.calendars[formId][field] != 'undefined')
									{
										RSFormPro.jQueryCalendar.calendars[formId][field].calendarInstance.setOptions({onChangeDateTime: function() {
											if (typeof func == "function") {
												func();
											}
										}});
									}
								
								if (typeof RSFormPro.YUICalendar != 'undefined' &&
									typeof RSFormPro.YUICalendar.calendars[formId] != 'undefined' &&
									typeof RSFormPro.YUICalendar.calendars[formId][field] != 'undefined')
									{
										RSFormPro.YUICalendar.calendars[formId][field].selectEvent.subscribe(function() {
											if (typeof func == "function") {
												func();
											}
										}, RSFormPro.YUICalendar.calendars[formId][field], true);
									}
							break;
						}
					}

					RSFormProUtils.addEvent(objects[i], event, function() {
						if (typeof func == "function") {
							func();
						}
					});
				}
			}
		}
	}
};

/* AJAX functions */
RSFormPro.Ajax = {
	Wait: false,
	URL: false,
	Params: {},
	getParamsObject: function() {
		return {
			vars: [],
			push: function(str) {
				return this.vars.push(str);
			},
			
			indexOf: function(str) {
				return this.vars.join('&').indexOf(str);
			},
			
			replace: function(r, w) {
				this.vars = this.vars.join('&').replace(r, w).split('&');
				
				return this.vars.join('&');
			},
			
			toString: function() {
				return this.vars.join('&');
			}
		};
	},
	getXHR: function() {
		try {
			return new window.XMLHttpRequest();
		} catch ( e ) {}
	},
	getXHRLegacy: function() {
		return {
			send: function(data)
			{
				if (data === RSFormPro.Ajax.Params)
				{
					data = data.toString();
				}
				
				return RSFormPro.Ajax.xhr.send(data);
			}
		}
	},
	displayValidationErrors: function(formComponents, task, formId, data) {
		if (task == 'afterSend') {
			var ids,
				i,
				j,
				id,
				formComponent,
				firstErrorElement,
				elementBlock;

			ids = data.response[0].split(',');
			for (i = 0; i < ids.length; i++) {
				id = parseInt(ids[i]);
				if (!isNaN(id) && typeof formComponents[id] != 'undefined') {
					formComponent = RSFormPro.getFieldsByName(formId, formComponents[id]);
					if (formComponent && formComponent.length > 0) {
						for (j = 0; j < formComponent.length; j++) {
							if (formComponent[j]) {
								RSFormProUtils.removeClass(formComponent[j], 'rsform-error');
								if (typeof data.parentErrorClass != 'undefined' && data.parentErrorClass.length > 0) {
									try {
										elementBlock = RSFormPro.getBlock(formId, RSFormProUtils.getAlias(formComponents[id]));
										RSFormProUtils.removeClass(elementBlock[0], data.parentErrorClass);
									} catch (err) {}
								}
								if (typeof data.fieldErrorClass != 'undefined' && data.fieldErrorClass.length > 0) {
									try {
										results = RSFormPro.getFieldsByName(formId, formComponents[id]);
										if (results.length > 0)
										{
											for (var r = 0; r < results.length; r++)
											{
												RSFormProUtils.removeClass(results[r], data.fieldErrorClass);
											}
										}
									} catch (err) {}
								}
							}
						}
					}
				}
			}

			ids = data.response[1].split(',');
			var doScroll = false;
			var doFocus = false;
			for (i = 0; i < ids.length; i++)
			{
				id = parseInt(ids[i]);
				
				if (isNaN(id))
				{
					continue;
				}
				
				if (typeof formComponents[id] == 'undefined')
				{
					continue;
				}
				
				formComponent = RSFormPro.getFieldsByName(formId, formComponents[id]);
				if (!formComponent || formComponent.length < 1)
				{
					continue;
				}
				
				for (j = 0; j < formComponent.length; j++)
				{
					if (formComponent[j])
					{
						RSFormProUtils.addClass(formComponent[j], 'rsform-error');

						if (!doScroll)
						{
							doScroll = true;
						}
						
						if (typeof data.parentErrorClass != 'undefined' && data.parentErrorClass.length > 0)
						{
							try
							{
								elementBlock = RSFormPro.getBlock(formId, RSFormProUtils.getAlias(formComponents[id]));
								RSFormProUtils.addClass(elementBlock[0], data.parentErrorClass);
							}
							catch (err) {}
						}
						try
						{
							results = RSFormPro.getFieldsByName(formId, formComponents[id]);
							if (results.length > 0)
							{
								for (var r = 0; r < results.length; r++)
								{									
									if (typeof data.fieldErrorClass != 'undefined' && data.fieldErrorClass.length > 0)
									{
										RSFormProUtils.addClass(results[r], data.fieldErrorClass);
									}
									
									if (!doFocus)
									{
										results[r].focus();
										doFocus = true;
									}
								}
							}
						}
						catch (err) {}
					}
				}
			}
			
			// scroll to first error elment
			if (RSFormPro.scrollToError && doScroll){
				RSFormPro.gotoErrorElement(formId);
			}
		}
	},
	validate: function(form, page, errorClasses, totalPages) {
		try {
			var el = form.elements.length;
		} catch (err) {
			form = this;
		}

		RSFormPro.Ajax.xhr = RSFormPro.Ajax.getXHR();
		RSFormPro.Ajax.xmlHttp = RSFormPro.Ajax.getXHRLegacy();
		var url = 'index.php?option=com_rsform&task=ajaxValidate';
		if (typeof RSFormPro.Ajax.URL == 'string') {
			url = RSFormPro.Ajax.URL;
		}
		
		RSFormPro.Ajax.Params = RSFormPro.Ajax.getParamsObject();

		var submits = [],
			errorFields = [],
			success = false,
			formId = 0,
			ids,
			totalJSDetectedPages = 0,
			lastClickedElement,
			i,
			j;
		
		if (typeof errorClasses != 'object')
		{
			errorClasses = {'parent': '', 'field': ''}
		}

		for (i=0; i<form.elements.length; i++)
		{
			// try and detect total pages of the form
			if (form.elements[i].type == 'button') {
				var onclick = form.elements[i].getAttribute('onclick');
				if (typeof onclick == 'string' && onclick.indexOf('rsfp_changePage') >= 0) {
					var countCommas = 0;
					var pos = onclick.indexOf(',');
					while (pos > -1) {
						++countCommas;
						pos = onclick.indexOf(',', ++pos);
					}

					if (countCommas > 2) {
						totalJSDetectedPages++;
					}
				}
			}

			// don't send an empty value
			if (!form.elements[i].name) continue;
			if (form.elements[i].name.length == 0) continue;
			// check if the checkbox is checked
			if (form.elements[i].type == 'checkbox' && form.elements[i].checked == false) continue;
			// check if the radio is selected
			if (form.elements[i].type == 'radio' && form.elements[i].checked == false) continue;

			if (form.elements[i].type == 'submit' && form.elements[i].getAttribute('data-disableonsubmit') == '1') {
				submits.push(form.elements[i]);
				form.elements[i].disabled = true;
			}

			// check if form is a dropdown with multiple selections
			if (form.elements[i].type == 'select-multiple')
			{
				for (j = 0; j < form.elements[i].options.length; j++) {
					if (form.elements[i].options[j].selected) {
						RSFormPro.Ajax.Params.push(form.elements[i].name + '=' + encodeURIComponent(form.elements[i].options[j].value));
					}
				}

				continue;
			}

			if (form.elements[i].name == 'form[formId]') {
				formId = form.elements[i].value;
			}

			if (typeof RSFormPro.Editors[form.elements[i].name] == 'function') {
				RSFormPro.Ajax.Params.push(form.elements[i].name + '=' + encodeURIComponent(RSFormPro.Editors[form.elements[i].name]()));
			} else {
				RSFormPro.Ajax.Params.push(form.elements[i].name + '=' + encodeURIComponent(form.elements[i].value));
			}
		}

		errorFields = RSFormPro.HTML5.validation(formId);

		if (page) {
			RSFormPro.Ajax.Params.push('page=' + page);
		}

		RSFormPro.Ajax.xhr.open("POST", url, true);

		//Send the proper header information along with the request
		RSFormPro.Ajax.xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
		if (typeof ajaxExtraValidationScript[formId] == 'function') {
			ajaxExtraValidationScript[formId]('beforeSend', formId, {'url': url, 'params': RSFormPro.Ajax.Params});
		}

		if (!RSFormPro.Ajax.Wait)
		{
			// B/C layer
			RSFormPro.Ajax.xmlHttp.send(RSFormPro.Ajax.Params);
		}
		success = true;
		
		RSFormPro.Ajax.xhr.onreadystatechange = function() {
			if (RSFormPro.Ajax.xhr.readyState == 4 && RSFormPro.Ajax.xhr.status == 200) {
				if (RSFormPro.Ajax.xhr.responseText.indexOf("\n") != -1)
				{

					var response = RSFormPro.Ajax.xhr.responseText.split("\n");
					// All spans set to no error
					ids = response[0].split(',');
					for (i = 0; i < ids.length; i++)
						if (!isNaN(parseInt(ids[i])) && document.getElementById('component'+ids[i])) {
							document.getElementById('component'+ids[i]).className = 'formNoError';
						}

					// Show errors
					ids = response[1].split(',');

					// add the HTML5 elements with errors
					var errorOnPage;
					if (errorFields.length) {
						for (i = 0; i < errorFields.length; i++) {
							if (typeof errorFields[i].componentId != 'undefined') {
								if (typeof page == 'undefined' || (page - 1) == errorFields[i].page) {
									ids.push(errorFields[i].componentId);
									if (typeof errorOnPage != 'undefined') {
										errorOnPage = errorFields[i].page < errorOnPage ? errorFields[i].page : errorOnPage;
									} else {
										errorOnPage = errorFields[i].page;
									}
								}
							}
						}
					}


					var errorComponents = [];
					for (i = 0; i < ids.length; i++) {
						if (!isNaN(parseInt(ids[i])))
						{
							if (document.getElementById('component' + ids[i]))
							{
								document.getElementById('component' + ids[i]).className = 'formError';
							}
							errorComponents.push(ids[i]);
							success = false;
						}
					}

					// lets detect if the multiple page form is submitted

					var changePageHTML5Errors = false;
					if (totalJSDetectedPages > 0 && RSClickedSubmitElement && submits.indexOf(RSClickedSubmitElement) >= 0 && typeof errorOnPage != 'undefined') {
						changePageHTML5Errors = true;
					}
					//return false;
					if (response.length == 4 || changePageHTML5Errors)
					{
						if (response.length == 4) {
							page = parseInt(response[2]) - 1;
							totalPages = parseInt(response[3]);
							if (changePageHTML5Errors) {
								page = page > errorOnPage ? errorOnPage : page;
							}
						} else {
							if (changePageHTML5Errors) {
								page = errorOnPage;
								totalPages = totalJSDetectedPages;
							}
						}

						rsfp_changePage(formId, page, totalPages, false);
					}

					if (typeof ajaxExtraValidationScript[formId] == 'function') {
						// rewrite the response so that all the error components are added (that includes HTML5 fields)
						if (errorComponents.length) {
							response[1] = errorComponents.join();
						}
						ajaxExtraValidationScript[formId]('afterSend', formId, {'url': url, 'params': RSFormPro.Ajax.Params, 'response': response, 'parentErrorClass': errorClasses.parent, 'fieldErrorClass': errorClasses.field});
					}
				}
				
				if (success == false)
				{
					// The submits must be clickable again
					for (i = 0; i < submits.length; i++) {
						submits[i].disabled = false;
					}

					try {
						if (typeof page == 'undefined' || page == 0) {
							RSFormPro.callbacks.runCallback(formId, 'afterValidationFailed'); // callback functions if validation fails when form is submitted
						} else {
							RSFormPro.callbacks.runCallback(formId, 'nextPageFailed'); // callback functions if validation fails when a specific page of the form is checked
						}
						if (document.getElementById('rsform_error_' + formId)) {
							document.getElementById('rsform_error_' + formId).style.display = 'block';
						}
					}
					catch (err) { }
				}

				if (success == true) {
					// Change the page if the validation passes
					if (page) {
						rsfp_changePage(formId, page, totalPages, false);
						// The submits must be clickable again
						for (i = 0; i < submits.length; i++) {
							submits[i].disabled = false;
						}
					} else {
						// the submit button has been presed so we need to submit the form
						// if the submit button or any other element has the id submit the form.submit() function is overwritten
						if (typeof form.submit != 'function') {
							document.createElement('form').submit.call(form)
						} else {
							form.submit();
						}
					}

					try {
						if (typeof page == 'undefined' || page == 0) {
							RSFormPro.callbacks.runCallback(formId, 'afterValidationSuccess'); // callback functions if validation succeeds
						} else {
							RSFormPro.callbacks.runCallback(formId, 'nextPageSuccess'); // callback functions if validation succeeds when a specific page of the form is checked
						}
						document.getElementById('rsform_error_' + formId).style.display = 'none';
					}
					catch (err) { }
				}
				return success;
			}
		};

		return false;
	},
	overrideSubmit: function(formId, validationParams) {
        var form = RSFormPro.getForm(formId);
        var submitElement = RSFormPro.getElementByType(formId, 'submit');
		var i, j;
        for (i = 0; i < submitElement.length; i++) {
			if (RSFormProUtils.hasClass(submitElement[i],'rsform-submit-button')) {
				RSFormProUtils.addEvent(submitElement[i],'click', (function(event) {
					event.preventDefault();
					RSClickedSubmitElement = this;
					for (j = 0; j < submitElement.length; j++) {
						submitElement[j].setAttribute('data-disableonsubmit','1');
					}
					RSFormPro.Ajax.validate(form, undefined, validationParams);
				}));
			}
		}
	}
};

RSFormPro.callbacks = {
	allCallbacks: {},
	
	addCallback: function(formId, callbackName, args) {
		var base = RSFormPro.callbacks;
		
		if (typeof base.allCallbacks[formId] == 'undefined') {
			base.allCallbacks[formId] = {}
		}
		
		if (typeof base.allCallbacks[formId][callbackName] == 'undefined') {
			base.allCallbacks[formId][callbackName] = [];
		}
		
		base.allCallbacks[formId][callbackName].push(args);
	},
	
	runCallback: function(formId, callbackName) {
		var base = RSFormPro.callbacks;
		if (typeof base.allCallbacks[formId] != 'undefined' && typeof base.allCallbacks[formId][callbackName] != 'undefined' && base.allCallbacks[formId][callbackName].length > 0) {
			for (var i = 0; i < base.allCallbacks[formId][callbackName].length; i++) {
				var args = base.allCallbacks[formId][callbackName][i];
				var functionName = args[0];
				
				var functionArgs =  [];
				for (var j = 1; j < args.length; j++) {
					functionArgs.push(args[j]);
				}
				
				// execute the callback function set for this trigger
				functionName.apply(self, functionArgs);
			}
		}
	}
};

/* Helper functions */

var RSFormProUtils = {
	addEvent: function(obj, evType, fn) {
		if (obj.addEventListener)
		{
			obj.addEventListener(evType, fn, false);
			return true;
		}
		else if (obj.attachEvent)
		{
			var r = obj.attachEvent("on"+evType, fn);
			return r;
		}
		else
		{
			return false;
		}
	},
	hasClass: function (el, name) {
		return new RegExp('(\\s|^)' + name + '(\\s|$)').test(el.className);
	},
	addClass: function(el, name) {
		if (!RSFormProUtils.hasClass(el, name)) {
			el.className += (el.className ? ' ' : '') + name;
		}
	},
	removeClass: function(el, name) {
		if (RSFormProUtils.hasClass(el, name)) {
			el.className = el.className.replace(new RegExp('(\\s|^)' + name + '(\\s|$)'),' ').replace(/^\s+|\s+$/g, '');
		}
	},
	setDisplay: function (items, value) {
		for (var i = 0; i < items.length; i++) {
			if (!RSFormPro.usePositioning) {
				items[i].style.display = value;
			} else {
				value === 'none' ? RSFormProUtils.addClass(items[i], 'formHidden') : RSFormProUtils.removeClass(items[i], 'formHidden');
			}
		}
	},
	setAttr: function (items, attr, value) {
        for (var i = 0; i < items.length; i++) {
            items[i].setAttribute(attr, value);
        }
	},
    remAttr: function (items, attr) {
        for (var i = 0; i < items.length; i++) {
            items[i].removeAttribute(attr);
        }
    },
	getChecked: function (items) {
		var elements = [];
		var element, tagName;
        for (var i = 0; i < items.length; i++) {
        	element = items[i];
            tagName = element.tagName || element.nodeName;

            if (tagName == 'INPUT' && element.type && element.type.toUpperCase() == 'CHECKBOX' && element.checked == true) {
				elements.push(element);
			}
        }

        return elements;
	},
    getUnchecked: function (items) {
        var elements = [];
        var element, tagName;
        for (var i = 0; i < items.length; i++) {
            element = items[i];
            tagName = element.tagName || element.nodeName;

            if (tagName == 'INPUT' && element.type && element.type.toUpperCase() == 'CHECKBOX' && !element.checked) {
                elements.push(element);
            }
        }

        return elements;
    },
	getAlias: function(str) {
		str = str.replace(/\-/g, ' ');

		if (!String.prototype.trim) {
			str = str.replace(/^\s+|\s+$/g, '');
		} else {
			str = str.trim();
		}

		str = str.toLowerCase();
		str = str.replace(/(\s|[^A-Za-z0-9\-])+/g, '-');
		str = str.replace(/^\-+|\-+$/g, '');

		return str;
	},
	getElementsByClassName: function (className, tag, elm) {
		if (document.getElementsByClassName) {
			getElementsByClassName = function (className, tag, elm) {
				elm = elm || document;
				var elements = elm.getElementsByClassName(className),
					nodeName = (tag)? new RegExp("\\b" + tag + "\\b", "i") : null,
					returnElements = [],
					current;
				for(var i=0, il=elements.length; i<il; i+=1){
					current = elements[i];
					if(!nodeName || nodeName.test(current.nodeName)) {
						returnElements.push(current);
					}
				}
				return returnElements;
			};
		} else if (document.evaluate) {
			getElementsByClassName = function (className, tag, elm) {
				tag = tag || "*";
				elm = elm || document;
				var classes = className.split(" "),
					classesToCheck = "",
					xhtmlNamespace = "http://www.w3.org/1999/xhtml",
					namespaceResolver = (document.documentElement.namespaceURI === xhtmlNamespace)? xhtmlNamespace : null,
					returnElements = [],
					elements,
					node;
				for(var j=0, jl=classes.length; j<jl; j+=1){
					classesToCheck += "[contains(concat(' ', @class, ' '), ' " + classes[j] + " ')]";
				}
				try	{
					elements = document.evaluate(".//" + tag + classesToCheck, elm, namespaceResolver, 0, null);
				}
				catch (e) {
					elements = document.evaluate(".//" + tag + classesToCheck, elm, null, 0, null);
				}
				while ((node = elements.iterateNext())) {
					returnElements.push(node);
				}
				return returnElements;
			};
		} else {
			getElementsByClassName = function (className, tag, elm) {
				tag = tag || "*";
				elm = elm || document;
				var classes = className.split(" "),
					classesToCheck = [],
					elements = (tag === "*" && elm.all)? elm.all : elm.getElementsByTagName(tag),
					current,
					returnElements = [],
					match;
				for(var k=0, kl=classes.length; k<kl; k+=1){
					classesToCheck.push(new RegExp("(^|\\s)" + classes[k] + "(\\s|$)"));
				}
				for(var l=0, ll=elements.length; l<ll; l+=1){
					current = elements[l];
					match = false;
					for(var m=0, ml=classesToCheck.length; m<ml; m+=1){
						match = classesToCheck[m].test(current.className);
						if (!match) {
							break;
						}
					}
					if (match) {
						returnElements.push(current);
					}
				}
				return returnElements;
			};
		}

		return getElementsByClassName(className, tag, elm);
	}
};

function isset() {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: FremyCompany
  // +   improved by: Onno Marsman
  // +   improved by: Rafał Kukawski
  // *     example 1: isset( undefined, true);
  // *     returns 1: false
  // *     example 2: isset( 'Kevin van Zonneveld' );
  // *     returns 2: true
  var a = arguments,
    l = a.length,
    i = 0,
    undef;

  if (l === 0) {
    throw new Error('Empty isset');
  }

  while (i !== l) {
    if (a[i] === undef || a[i] === null) {
      return false;
    }
    i++;
  }
  return true;
}

/* Legacy functions */

function rsfp_geolocation(term, id, mapid,  map, marker, geocoder, type) {
	return RSFormPro.initGeoLocation(term, id, mapid, map, marker, geocoder, type);
}

function refreshCaptcha(componentId, captchaPath) {
	return RSFormPro.refreshCaptcha(componentId, captchaPath);
}

function number_format(number, decimals, dec_point, thousands_sep) {
	return RSFormPro.formatNumber(number, decimals, dec_point, thousands_sep);
}

function rsfp_toNumber(number, decimal, thousands, decimals) {
	return RSFormPro.toNumber(number, decimal, thousands, decimals);
}

function rsfp_getForm(formId) {
	return RSFormPro.getForm(formId);
}

function rsfp_getValue(formId, name) {
	return RSFormPro.getValue(formId, name);
}

function rsfp_verifyChecked(formId, name, value) {
	return RSFormPro.isChecked(formId, name, value);
}

function rsfp_getBlock(formId, block) {
	return RSFormPro.getBlock(formId, block);
}

function rsfp_getFieldsByName(formId, name) {
	return RSFormPro.getFieldsByName(formId, name);
}

function rsfp_addEvent(obj, evType, fn) {
	return RSFormProUtils.addEvent(obj, evType, fn);
}

function rsfp_setDisplay(items, value) {
	return RSFormProUtils.setDisplay(items, value);
}

function stringURLSafe(str) {
	return RSFormProUtils.getAlias(str);
}

function rsfp_changePage(formId, page, totalPages, validate, errorClasses) {
	return RSFormPro.Pages.change(formId, page, totalPages, validate, errorClasses);
}

function rsfp_hidePage(thePage) {
	return RSFormPro.Pages.hide(thePage);
}

function rsfp_showPage(thePage) {
	return RSFormPro.Pages.show(thePage);
}

function rsfp_checkValidDate(fieldName) {
	return RSFormPro.disableInvalidDates(fieldName);
}

function rsfp_addCondition(formId, name, fnCondition) {
	return RSFormPro.Conditions.add(formId, name, fnCondition);
}

function rsfp_runAllConditions(formId) {
	return RSFormPro.Conditions.runAll(formId);
}

function rsfp_setCalculationsEvents(formId, fields) {
	return RSFormPro.Calculations.addEvents(formId, fields);
}

function getElementsByClassName(className, tag, elm) {
	return RSFormProUtils.getElementsByClassName(className, tag, elm);
}

function buildXmlHttp() {
	return RSFormPro.Ajax.getXHR();
}

function ajaxDisplayValidationErrors(formComponents, task, formId, data) {
	return RSFormPro.Ajax.displayValidationErrors(formComponents, task, formId, data);
}

function ajaxValidation(form, page, errorClasses) {
	return RSFormPro.Ajax.validate(form, page, errorClasses);
}