ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

    var wrapper = $('[data-ed-post-comments-wrapper]');
    var addCommentButton = $('[data-ed-toggle-comment]');
    var commentInput = $('[data-ed-comment-message]');
    var commentForm = $('[data-ed-comment-form]');
    var submitButton = $('[data-ed-comment-submit]');

    addCommentButton.on('click', function() {

        $(this).siblings(commentForm).toggleClass('hide');

    });

    submitButton.on('click', function() {

        var button = $(this);

        // Check if the button was disabled
        var disabled = button.attr('disabled');

        if (disabled) {
            return;
        }

        // // Perform validations
        // var title = $('[data-ed-editor]');

        // if (title.val() == '') {

        //     // Add error
        //     title.parent().addClass('has-error');
            
        //     return false;
        // }

        // Disable the button to avoid posting multiple times
        button.attr('disabled', true);

        // Submit the form
        //commentForm.submit();

        return false;
    });

});
