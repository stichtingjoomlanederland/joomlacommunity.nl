if (typeof RSFormPro != 'object') {
	var RSFormPro = {};
}

RSFormPro.initCodeMirror = false;

RSFormPro.$ = jQuery;

function initRSFormPro() {
	RSFormPro.$('#componentPreview tbody').tableDnD({
		onDragClass: 'rsform_dragged',
		onDrop     : function (table, row) {
			tidyOrder(true);
		}
	});

	RSFormPro.$('#mappingTable tbody').tableDnD({
		onDragClass: 'rsform_dragged',
		onDrop     : function (table, row) {
			tidyOrderMp(true);
		}
	});

	RSFormPro.$('#rsfp_calculations').tableDnD({
		onDragClass: 'rsform_dragged',
		onDrop     : function (table, row) {
			tidyOrderCalculationsDir();
		}
	});

	$$('a.rsmodal').each(function (el) {
		el.addEvent('click', function (e) {
			new Event(e).stop();
			openRSModal(el.href);
		});
	});

	RSFormPro.$(document).click(function () {
		RSFormPro.$(this).mousedown(function (e) {
			if (!RSFormPro.$(e.target).is('input')) {
				var checkParent = RSFormPro.$(e.target).parents('.dropdownContainer').length;
				if (!checkParent) {
					closeAllDropdowns();
				}
			}
		});
	});

	RSFormPro.$("#rsform_tab2").hide();

	RSFormPro.$("#properties").click(function () {
		RSFormPro.$("#rsform_tab2").show();
		RSFormPro.$("#rsform_tab1").hide();
		RSFormPro.$("#components").removeClass('btn-primary');
		RSFormPro.$("#properties").addClass('btn-primary');
	});

	RSFormPro.$("#components").click(function () {
		RSFormPro.$("#rsform_tab1").show();
		RSFormPro.$("#rsform_tab2").hide();
		RSFormPro.$("#properties").removeClass('btn-primary');
		RSFormPro.$("#components").addClass('btn-primary');
	});

	RSFormPro.$(".rsform_hide").hide();

	RSFormPro.$("div a.rsform_close").click(function () {
		RSFormPro.$(this).parent().animate({width: 'toggle'});

		RSFormPro.$('#rsform_firstleftnav li a').each(function (index, el) {
			RSFormPro.$(el).removeClass('active');
		});
	});

	RSFormPro.$('[data-placeholders]').rsplaceholder();

}

RSFormPro.$(document).on('renderedMappings', function(){
	RSFormPro.$('[data-placeholders]').rsplaceholder();
});

RSFormPro.$(document).on('renderedRsfpmappingWhere', function(event, element){
	RSFormPro.$('#'+element).find('[data-placeholders]').rsplaceholder();
});

RSFormPro.$(document).on('renderedSilentPostField', function($event, $field_one, $field_two){
	RSFormPro.$($field_one).find('input').rsplaceholder();
	RSFormPro.$($field_two).find('input').rsplaceholder();
});

RSFormPro.$(document).on('renderedCalculationsField', function($event, $field){
	RSFormPro.$('#'+$field).rsplaceholder();
});

