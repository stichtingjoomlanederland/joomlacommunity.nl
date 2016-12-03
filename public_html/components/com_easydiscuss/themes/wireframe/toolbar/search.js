ed.require(['edq', 'site/src/discuss'], function($, EasyDiscuss) {
    $(document)
        .on('click', '[data-search-button]', function() {
            $('[data-search-form]').submit();
        });
});
