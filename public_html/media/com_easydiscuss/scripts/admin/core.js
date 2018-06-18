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
		'jquery.flot': 'admin/vendors/jquery.flot',
		'jquery.joomla': 'admin/vendors/jquery.joomla',

		'dialog': 'vendors/dialog',
		'bootstrap': 'vendors/bootstrap',
		'lodash': 'vendors/lodash',
		'chartjs': 'vendors/chart',
		'selectize': 'vendors/selectize',
		'composer': 'vendors/composer',
		'markitup': 'vendors/markitup',
		'jquery.expanding': "vendors/jquery.expanding",
		'jquery.atwho': 'vendors/jquery.atwho',
		'jquery.caret': 'vendors/jquery.caret',
		'chosen': 'vendors/jquery.chosen',
		'select2': 'vendors/select2'
	}
});

ed.define('edq', ['edjquery', 'easydiscuss', 'jquery.uri', 'bootstrap', 'jquery.utils', 'jquery.migrate', 'jquery.server', 'lodash', 'dialog', 'jquery.joomla', 'chartjs', 'select2'], function($) {

	// Implement popover
	$(document).on("mouseover", "[rel=ed-popover]", function(){
		$(this).popover({container: 'body', delay: { show: 100, hide: 100},animation: false, trigger: 'hover'});
	});

	$('[data-ed-table-filter]').select2({
		'theme': 'backend',
		'width': 'resolve',
		'minimumResultsForSearch': Infinity
	});

	$('[data-ed-table-filter]').on('select2:open', function() {
		$('body').addClass('has-select2-dropdown');
	});

	$('[data-ed-table-filter]').on('select2:close', function() {
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

	var addTooltip = $._.debounce(function(){

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