function buildXmlHttp() {
	var xmlHttp;
	try {
		xmlHttp = new XMLHttpRequest();
	}
	catch (e) {
		try {
			xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e) {
			try {
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) {
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	return xmlHttp;
}

function tidyOrder(update_php) {
	if (!update_php)
		update_php = false;

	stateLoading();

	var params = [];

	var must_update_php = update_php;
	var orders = document.getElementsByName('order[]');
	var cids = document.getElementsByName('cid[]');
	for (i = 0; i < orders.length; i++) {
		params.push('cid_' + cids[i].value + '=' + parseInt(i + 1));

		if (orders[i].value != i + 1)
			must_update_php = true;

		orders[i].value = i + 1;
	}

	if (update_php && must_update_php) {
		xml = buildXmlHttp();

		var url = 'index.php?option=com_rsform&task=components.save.ordering&randomTime=' + Math.random();
		xml.open("POST", url, true);

		params = params.join('&');

		//Send the proper header information along with the request
		xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xml.setRequestHeader("Content-length", params.length);
		xml.setRequestHeader("Connection", "close");

		xml.send(params);
		xml.onreadystatechange = function () {
			if (xml.readyState == 4) {
				formId = document.getElementById('formId').value;
				if (document.getElementById('FormLayoutAutogenerate1').checked == true)
					generateLayout(formId, false);

				stateDone();
			}
		}
	}
	else {
		stateDone();
	}
}


function tidyOrderMp(update_php) {
	if (!update_php)
		update_php = false;

	stateLoading();

	var params = [];

	var must_update_php = update_php;
	var orders = document.getElementsByName('mporder[]');
	var cids = document.getElementsByName('mpid[]');
	for (i = 0; i < orders.length; i++) {
		params.push('mpid_' + cids[i].value + '=' + parseInt(i + 1));

		if (orders[i].value != i + 1)
			must_update_php = true;

		orders[i].value = i + 1;
	}

	if (update_php && must_update_php) {
		xml = buildXmlHttp();

		var url = 'index.php?option=com_rsform&task=ordering&controller=mappings&randomTime=' + Math.random();
		xml.open("POST", url, true);

		params = params.join('&');

		//Send the proper header information along with the request
		xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xml.setRequestHeader("Content-length", params.length);
		xml.setRequestHeader("Connection", "close");

		xml.send(params);
		xml.onreadystatechange = function () {
			if (xml.readyState == 4) {
				stateDone();
			}
		}
	}
	else {
		stateDone();
	}
}

function displayTemplate(componentTypeId, componentId) {
	if (RSFormPro.$('#rsfpc' + componentTypeId).hasClass('active') && (document.getElementById('componentIdToEdit').value == componentId || !componentId)) {
		document.getElementById('rsfptabcontent0').innerHTML = '';
		document.getElementById('rsfptabcontent1').innerHTML = '';
		document.getElementById('rsfptabcontent2').innerHTML = '';

		RSFormPro.$(".rsform_hide").animate({width: 'toggle'});
		RSFormPro.$('#rsfpc' + componentTypeId).removeClass('active');

		return;
	}

	document.getElementById('rsfptab0').style.display = '';
	document.getElementById('rsfptab1').style.display = '';
	document.getElementById('rsfptab2').style.display = '';

	//hide the editor tab
	RSFormPro.$(".rsform_hide").hide();

	RSFormPro.$('#rsfpc' + componentTypeId).addClass('rsform_loading_btn');

	//remove the active class
	RSFormPro.$('#rsform_firstleftnav li a').each(function (index, el) {
		RSFormPro.$(el).removeClass('active');
	});

	stateLoading();

	document.getElementById('componentIdToEdit').value = -1;

	xml = buildXmlHttp();
	xml.onreadystatechange = function () {
		if (xml.readyState == 4) {
			try {
				var top = f_scrollTop();
				if (top > 200)
					RSFormPro.$.scrollTo(RSFormPro.$('#rsform_firstleftnav'), 100);
			}
			catch (err) {
				// do nothing
			}

			RSFormPro.$('#rsfpc' + componentTypeId).removeClass('rsform_loading_btn');
			response = xml.responseText.split('{rsfsep}');

			if (RSFormPro.$.trim(response[1]) == '')
				document.getElementById('rsfptab1').style.display = 'none';
			if (RSFormPro.$.trim(response[2]) == '')
				document.getElementById('rsfptab2').style.display = 'none';

			document.getElementById('rsfptabcontent0').innerHTML = response[0];
			document.getElementById('rsfptabcontent1').innerHTML = response[1];
			document.getElementById('rsfptabcontent2').innerHTML = response[2];

			stateDone();

			//set the active tab
			RSFormPro.$('#rsfpc' + componentTypeId).addClass('active');

			//show the editor tab
			RSFormPro.$(".rsform_hide").animate({width: 'toggle'});

			RSFormPro.$('.rsform_secondarytabs li a').each(function (index, el) {
				RSFormPro.$(el).removeClass('active');
			});

			RSFormPro.$('#rsform_textboxdiv').formTabs(0);

			changeValidation(document.getElementById('VALIDATIONRULE'));

			// enable the inner tabs close buttons
			RSFormPro.$("#rsform_secondarytabcontent .rsform_close").click(function () {
				RSFormPro.$('#rsform_fixed > div').animate({width: 'toggle'});

				RSFormPro.$('#rsform_firstleftnav li a').each(function (index, el) {
					RSFormPro.$(el).removeClass('active');
				});
			});

			// Focus on textbox
			if (RSFormPro.$('#NAME').length > 0) {
				RSFormPro.$('#NAME').focus();
			}

			var $fields = jQuery('[data-properties="oneperline"], [data-properties="toggler"]');

			var $object = {};

			jQuery.each($fields, function () {
				$name = jQuery(this).attr('id');

				$object[$name] = {
					selector: $name
				};

				if (jQuery(this).attr('data-properties') == 'toggler') {
					$object[$name].data = jQuery.parseJSON( jQuery(this).attr('data-toggle') );
				}
			});

			jQuery('.rsform_hide').trigger('renderedLayout', $object);
		}
	};

	if (componentId) {
		document.getElementById('componentIdToEdit').value = componentId;
		xml.open('GET', 'index.php?option=com_rsform&task=components.display&componentType=' + componentTypeId + '&componentId=' + componentId + '&formId=' + document.getElementById('formId').value + '&format=raw&randomTime=' + Math.random(), true);
	}
	else {
		xml.open('GET', 'index.php?option=com_rsform&task=components.display&componentType=' + componentTypeId + '&formId=' + document.getElementById('formId').value + '&format=raw&randomTime=' + Math.random(), true);
	}

	xml.send(null);

}

function rsfp_validateDate(value) {
	value = value.replace(/[^0-9\/]/g, '');
	return value;
}

function f_scrollTop() {
	return f_filterResults(
		window.pageYOffset ? window.pageYOffset : 0,
		document.documentElement ? document.documentElement.scrollTop : 0,
		document.body ? document.body.scrollTop : 0
	);
}
function f_filterResults(n_win, n_docel, n_body) {
	var n_result = n_win ? n_win : 0;
	if (n_docel && (!n_result || (n_result > n_docel)))
		n_result = n_docel;
	return n_body && (!n_result || (n_result > n_body)) ? n_body : n_result;
}

function removeComponent(formId, componentId) {
	stateLoading();

	// Build URL to post to
	var url = 'index.php?option=com_rsform&task=components.remove&randomTime=' + Math.random();

	// Build data array
	var data = {
		'ajax'  : 1,
		'cid[]' : componentId,
		'formId': formId
	};

	RSFormPro.$.post(url, data, function (response, status, jqXHR) {
		// Remove row
		var table = document.getElementById('componentPreview');
		var rows = document.getElementsByName('previewComponentId');
		for (var i = 0; i < rows.length; i++) {
			if (rows[i].value == componentId) {
				table.deleteRow(i);
			}
		}

		if (!response.submit) {
			RSFormPro.$('#rsform_submit_button_msg').show();
		}

		tidyOrder(true);

		stateDone();
	}, 'json');
}

function processComponent(componentType) {
	RSFormPro.$(document.getElementsByName('componentSaveButton')).prop('disabled', true);

	RSFormPro.$('#rsformerror0').hide();
	RSFormPro.$('#rsformerror1').hide();
	RSFormPro.$('#rsformerror2').hide();
	RSFormPro.$('#rsformerror3').hide();

	stateLoading();

	// Build URL to post to
	var url = 'index.php?option=com_rsform&task=components.validate.name&randomTime=' + Math.random();

	// Build data array
	var data = {
		'componentName'     : RSFormPro.$('#NAME').val(),
		'formId'            : RSFormPro.$('#formId').val(),
		'currentComponentId': RSFormPro.$('#componentIdToEdit').val(),
		'componentType'     : componentType
	};

	if (componentType == 9) {
		data['destination'] = RSFormPro.$('#DESTINATION').val();
	}

	RSFormPro.$.post(url, data, function (response, status, jqXHR) {
		if (response.result == false) {
			// Remove current tab selection
			RSFormPro.$('.rsform_secondarytabs li a').removeClass('active');

			// Switch to tab
			RSFormPro.$('#rsform_textboxdiv').formTabs(response.tab);

			// Show error message
			RSFormPro.$('#rsformerror' + response.tab).text(response.message).show();

			stateDone();

			RSFormPro.$(document.getElementsByName('componentSaveButton')).prop('disabled', false);
		} else {
			Joomla.submitbutton('components.save');
		}
	}, 'json');
}

function changeDirectoryAutoGenerateLayout(formId, value) {
	stateLoading();
	var layouts = document.getElementsByName('jform[ViewLayoutName]');
	var layoutName = '';
	for (i = 0; i < layouts.length; i++)
		if (layouts[i].checked)
			layoutName = layouts[i].value;

	xml = buildXmlHttp();
	xml.onreadystatechange = function () {
		if (xml.readyState == 4) {
			if (value == 1) {
				document.getElementById('rsform_layout_msg').style.display = 'none';
				document.getElementById('ViewLayout').readOnly = true;
				if (typeof(window.codemirror_html) != 'undefined') {
					if (typeof(window.codemirror_html.ViewLayout) != 'undefined') {
						window.codemirror_html.ViewLayout.setOption('readOnly', true);
					}
				}
			}
			else {
				document.getElementById('rsform_layout_msg').style.display = '';
				document.getElementById('ViewLayout').readOnly = false;
				if (typeof(window.codemirror_html) != 'undefined') {
					if (typeof(window.codemirror_html.ViewLayout) != 'undefined') {
						window.codemirror_html.ViewLayout.setOption('readOnly', false);
					}
				}
			}

			stateDone();
		}
	};
	xml.open('GET', 'index.php?option=com_rsform&task=directory.changeAutoGenerateLayout&formId=' + formId + '&randomTime=' + Math.random() + '&ViewLayoutName=' + layoutName, true);
	xml.send(null);
}

function changeFormAutoGenerateLayout(formId, value) {
	stateLoading();

	// Build URL to post to
	var url = 'index.php?option=com_rsform&task=forms.changeAutoGenerateLayout&randomTime=' + Math.random();

	// Build data array
	var data = {
		'formLayoutName': RSFormPro.$('[name=FormLayoutName]:checked').val(),
		'formId'        : formId,
		'status'        : value
	};

	RSFormPro.$.post(url, data, function (response, status, jqXHR) {
		var hasCodeMirror = typeof window.codemirror_html != 'undefined' && typeof window.codemirror_html.formLayout != 'undefined';

		value = Boolean(parseInt(value));

		value ? RSFormPro.$('#rsform_layout_msg').hide() : RSFormPro.$('#rsform_layout_msg').show();
		RSFormPro.$('#formLayout').prop('readonly', value);

		if (hasCodeMirror) {
			window.codemirror_html.formLayout.setOption('readOnly', value);
		}

		stateDone();
	}, 'json');
}

function generateLayout(formId, alert) {
	if (alert && !confirm(Joomla.JText._('RSFP_AUTOGENERATE_LAYOUT_WARNING_SURE'))) {
		return;
	}

	stateLoading();

	// Build URL to post to
	var url = 'index.php?option=com_rsform&task=layouts.generate&randomTime=' + Math.random();

	// Build data array
	var data = {
		'layoutName': RSFormPro.$('[name=FormLayoutName]:checked').val(),
		'formId'    : formId
	};

	RSFormPro.$.post(url, data, function (response, status, jqXHR) {
		var hasCodeMirror = typeof window.codemirror_html != 'undefined' && typeof window.codemirror_html.formLayout != 'undefined';

		RSFormPro.$('#formLayout').val(response);

		if (hasCodeMirror) {
			window.codemirror_html.formLayout.setValue(xml.responseText);
		}

		stateDone();
	}, 'text');
}

function generateDirectoryLayout(formId, alert) {
	if (alert != 'no') {
		var answer = confirm("Pressing the 'Generate layout' button will ERASE your current layout. Are you sure you want to continue?");
		if (answer == false) return;
	}
	var layoutName = 'inline-xhtml';
	for (var i = 0; i < document.getElementsByName('jform[ViewLayoutName]').length; i++)
		if (document.getElementsByName('jform[ViewLayoutName]')[i].checked)
			layoutName = document.getElementsByName('jform[ViewLayoutName]')[i].value;

	stateLoading();
	xml = buildXmlHttp();
	xml.onreadystatechange = function () {
		if (xml.readyState == 4) {
			document.getElementById('ViewLayout').value = xml.responseText;
			if (typeof(window.codemirror_html) != 'undefined') {
				if (typeof(window.codemirror_html.ViewLayout) != 'undefined') {
					window.codemirror_html.ViewLayout.setValue(xml.responseText);
				}
			}
			stateDone();
		}
	};
	xml.open('GET', 'index.php?option=com_rsform&task=directory.generate&layoutName=' + layoutName + '&formId=' + formId + '&randomTime=' + Math.random(), true);
	xml.send(null);
}

function saveLayoutName(formId, layoutName) {
	var layoutsWithoutTheme = ['responsive', 'bootstrap2', 'bootstrap3', 'uikit', 'foundation'];

	for (var i = 0; i < document.getElementsByName('ThemeName').length; i++) {
		document.getElementsByName('ThemeName')[i].disabled = layoutsWithoutTheme.indexOf(layoutName) >= 0;
	}

	document.getElementById('rsform_themes_disabled').style.display = layoutsWithoutTheme.indexOf(layoutName) >= 0 ? '' : 'none';
	layoutsWithoutTheme.indexOf(layoutName) >= 0 ? jQuery('#formtheme').hide() : jQuery('#formtheme').show();

	stateLoading();
	xml = buildXmlHttp();
	xml.open('GET', 'index.php?option=com_rsform&task=layouts.save.name&formId=' + formId + '&randomTime=' + Math.random() + '&formLayoutName=' + layoutName, true);
	xml.send(null);
	xml.onreadystatechange = function () {
		if (xml.readyState == 4) {
			if (document.getElementById('FormLayoutAutogenerate1').checked == true)
				generateLayout(formId, false);
			stateDone();
		}
	}
}

function saveDirectoryLayoutName(formId, layoutName) {
	stateLoading();
	xml = buildXmlHttp();
	xml.open('GET', 'index.php?option=com_rsform&task=directory.savename&formId=' + formId + '&randomTime=' + Math.random() + '&ViewLayoutName=' + layoutName, true);
	xml.send(null);
	xml.onreadystatechange = function () {
		if (xml.readyState == 4) {
			var autogenerate = document.getElementsByName('jform[ViewLayoutAutogenerate]');
			for (var i = 0; i < autogenerate.length; i++)
				if (autogenerate[i].value == 1 && autogenerate[i].checked)
					generateDirectoryLayout(formId, 'no');
			stateDone();
		}
	}
}

function stateLoading() {
	document.getElementById('state').style.display = '';
}

function stateDone() {
	document.getElementById('state').style.display = 'none';
}

function refreshCaptcha(componentId, captchaPath) {
	if (!captchaPath) captchaPath = 'index.php?option=com_rsform&task=captcha&format=image&componentId=' + componentId;
	document.getElementById('captcha' + componentId).src = captchaPath + '&' + Math.random();
	document.getElementById('captchaTxt' + componentId).value = '';
	document.getElementById('captchaTxt' + componentId).focus();
}

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

function exportProcess(start, limit, total) {
	xml = buildXmlHttp();
	xml.onreadystatechange = function () {
		if (xml.readyState == 4) {
			post = xml.responseText;
			if (post.indexOf('END') != -1) {
				document.getElementById('backButtonContainer').style.display = '';
				document.getElementById('progressBar').style.width = document.getElementById('progressBar').innerHTML = '100%';
				document.location = 'index.php?option=com_rsform&task=submissions.export.file&ExportFile=' + document.getElementById('ExportFile').value + '&ExportType=' + document.getElementById('exportType').value;
			}
			else {
				document.getElementById('progressBar').style.width = Math.ceil(start * 100 / total) + '%';
				document.getElementById('progressBar').innerHTML = Math.ceil(start * 100 / total) + '%';
				start = start + limit;
				exportProcess(start, limit, total);
			}
		}
	};

	xml.open('GET', 'index.php?option=com_rsform&task=submissions.export.process&exportStart=' + start + '&exportLimit=' + limit + '&randomTime=' + Math.random(), true);
	xml.send(null);
}

function number_format(number, decimals, dec_point, thousands_sep) {
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

		_[0] = s.slice(0, i + (n < 0)) +
			_[0].slice(i).replace(/(\d{3})/g, sep + '$1');

		s = _.join(dec);
	} else {
		s = s.replace('.', dec);
	}

	return s;
}

function changeValidation(elem) {
	if (elem == null) return;
	if (elem.id == 'VALIDATIONRULE') {
		if (document.getElementById('idVALIDATIONEXTRA')) {
			if (elem.value == 'regex' || elem.value == 'sameas') {
				theText = RStranslateText(elem.value)
			} else {
				theText = RStranslateText('extra');
			}
			document.getElementById('captionVALIDATIONEXTRA').innerHTML = theText;

			if (elem.value == 'custom' || elem.value == 'numeric' || elem.value == 'alphanumeric' || elem.value == 'alpha' || elem.value == 'regex' || elem.value == 'sameas')
				document.getElementById('idVALIDATIONEXTRA').className = 'showVALIDATIONEXTRA';
			else
				document.getElementById('idVALIDATIONEXTRA').className = 'hideVALIDATIONEXTRA';
		}
		
		var multipleRulesField = document.getElementById('idVALIDATIONMULTIPLE');
		if (elem.value == 'multiplerules') {
			multipleRulesField.style.display = 'table-row';
			changeValidation(document.getElementById('VALIDATIONMULTIPLE'));
		} else {
			multipleRulesField.style.display = 'none';
			document.getElementById('VALIDATIONEXTRA').name='param[VALIDATIONEXTRA]';
			
			// if the saved extra value of the multiple rule exist in the current rule selection keep it, if no leave it as it is
			var savedExtra = document.getElementById('VALIDATIONEXTRA').value;
			try {
				eval('var savedExtraObject='+savedExtra);
			} catch(e) {
				var savedExtraObject = {};
			}
			
			if (typeof savedExtraObject == 'object' && typeof savedExtraObject[elem.value] != 'undefined') {
				document.getElementById('VALIDATIONEXTRA').value = savedExtraObject[elem.value];
			}
			
			// remove previous created extra validations for the multiple validation
			var previousExtras = document.querySelectorAll('.mValidation');
			for (i = 0; i < previousExtras.length; i++) {
				previousExtras[i].parentNode.removeChild(previousExtras[i]);
			} 
		}
	} else if (elem.id == 'VALIDATIONMULTIPLE') {
		var selectedValues = new Array();
		for (i = 0; i < elem.length; i++) {
			if (elem[i].selected && (elem[i].value == 'custom' || elem[i].value == 'numeric' || elem[i].value == 'alphanumeric' || elem[i].value == 'alpha' || elem[i].value == 'regex' || elem[i].value == 'sameas')) {
				selectedValues.push(elem[i].value);
			}
		}
		
		// remove previous created extra validations
		var previousExtras = document.querySelectorAll('.mValidation');
		for (i = 0; i < previousExtras.length; i++) {
			previousExtras[i].parentNode.removeChild(previousExtras[i]);
		} 
		
		// set the name of the normal validation to 'empty'
		document.getElementById('VALIDATIONEXTRA').name='';
		
		// the default validation extra value if already saved
		var savedExtra = document.getElementById('VALIDATIONEXTRA').value;
		try {
			eval('var savedExtraObject='+savedExtra);
		} catch(e) {
			var savedExtraObject = {};
		}
		
		var clonedElement = document.getElementById('idVALIDATIONEXTRA').cloneNode(true);
		clonedElement.removeAttribute('id');
		clonedElement.removeClass('hideVALIDATIONEXTRA');
		
		var afterElement = document.getElementById('idVALIDATIONMULTIPLE');
		
		for(i = 0; i < selectedValues.length; i++) {
			var newclonedElement = clonedElement.cloneNode(true);
			newclonedElement.addClass('mValidation '+selectedValues[i]);
			
			var captionElement = newclonedElement.querySelector('#captionVALIDATIONEXTRA');
			var validationElement = newclonedElement.querySelector('#VALIDATIONEXTRA');
			
			captionElement.id='captionValidation'+selectedValues[i];
			validationElement.id='Validation'+selectedValues[i];
			validationElement.name="param[VALIDATIONEXTRA]["+selectedValues[i]+"]";
			if (typeof savedExtraObject[selectedValues[i]] != 'undefined') {
				validationElement.value = savedExtraObject[selectedValues[i]];
			} else {
				validationElement.value = '';
			}
			
			if (selectedValues[i] == 'regex' || selectedValues[i] == 'sameas') {
				theText = RStranslateText(selectedValues[i])
			} else {
				theText = RStranslateText('extra');
			}
			
			jQuery(document.getElementById('VALIDATIONRULE').options).each(function(){
				if (this.value == selectedValues[i])
				{
					theText = this.text + ' - ' + theText;
				}
			});
			
			captionElement.innerHTML = theText;
			
			afterElement.parentNode.insertBefore(newclonedElement, afterElement.nextSibling);
		}
		
	}
}

function submissionChangeForm(formId) {
	document.location = 'index.php?option=com_rsform&task=submissions.manage&formId=' + formId;
}

function toggleCustomizeColumns() {
	var el = RSFormPro.$('#columnsDiv');

	if (el.is(':hidden')) {
		var windowH = RSFormPro.$(window).height();
		var remove = 0;
		if (RSFormPro.$('body > #status').length > 0) {
			remove += parseInt(RSFormPro.$('body > #status').height());
		}
		var parentElementOffset = el.parent().offset();
		remove += parentElementOffset.top;
		var innerHeight = windowH - remove - 120;

		if (innerHeight <= 0) {
			innerHeight = 400;
		}
		el.find('#columnsInnerDiv').css('max-height', innerHeight+'px');
		el.slideDown('fast');
	} else {
		el.slideUp('fast');
	}
}

function closeAllDropdowns(except) {
	var dropdowns = RSFormPro.$('.dropdownContainer');
	var except = RSFormPro.$('#dropdown' + except);

	for (var i = 0; i < dropdowns.length; i++) {
		var dropdown = RSFormPro.$(dropdowns[i]).children('div');
		if (dropdown.attr('id') != except.attr('id'))
			RSFormPro.$(dropdowns[i]).children('div').hide();
	}
}

/**
 * @deprecated, used to generate the new type of fields
 * @param what
 * @param extra
 * @param inner
 */
function toggleDropdown(what, extra, inner) {

		RSFormPro.$(what).addClass('placeholders-initiated');

		$attr = {
			'data-delimiter' : ' ',
			'data-placeholders' : 'display',
			'onclick' : '',
			'onkeydown' : ''
		};
		RSFormPro.$(what).attr($attr);

		RSFormPro.$(what).rsplaceholder();

}

function toggleQuickAdd() {
	var what = 'none';
	if (document.getElementById('QuickAdd1').style.display == 'none')
		what = '';

	document.getElementById('QuickAdd1').style.display = what;
	document.getElementById('QuickAdd2').style.display = what;
	document.getElementById('QuickAdd3').style.display = what;
	document.getElementById('QuickAdd4').style.display = what;
}

function mpConnect() {
	var fields = RSFormPro.$("#tablers :input");
	var params = [];
	var fname = '';
	var fvalue = '';

	for (i = 0; i < fields.length; i++) {
		if (fields[i].type == 'button') continue;

		if (fields[i].type == 'radio') {
			if (fields[i].checked) {
				if (fields[i].name == 'rsfpmapping[connection]') {
					fname = 'connection';
					fvalue = fields[i].value;
				}

				if (fields[i].name == 'rsfpmapping[method]') {
					fname = 'method';
					fvalue = fields[i].value;
				}
			} else continue;
		}

		fname = fields[i].name;
		fvalue = fields[i].value;

		params.push(fname + '=' + encodeURIComponent(fvalue));
	}
	params.push('randomTime=' + Math.random());
	params = params.join('&');

	document.getElementById('mappingloader').style.display = '';

	xmlHttp = buildXmlHttp();
	xmlHttp.open("POST", 'index.php?option=com_rsform&task=gettables&controller=mappings', true);

	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", params.length);
	xmlHttp.setRequestHeader("Connection", "close");

	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState == 4) {
			response = xmlHttp.responseText.split('|');

			if (response[0] == 1) {
				document.getElementById('rsfpmappingContent').innerHTML = response[1];
				document.getElementById('mpConnectionOn').style.display = 'none';
				document.getElementById('mpConnectionOff').style.display = '';
				document.getElementById('mpMethodOn').style.display = 'none';
				document.getElementById('mpMethodOff').style.display = '';
				document.getElementById('mpHostOn').style.display = 'none';
				document.getElementById('mpHostOff').style.display = '';
				document.getElementById('mpDriverOn').style.display = 'none';
				document.getElementById('mpDriverOff').style.display = '';
				document.getElementById('mpPortOn').style.display = 'none';
				document.getElementById('mpUsernameOn').style.display = 'none';
				document.getElementById('mpUsernameOff').style.display = '';
				document.getElementById('mpPasswordOn').style.display = 'none';
				document.getElementById('mpPasswordOff').style.display = '';
				document.getElementById('mpDatabaseOn').style.display = 'none';
				document.getElementById('mpDatabaseOff').style.display = '';

				if (document.getElementById('connection0').checked) document.getElementById('mpConnectionOff').innerHTML = getLabelText('connection0');
				if (document.getElementById('connection1').checked) document.getElementById('mpConnectionOff').innerHTML = getLabelText('connection1');
				if (document.getElementById('method0').checked) document.getElementById('mpMethodOff').innerHTML = getLabelText('method0');
				if (document.getElementById('method1').checked) document.getElementById('mpMethodOff').innerHTML = getLabelText('method1');
				if (document.getElementById('method2').checked) document.getElementById('mpMethodOff').innerHTML = getLabelText('method2');
				if (document.getElementById('method3').checked) document.getElementById('mpMethodOff').innerHTML = getLabelText('method3');
				document.getElementById('mpHostOff').innerHTML = document.getElementById('MappingHost').value + ':' + document.getElementById('MappingPort').value;
				document.getElementById('mpDriverOff').innerHTML = document.getElementById('driver').value;
				document.getElementById('mpUsernameOff').innerHTML = document.getElementById('MappingUsername').value;
				document.getElementById('mpPasswordOff').innerHTML = document.getElementById('MappingPassword').value;
				document.getElementById('mpDatabaseOff').innerHTML = document.getElementById('MappingDatabase').value;
			} else {
				document.getElementById('rsfpmappingContent').innerHTML = '<font color="red">' + response[0] + '</font>';
			}

			document.getElementById('mappingloader').style.display = 'none';
		}
	};

	xmlHttp.send(params);
}

