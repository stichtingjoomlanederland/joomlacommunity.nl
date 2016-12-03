ed.require(['edq', 'easydiscuss'], function($) {

    $(document)
        .on('click', '[data-ed-tab]', function() {

            var id = $(this).data('id');

            var activeInput = $('[data-ed-active-tab]');

            // Set the active tab value
            activeInput.val(id);
        });


});