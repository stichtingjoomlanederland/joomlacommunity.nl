
(function($) {
    
if (typeof $.validator !== 'undefined') {
    $.validator.addMethod("storage", function(value, element) {
        var storage_type = $('.js-document-path').data('current'),
            type = kQuery(element).data('type');

        if (storage_type === type) {
            return value;
        } else {
            return true;
        }
    }, kQuery.validator.messages.required);

    $.validator.addMethod("streamwrapper", function(value, element) {
        var storage_type = $('.js-document-path').data('current');

        if (storage_type === 'remote') {
            var streams = $(element).data('streams'),
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
        }
        return true;
    });

    $.validator.addMethod("scheme", function(value) {
        var storage_type = $('.js-document-path').data('current');

        if (storage_type === 'remote') {
            return value.match(/^([a-zA-Z0-9\-]+):/);
        }

        return true;
    });

    $.validator.addMethod("upload", function(value) {
        var storage_type = $('.js-document-path').data('current');

        if (storage_type === 'file') {
            var uploader = $('.docman-uploader').uploader('instance').getUploader();

            if (uploader.state === plupload.STARTED) {
                return false;
            }
        }

        return true;
    }, Koowa.translate('Please wait for the upload to finish before saving the document'));
}

if (typeof Docman === 'undefined') {
    Docman = {};
}

$(function($) {
    $.validator.messages.scheme = Koowa.translate('Your link should either start with http:// or another protocol');
    $.validator.messages.streamwrapper = Koowa.translate('Invalid remote link. This link type is not supported by your server.');

    $.validator.setDefaults({
        ignore: ''
    });

	var path_container = $('.js-document-path'),
        form           = path_container.closest('form'),
        storage_type   = path_container.data('current'),
        storage_path_file    = $('#storage_path_file'),
        interval_cache   = null,
        interval_count   = 0,
        thumbnail_cache  = {},
        updateThumbnail = function(data){
            var thumbnail = form.data('docman:thumbnail');
            if (typeof thumbnail === 'object') {
                thumbnail.setSource(data);
            } else {
                // Object does not exist yet. Try every 100ms for 2 seconds
                $.extend(true, thumbnail_cache, data);

                if (interval_cache) { // Clear the previous interval call for the new call
                    clearInterval(interval_cache);
                }

                interval_cache = setInterval(function() {
                    var thumbnail = form.data('docman:thumbnail');
                    if (typeof thumbnail === 'object') {
                        thumbnail.setSource(thumbnail_cache);

                        clearInterval(interval_cache);
                        thumbnail_cache = {};
                    }

                    if (interval_count > 20) {
                        clearInterval(interval_cache);
                    }
                    interval_count++;
                }, 100);
            }
        },
        getStorageType = function() {
            return path_container.data('current');
        },
        setStorageType = function(value) {
            path_container.data('current', value);

            storage_type = value;

            if (value === 'remote') {
                path_container.find('.js-document-type-file').hide();
                path_container.find('.js-document-type-remote').show();
            } else {
                path_container.find('.js-document-type-remote').hide();
                path_container.find('.js-document-type-file').show();
            }

            updateThumbnail({
                automatic: {
                    storage: value === 'file' ? 'local' : 'remote'
                }
            });
        },
        setIconFromExtension = function(extension) {
            var element = $('#params_icon');
            if (element.val().indexOf('icon:') !== 0) {
                /** @namespace Docman.icon_map */
                $.each(Docman.icon_map, function(key, value) {
                    if ($.inArray(extension, value) !== -1) {
                        element.val(key).trigger('change');
                    }
                });
            }
        };

    path_container.find('a[data-switch]').click(function(event) {
        event.preventDefault();

        var $this = $(this),
            value = $this.data('switch');

        setStorageType(value);
    });

    // Set the initial values
    setStorageType(getStorageType());

    // Check if file can have automatic thumbnails based on file extension
    // Check if file type has an icon we can use

    storage_path_file.on('change', function() {
        var path = $(this).val(),
            extension = path.substr(path.lastIndexOf('.')+1).toLowerCase();

        var folder = path.substr(0, path.lastIndexOf('/'));
        var file   = path.substr(path.lastIndexOf('/')+1);
        var file_url = '?'+$.param({option: 'com_docman', view: 'file', routed: 1, container: 'docman-files', folder: folder, name: file});

        updateThumbnail({
            file_url: file_url,
            automatic: {
                'default': storage_path_file[0].defaultValue,
                source: path,
                storage: 'local',
                preview: file_url,
                extension: extension
            }
        });

        setIconFromExtension(extension);
    });

    // Sets default value if defined
    if(storage_path_file.val()) {
        var path = storage_path_file.val(),
            extension = path.substr(path.lastIndexOf('.')+1).toLowerCase(),
            folder = path.substr(0, path.lastIndexOf('/'));
            file   = path.substr(path.lastIndexOf('/')+1);
            state = {
                file_url: '?'+$.param({option: 'com_docman', view: 'file', routed: 1, container: 'docman-files', folder: folder, name: file}),
                automatic: {
                    source: path,
                    storage: 'local',
                    preview: $('#image').val(),
                    extension: extension
                }
            };

        if (state.automatic.preview.indexOf('generated/') !== 0) {
            state.automatic.preview = state.file_url;
        }

        if ($('#params_icon').val() === 'default' && window.location.href.match('&storage_path=')) {
            setTimeout(function() {
                setIconFromExtension(extension);
            }, 200);
        }

        if (!$('docman_form_title').val() && window.location.href.match('&storage_path=')) {
            Docman.onSelectFile(path);
        }

        if (path && typeof docmanUploadFolder !== 'undefined') {
            Docman.onSelectFile(path);
        }

        updateThumbnail(state);
    }

    $('.docman-uploader').on('uploader:uploaded', function(event, data) {
        if (data.file && data.file.status === plupload.DONE) {
            var uploader = $(this).uploader('instance'),
                folder = uploader.options.multipart_params.folder,
                name   = data.file.name,
                path   = ((folder && folder !== '') ? folder+'/' : '')+name;

            Docman.onSelectFile(path);
            //storage_path_file.val(path);
        }
    }).on('uploader:create', function() {
        var button = $('.js-more-button');

        if (button.data('enabled')) {
            button.show();
            $('.k-upload__content').after(button);
        }
    });


    Docman.setSelectedFolder = function(folder) {
        if (folder) {
            var uploader = $('.docman-uploader').uploader('instance');
            uploader.options.multipart_params.folder = folder;
        }
    };

    Docman.setSelectedFile = function(name, size) {
        if (name && name !== '') {
            var uploader = $('.docman-uploader').uploader('instance');

            var tmp_file     = new plupload.File({
                'name': name,
                'size' : size || 0
            });

            tmp_file.loaded = size || 0;
            tmp_file.status = plupload.DONE;
            tmp_file.percent = 100;
            tmp_file.destroy = function() {};

            uploader.getUploader().addFile(tmp_file);
        }
    };

    // Send the correct storage_path value on save
    var evt = function(event) {
        var type  = getStorageType(),
            value = $('#storage_path_'+type).val();

        $('<input type="hidden" name="storage_type" />').val(type).appendTo($(this));
        $('<input type="hidden" name="storage_path" />').val(value).appendTo($(this));
    };

	$('.k-js-form-controller')
		.on('koowa:beforeApply', evt)
		.on('koowa:beforeSave', evt)
		.on('koowa:beforeSave2new', evt);

    // Make hits editable hits-container
    var hits_container = $('#hits-container');
    hits_container.on('click', 'a', function(e) {
        e.preventDefault();
        hits_container.find('span').text('0');

        $('<input type="hidden" class="required" size="25" name="hits" maxlength="11" />')
            .val(0)
            .appendTo(hits_container);
        $(this).remove();
    });

    // Open advanced permissions in a modal
    $('#advanced-permissions-toggle').on('click', function(e){
        e.preventDefault();

        $.magnificPopup.open({
            items: {
                src: $('#advanced-permissions'),
                type: 'inline'
            }
        });
    });
});

var humanizeFileName = function(name) {
    // strip extension
    name = name.substr(0, name.lastIndexOf('.'));

    // Replace - _ . with space character
    name = name.replace(/[\-_\.]/g, ' ');

    // Trim the whitespaces
    name = $.trim(name.replace(/[\s]{2,}/g, ' '));

    // First character uppercase
    name = name.charAt(0).toUpperCase()+name.substr(1);

    return name;
};

// Callback to the file selector
Docman.onSelectFile = function(selected) {
    var title              = $('#docman_form_title'),
        storage_path_file  = $('#storage_path_file'),
        link   = $('.js-more-button').find('a'),
        href   = link.attr('href'),
        folder = selected.substr(0, selected.lastIndexOf('/')),
        file   = selected.substr(selected.lastIndexOf('/')+1);

    storage_path_file.val(selected).trigger('change');

    if (folder) {
        Docman.setSelectedFolder(folder);
    }

    if (file && file !== '') {
        Docman.setSelectedFile(file);
    }

    if (typeof docmanUploadFolder !== 'undefined' && docmanUploadFolder) {
        if (folder.indexOf(docmanUploadFolder) === 0) {
            folder = folder.replace(docmanUploadFolder, '');

            if (folder.indexOf('/') === 0) {
                folder = folder.substr(1);
            }
        }
    }

    href = href.replace(/\&folder=(.*?)&/i, '&folder='+folder+'&');
    href = href.replace(/\&file=(.*?)&/i, '&file='+file+'&');

    link.attr('href', href);

    if (!title.val()) {
        title.val(humanizeFileName(file));
    }

    if (typeof $.magnificPopup !== 'undefined' && $.magnificPopup.instance) {
        $.magnificPopup.close();
    }
};
    
})(kQuery);