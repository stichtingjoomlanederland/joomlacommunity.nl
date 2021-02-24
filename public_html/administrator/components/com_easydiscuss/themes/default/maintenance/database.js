ed.require(['edq', 'admin/src/maintenance.database'], function($, maintanance) {
    maintanance.execute('[data-maintenance-database]');
});