function getLabelText(element) {
	return RSFormPro.$('#' + element).parent().text();
}


function mpColumns(table) {
	var fields = RSFormPro.$("#tablers :input");
	var params = [];
	var fname = '';
	var fvalue = '';

	for (i = 0; i < fields.length; i++) {
		if (fields[i].type == 'button') continue;

		if (fields[i].type == 'radio') {
			if (fields[i].checked) {
				if (fields[i].name == 'rsfpmapping[connection]') {
					fname = 'connection';
					fvalue = fields[i].value;
				}

				if (fields[i].name == 'rsfpmapping[method]') {
					fname = 'method';
					fvalue = fields[i].value;
				}
			} else continue;
		}

		fname = fields[i].name;
		fvalue = fields[i].value;

		params.push(fname + '=' + encodeURIComponent(fvalue));
	}
	params.push('table=' + table);
	params.push('type=set');
	params.push('tmpl=component');
	params.push('randomTime=' + Math.random());

	if (document.getElementById('mappingid') && document.getElementById('mappingid').value) {
		params.push('cid=' + document.getElementById('mappingid').value);
	}

	params = params.join('&');

	document.getElementById('mappingloader2').style.display = '';

	xmlHttp = buildXmlHttp();
	xmlHttp.open("POST", 'index.php?option=com_rsform&task=getcolumns&controller=mappings', true);

	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", params.length);
	xmlHttp.setRequestHeader("Connection", "close");

	xmlHttp.onreadystatechange = function () {//Call a function when the state changes.
		if (xmlHttp.readyState == 4) {
			if ((isset(document.getElementById('method0')) && document.getElementById('method0').checked) || (isset(document.getElementById('method1')) && document.getElementById('method1').checked) || (isset(document.getElementById('method3')) && document.getElementById('method3').checked) || (isset(document.getElementById('method')) && document.getElementById('method').value == 0) || (isset(document.getElementById('method')) && document.getElementById('method').value == 1) || (isset(document.getElementById('method')) && document.getElementById('method').value == 3))
				document.getElementById('rsfpmappingColumns').innerHTML = xmlHttp.responseText;
			document.getElementById('mappingloader2').style.display = 'none';

			if ((isset(document.getElementById('method1')) && document.getElementById('method1').checked) || (isset(document.getElementById('method2')) && document.getElementById('method2').checked) || (isset(document.getElementById('method')) && document.getElementById('method').value == 1) || (isset(document.getElementById('method')) && document.getElementById('method').value == 2))
				mappingWhere(table);

			jQuery(document).trigger('renderedMappings');
		}
	};

	xmlHttp.send(params);
}

