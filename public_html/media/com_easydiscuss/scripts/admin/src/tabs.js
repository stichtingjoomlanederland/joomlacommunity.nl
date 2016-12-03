ed.define('admin/src/tabs', ['edq'], function($) {

	var tabWrapper = $('[data-ed-state-tabs]');
	var tab = $('[data-ed-toggle=tab]');
	var activeInput = $('[data-ed-state-tabs-current]')

	$(document)
		.on('click.ed.toggle.tab', tab.selector, function() {
			var el = $(this);

			var id = el.attr('href').replace(/#/, '');
			
			activeInput.val(id);
		});
});