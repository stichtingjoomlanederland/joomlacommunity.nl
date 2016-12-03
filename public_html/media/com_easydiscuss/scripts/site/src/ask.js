ed.define('site/src/ask', ['edq', 'abstract'], function($, Abstract){

    return new Abstract(function(self){
        return {
            opts: {
                '{submit}': '[data-form-submit]'
            },

            '{submit} click' : function(el) {

                // Disable the submit button if it's already pressed to avoid duplicate clicks.
                if (el.attr('disabled')) {
                    return;
                }

                var errorString = '';
                var isError = false;
                var selectedCategory = $('.discuss-form *[name=category_id]').val();

                if (selectedCategory == 0 || selectedCategory.length == 0) {
                    var msg = $.language('COM_EASYDISCUSS_PLEASE_SELECT_CATEGORY_DESC');
                    errorString += '<li>' + msg + '</li>';

                    isError = true;
                }

                if ($('#ez-title').val() == '' && $('#post-topic-title').val() == '') {
                    var msg = $.language('COM_EASYDISCUSS_POST_TITLE_CANNOT_EMPTY');
                    errorString += '<li>' + msg + '</li>';

                    isError = true;
                }

                // this discuss.getContent is a function from form.new.php
                var dcReplyContent = discuss.getContent();

                if (dcReplyContent == '') {
                    var msg = $.language('COM_EASYDISCUSS_POST_CONTENT_IS_EMPTY');
                    errorString += '<li>' + msg + '</li>';

                    isError = true;
                }

                if (isError) {
                    errorString = '<div class="alert alert-error"><ul class="unstyled">' + errorString + '</ul></div>';
                    $('.ask-notification').html('');
                    $('.ask-notification').append(errorString);

                    $(document).scrollTop($('.ask-notification').offset().top);

                    return false;
                }

                $(this).prop('disabled' , true);

                // Submit the form now.
                $('#dc_submit').submit();
                return false;
            }
        }
    });
});
