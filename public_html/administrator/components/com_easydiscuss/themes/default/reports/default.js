ed.require(['edq', 'admin/src/reports'], function($, reports) {

	reports.execute('[data-ed-reports]');

	$.Joomla( 'submitbutton' , function(action){
		$.Joomla( 'submitform' , [action] );
	});

    $('[data-reports-preview]').on('click', function() {

        EasyDiscuss.dialog({
            content: EasyDiscuss.ajax('admin/views/reports/preview', {
                "id" : $(this).data('id')
            })
        });
    });
});