function mappingdelete(formid, mid) {
	stateLoading();

	params = 'formId=' + formid + '&mid=' + mid + '&tmpl=component&randomTime=' + Math.random();

	xmlHttp = buildXmlHttp();
	xmlHttp.open("POST", 'index.php?option=com_rsform&task=remove&controller=mappings', true);

	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", params.length);
	xmlHttp.setRequestHeader("Connection", "close");

	xmlHttp.onreadystatechange = function () {//Call a function when the state changes.
		if (xmlHttp.readyState == 4) {
			document.getElementById('mappingcontent').innerHTML = xmlHttp.responseText;
			stateDone();

			RSFormPro.$('#mappingTable tbody').tableDnD({
				onDragClass: 'rsform_dragged',
				onDrop     : function (table, row) {
					tidyOrderMp(true);
				}
			});
		}
	};
	xmlHttp.send(params);
}

function ShowMappings(formid) {
	stateLoading();

	params = 'formId=' + formid + '&tmpl=component&randomTime=' + Math.random();

	xmlHttp = buildXmlHttp();
	xmlHttp.open("POST", 'index.php?option=com_rsform&task=showmappings&controller=mappings', true);

	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", params.length);
	xmlHttp.setRequestHeader("Connection", "close");

	xmlHttp.onreadystatechange = function () {//Call a function when the state changes.
		if (xmlHttp.readyState == 4) {
			document.getElementById('mappingcontent').innerHTML = xmlHttp.responseText;
			stateDone();

			RSFormPro.$('#mappingTable tbody').tableDnD({
				onDragClass: 'rsform_dragged',
				onDrop     : function (table, row) {
					tidyOrderMp(true);
				}
			});
		}
	};
	xmlHttp.send(params);
}

