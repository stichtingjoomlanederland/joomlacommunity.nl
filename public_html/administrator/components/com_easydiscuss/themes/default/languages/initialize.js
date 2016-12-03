ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {


    EasyDiscuss.ajax('admin/views/languages/getLanguages', {

    })
    .done(function() {

        window.location = "<?php echo rtrim(JURI::root(), '/');?>/administrator/index.php?option=com_easydiscuss&view=languages";

    }).fail(function(html, message) {
        $('[data-ed-table-grid]').replaceWith(html);
    });

});
