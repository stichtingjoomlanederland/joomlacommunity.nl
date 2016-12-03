ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

    // Form submission
    var holidayForm = $('[data-ed-holiday-form]');
    var submitButton = $('[data-ed-submit-button]');

    submitButton.on('click', function() {

        var button = $(this);

        // Check if the button was disabled
        var disabled = button.attr('disabled');

        if (disabled) {
            return;
        }

        // Perform validations
        var title = $('[data-ed-holiday-title]');

        if (title.val() == '') {

            // Add error
            title.parent().addClass('has-error');
            return false;
        }

        // Convert the string to date format
        var startDate = $('[data-ed-holiday-start]')
            endDate = $('[data-ed-holiday-end]'),

            start = new Date(startDate.val()),
            end = new Date(endDate.val());

        // Don't let user choose the end date lower than start date
        if (end < start) {
            // Add error
            endDate.parent().addClass('has-error');
            return false;
        }

        // Disable the button to avoid posting multiple times
        button.attr('disabled', true);

        // Submit the form
        holidayForm.submit();

        return false;
    });

});