function mappingWhere(table) {
	var fields = RSFormPro.$("#tablers :input");
	var params = [];
	var fname = '';
	var fvalue = '';

	for (i = 0; i < fields.length; i++) {
		if (fields[i].type == 'button') continue;

		if (fields[i].type == 'radio') {
			if (fields[i].checked) {
				if (fields[i].name == 'rsfpmapping[connection]') {
					fname = 'connection';
					fvalue = fields[i].value;
				}

				if (fields[i].name == 'rsfpmapping[method]') {
					fname = 'method';
					fvalue = fields[i].value;
				}
			} else continue;
		}

		fname = fields[i].name;
		fvalue = fields[i].value;

		params.push(fname + '=' + encodeURIComponent(fvalue));
	}
	params.push('table=' + table);
	params.push('type=where');
	params.push('tmpl=component');
	params.push('randomTime=' + Math.random());
	params = params.join('&');


	document.getElementById('mappingloader2').style.display = '';

	xmlHttp = buildXmlHttp();
	xmlHttp.open("POST", 'index.php?option=com_rsform&task=getcolumns&controller=mappings', true);

	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", params.length);
	xmlHttp.setRequestHeader("Connection", "close");

	xmlHttp.onreadystatechange = function () {//Call a function when the state changes.
		if (xmlHttp.readyState == 4) {
			document.getElementById('rsfpmappingWhere').innerHTML = xmlHttp.responseText;
			document.getElementById('mappingloader2').style.display = 'none';
			RSFormPro.$(document).trigger('renderedRsfpmappingWhere', 'rsfpmappingWhere');
		}
	};
	xmlHttp.send(params);
}

