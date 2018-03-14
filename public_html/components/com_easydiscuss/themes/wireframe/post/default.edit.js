ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

    var wrapper = $('[data-ed-composer-wrapper]');
    var notification = wrapper.find('[data-ed-composer-alert]');


    $('[data-ed-submit-button]').click(function() {

        //reset message
        resetAlert(notification);

        // lets go some simple validation.
        if (! formValidateInputs()){
            return false;
        }

        // lets proceed to submit the form
        var inputForm = $('[data-ed-reply-form]');
        inputForm.submit();
    });

    function resetAlert(alert)
    {

        // Get the notification bar
        alert
            .html('')
            .removeClass('o-alert--success')
            .removeClass('o-alert--danger')
            .addClass('t-hidden');

    };

    function setAlert(alert, message, state)
    {

        var newClass = state == 'error' ? 'o-alert--danger' : 'o-alert--success';

        alert
            .html(message)
            .removeClass('o-alert--success')
            .removeClass('o-alert--danger')
            .addClass(newClass);
    };


    function formValidateInputs()
    {

        var content = wrapper.find('[data-ed-editor]');

        if(content.val() == '') {
            notification.removeClass('t-hidden');
            setAlert(notification, '<?php echo JText::_('COM_EASYDISCUSS_ERROR_REPLY_EMPTY'); ?>', 'error');
            return false;
        }

        // Get custom field data attribute
        var textbox = wrapper.find('[data-ed-textbox-fields]');
        var textarea = wrapper.find('[data-ed-textarea-fields]');
        var radio = wrapper.find('[data-ed-radio-fields]');
        var checkbox = wrapper.find('[data-ed-checkbox-fields]');
        var selectList = wrapper.find('[data-ed-select-fields]');
        var selectMultipleList = wrapper.find('[data-ed-select-multiple-fields]');

        // Check whether custom fields is it set as required
        var fields = wrapper.find('[data-ed-custom-fields-required]');

        var fieldsWrapper = fields.parents('[data-ed-custom-fields]');
        var fieldTab = fieldsWrapper.children('[data-ed-custom-fields-tab]');
        // Get the field required state
        var fieldsRequiredWrapper = fieldTab.siblings().find('[data-ed-custom-fields-required]');
        // Get all the custom field form groups
        var fieldGroup = fieldsRequiredWrapper.parents('[data-ed-custom-fields-required-group]');

        var inputRequired = false;

        // highlight the field tab
        var tabField = wrapper.find('[data-ed-ask-tabs]');
        var tabFieldError = tabField.children('[data-ed-tab-field-heading]');

        // re-check again if required field value already fill in
        tabFieldError.removeClass('has-error');
        fieldGroup.removeClass('has-error');

        if (fieldGroup.length > 0) {

            // Get each of the existing required custom fields
            fieldGroup.each(function(idx, el) {

                var field = $(el);
                var fType = field.data('field-type');

                if (fType == 'text' && textbox.val() == '') {
                    inputRequired = true;
                    field.addClass('has-error');
                }

                if (fType == 'area' && textarea.val() == '') {
                    inputRequired = true;
                     field.addClass('has-error');
                }

                if (fType == 'radio' && !radio.is(':checked')) {
                    inputRequired = true;
                     field.addClass('has-error');
                }

                if (fType == 'check' && !checkbox.is(':checked')) {
                    inputRequired = true;
                     field.addClass('has-error');
                }

                if (fType == 'select' && !selectList.is(':selected')) {
                    inputRequired = true;
                     field.addClass('has-error');
                }

                if (fType == 'multiple' && !selectMultipleList.is(':selected')) {
                    inputRequired = true;
                    field.addClass('has-error');
                }
            });

            // if there are some required field still empty value
            if (inputRequired) {

                tabFieldError.addClass('has-error');

                notification.removeClass('t-hidden');
                setAlert(notification, '<?php echo JText::_('COM_EASYDISCUSS_FIELDS_REQUIRED_FIELDS_NOT_PROVIDED'); ?>', 'error');
                return false;
            }
        }

        return true;
    };


});
