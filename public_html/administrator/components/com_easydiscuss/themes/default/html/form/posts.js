ed.require(['edq'], function($) {
    window.insertPost = function(id, name) {
        $('#<?php echo $id;?>-placeholder').val(name);
        $('#<?php echo $id;?>').val(id);

        EasyDiscuss.dialog().close();
    }

    $('[data-form-remove-post]').on('click', function() {
        var button = $(this);
        var parent = button.parents('[data-form-post-wrapper]');

        // Reset the form
        parent.find('input[type=hidden]').val('');
        parent.find('input[type=text]').val('');
    });

    $('[data-form-browse-posts]').on('click', function() {
        EasyDiscuss.dialog({
            content: EasyDiscuss.ajax('admin/views/subscription/browse')
        });
    });
});