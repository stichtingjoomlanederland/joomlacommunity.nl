if (typeof RSFormPro != 'object') {
	var RSFormPro = {};
}

RSFormPro.$ = jQuery;

function initRSFormPro() {
	jQuery('#mappingTable tbody').tableDnD({
		onDragClass: 'rsform_dragged',
		onDragStop : function (table, row) {
			tidyOrderMp(true);
		}
	});

	jQuery('#rsfp_calculations').tableDnD({
		onDragClass: 'rsform_dragged',
		onDragStop : function (table, row) {
			tidyOrderCalculationsDir();
		}
	});

	jQuery(document).click(function () {
		jQuery(this).mousedown(function (e) {
			if (!jQuery(e.target).is('input')) {
				var checkParent = jQuery(e.target).parents('.dropdownContainer').length;
				if (!checkParent) {
					closeAllDropdowns();
				}
			}
		});
	});

	jQuery("#properties").click(function () {
		jQuery("#rsform_tab2").show();
		jQuery("#rsform_tab1").hide();
		jQuery("#components").removeClass('btn-primary');
		jQuery("#properties").addClass('btn-primary');
	});

	jQuery("#components").click(function () {
		jQuery("#rsform_tab1").show();
		jQuery("#rsform_tab2").hide();
		jQuery("#properties").removeClass('btn-primary');
		jQuery("#components").addClass('btn-primary');
	});

	jQuery('[data-placeholders]').rsplaceholder();

}

function legacyOrderingEnable()
{
	var $table = jQuery('#componentPreview');
    $table.find('tbody').tableDnD({
        onDragClass: 'rsform_dragged',
        onDragStop : function (table, row) {
            tidyOrder(true);
        }
    });

    $table.find('.order').show();

    RSFormPro.Grid.hide();

    jQuery('#rsform_ordering_msg').hide();
}

function legacyOrderingDisable()
{
    var $table = jQuery('#componentPreview');
    $table.find('tbody').tableDnDDestroy();
    $table.find('.order').hide();

    RSFormPro.Grid.show();

    jQuery('#rsform_ordering_msg').show();
}

jQuery(document).on('renderedMappings', function(){
	jQuery('[data-placeholders]').rsplaceholder();
});

jQuery(document).on('renderedRsfpmappingWhere', function(event, element){
	jQuery('#'+element).find('[data-placeholders]').rsplaceholder();
});

jQuery(document).on('renderedSilentPostField', function($event, $field_one, $field_two){
	jQuery($field_one).find('input').rsplaceholder();
	jQuery($field_two).find('input').rsplaceholder();
});

jQuery(document).on('renderedCalculationsField', function($event, $field){
	jQuery('#'+$field).rsplaceholder();
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
		params.push('cid[' + cids[i].value + ']=' + parseInt(i + 1));

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

function orderMapping(mp, task)
{
    if (task == 'orderdown' || task == 'orderup')
    {
        var table = RSFormPro.$('#mappingTable');
        currentRow = RSFormPro.$(document.getElementById(mp)).parent().parent();
        if (task == 'orderdown')
        {
            try { currentRow.insertAfter(currentRow.next()); }
            catch (dnd_e) { }
        }
        if (task == 'orderup')
        {
            try { currentRow.insertBefore(currentRow.prev()); }
            catch (dnd_e) { }
        }

        tidyOrderMp(true);
    }
}

function displayTemplate(componentTypeId, componentId) {
	RSFormPro.editModal.display(componentTypeId, componentId);
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
		
		RSFormPro.Grid.deleteField(componentId);

		if (!response.submit) {
			jQuery('#rsform_submit_button_msg').show();
		}

		tidyOrder(true);

		stateDone();
	}, 'json');
}