function removeEmail(id, fid, type) {
	stateLoading();

	var params = [];
	params.push('cid=' + id);
	params.push('formId=' + fid);
	params.push('type=' + type);
	params.push('tmpl=component');
	params.push('randomTime=' + Math.random());
	params = params.join('&');

	xmlHttp = buildXmlHttp();
	xmlHttp.open("POST", 'index.php?option=com_rsform&task=emails.remove', true);

	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", params.length);
	xmlHttp.setRequestHeader("Connection", "close");

	xmlHttp.onreadystatechange = function () {//Call a function when the state changes.
		if (xmlHttp.readyState == 4) {
			stateDone();
			document.getElementById('emailscontent').innerHTML = xmlHttp.responseText;
		}
	};
	xmlHttp.send(params);
}

function updateemails(fid, type) {
	var content = document.getElementById('emailscontent');

	stateLoading();

	var params = [];
	params.push('formId=' + fid);
	params.push('type=' + type);
	params.push('tmpl=component');
	params.push('randomTime=' + Math.random());
	params = params.join('&');

	xmlHttp = buildXmlHttp();
	xmlHttp.open("POST", 'index.php?option=com_rsform&task=emails.update', true);

	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", params.length);
	xmlHttp.setRequestHeader("Connection", "close");

	xmlHttp.onreadystatechange = function () {//Call a function when the state changes.
		if (xmlHttp.readyState == 4) {
			stateDone();
			content.innerHTML = xmlHttp.responseText;
		}
	};
	xmlHttp.send(params);
}

