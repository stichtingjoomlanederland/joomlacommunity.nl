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
				var errorElements = RSFormPro.HTML5.validation(formId);
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
						RSFormPro.submitForm(this.form);
					}
				}
			}));
		}
	}
};

RSFormPro.formEvents = {};

RSFormPro.addFormEvent = function(formId, func) {
	if (typeof RSFormPro.formEvents[formId] !== 'object') {
		RSFormPro.formEvents[formId] = [];
	}

	RSFormPro.formEvents[formId].push(func);
};

RSFormPro.submitForm = function(form) {
	var formId = form.elements['form[formId]'].value;
	var func;

	if (typeof RSFormPro.formEvents[formId] === 'object' && RSFormPro.formEvents[formId].length > 0) {
		if (func = RSFormPro.formEvents[formId].shift()) {
			if (typeof func === 'function') {
				func();

				return false;
			}
		}
	}

	// if the submit button or any other element has the id submit the form.submit() function is overwritten
	if (typeof form.submit === 'function') {
		form.submit();
	} else {
		document.createElement('form').submit.call(form)
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
					event.preventDefault();
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
	thankYouContainer.parentNode.removeChild(thankYouContainer);

	RSFormProUtils.addClass(document.body, 'rsfp_popup_activated');

	var outer = document.createElement('div');
	outer.setAttribute('class', 'rsfp_thankyou_popup_outer');
	outer.onclick = function() {
		if (event && event.target && (RSFormProUtils.hasClass(event.target, 'rsfp_thankyou_popup_outer') || RSFormProUtils.hasClass(event.target, 'rsfp_thankou_popup_close_btn'))) {
            destroyPopup();
            redirectUrl();
		}
	};

	var inner = document.createElement('div');
	inner.setAttribute('class', 'rsfp_thankyou_popup_inner');
	inner.setAttribute('id', 'rsfp_thankyou_popup_inner');
	inner.innerHTML = content;

	var close = document.createElement('div');
	close.setAttribute('class', 'rsfp_thankou_popup_close_btn');
	close.innerHTML = '&times;';

	outer.appendChild(inner);
	inner.appendChild(close);

	document.body.appendChild(outer);

	document.getElementById('rsfp_thankyou_popup_inner').style.marginTop = (window.innerHeight - document.getElementById('rsfp_thankyou_popup_inner').offsetHeight) / 2 + 'px';

	var continueButton = outer.querySelector('[name=continue]');
	if (continueButton) {
		continueButton.removeAttribute('onclick');
		continueButton.onclick = function() {
			destroyPopup();
			redirectUrl();
		}
	}

	var redirectUrl = function() {
        if (gotoUrl.length > 0) {
            document.location = gotoUrl;
        } else {
            document.location.reload(true);
        }
	};

	var destroyPopup = function() {
        RSFormProUtils.removeClass(document.body, 'rsfp_popup_activated');

        document.body.removeChild(outer);
	};
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
		if (elementClasses.indexOf(cls) > -1) {
			return el;
		}
	}

	return false;
};

/* Reset all values, used with reset button for the submit */
RSFormPro.resetElements = function(formId) {
	var form 	= RSFormPro.getForm(formId);

	if (form && form.elements.length) {
		RSFormPro.resetValues(form.elements);
	}
};