function processComponent(componentType) {
	RSFormPro.editModal.disableButton();

	jQuery('#rsformerror0').hide();
	jQuery('#rsformerror1').hide();
	jQuery('#rsformerror2').hide();
	jQuery('#rsformerror3').hide();

	stateLoading();

	// Build URL to post to
	var url = 'index.php?option=com_rsform&task=components.validate.name&randomTime=' + Math.random();

	// Build data array
	var data = {
		'componentName'     : jQuery('#NAME').val(),
		'formId'            : jQuery('#formId').val(),
		'currentComponentId': jQuery('#componentIdToEdit').val(),
		'componentType'     : componentType
	};

	if (componentType == 9) {
		data['destination'] = jQuery('#DESTINATION').val();
	}

	RSFormPro.$.post(url, data, function (response, status, jqXHR) {
		if (response.result == false) {
			// Switch to tab
			jQuery('[href="#rsfptab' + response.tab + '"]').click();

			// Show error message
			jQuery('#rsformerror' + response.tab).text(response.message).show();

			stateDone();

			RSFormPro.editModal.enableButton();
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
				
				if (typeof Joomla.editors.instances['ViewLayout'] != 'undefined')
				{
					Joomla.editors.instances['ViewLayout'].setOption('readOnly', true);
				}
			}
			else {
				document.getElementById('rsform_layout_msg').style.display = '';
				document.getElementById('ViewLayout').readOnly = false;
				if (typeof Joomla.editors.instances['ViewLayout'] != 'undefined')
				{
					Joomla.editors.instances['ViewLayout'].setOption('readOnly', false);
				}
			}

			stateDone();
		}
	};
	xml.open('GET', 'index.php?option=com_rsform&task=directory.changeAutoGenerateLayout&formId=' + formId + '&status=' + value + '&randomTime=' + Math.random() + '&ViewLayoutName=' + layoutName, true);
	xml.send(null);
}

function autoGenerateLayout()
{
    if (document.getElementById('FormLayoutAutogenerate1').checked == true)
    {
        var formId = document.getElementById('formId').value;
        generateLayout(formId, false);
    }
}

function changeFormLayoutFlow()
{
    stateLoading();

    // Build URL to post to
    var url = 'index.php?option=com_rsform&task=forms.changeFormLayoutFlow&randomTime=' + Math.random();

    // Build data array
    var data = {
        'status': jQuery('[name=FormLayoutFlow]').val(),
        'formId': document.getElementById('formId').value
    };

    RSFormPro.$.post(url, data, function (response, status, jqXHR) {
        stateDone();

        autoGenerateLayout();
    }, 'json');
}

