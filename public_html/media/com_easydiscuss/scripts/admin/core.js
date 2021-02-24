ed.require.config({
	baseUrl: window.ed_site ? window.ed_site + 'media/com_easydiscuss/scripts' : '/media/com_easydiscuss/scripts',
	paths: {
		'abstract': 'vendors/abstract',

		// EasyDiscuss version of jquery
		'easydiscuss': 'vendors/easydiscuss',
		'edjquery': 'vendors/edjquery',
		'jquery.utils': 'vendors/jquery.utils',
		'jquery.uri': 'vendors/jquery.uri',
		'jquery.server': 'vendors/jquery.server',
		'jquery.migrate': 'vendors/jquery.migrate',
		'dialog': 'vendors/dialog',
		'bootstrap': 'vendors/bootstrap',
		'markitup': 'vendors/markitup',
		'jquery.expanding': "vendors/jquery.expanding",
		'jquery.debounce': 'vendors/jquery.debounce',
		'select2': 'vendors/select2',
		'iconpicker': 'admin/vendors/fontawesome-iconpicker',
		'jquery.joomla': 'admin/vendors/jquery.joomla',
		'chartjs': 'admin/vendors/chart'
	}
});

ed.define('edq', ['edjquery', 'easydiscuss', 'jquery.uri', 'bootstrap', 'jquery.utils', 'jquery.debounce', 'jquery.migrate', 'jquery.server', 'dialog', 'jquery.joomla', 'chartjs', 'select2'], function($) {

	var filters = $('[data-table-filter]');

	var initSelect2Dropdown = function(element, options) {
		var opts = $.extend({}, {
						'width': 'resolve',
						'minimumResultsForSearch': 3
					}, options);

		$(element).select2(opts);
	};

	filters.each(function() {
		var filter = $(this);
		var totalChildren = filter.children().length;

		initSelect2Dropdown(filter, {
			'theme': 'backend',
			'minimumResultsForSearch': totalChildren > 5 ? 3 : Infinity
		});
	});

	initSelect2Dropdown('[data-ed-select]');

	$('[data-table-filter]').on('select2:open', function() {
		$('body').addClass('has-select2-dropdown');
	});

	$('[data-table-filter]').on('select2:close', function() {
		$('body').removeClass('has-select2-dropdown');
	});

	$(document).on('change.form.toggler', '[data-ed-toggler-checkbox]', function() {
		var checkbox = $(this);
		var checked = checkbox.is(':checked');
		var parent = checkbox.parents('[data-ed-toggler]');

		if (parent.length > 0) {
			var input = parent.find('input[type=hidden]');
			input.val(checked ? 1 : 0).trigger('change');
		}
	});

	$(document).on('change.form.toggler.value', '[data-ed-toggler-checkbox] ~ input[type=hidden]', function() {
		var input = $(this);
		var value = input.val();
		var checked = value == 1 ? true : false;

		var checkbox = input.siblings('[data-ed-toggler-checkbox]');
		var isChecked = checkbox.is(':checked');
		
		// When hidden value is enabled, but checkbox has been disabled, we need to update it
		if (checked && !isChecked) {
			checkbox.prop('checked', true);
			return;
		}

		if (!checked && isChecked) {
			checkbox.prop('checked', false);
			return;
		}

	});

	// Admin toolbar actions
	//
	var actionsButton = $('[data-ed-admin-actions]');

	if (actionsButton.length > 0) {
		actionsButton
			.removeClass('t-hidden')
			.appendTo('#toolbar');
	}

	// End Admin toolbar
	
	// Tooltips
	// detect if mouse is being used or not.
	var tooltipLoaded = false;
	var mouseCount = 0;
	window.onmousemove = function() {

		mouseCount++;

		addTooltip();
	};

	EasyDiscuss.compareVersion = function(version1, version2) {
		var nRes = 0;
		var parts1 = version1.split('.');
		var parts2 = version2.split('.');
		var nLen = Math.max(parts1.length, parts2.length);

		for (var i = 0; i < nLen; i++) {
			var nP1 = (i < parts1.length) ? parseInt(parts1[i], 10) : 0;
			var nP2 = (i < parts2.length) ? parseInt(parts2[i], 10) : 0;

			if (isNaN(nP1)) { 
				nP1 = 0; 
			}
			
			if (isNaN(nP2)) { 
				nP2 = 0; 
			}

			if (nP1 != nP2) {
				nRes = (nP1 > nP2) ? 1 : -1;
				break;
			}
		}

		return nRes;
	}

	var addTooltip = $.debounce(function(){
	
		if (!tooltipLoaded && mouseCount > 10) {

			tooltipLoaded = true;
			mouseCount = 0;

			$(document).on('mouseover.tooltip.data-ed-api', '[data-ed-provide=tooltip]', function() {

				$(this)
					.tooltip({
						delay: {
							show: 200,
							hide: 100
						},
						animation: false,
						template: '<div id="ed" class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
						container: 'body'
					})
					.tooltip("show");
			});
		} else {
			mouseCount = 0;
		}
	}, 500);


	return $;
});