/* ScrollTo functions */
RSFormPro.scrollToElement = function(element){
	var to = element.offsetTop;
	var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
	var documentView = window.innerHeight + scrollTop;

	// this is more specific for the modern browsers
	if (typeof element.getBoundingClientRect === 'function') {
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
	
	window.setTimeout(function() {
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

RSFormPro.geoLocationTimeouts = {};

RSFormPro.initGeoLocation = function(term, id, mapid, map, marker, geocoder, type) {
	var content = document.getElementById('rsform_geolocation'+id);
	var address	= document.getElementById(mapid).clientWidth;

	document.getElementById('rsform_geolocation'+id).style.width = address+'px';
	document.getElementById('rsform_geolocation'+id).style.display = 'none';
	document.getElementById('rsform_geolocation'+id).innerHTML = '';

	if (typeof RSFormPro.geoLocationTimeouts[id] !== 'undefined')
	{
		window.clearTimeout(RSFormPro.geoLocationTimeouts[id]);
	}

	if (term != '') {
		RSFormPro.geoLocationTimeouts[id] = window.setTimeout(function(){
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
		}, 500);
	}
};

RSFormPro.googleMapIds = [];

RSFormPro.requestLocation = function() {
	if (navigator && navigator.geolocation && navigator.geolocation.getCurrentPosition) {
		navigator.geolocation.getCurrentPosition(function(position){
			var id, map, marker;

			for (var i = 0; i < RSFormPro.googleMapIds.length; i++)
			{
				id = RSFormPro.googleMapIds[i];
				map = window['rsformmap' + id];
				marker = window['rsformmarker' + id];

				map.setCenter({lat: position.coords.latitude, lng: position.coords.longitude});
				marker.setPosition({lat: position.coords.latitude, lng: position.coords.longitude});
				google.maps.event.trigger(marker, 'drag');
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

RSFormPro.addMoreFiles = function(button) {
	var clone = button.previousSibling.cloneNode(true);

	var inputs = clone.getElementsByTagName('input');
	if (inputs.length)
	{
		for (var i = 0; i < inputs.length; i++)
		{
			if (inputs[i].type && inputs[i].type.toUpperCase() === 'FILE')
			{
				var fieldName = inputs[i].getAttribute('id');
				inputs[i].value = '';
			}
		}
	}

	var images = clone.getElementsByTagName('img');
	if (images.length)
	{
		for (var j = 0; j < images.length; j++)
		{
			if (RSFormProUtils.hasClass(images[j], 'rsfp-image-polaroid'))
			{
				images[j].parentNode.parentNode.removeChild(images[j].parentNode);
			}
		}
	}

	button.parentNode.appendChild(clone);

	var maxFiles = parseInt(button.getAttribute('data-rsfp-maxfiles'));
	if (maxFiles > 0)
	{
		if (RSFormPro.getFieldsByName(button.getAttribute('data-rsfp-formid'), fieldName).length / 2 >= maxFiles)
		{
			button.disabled = true;
		}
	}
};

RSFormPro.loadImage = function(input) {
	if ('files' in input && typeof FileReader === 'function')
	{
		var file, reader;

		var images = input.parentNode.querySelectorAll('.rsfp-image-container');

		for (var j = 0; j < images.length; j++)
		{
			images[j].parentNode.removeChild(images[j]);
		}

		for (var i = 0; i < input.files.length; i++)
		{
			file = input.files[i];

			reader = new FileReader();
			reader.onload = function(e) {
				var div = document.createElement('div');
				div.setAttribute('class', 'rsfp-image-container');

				var img = document.createElement('img');
				img.setAttribute('src', e.target.result);
				img.setAttribute('alt', file.name);
				img.setAttribute('title', file.name);
				img.setAttribute('class', 'rsfp-image-polaroid');
				img.onerror = function() {
					img.src = "data:image/svg+xml,%3Csvg role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3Cpath fill='currentColor' d='M256 8C119.034 8 8 119.033 8 256s111.034 248 248 248 248-111.034 248-248S392.967 8 256 8zm130.108 117.892c65.448 65.448 70 165.481 20.677 235.637L150.47 105.216c70.204-49.356 170.226-44.735 235.638 20.676zM125.892 386.108c-65.448-65.448-70-165.481-20.677-235.637L361.53 406.784c-70.203 49.356-170.226 44.736-235.638-20.676z'%3E%3C/path%3E%3C/svg%3E";
					img.onclick = false;
				};
				img.onclick = function() {
					var modal = document.createElement('div');
					modal.setAttribute('class', 'rsfp-modal');

					var close = document.createElement('span');
					close.innerHTML = '&times;';
					close.setAttribute('class', 'rsfp-modal-close-button');
					modal.onclick = close.onclick = function() {
						modal.parentNode.removeChild(modal);
					};

					var img = document.createElement('img');
					img.setAttribute('class', 'rsfp-modal-image');
					img.setAttribute('src', this.getAttribute('src'));

					var caption = document.createElement('div');
					caption.setAttribute('class', 'rsfp-modal-caption');
					caption.innerHTML = this.getAttribute('alt');

					modal.appendChild(close);
					modal.appendChild(img);
					modal.appendChild(caption);

					document.getElementsByTagName('body')[0].appendChild(modal);

					modal.style.display = 'block';
				};

				div.appendChild(img);

				input.parentNode.insertBefore(div, input.nextSibling);
			};

			reader.readAsDataURL(file);
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
										var tmpDate = RSFormPro.jQueryCalendar.calendars[formId][name].calendarInstance.getValue();

										if (RSFormPro.jQueryCalendar.calendars[formId][name].hiddenFormat.indexOf('HH:mm') === -1)
										{
											tmpDate.setHours(0, 0, 0, 0);
										}

										return parseInt(tmpDate.getTime() / 1000).toString();
									}

                                if (element.hasOwnProperty('rsfpGetValue'))
                                {
                                    return element.rsfpGetValue();
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

								if (element.id)
								{
									// Range Slider
									if (element.id.indexOf('rs-range-slider') === 0 && typeof jQuery !== 'undefined')
									{
										jQuery(element).data('ionRangeSlider').reset();
									}

									// Date and Time Picker
									if (element.id.indexOf('txtjQcal') === 0 && typeof jQuery !== 'undefined' && typeof RSFormPro.jQueryCalendar !== 'undefined')
									{
										jQuery(element).datetimepicker('reset');
									}

									// Calendar
									if (element.id.indexOf('txtcal') === 0 && typeof RSFormPro.YUICalendar !== 'undefined')
									{
										var match = element.id.match(/^txtcal([0-9]+)_[0-9]+$/);
										if (match.length === 2)
										{
											var formId = match[1];
											var calendarName = element.name.match(/^form\[(.*)\]$/)[1];

											if (typeof RSFormPro.YUICalendar.calendars[formId][calendarName] !== 'undefined')
											{
												RSFormPro.YUICalendar.calendars[formId][calendarName].reset();
											}
										}
									}
								}

								RSFormPro.triggerEvent(element, 'change');
								RSFormPro.triggerEvent(element, 'input');
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
						var unselected = [];
						for (var o = 0; o < element.options.length; o++) {
							element.options[o].selected = element.options[o].defaultSelected;

							if (!element.options[o].defaultSelected) {
								unselected.push(o);
							}
						}
						
						// in safari something must be selected
						if (element.options.length == unselected.length) {
							element.selectedIndex = 0;
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
								default:
									if (!element.name || element.name != 'form[' + name + ']') continue;
									if (!element.value) continue;

									if (element.value == value)
									{
										isChecked = true;
										break primary_loop;
									}

									break;
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
		var multipleTypes = ['radio'];

		var checkValidityExists = true;
		var page = 0;
		var componentId;
		var elementId;
		var match;

		if (form.elements.length) {
			for (var i = 0; i < form.elements.length; i++) {
				if (!checkValidityExists) {
					break;
				}

				// if the multiple form is present and also validation we will need to find in which page the element is located
				if (form.elements[i].type == 'button') {
					var onclick = form.elements[i].getAttribute('onclick');
					if (typeof onclick == 'string' && (onclick.indexOf('rsfp_changePage') > -1 || onclick.indexOf('RSFormPro.Pages.change') > -1)) {
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
				if (html5types.indexOf(form.elements[i].type) > -1) {
					if ( typeof(form.elements[i].checkValidity) === 'function' && checkValidityExists) {
						if (!form.elements[i].checkValidity()) {
							var elementObj = {
								field: form.elements[i],
								page: page
							};
							// try to get the componentId
							elementId = form.elements[i].getAttribute('id');
							if (multipleTypes.indexOf(form.elements[i].type) > -1)
							{
								if (match = form.elements[i].getAttribute('name').match(/form\[(.*)\]/))
								{
									elementId = match[1];
								}
							}
							componentId = RSFormPro.HTML5.getComponentId(formId, elementId);

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
			RSFormPro.HTML5.componentIds[formId][elementAlias] = false;
			var block = RSFormPro.getBlock(formId, RSFormProUtils.getAlias(elementAlias));

			if (typeof block[0] !== 'undefined') {
				var componentIdBlock;

				componentIdBlock = RSFormProUtils.getElementsByClassName('formNoError', 'span', block[0]);
				if (componentIdBlock.length === 0) {
					componentIdBlock = RSFormProUtils.getElementsByClassName('formError', 'span', block[0]);
				}

				if (componentIdBlock.length > 0) {
					var componentId = componentIdBlock[0].getAttribute('id');
					RSFormPro.HTML5.componentIds[formId][elementAlias] = componentId.replace('component', '');
				}
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
				RSFormPro.Pages.hide(thePage);
			}
		}

		thePage = document.getElementById('rsform_' + formId + '_page_' + page);
		if (thePage) {
			RSFormPro.Pages.show(thePage);
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

		var i, c;

		if (typeof condition.details === 'object')
		{
			for (i = 0; i < condition.details.length; i++)
			{
				detail = condition.details[i];
                isChecked = RSFormPro.isChecked(formId, detail.ComponentName, detail.value);
                conditions.push(isChecked === (detail.operator === 'is'));
			}

			items = [];

			if (condition.ComponentNames)
			{
				for (c = 0; c < condition.ComponentNames.length; c++)
				{
					if (parseInt(condition.block) === 1)
					{
						items = items.concat(RSFormPro.getBlock(formId, RSFormProUtils.getAlias(condition.ComponentNames[c])));
					}
					else
					{
						items = items.concat(RSFormPro.getFieldsByName(formId, condition.ComponentNames[c]));
					}
				}
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
					if (condition.ComponentNames)
					{
						for (c = 0; c < condition.ComponentNames.length; c++)
						{
							RSFormPro.resetValues(RSFormPro.getFieldsByName(formId, condition.ComponentNames[c]));
						}
					}
				}
			}
		}
	},
	runAll: function(formId) {
		var func = window['rsfp_runAllConditions' + formId];
		if (typeof func === 'function') {
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
	},
	delayRun: function(formId) {
		var func = window['rsfp_runAllConditions' + formId];
		if (typeof func === 'function') {
			RSFormProUtils.addEvent(window, 'load', func);
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
	toMinutes: function(seconds) {
        return Math.round(parseFloat(seconds) / 86400 * 24 * 60);
    },
	addEvents: function(formId, fields) {
		RSFormProUtils.addEvent(window, 'load', function(){
			RSFormPro.Calculations._addEvents(formId, fields);
		});
	},
	_addEvents: function(formId, fields) {
		var func 		= window['rsfp_Calculations' + formId];
		var thefields	= fields ? fields : RSFormProPrices;
		var event 		= 'change';
		var objects, tagName;
		
		var resetElements = RSFormPro.getElementByType(formId, 'reset');
		if (resetElements.length > 0)
		{
			for (var i = 0; i < resetElements.length; i++)
			{
				RSFormProUtils.addEvent(resetElements[i], 'click', function() {
					if (typeof func === 'function') {
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

				if (tagName === 'INPUT' || tagName === 'SELECT') {
					event = 'change';
					if (tagName === 'INPUT' && typeof objects[i].type == 'string') {
						switch (objects[i].type.toUpperCase())
						{
							default:
								event = 'change';
							break;
							
							case 'NUMBER':
							case 'TEXT':
								event = 'input';
								
								if (typeof RSFormPro.jQueryCalendar !== 'undefined' &&
									typeof RSFormPro.jQueryCalendar.calendars[formId] !== 'undefined' &&
									typeof RSFormPro.jQueryCalendar.calendars[formId][field] !== 'undefined')
									{
										RSFormPro.jQueryCalendar.calendars[formId][field].calendarInstance.setOptions({onChangeDateTime: function() {
											if (typeof func === 'function') {
												func();
											}
										}});
									}
								
								if (typeof RSFormPro.YUICalendar !== 'undefined' &&
									typeof RSFormPro.YUICalendar.calendars[formId] !== 'undefined' &&
									typeof RSFormPro.YUICalendar.calendars[formId][field] !== 'undefined')
									{
										RSFormPro.YUICalendar.calendars[formId][field].selectEvent.subscribe(function() {
											if (typeof func === 'function') {
												func();
											}
										}, RSFormPro.YUICalendar.calendars[formId][field], true);
									}

								if (objects[i].hasOwnProperty('rsfpOnChange'))
								{
									objects[i].rsfpOnChange(func);
								}
							break;
						}
					}

					RSFormProUtils.addEvent(objects[i], event, function() {
						if (typeof func === 'function') {
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
			var ids, i, j, id, formComponent, elementBlock;
			var parentErrorClass = typeof data.parentErrorClass !== 'undefined' && data.parentErrorClass.length > 0 ? data.parentErrorClass : false;
			var fieldErrorClass = typeof data.fieldErrorClass !== 'undefined' && data.fieldErrorClass.length > 0 ? data.fieldErrorClass : false;

			ids = data.response[0].split(',');
			for (i = 0; i < ids.length; i++)
			{
				id = parseInt(ids[i]);

				if (isNaN(id))
				{
					continue;
				}

				if (typeof formComponents[id] === 'undefined')
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
						RSFormProUtils.removeClass(formComponent[j], 'rsform-error');
						formComponent[j].removeAttribute('aria-invalid');
						if (formComponent[j].getAttribute('aria-describedby') === 'component' + id) {
							formComponent[j].removeAttribute('aria-describedby');
						}

						if (parentErrorClass)
						{
							try
							{
								if (elementBlock = RSFormPro.getBlock(formId, RSFormProUtils.getAlias(formComponents[id])))
								{
									RSFormProUtils.removeClass(elementBlock[0], parentErrorClass);
								}
							}
							catch (err) {}
						}

						if (fieldErrorClass)
						{
							RSFormProUtils.removeClass(formComponent[j], fieldErrorClass);
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
				
				if (typeof formComponents[id] === 'undefined')
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
						
						if (parentErrorClass)
						{
							try
							{
								if (elementBlock = RSFormPro.getBlock(formId, RSFormProUtils.getAlias(formComponents[id])))
								{
									RSFormProUtils.addClass(elementBlock[0], parentErrorClass);
								}
							}
							catch (err) {}
						}

						if (fieldErrorClass)
						{
							RSFormProUtils.addClass(formComponent[j], fieldErrorClass);
						}

						if (['INPUT', 'SELECT', 'TEXTAREA'].indexOf(formComponent[j].nodeName) > -1)
						{
							if (formComponent[j].getAttribute('type') !== 'button') {
								formComponent[j].setAttribute('aria-invalid', 'true');
								formComponent[j].setAttribute('aria-describedby', 'component' + id);

								if (!doFocus && typeof formComponent[j].focus === 'function') {
									formComponent[j].focus();
									doFocus = true;
								}
							}
						}
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
			formId = form.elements['form[formId]'].value,
			totalJSDetectedPages = 0,
			i,
			j;

		// File uploads AJAX validation variabiles
		var ajaxSkippedFileUploads = [],
			filesLength = 0,
			filesCollection = [],
			identicalFiles = [];
		
		if (typeof errorClasses != 'object')
		{
			errorClasses = {'parent': '', 'field': ''}
		}

		for (i=0; i<form.elements.length; i++)
		{
			// try and detect total pages of the form
			if (form.elements[i].type == 'button') {
				var onclick = form.elements[i].getAttribute('onclick');
				if (typeof onclick === 'string' && (onclick.indexOf('rsfp_changePage') > -1 || onclick.indexOf('RSFormPro.Pages.change') > -1)) {
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

			if (form.elements[i].type == 'file')
			{
				if ('files' in form.elements[i])
				{
					try
					{
						if (RSFormPro.usePositioning)
						{
							if (form.elements[i].offsetParent !== document.getElementsByTagName('body')[0])
							{
								throw 'CONDITIONAL_HIDDEN';
							}
						}
						else
						{
							if (form.elements[i].offsetParent === null)
							{
								throw 'CONDITIONAL_HIDDEN';
							}
						}

						if (form.elements[i].getAttribute('data-rsfp-skip-ajax') === 'true')
						{
							// Not already processed
							if (ajaxSkippedFileUploads.indexOf(form.elements[i].name) === -1)
							{
								// All fields are identical, make sure this code is only run once.
								ajaxSkippedFileUploads.push(form.elements[i].name);

								// Get all identical fields
								identicalFiles = form.elements[form.elements[i].name];

								filesLength = 0;
								filesCollection = [];

								if (typeof identicalFiles === 'object')
								{
									// NodeList (multiple inputs)
									if (typeof identicalFiles.length !== 'undefined' && identicalFiles.length > 0)
									{
										for (var tmp = 0; tmp < identicalFiles.length; tmp++)
										{
											if (identicalFiles[tmp].files.length > 0)
											{
												filesLength += identicalFiles[tmp].files.length;
												filesCollection = filesCollection.concat(identicalFiles[tmp].files[0]);
											}
										}
									}
									else if (typeof identicalFiles.files !== 'undefined')
									{
										// Just one input
										filesLength += identicalFiles.files.length;
										filesCollection = filesCollection.concat(identicalFiles.files[0]);
									}
								}
							}
							else
							{
								throw 'AJAX_SKIPPED_FILE_UPLOAD';
							}
						}
						else
						{
							filesLength = form.elements[i].files.length;
							filesCollection = form.elements[i].files;
						}

						if (filesLength === 0 && form.elements[i].getAttribute('data-rsfp-required') === 'true')
						{
							throw new RSFormPro.validationError('VALIDATION_ERROR', RSFormPro.Translations.translate(formId, form.elements[i].getAttribute('id'), 'VALIDATIONMESSAGE'));
						}

						if (form.elements[i].getAttribute('data-rsfp-required') === 'true' || filesLength > 0)
						{
							if (form.elements[i].getAttribute('data-rsfp-minfiles'))
							{
								var minFiles = parseInt(form.elements[i].getAttribute('data-rsfp-minfiles'));

								if (filesLength < minFiles)
								{
									throw new RSFormPro.validationError('VALIDATION_ERROR', RSFormProUtils.sprintf(RSFormPro.Translations.translate(formId, form.elements[i].getAttribute('id'), 'COM_RSFORM_MINFILES_REQUIRED'), minFiles));
								}
							}

							if (form.elements[i].getAttribute('data-rsfp-maxfiles'))
							{
								var maxFiles = parseInt(form.elements[i].getAttribute('data-rsfp-maxfiles'));

								if (filesLength > maxFiles)
								{
									throw new RSFormPro.validationError('VALIDATION_ERROR', RSFormProUtils.sprintf(RSFormPro.Translations.translate(formId, form.elements[i].getAttribute('id'), 'COM_RSFORM_MAXFILES_REQUIRED'), maxFiles));
								}
							}
						}

						for (var f = 0; f < filesLength; f++)
						{
							var file = filesCollection[f];
							var maxSize = parseInt(form.elements[i].getAttribute('data-rsfp-size'));
							if ('size' in file && maxSize > 0)
							{
								if (file.size > maxSize)
								{
									throw new RSFormPro.validationError('VALIDATION_ERROR', RSFormProUtils.sprintf(RSFormPro.Translations.translate(formId, form.elements[i].getAttribute('id'), 'COM_RSFORM_FILE_EXCEEDS_LIMIT'), file.name, maxSize / 1024));
								}
							}
							if ('name' in file)
							{
								var exts = form.elements[i].getAttribute('data-rsfp-exts');
								if (exts)
								{
									exts = JSON.parse(exts);

									var ext = file.name.slice((file.name.lastIndexOf(".") - 1 >>> 0) + 2);
									if (exts.indexOf(ext.toLowerCase()) === -1)
									{
										throw new RSFormPro.validationError('VALIDATION_ERROR', RSFormProUtils.sprintf(RSFormPro.Translations.translate(formId, form.elements[i].getAttribute('id'), 'COM_RSFORM_FILE_EXTENSION_NOT_ALLOWED'), file.name));
									}
								}
							}
						}
					}
					catch (error)
					{
						if (typeof error === 'object')
						{
							if (error.toString() === 'VALIDATION_ERROR')
							{
								var parents = RSFormProUtils.getParents(form.elements[i]),
									page_number,
									parent;
								if (parents.length > 0)
								{
									for (var p = 0; p < parents.length; p++)
									{
										parent = parents[p];

										if ('getAttribute' in parent)
										{
											var hasId = parents[p].getAttribute('id');
											var pageId = 'rsform_' + formId + '_page_';
											if (hasId && hasId.indexOf(pageId) === 0)
											{
												page_number = hasId.slice(pageId.length);
												break;
											}
										}
									}
								}

								var elementObj = {
									field: form.elements[i],
									page: page_number
								};

								// try to get the componentId
								var componentId = RSFormPro.HTML5.getComponentId(formId, form.elements[i].getAttribute('id'));
								if (componentId)
								{
									elementObj.componentId = componentId;

									if (document.getElementById('component' + componentId) && error.message.length > 0)
									{
										document.getElementById('component' + componentId).innerText = error.message;
									}
								}

								errorFields.push(elementObj);
							}
						}
					}
				}
			}

			if (form.elements[i].type !== 'file')
			{
				if (typeof RSFormPro.Editors[form.elements[i].name] === 'function')
				{
					RSFormPro.Ajax.Params.push(form.elements[i].name + '=' + encodeURIComponent(RSFormPro.Editors[form.elements[i].name]()));
				}
				else
				{
					RSFormPro.Ajax.Params.push(form.elements[i].name + '=' + encodeURIComponent(form.elements[i].value));
				}
			}
		}

		errorFields = errorFields.concat(RSFormPro.HTML5.validation(formId));

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
			if (RSFormPro.Ajax.xhr.readyState === 4 && RSFormPro.Ajax.xhr.status === 200) {
				try
				{
					var RSFormProResponse = JSON.parse(RSFormPro.Ajax.xhr.responseText);
				}
				catch (error)
				{
					// Parse invalid data
					if (typeof RSFormProResponse !== 'object')
					{
						var match = RSFormPro.Ajax.xhr.responseText.match(/{.*}/);

						if (match.length > 0)
						{
							RSFormProResponse = JSON.parse(match[0]);
						}
					}
				}

				var response = [];
				var ids;
				var i;

				if (typeof RSFormProResponse.formComponents !== 'undefined')
				{
					// All spans set to no error
					ids = RSFormProResponse.formComponents;

					for (i = 0; i < ids.length; i++)
					{
						if (!isNaN(parseInt(ids[i])) && document.getElementById('component'+ids[i]))
						{
							document.getElementById('component'+ids[i]).className = 'formNoError';
						}
					}

					// Legacy
					response.push(RSFormProResponse.formComponents.join(','));
				}

				if (typeof RSFormProResponse.invalidComponents === 'undefined')
				{
					RSFormProResponse.invalidComponents = [];
				}

				// add the HTML5 elements with errors
				var errorOnPage;
				if (errorFields.length)
				{
					for (i = 0; i < errorFields.length; i++)
					{
						if (typeof errorFields[i].componentId !== 'undefined')
						{
							if (typeof page === 'undefined' || (page - 1) == errorFields[i].page)
							{
								RSFormProResponse.invalidComponents.push(errorFields[i].componentId);
								if (typeof errorOnPage !== 'undefined')
								{
									errorOnPage = errorFields[i].page < errorOnPage ? errorFields[i].page : errorOnPage;
								}
								else 
								{
									errorOnPage = errorFields[i].page;
								}
							}
						}
					}
				}

				// Show errors
				var errorComponents = [];

				var errorMessageContainer;

				for (i = 0; i < RSFormProResponse.invalidComponents.length; i++)
				{
					if (!isNaN(parseInt(RSFormProResponse.invalidComponents[i])))
					{
						errorMessageContainer = document.getElementById('component' + RSFormProResponse.invalidComponents[i]);

						if (errorMessageContainer)
						{
							errorMessageContainer.className = 'formError';

							if (RSFormProResponse.validationMessages && typeof RSFormProResponse.validationMessages[RSFormProResponse.invalidComponents[i]] !== 'undefined')
							{
								errorMessageContainer.innerHTML = RSFormProResponse.validationMessages[RSFormProResponse.invalidComponents[i]];
							}
						}

						errorComponents.push(RSFormProResponse.invalidComponents[i]);
						success = false;
					}
				}

				// lets detect if the multiple page form is submitted
				var changePageHTML5Errors = false;
				if (totalJSDetectedPages > 0 && RSClickedSubmitElement && submits.indexOf(RSClickedSubmitElement) >= 0 && typeof errorOnPage !== 'undefined') {
					changePageHTML5Errors = true;
				}

				// Legacy
				response.push(RSFormProResponse.invalidComponents.join(','));

				if (typeof RSFormProResponse.allPages !== 'undefined' || changePageHTML5Errors)
				{
					// Legacy
					response.push(RSFormProResponse.currentPage);
					response.push(RSFormProResponse.allPages);

					if (typeof RSFormProResponse.allPages !== 'undefined')
					{
						page = parseInt(RSFormProResponse.currentPage) - 1;
						totalPages = parseInt(RSFormProResponse.allPages);
						if (changePageHTML5Errors)
						{
							page = page > errorOnPage ? errorOnPage : page;
						}
					}
					else
					{
						if (changePageHTML5Errors)
						{
							page = errorOnPage;
							totalPages = totalJSDetectedPages;
						}
					}

					RSFormPro.Pages.change(formId, page, totalPages, false);
				}

				if (typeof ajaxExtraValidationScript[formId] === 'function')
				{
					// rewrite the response so that all the error components are added (that includes HTML5 fields)
					if (errorComponents.length)
					{
						response[1] = errorComponents.join();
					}

					ajaxExtraValidationScript[formId]('afterSend', formId, {'url': url, 'params': RSFormPro.Ajax.Params, 'response': response, 'parentErrorClass': errorClasses.parent, 'fieldErrorClass': errorClasses.field, 'json': RSFormProResponse});
				}
				
				if (success === false)
				{
					// The submits must be clickable again
					for (i = 0; i < submits.length; i++)
					{
						submits[i].disabled = false;
					}

					try
					{
						if (typeof page === 'undefined' || page == 0)
						{
							RSFormPro.callbacks.runCallback(formId, 'afterValidationFailed'); // callback functions if validation fails when form is submitted
						}
						else
						{
							RSFormPro.callbacks.runCallback(formId, 'nextPageFailed'); // callback functions if validation fails when a specific page of the form is checked
						}

						if (document.getElementById('rsform_error_' + formId))
						{
							document.getElementById('rsform_error_' + formId).style.display = 'block';
						}
					}
					catch (err) { }
				}
				else
				{
					// Change the page if the validation passes
					if (page)
					{
						RSFormPro.Pages.change(formId, page, totalPages, false);
						// The submits must be clickable again
						for (i = 0; i < submits.length; i++)
						{
							submits[i].disabled = false;
						}
					}
					else
					{
						// the submit button has been pressed so we need to submit the form
						// if the submit button or any other element has the id submit the form.submit() function is overwritten
						RSFormPro.submitForm(form);
					}

					try
					{
						if (typeof page === 'undefined' || page == 0)
						{
							RSFormPro.callbacks.runCallback(formId, 'afterValidationSuccess'); // callback functions if validation succeeds
						}
						else
						{
							RSFormPro.callbacks.runCallback(formId, 'nextPageSuccess'); // callback functions if validation succeeds when a specific page of the form is checked
						}

						if (document.getElementById('rsform_error_' + formId))
						{
							document.getElementById('rsform_error_' + formId).style.display = 'none';
						}
					}
					catch (err) { }
				}

				return success;
			}
		};

		return false;
	},
	overrideSubmit: function(formId, validationParams, disableSubmitButton) {
        var form = RSFormPro.getForm(formId);
        var submitElement = RSFormPro.getElementByType(formId, 'submit');
		var i, j;
        for (i = 0; i < submitElement.length; i++) {
			if (RSFormProUtils.hasClass(submitElement[i],'rsform-submit-button')) {
				RSFormProUtils.addEvent(submitElement[i],'click', (function(event) {
					event.preventDefault();
					RSClickedSubmitElement = this;
					if (disableSubmitButton) {
						for (j = 0; j < submitElement.length; j++) {
							submitElement[j].setAttribute('data-disableonsubmit','1');
						}
					}

					RSFormPro.Ajax.validate(form, undefined, validationParams);
				}));
			}
		}
	}
};

RSFormPro.validationError = function(type, message) {
	this.type = type;
	this.message = message;
};

RSFormPro.validationError.prototype.toString = function() {
	return this.type;
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

/* Translations */

RSFormPro.Translations = {
	translations: {},

	add: function(formId, name, key, translation) {
		if (typeof this.translations[formId + '-' + name] !== 'object')
		{
			this.translations[formId + '-' + name] = {};
		}

		this.translations[formId + '-' + name][key] = translation;
	},

	translate: function(formId, name, key) {
		if (typeof this.translations[formId + '-' + name][key] == 'string')
		{
			return this.translations[formId + '-' + name][key];
		}

		return key;
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
		if (!items || !items.length) {
			return false;
		}

		for (var i = 0; i < items.length; i++) {
			if (!items[i]) {
				continue;
			}

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
	getParents: function(a) {
		var els = [];
		while (a) {
			els.push(a);
			a = a.parentNode;
		}
		// Remove our own element
		if (els.length > 0)
		{
			els.shift()
		}

		return els;
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
	},

	/*!
	**  sprintf.js -- POSIX sprintf(3)-style String Formatting for JavaScript
	**  Copyright (c) 2006-2019 Dr. Ralf S. Engelschall <rse@engelschall.com>
	**  Partly based on Public Domain code by Jan Moesen <http://jan.moesen.nu/>
	**
	**  Permission is hereby granted, free of charge, to any person obtaining
	**  a copy of this software and associated documentation files (the
	**  "Software"), to deal in the Software without restriction, including
	**  without limitation the rights to use, copy, modify, merge, publish,
	**  distribute, sublicense, and/or sell copies of the Software, and to
	**  permit persons to whom the Software is furnished to do so, subject to
	**  the following conditions:
	**
	**  The above copyright notice and this permission notice shall be included
	**  in all copies or substantial portions of the Software.
	**
	**  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	**  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	**  MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
	**  IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
	**  CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
	**  TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
	**  SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	*/
	sprintf: function () {
		/*  argument sanity checking  */
		if (!arguments || arguments.length < 1)
			throw new Error("sprintf: ERROR: not enough arguments");

		/*  initialize processing queue  */
		var argumentnum = 0;
		var done = "", todo = arguments[argumentnum++];

		/*  parse still to be done format string  */
		var m;
		while ((m = /^([^%]*)%(?:(\d+)\$|\((.*?)\))?([#0 +'-]+)?(\*|\d+)?(\.\*|\.\d+)?([%diouxXfFeEcs])((?:.|[\r\n])*)$/.exec(todo))) {
			var pProlog    = m[1],
				pAccessD   = m[2],
				pAccessN   = m[3],
				pFlags     = m[4],
				pMinLength = m[5],
				pPrecision = m[6],
				pType      = m[7],
				pEpilog    = m[8];

			/*  determine substitution  */
			var subst;
			if (pType === "%")
			/*  special case: escaped percent character  */
				subst = "%";
			else {
				/*  parse padding and justify aspects of flags  */
				var padWith = " ";
				var justifyRight = true;
				if (pFlags) {
					if (pFlags.indexOf("0") >= 0)
						padWith = "0";
					if (pFlags.indexOf("-") >= 0) {
						padWith = " ";
						justifyRight = false;
					}
				}
				else
					pFlags = "";

				/*  determine minimum length  */
				var access;
				var minLength = -1;
				if (pMinLength) {
					if (pMinLength === "*") {
						access = argumentnum++;
						if (access >= arguments.length)
							throw new Error("sprintf: ERROR: not enough arguments");
						minLength = arguments[access];
					}
					else
						minLength = parseInt(pMinLength, 10);
				}

				/*  determine precision  */
				var precision = -1;
				if (pPrecision) {
					if (pPrecision === ".*") {
						access = argumentnum++;
						if (access >= arguments.length)
							throw new Error("sprintf: ERROR: not enough arguments");
						precision = arguments[access];
					}
					else
						precision = parseInt(pPrecision.substring(1), 10);
				}

				/*  determine how to fetch argument  */
				access = argumentnum++;
				if (pAccessD) {
					access = parseInt(pAccessD, 10);
					if (access >= arguments.length)
						throw new Error("sprintf: ERROR: not enough arguments");
					subst = arguments[access];
				}
				else if (pAccessN) {
					if (typeof arguments[1] !== "object")
						throw new Error("sprintf: ERROR: invalid non-object arguments for named argument");
					subst = arguments[1][pAccessN];
					if (typeof subst === "undefined")
						throw new Error("sprintf: ERROR: invalid undefined value for named argument");
				}
				else {
					if (access >= arguments.length)
						throw new Error("sprintf: ERROR: not enough arguments");
					subst = arguments[access];
				}

				/*  dispatch into expansions according to type  */
				var prefix = "";
				switch (pType) {
					/*  decimal number  */
					case "d":
					case "i":
						if (typeof subst !== "number")
							subst = 0;
						subst = subst.toString(10);
						if (pFlags.indexOf("#") >= 0 && subst >= 0)
							subst = "+" + subst;
						if (pFlags.indexOf(" ") >= 0 && subst >= 0)
							subst = " " + subst;
						break;

					/*  binary number  */
					case "b":
						if (typeof subst !== "number")
							subst = 0;
						subst = subst.toString(2);
						break;

					/*  octal number  */
					case "o":
						if (typeof subst !== "number")
							subst = 0;
						subst = subst.toString(8);
						break;

					/*  unsigned decimal number  */
					case "u":
						if (typeof subst !== "number")
							subst = 0;
						subst = Math.abs(subst);
						subst = subst.toString(10);
						break;

					/*  (lower-case) hexadecimal number  */
					case "x":
						if (typeof subst !== "number")
							subst = 0;
						subst = subst.toString(16).toLowerCase();
						if (pFlags.indexOf("#") >= 0)
							prefix = "0x";
						break;

					/*  (upper-case) hexadecimal number  */
					case "X":
						if (typeof subst !== "number")
							subst = 0;
						subst = subst.toString(16).toUpperCase();
						if (pFlags.indexOf("#") >= 0)
							prefix = "0X";
						break;

					/*  (lower/upper-case) floating point number (fixed precision)  */
					case "f":
					case "F":
						if (typeof subst !== "number")
							subst = 0.0;
						subst = 0.0 + subst;
						if (precision > -1) {
							if (subst.toFixed)
								subst = subst.toFixed(precision);
							else {
								subst = (Math.round(subst * Math.pow(10, precision)) / Math.pow(10, precision));
								subst += "0000000000";
								subst = subst.substr(0, subst.indexOf(".") + precision + 1);
							}
						}
						subst = "" + subst;
						if (pFlags.indexOf("'") >= 0) {
							var k = 0;
							for (var i = (subst.length - 1) - 3; i >= 0; i -= 3) {
								subst = subst.substring(0, i) + (k === 0 ? "." : ",") + subst.substring(i);
								k = (k + 1) % 2;
							}
						}
						break;

					/*  (lower/upper-case) floating point number (exponential-based precision)  */
					case "e":
					case "E":
						if (typeof subst !== "number")
							subst = 0.0;
						subst = 0.0 + subst;
						if (precision > -1) {
							if (subst.toExponential)
								subst = subst.toExponential(precision);
							else
								throw new Error("sprintf: ERROR: toExponential() method not supported");
						}
						subst = "" + subst;
						if (pType === "E")
							subst = subst.replace(/e\+/, "E+");
						break;

					/*  single character  */
					case "c":
						if (typeof subst !== "number")
							subst = 0;
						subst = String.fromCharCode(subst);
						break;

					/*  string  */
					case "s":
						if (typeof subst !== "string")
							subst = String(subst);
						if (precision > -1)
							subst = subst.substr(0, precision);
						break;
					default:
						throw new Error("sprintf: ERROR: invalid conversion character \"" + pType + "\"");
				}

				/*  apply optional padding  */
				var padding = minLength - subst.toString().length - prefix.toString().length;
				if (padding > 0) {
					var arrTmp = new Array(padding + 1);
					if (justifyRight)
						subst = arrTmp.join(padWith) + subst;
					else
						subst = subst + arrTmp.join(padWith);
				}

				/*  add optional prefix  */
				subst = prefix + subst;
			}

			/*  update the processing queue  */
			done = done + pProlog + subst;
			todo = pEpilog;
		}

		/*  return finally formatted string  */
		return (done + todo);
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