function changeFormAutoGenerateLayout(formId, value) {
	stateLoading();

	// Build URL to post to
	var url = 'index.php?option=com_rsform&task=forms.changeAutoGenerateLayout&randomTime=' + Math.random();

	// Build data array
	var data = {
		'formLayoutName': jQuery('[name=FormLayoutName]:checked').val(),
		'formId'        : formId,
		'status'        : value
	};

	RSFormPro.$.post(url, data, function (response, status, jqXHR) {
		var hasCodeMirror = typeof Joomla.editors.instances['formLayout'] != 'undefined';

		value = Boolean(parseInt(value));

		value ? jQuery('#rsform_layout_msg').hide() : jQuery('#rsform_layout_msg').show();
		jQuery('#formLayout').prop('readonly', value);

		if (hasCodeMirror) {
			Joomla.editors.instances['formLayout'].setOption('readOnly', value);
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
		'layoutName': jQuery('[name=FormLayoutName]:checked').val(),
		'formId'    : formId
	};

	RSFormPro.$.post(url, data, function (response, status, jqXHR) {
		var hasCodeMirror = typeof Joomla.editors.instances['formLayout'] != 'undefined';

		jQuery('#formLayout').val(response);
		if (hasCodeMirror)
		{
			Joomla.editors.instances['formLayout'].setValue(response);
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
			if (typeof Joomla.editors.instances['ViewLayout'] != 'undefined')
			{
				Joomla.editors.instances['ViewLayout'].setValue(xml.responseText);
			}
			stateDone();
		}
	};
	xml.open('GET', 'index.php?option=com_rsform&task=directory.generate&layoutName=' + layoutName + '&formId=' + formId + '&randomTime=' + Math.random(), true);
	xml.send(null);
}

function saveLayoutName(formId, layoutName, isLegacy) {
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
	};

	isLegacy ?  legacyOrderingEnable() : legacyOrderingDisable();
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
	var xml = buildXmlHttp();
	xml.onreadystatechange = function () {
		if (xml.readyState == 4) {
			var post = xml.responseText;
			if (post.indexOf('END') != -1) {
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

function importProcess(start, limit, total, formId)
{
    var xml = buildXmlHttp();
    xml.onreadystatechange = function ()
	{
        if (xml.readyState == 4)
        {
            var post = xml.responseText;
            if (post.indexOf('END') > -1)
            {
                document.getElementById('progressBar').style.width = document.getElementById('progressBar').innerHTML = '100%';

                Joomla.renderMessages({'message': [Joomla.JText._('COM_RSFORM_IMPORT_HAS_FINISHED')]});
            }
            else
            {
            	var start = post;

                document.getElementById('progressBar').style.width = Math.ceil(start * 100 / total) + '%';
                document.getElementById('progressBar').innerHTML = Math.ceil(start * 100 / total) + '%';

                importProcess(start, limit, total);
            }
        }
    };

    xml.open('GET', 'index.php?option=com_rsform&task=submissions.importprocess&formId=' + formId + '&importStart=' + start + '&importLimit=' + limit + '&randomTime=' + Math.random(), true);
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
			if (elem.value == 'regex') {
                theText = Joomla.JText._('RSFP_COMP_FIELD_VALIDATIONEXTRAREGEX');
			} else if (elem.value == 'sameas') {
                theText = Joomla.JText._('RSFP_COMP_FIELD_VALIDATIONEXTRASAMEAS');
			} else {
				theText = Joomla.JText._('RSFP_COMP_FIELD_VALIDATIONEXTRA');
			}
			document.getElementById('captionVALIDATIONEXTRA').innerHTML = theText;

			if (elem.value == 'custom' || elem.value == 'numeric' || elem.value == 'alphanumeric' || elem.value == 'alpha' || elem.value == 'regex' || elem.value == 'sameas')
				document.getElementById('idVALIDATIONEXTRA').className = 'showVALIDATIONEXTRA control-group';
			else
				document.getElementById('idVALIDATIONEXTRA').className = 'hideVALIDATIONEXTRA control-group';
		}
		
		var multipleRulesField = document.getElementById('idVALIDATIONMULTIPLE');
		if (elem.value == 'multiplerules') {
			multipleRulesField.style.display = 'block';
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
		var selectedValues = [];
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
			
			if (selectedValues[i] == 'regex') {
                theText = Joomla.JText._('RSFP_COMP_FIELD_VALIDATIONEXTRAREGEX');
			} else if (selectedValues[i] == 'sameas') {
                theText = Joomla.JText._('RSFP_COMP_FIELD_VALIDATIONEXTRASAMEAS');
			} else {
				theText = Joomla.JText._('RSFP_COMP_FIELD_VALIDATIONEXTRA');
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
	var el = jQuery('#columnsDiv');

	if (el.is(':hidden')) {
		var windowH = jQuery(window).height();
		var remove = 0;
		if (jQuery('body > #status').length > 0) {
			remove += parseInt(jQuery('body > #status').height());
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
	var dropdowns = jQuery('.dropdownContainer');
	var except = jQuery('#dropdown' + except);

	for (var i = 0; i < dropdowns.length; i++) {
		var dropdown = jQuery(dropdowns[i]).children('div');
		if (dropdown.attr('id') != except.attr('id'))
			jQuery(dropdowns[i]).children('div').hide();
	}
}

/**
 * @deprecated, used to generate the new type of fields
 * @param what
 * @param extra
 * @param inner
 */
function toggleDropdown(what, extra, inner) {

		jQuery(what).addClass('placeholders-initiated');

		$attr = {
			'data-delimiter' : ' ',
			'data-placeholders' : 'display',
			'onclick' : '',
			'onkeydown' : ''
		};
		jQuery(what).attr($attr);

		jQuery(what).rsplaceholder();

}

function toggleQuickAdd() {
	var what = 'none';
	if (document.getElementById('QuickAdd1').style.display == 'none')
		what = '';

	document.getElementById('QuickAdd1').style.display = what;
	document.getElementById('QuickAdd2').style.display = what;
	document.getElementById('QuickAdd3').style.display = what;
	document.getElementById('QuickAdd4').style.display = what;
	document.getElementById('QuickAdd5').style.display = what;
}

function mpConnect() {
	var fields = jQuery("#tablers :input");
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

	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState == 4) {
			response = xmlHttp.responseText.split('|');

			if (response[0] == 1) {
				document.getElementById('rsfpmappingContent').innerHTML = response[1];
				document.getElementById('connectBtn').style.display = 'none';
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
	return jQuery('#' + element).parent().text();
}


function mpColumns(table) {
	var fields = jQuery("#tablers :input");
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

	xmlHttp.onreadystatechange = function () {//Call a function when the state changes.
		if (xmlHttp.readyState == 4) {
			document.getElementById('mappingcontent').innerHTML = xmlHttp.responseText;
			stateDone();

			jQuery('#mappingTable tbody').tableDnD({
				onDragClass: 'rsform_dragged',
				onDragStop     : function (table, row) {
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

	xmlHttp.onreadystatechange = function () {//Call a function when the state changes.
		if (xmlHttp.readyState == 4) {
			document.getElementById('mappingcontent').innerHTML = xmlHttp.responseText;
			stateDone();

			jQuery('#mappingTable tbody').tableDnD({
				onDragClass: 'rsform_dragged',
				onDragStop     : function (table, row) {
					tidyOrderMp(true);
				}
			});
		}
	};
	xmlHttp.send(params);
}

function mappingWhere(table) {
	var fields = jQuery("#tablers :input");
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

	xmlHttp.onreadystatechange = function () {//Call a function when the state changes.
		if (xmlHttp.readyState == 4) {
			document.getElementById('rsfpmappingWhere').innerHTML = xmlHttp.responseText;
			document.getElementById('mappingloader2').style.display = 'none';
			jQuery(document).trigger('renderedRsfpmappingWhere', 'rsfpmappingWhere');
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

	xmlHttp.onreadystatechange = function () {//Call a function when the state changes.
		if (xmlHttp.readyState == 4) {
			stateDone();
			content.innerHTML = xmlHttp.responseText;
		}
	};
	xmlHttp.send(params);
}

function conditionDelete(formid, cid) {
	stateLoading();

	params = 'formId=' + formid + '&cid=' + cid + '&tmpl=component&randomTime=' + Math.random();

	xmlHttp = buildXmlHttp();
	xmlHttp.open("POST", 'index.php?option=com_rsform&task=remove&controller=conditions', true);

	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

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

	window.open(href, type, 'width=' + size[0] + ', height=' + size[1] + ',scrollbars=1');
}

function addCalculation(formId) {
	if (document.getElementById('rsfp_expression').value == '')
	{
		return;
	}

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

				var a = document.createElement('button');
				a.setAttribute('class', 'btn btn-danger btn-mini');
				a.setAttribute('type', 'button');
				a.onclick = function () {
					removeCalculation(response[0]);
				};

				var img = document.createElement('i');
				img.setAttribute('class', 'rsficon rsficon-remove');

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

				jQuery('#rsfp_calculations').tableDnD({
					onDragClass: 'rsform_dragged',
					onDragStop: function (table, row) {
						tidyOrderCalculationsDir();
					}
				});

				jQuery(document).trigger('renderedCalculationsField', $input_id);
			}

			stateDone();
		}
	};
	xmlHttp.send(params);
}

function removeCalculation(id) {
	if (!confirm(Joomla.JText._('RSFP_DELETE_SURE_CALCULATION')))
	{
		return;
	}
	
	stateLoading();

	params = 'id=' + id + '&tmpl=component&randomTime=' + Math.random();

	xmlHttp = buildXmlHttp();
	xmlHttp.open("POST", 'index.php?option=com_rsform&task=removeCalculation&controller=forms', true);

	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

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

	xml.send(params);
	xml.onreadystatechange = function () {
		if (xml.readyState == 4) {
			stateDone();
		}
	}
}

function validateEmailFields() {
    var fields = [
        'UserEmailFrom', 'UserEmailTo', 'UserEmailReplyTo', 'UserEmailCC', 'UserEmailBCC',
        'AdminEmailFrom', 'AdminEmailTo', 'AdminEmailReplyTo', 'AdminEmailCC', 'AdminEmailBCC',
        'DeletionEmailFrom', 'DeletionEmailTo', 'DeletionEmailReplyTo', 'DeletionEmailCC', 'DeletionEmailBCC'
    ];

    var result = true;
    var fieldName, field, fieldValue, values, value, match;
    var pattern = /{.*?}/g;

    for (var i = 0; i < fields.length; i++) {
        // Grab field name from array
        fieldName 	= fields[i];
        field 		= document.getElementById(fieldName);
        // Grab value
        fieldValue 	= field.value;

        RSFormPro.$(field).removeClass('rs_error_field');

        // Something's been typed in
        if (fieldValue.length > 0) {
            // Check for multiple values
            values = fieldValue.split(',');

            for (var v = 0; v < values.length; v++) {
                value = values[v].replace(/^\s+|\s+$/gm,'');

                // Has placeholder
                hasPlaceholder = value.indexOf('{') > -1 && value.indexOf('}') > -1;

                // Defaults to false, the code below will actually check the placeholder
                wrongPlaceholder = false;

                // Let's take into account multiple placeholders
                if (hasPlaceholder) {
                    do {
                        match = pattern.exec(value);
                        if (match && typeof match[0] != 'undefined') {
                            // Wrong placeholder
                            if (RSFormPro.Placeholders.indexOf(match[0]) == -1) {
                                wrongPlaceholder = true;
                            }
                        }
                    } while (match);
                }

                // Not an email
                notAnEmail = !hasPlaceholder && value.indexOf('@') == -1;
                // A situation where we have a wrong delimiter thus ending up in multiple @ addresses
                wrongDelimiter = !hasPlaceholder && (value.match(/@/g) || []).length > 1;

                if (wrongPlaceholder || notAnEmail || wrongDelimiter) {
                    // Switch to the correct tab only on the first error
                    if (result == true) {
                        RSFormPro.$('#properties').click();
                        if (fieldName.indexOf('User') > -1) {
                            RSFormPro.$('#useremails').click();
                        } else {
                            RSFormPro.$('#adminemails').click();
                        }
                    }
                    RSFormPro.$(field).addClass('rs_error_field');
                    result = false;
                }
            }
        }
    }

    return result;
}

function enableAttachFile(value)
{
    if (value == 1)
    {
        document.getElementById('rsform_select_file').style.display = '';
        document.getElementById('UserEmailAttachFile').disabled = false;
    }
    else
    {
        document.getElementById('rsform_select_file').style.display = 'none';
        document.getElementById('UserEmailAttachFile').disabled = true;
    }
}

function enableThankyou(value)
{
    if (value == 1)
    {
        document.getElementById('showContinueContainer').style.display = 'table-row';
        document.getElementById('systemMessageContainer').style.display = 'none';

        if (document.getElementById('ScrollToThankYou0').checked)
        {
            document.getElementById('thankyouMessagePopupContainer').style.display = 'table-row';
        }
    }
    else
    {
        document.getElementById('showContinueContainer').style.display = 'none';
        document.getElementById('systemMessageContainer').style.display = 'table-row';

        document.getElementById('thankyouMessagePopupContainer').style.display = 'none';
    }
}

function enableThankyouPopup(value)
{
    if (value == 0)
    {
        if (document.getElementById('ShowThankyou1').checked)
        {
            document.getElementById('thankyouMessagePopupContainer').style.display = 'table-row';
        }
    }
    else
    {
        if (document.getElementById('ShowThankyou1').checked)
        {
            document.getElementById('thankyouMessagePopupContainer').style.display = 'none';
        }
    }
}

RSFormPro.Post = {};

RSFormPro.Post.addField = function () {
	var $table = jQuery('#com-rsform-post-fields tbody');
	var $row = jQuery('<tr>');

	var $inputName = jQuery('<td><input type="text" id="form_post_name'+ Math.floor((Math.random() * 100000) + 1) +'" data-delimiter=" " data-placeholders="display" name="form_post[name][]" placeholder="' + Joomla.JText._('RSFP_POST_NAME_PLACEHOLDER') + '" class="rs_inp rs_80"></td>');
	var $inputValue = jQuery('<td><input type="text" id="form_post_value'+ Math.floor((Math.random() * 100000) + 1) +'" data-delimiter=" " data-placeholders="display" data-filter-type="include" data-filter="value,global" name="form_post[value][]" placeholder="' + Joomla.JText._('RSFP_POST_VALUE_PLACEHOLDER') + '" class="rs_inp rs_80"></td>');
	var $deleteBtn = jQuery('<td>').append(jQuery('<button type="button" class="btn btn-danger btn-mini"><i class="rsficon rsficon-remove"></i></button>').click(RSFormPro.Post.deleteField));

	$row.append($inputName, $inputValue, $deleteBtn);
	$table.append($row);
	var $object = [$inputName, $inputValue];
	jQuery(document).trigger('renderedSilentPostField', $object);
};

RSFormPro.Post.deleteField = function () {
	jQuery(this).parents('tr').remove();
};

jQuery(document).ready(initRSFormPro);