function initCodeMirror() {
	if (!RSFormPro.initCodeMirror || typeof(CodeMirror) == 'undefined')
		return false;

	var codemirrors = [];
	codemirrors['js'] = RSFormPro.$('.codemirror-js');
	codemirrors['css'] = RSFormPro.$('.codemirror-css');
	codemirrors['php'] = RSFormPro.$('.codemirror-php');
	codemirrors['html'] = RSFormPro.$('.codemirror-html');

	// js
	for (var i = 0; i < codemirrors['js'].length; i++) {
		CodeMirror.fromTextArea(codemirrors['js'][i], {
			lineNumbers   : true,
			matchBrackets : true,
			mode          : "text/html",
			indentUnit    : 4,
			indentWithTabs: true,
			enterMode     : "keep",
			tabMode       : "shift"
		});
	}

	// css
	for (var i = 0; i < codemirrors['css'].length; i++) {
		var editor = CodeMirror.fromTextArea(codemirrors['css'][i], {
			lineNumbers   : true,
			matchBrackets : true,
			mode          : "text/html",
			indentUnit    : 4,
			indentWithTabs: true,
			enterMode     : "keep",
			tabMode       : "shift"
		});
	}

	// php
	for (var i = 0; i < codemirrors['php'].length; i++) {
		CodeMirror.fromTextArea(codemirrors['php'][i], {
			lineNumbers   : true,
			matchBrackets : true,
			mode          : "application/x-httpd-php-open",
			indentUnit    : 4,
			indentWithTabs: true,
			enterMode     : "keep",
			tabMode       : "shift"
		});
	}

	// html
	if (codemirrors['html'].length > 0) {
		window.codemirror_html = {};
	}
	for (var i = 0; i < codemirrors['html'].length; i++) {
		var codeMirrorType = RSFormPro.$(codemirrors['html'][i]).attr('id');
		window.codemirror_html[codeMirrorType] = CodeMirror.fromTextArea(codemirrors['html'][i], {
			lineNumbers   : true,
			matchBrackets : true,
			mode          : "text/html",
			indentUnit    : 4,
			indentWithTabs: true,
			enterMode     : "keep",
			tabMode       : "shift",
			matchTags     : {bothTags: true},
			readOnly      : RSFormPro.$(codemirrors['html'][i]).attr('readonly')
		});
	}
}

function conditionDelete(formid, cid) {
	stateLoading();

	params = 'formId=' + formid + '&cid=' + cid + '&tmpl=component&randomTime=' + Math.random();

	xmlHttp = buildXmlHttp();
	xmlHttp.open("POST", 'index.php?option=com_rsform&task=remove&controller=conditions', true);

	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", params.length);
	xmlHttp.setRequestHeader("Connection", "close");

	xmlHttp.onreadystatechange = function () {//Call a function when the state changes.
		if (xmlHttp.readyState == 4) {
			document.getElementById('conditionscontent').innerHTML = xmlHttp.responseText;
			stateDone();
		}
	};
	xmlHttp.send(params);
}

function showConditions(formid) {
	stateLoading();

	params = 'formId=' + formid + '&tmpl=component&randomTime=' + Math.random();

	xmlHttp = buildXmlHttp();
	xmlHttp.open("POST", 'index.php?option=com_rsform&task=showconditions&controller=conditions', true);

	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", params.length);
	xmlHttp.setRequestHeader("Connection", "close");

	xmlHttp.onreadystatechange = function () {//Call a function when the state changes.
		if (xmlHttp.readyState == 4) {
			document.getElementById('conditionscontent').innerHTML = xmlHttp.responseText;
			stateDone();
		}
	};
	xmlHttp.send(params);
}

function openRSModal(href, type, size) {
	if (!type)
		type = 'Richtext';
	if (!size)
		size = '600x500';
	size = size.split('x');
	width = size[0];
	height = size[1];
	window.open(href, type, 'width=' + width + ', height=' + height + ',scrollbars=1');
}

