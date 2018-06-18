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
		'lodash': 'vendors/lodash',
		'bootstrap': 'vendors/bootstrap',
		'typeahead': 'vendors/typeahead',
		'chosen': 'vendors/jquery.chosen',
		'chartjs': 'vendors/chart',
		'selectize': 'vendors/selectize',
		'composer': 'vendors/composer',
		'markitup': 'vendors/markitup',
		'jquery.expanding': "vendors/jquery.expanding",
		'jquery.atwho': 'vendors/jquery.atwho',
		'jquery.caret': 'vendors/jquery.caret',
		'select2': 'vendors/select2',
		'historyjs': 'site/vendors/history',

		// Site scripts
		'jquery.ui.core': 'site/vendors/jquery.ui.core',
		'jquery.ui.position': 'site/vendors/jquery.ui.position',
		'jquery.ui.autocomplete': 'site/vendors/jquery.ui.autocomplete',
		'jquery.ui.widget': 'site/vendors/jquery.ui.widget',
		'jquery.ui.menu': 'site/vendors/jquery.ui.menu',
		'jquery.raty': 'site/vendors/jquery.raty',
		'cropper': 'site/vendors/cropper',
		'jquery.scrollto': 'site/vendors/jquery.scrollto',
		'jquery.fancybox': 'site/vendors/jquery.fancybox',
		'responsive': 'site/vendors/responsive',
		'dependencies': 'site/src/dependencies',

		'api': 'site/src/api'
	}
});

ed.define('edq', ['edjquery', 'jquery.uri', 'bootstrap', 'jquery.popbox', 'jquery.ui.position', 'jquery.utils', 'jquery.server', 'jquery.migrate', 'lodash', 'dialog', 'responsive', 'api', 'select2', 'historyjs'], function($) {
	ed.require(['dependencies']);

	return $;
});
