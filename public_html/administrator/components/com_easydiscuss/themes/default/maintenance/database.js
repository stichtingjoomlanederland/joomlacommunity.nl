// EasyBlog.require().script('admin/maintenance/database').done(function($) {
//    $('[data-base]').addController('EasyBlog.Controller.Maintenance.Database');
// });
ed.require(['edq', 'admin/src/maintenance.database'], function($, maintanance) {
    maintanance.execute('[data-maintenance-database]');
});
