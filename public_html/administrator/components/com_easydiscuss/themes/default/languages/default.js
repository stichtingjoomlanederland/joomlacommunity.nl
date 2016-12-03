ed.require(['edq', 'easydiscuss', 'admin/src/table'], function($, EasyDiscuss) {

    $.Joomla("submitbutton", function(task) {

        $.Joomla('submitform', [task]);
    });

});