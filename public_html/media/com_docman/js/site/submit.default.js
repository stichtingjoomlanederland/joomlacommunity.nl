
if (typeof kQuery.validator !== 'undefined') {
    kQuery.validator.addMethod("storage", function(value, element) {
        var storage_type = kQuery('.current-storage-type').data('type'),
            type = kQuery(element).data('type');

        if (storage_type === type) {
            return value;
        } else {
            return true;
        }
    }, kQuery.validator.messages.required);

    kQuery.validator.addMethod("streamwrapper", function(value, element) {
        var streams = kQuery(element).data('streams'),
            scheme = null,
            matches = value.match(/^([a-zA-Z0-9\-]+):/);

        if (matches) {
            scheme = matches[1];
        }

        // If scheme is in the array it will not be redirected by browser
        // Therefore we need to check if it's enabled
        if (scheme && typeof streams[scheme] !== 'undefined') {
            return streams[scheme];
        }

        return true;
    });

    kQuery.validator.addMethod("scheme", function(value, element) {
        return value.match(/^([a-zA-Z0-9\-]+):/);
    });
}

kQuery(function($) {
    $.validator.messages.scheme = Koowa.translate('Your link should either start with http:// or another protocol');
    $.validator.messages.streamwrapper = Koowa.translate('Invalid remote link. This link type is not supported by your server.');

    $('a.upload-method').click(function(e) {
        e.preventDefault();

        var $this = $(this);

        $this.parent().parent().find('li').removeClass('active');
        $this.parent().addClass('active');

        var type = $this.data('type');

        $('#storage_type').val(type);
        $('.upload-method-box').css('display', 'none');
        $('#document-'+type+'-path-row').css('display', 'block');
    });

    $('.upload-method-box').css('display', 'none');
    $('a.upload-method').first().trigger('click');

    var humanizeFileName = function(name) {
        // strip extension
        name = name.substr(0, name.lastIndexOf('.'));

        // Replace - _ . with space character
        name = name.replace(/[\-_\.]/g, ' ');

        // Trim the whitespaces
        name = kQuery.trim(name.replace(/[\s]{2,}/g, ' '));

        // First character uppercase
        name = name.charAt(0).toUpperCase()+name.substr(1);

        return name;
    };

    var title_field = $('#title_field'),
        overridable_value = title_field.val();
    $('input.input-file').change(function() {
        var title = title_field.val();

        if (!title || overridable_value == title) {
            var file = $(this).val().replace("C:\\fakepath\\", "");

            if (file) {
                overridable_value = humanizeFileName(file);
                title_field.val(overridable_value);
            }
        }
    });
});