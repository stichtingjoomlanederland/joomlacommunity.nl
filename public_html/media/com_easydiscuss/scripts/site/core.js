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
		'jquery.popbox': 'vendors/jquery.popbox',
		'dialog': 'vendors/dialog',
		'bootstrap': 'vendors/bootstrap',
		'markitup': 'vendors/markitup',
		'jquery.expanding': "vendors/jquery.expanding",
		'select2': 'vendors/select2',
		'jquery.debounce': 'vendors/jquery.debounce',

		// Required by popbox
		'jquery.ui.position': 'site/vendors/jquery.ui.position',

		// Site scripts
		'jquery.caret': 'site/vendors/jquery.caret',
		'jquery.atwho': 'site/vendors/jquery.atwho',
		'selectize': 'site/vendors/selectize',
		'toastr': 'site/vendors/toastr',
		'perfect-scrollbar': 'site/vendors/perfect-scrollbar',
		'eventsource': 'site/vendors/eventsource',
		'jquery.raty': 'site/vendors/jquery.raty',
		'jquery.scrollto': 'site/vendors/jquery.scrollto',
		'jquery.fancybox': 'site/vendors/jquery.fancybox',
		'dependencies': 'site/src/dependencies'
	}
});

ed.define('edq', ['edjquery', 'jquery.uri', 'bootstrap', 'jquery.popbox', 'jquery.ui.position', 'jquery.utils', 'jquery.debounce', 'jquery.server', 'jquery.migrate', 'dialog', 'select2'], function($) {
	ed.require(['dependencies']);

	document.documentElement.classList.add('si-theme--' + window.ed_mode);

	return $;
});
