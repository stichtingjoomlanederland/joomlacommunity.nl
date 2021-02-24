ed.require(['edq', 'site/src/postcount'], function($, counter) {
	// get category post count
	counter.execute('[data-ed-forums]');
});