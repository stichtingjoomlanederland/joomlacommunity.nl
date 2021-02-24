ed.require(['edq'], function($) {
    $(document)
        .on('click', '[data-search-button]', function() {
            $('[data-search-form]').submit();
        });
});
