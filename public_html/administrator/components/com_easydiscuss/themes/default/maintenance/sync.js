
ed.require(['edq', 'admin/src/maintenance.sync'], function($, maintanance) {
	maintanance.execute('[data-maintenance-sync]');
});
