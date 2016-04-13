ed.require(['edq'], function($) {
    // $('[data-grid-eb]').implement(EasyBlog.Controller.Grid);

    $.Joomla('submitbutton', function(task) {
        if (task == 'maintenance.form') {
            document.adminForm.layout.value = 'form';
            $.Joomla('submitform');
        }
    });
});
