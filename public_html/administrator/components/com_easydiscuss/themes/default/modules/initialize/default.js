ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

    EasyDiscuss.ajax('admin/controllers/modules/discover', {

    })
    .done(function() {
        window.location = '<?php echo rtrim(JURI::root(), '/');?>/administrator/index.php?option=com_easydiscuss&view=modules';
    }).fail(function(html, message) {
		$('[data-languages-wrapper]').addClass('has-error');
		$('[data-languages-error]').html(html);
    });

});