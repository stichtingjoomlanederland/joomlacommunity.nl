
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
        storage_path_file  = $('#storage_path_file'),
        uploader_el = $('.docman-uploader')
        ;

    var controller = $('.k-js-form-controller').data('controller');
    controller.implement({
        _actionSave: function(context) {
            if (context.validate && !this.trigger('validate', [context])) {
                return false;
            }

            if (typeof Joomla !== 'undefined' && typeof Joomla.editors !== 'undefined'
                && typeof Joomla.editors.instances !== 'undefined'
                && typeof Joomla.editors.instances['description'] !== 'undefined') {
                var editor = Joomla.editors.instances['description'];
                if (typeof editor.onSave === 'function') {
                    editor.onSave();
                }

                if (typeof editor.save === 'function') {
                    editor.save();
                }

                var value = '';

                if (typeof editor.getValue === 'function') {
                    value = editor.getValue();
                } else {
                    value = $('#description').val();
                }

                this.form.append($('<input/>', {name: 'description', type: 'hidden',
                    value: value
                }));

                // turn off you have unsaved changes feature of TinyMCE
                window.onbeforeunload = null;
            }

            this.form.append($('<input/>', {name: '_action', type: 'hidden', value: context.action}));

            var uploader = uploader_el.uploader('instance'),
                params = this.form.serializeArray(),
                action = this.form.attr('action');

            uploader.options.url = action+(action.indexOf('?') === -1 ? '?' : '&')+'format=json';
            uploader.uploader.bind('FileUploaded', function(up, file, result) {
                if (result.status === 201 && typeof result.response === 'object') {
                    window.location = result.response.redirect;
                }
            });

            $.each(params, function(i, item) {
                uploader.options.multipart_params[item.name] = item.value;
            });

            uploader_el.uploader('start');
        }
    });

    uploader_el.on('uploader:selected', function(event, data) {
        var file = data.files[0];

        if (!title_field.val()) {
            title_field.val(humanizeFileName(file.name));
        }

        storage_path_file.val(file.name);
    });
});