function rsfp_add_calculation(formId) {
	if (document.getElementById('rsfp_expression').value == '')
		return;

	stateLoading();

	params = [];
	params.push('formId=' + formId);
	params.push('total=' + document.getElementById('rsfp_total_add').value);
	params.push('expression=' + encodeURIComponent(document.getElementById('rsfp_expression').value));
	params.push('tmpl=component');
	params.push('randomTime=' + Math.random());
	params = params.join('&');

	xmlHttp = buildXmlHttp();
	xmlHttp.open("POST", 'index.php?option=com_rsform&task=calculations&controller=forms', true);

	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", params.length);
	xmlHttp.setRequestHeader("Connection", "close");

	xmlHttp.onreadystatechange = function () {//Call a function when the state changes.
		if (xmlHttp.readyState == 4) {
			var response = xmlHttp.responseText;

			if (response) {
				var response = response.split('|');
				var options = document.getElementById('rsfp_total_add').options;
				var container = document.getElementById('rsfp_calculations');

				var tr = document.createElement('tr');
				var td1 = document.createElement('td');
				var td2 = document.createElement('td');
				var td3 = document.createElement('td');

				tr.setAttribute('id', 'calculationRow' + response[0]);

				var select = document.createElement('select');
				select.setAttribute('id', 'total' + response[0]);
				select.setAttribute('name', 'calculations[' + response[0] + '][total]');
				select.setAttribute('size', '1');
				select.setAttribute('style', 'margin-bottom:0px;');

				select.options.length = 0;
				for (i = 0; i < options.length; i++) {
					option = new Option(options[i].value, options[i].value);
					if (options[i].value == document.getElementById('rsfp_total_add').value)
						option.selected = true;
					select.options[select.options.length] = option;
				}

				td2.innerHTML = ' = ';

				var input = document.createElement('input');
				input.setAttribute('id', 'calculations' + response[0] + 'expression');
				input.setAttribute('type', 'text');
				input.setAttribute('name', 'calculations[' + response[0] + '][expression]');
				input.setAttribute('class', 'rs_inp rs_80');
				input.setAttribute('size', '100');
				input.setAttribute('value', document.getElementById('rsfp_expression').value);
				input.setAttribute('data-filter-type', 'include');
				input.setAttribute('data-filter', 'value');
				input.setAttribute('data-delimiter', ' ');
				input.setAttribute('data-placeholders', 'display');

				var $input_id = 'calculations' + response[0] + 'expression';

				var a = document.createElement('a');
				a.setAttribute('href', 'javascript:void(0)');
				a.onclick = function () {
					rsfp_remove_calculation(response[0]);
				};

				var img = document.createElement('img');
				img.setAttribute('alt', '');
				img.setAttribute('src', 'components/com_rsform/assets/images/close.png');

				a.appendChild(img);

				var hidden1 = document.createElement('input');
				hidden1.setAttribute('type', 'hidden');
				hidden1.setAttribute('name', 'calcid[]');
				hidden1.setAttribute('value', response[0]);

				var hidden2 = document.createElement('input');
				hidden2.setAttribute('type', 'hidden');
				hidden2.setAttribute('name', 'calorder[]');
				hidden2.setAttribute('value', response[1]);

				td1.appendChild(select);
				td3.appendChild(input);
				td3.appendChild(document.createTextNode('\u00A0'));
				td3.appendChild(a);
				td3.appendChild(hidden1);
				td3.appendChild(hidden2);
				td3.setAttribute('colspan', '2');
				tr.appendChild(td1);
				tr.appendChild(td2);
				tr.appendChild(td3);
				container.appendChild(tr);

				document.getElementById('rsfp_expression').value = '';

				RSFormPro.$('#rsfp_calculations').tableDnD({
					onDragClass: 'rsform_dragged',
					onDrop     : function (table, row) {
						tidyOrderCalculationsDir();
					}
				});

				RSFormPro.$(document).trigger('renderedCalculationsField', $input_id);
			}

			stateDone();
		}
	};
	xmlHttp.send(params);
}

function rsfp_remove_calculation(id) {
	stateLoading();

	params = 'id=' + id + '&tmpl=component&randomTime=' + Math.random();

	xmlHttp = buildXmlHttp();
	xmlHttp.open("POST", 'index.php?option=com_rsform&task=removeCalculation&controller=forms', true);

	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", params.length);
	xmlHttp.setRequestHeader("Connection", "close");

	xmlHttp.onreadystatechange = function () {//Call a function when the state changes.
		if (xmlHttp.readyState == 4) {
			if (xmlHttp.responseText == 1)
				document.getElementById('calculationRow' + id).dispose();

			stateDone();
		}
	};
	xmlHttp.send(params);
}

function tidyOrderCalculationsDir() {
	stateLoading();

	var params = [];
	var orders = document.getElementsByName('calorder[]');
	var cids = document.getElementsByName('calcid[]');
	var formId = document.getElementById('formId').value;

	for (i = 0; i < orders.length; i++) {
		params.push('cid[' + cids[i].value + ']=' + parseInt(i + 1));
		orders[i].value = i + 1;
	}

	params.push('formId=' + formId);

	xml = buildXmlHttp();

	var url = 'index.php?option=com_rsform&task=forms.save.calculations.ordering&randomTime=' + Math.random();
	xml.open("POST", url, true);

	params = params.join('&');

	//Send the proper header information along with the request
	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");

	xml.send(params);
	xml.onreadystatechange = function () {
		if (xml.readyState == 4) {
			stateDone();
		}
	}
}

RSFormPro.Post = {};

RSFormPro.Post.addField = function () {
	var $table = RSFormPro.$('#com-rsform-post-fields tbody');
	var $row = RSFormPro.$('<tr>');

	var $inputName = RSFormPro.$('<td><input type="text" id="form_post_name'+ Math.floor((Math.random() * 100000) + 1) +'" data-delimiter=" " data-placeholders="display" name="form_post[name][]" placeholder="' + Joomla.JText._('RSFP_POST_NAME_PLACEHOLDER') + '" class="rs_inp rs_80"></td>');
	var $inputValue = RSFormPro.$('<td><input type="text" id="form_post_value'+ Math.floor((Math.random() * 100000) + 1) +'" data-delimiter=" " data-placeholders="display" data-filter-type="include" data-filter="value,global" name="form_post[value][]" placeholder="' + Joomla.JText._('RSFP_POST_VALUE_PLACEHOLDER') + '" class="rs_inp rs_80"></td>');
	var $deleteBtn = RSFormPro.$('<td>').append(RSFormPro.$('<button type="button" class="btn btn-danger btn-mini"><i class="rsficon rsficon-remove"></i></button>').click(RSFormPro.Post.deleteField));

	$row.append($inputName, $inputValue, $deleteBtn);
	$table.append($row);
	var $object = [$inputName, $inputValue];
	RSFormPro.$(document).trigger('renderedSilentPostField', $object);
};

RSFormPro.Post.deleteField = function () {
	RSFormPro.$(this).parents('tr').remove();
};

RSFormPro.$(document).ready(initCodeMirror);
RSFormPro.$(document).ready(initRSFormPro);