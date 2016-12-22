(function($) {

/**
 * Adds file overwrite support to uploader
 */
$.widget('koowa.uploader', $.koowa.uploader, {
    _init: function() {
        var result = this._super();

        if (this.options.check_duplicates) {
            this._on({
                'uploader:beforestart': this._handleBeforeStart
            });
        }

        return result;
    },
    _handleBeforeStart: function(event, data) {
        var names = [],
            uploader = data.uploader,
            self = this;

        $.each(uploader.files, function (i, file) {
            if (file.loaded == 0 && file.status != plupload.DONE) {
                names.push(file.name);
            }
        });

        var url = uploader.settings.url;

        url = updateUrlParameter(url, 'view', 'files');
        url = updateUrlParameter(url, 'format', 'json');
        url = updateUrlParameter(url, 'limit', '100');
        url = updateUrlParameter(url, 'folder', uploader.settings.multipart_params.folder);

        if (names.length) {
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _method: 'GET',
                    name: names
                }
            }).done(function(response) {
                checkDuplicates(response, uploader, self.options);
            }).fail(function() {
                uploader.start();
            });
        }


        event.preventDefault();
    }
});

function updateUrlParameter(uri, key, value) {
    // remove the hash part before operating on the uri
    var i = uri.indexOf('#');
    var hash = i === -1 ? ''  : uri.substr(i);
    uri = i === -1 ? uri : uri.substr(0, i);

    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
        uri = uri.replace(re, '$1' + key + "=" + value + '$2');
    } else {
        uri = uri + separator + key + "=" + value;
    }
    return uri + hash;  // finally append the hash as well
}

function getUniqueName(name, fileExists) {
    // Get a unique file name by appending (1) (2) etc.
    var i = 1,
        extension = name.substr(name.lastIndexOf('.') + 1),
        base = name.substr(0, name.lastIndexOf('.'));

    while (true) {
        name = base + ' (' + i + ').' + extension;

        if (!fileExists(name)) {
            break;
        }

        i++;
    }

    return name;
}

function getNamesFromArray(array) {
    var results = [];
    $.each(array, function (i, entity) {
        results.push(entity.name);
    });

    return results;
}


function getConfirmationMessage(files) {
    var message = '';

    if (files.length === 1) {
        message = Koowa.translate('A file with the same name already exists. Would you like to overwrite it?');
    } else if (files.length > 1) {
        message = Koowa.translate('Following files already exist. Would you like to overwrite them? {names}', {
            names: "\n" + files.join("\n")
        });
    }

    return message;
}


function makeUnique(file, similar, uploader) {
    var names = [];
    if (typeof similar.entities === 'object' && similar.entities.length) {
        names = getNamesFromArray(similar.entities);
    }
    $.each(uploader.files, function (i, f) {
        if (f.id !== file.id) {
            names.push(f.name);
        }
    });

    file.name = getUniqueName(file.name, function (name) {
        return $.inArray(name, names) !== -1;
    });

    $('#' + file.id).find('span.js-file-name-container').text(file.name);
}


function checkDuplicates(response, uploader, options) {
    uploader.settings.multipart_params.overwrite = 0;

    if (typeof response.entities === 'object' && response.entities.length) {
        var existing = getNamesFromArray(response.entities),
            promises = [],
            mode = typeof options.duplicate_mode === 'undefined' ? 'confirm' : options.duplicate_mode;

        if (mode === 'confirm' && confirm(getConfirmationMessage(existing))) {
            uploader.settings.multipart_params.overwrite = 1;

            return uploader.start();
        }

        $.each(uploader.files, function (i, file) {

            if ($.inArray(file.name, existing) !== -1) {
                var url = uploader.settings.url,
                    promise;

                url = updateUrlParameter(url, 'view', 'files');
                url = updateUrlParameter(url, 'format', 'json');
                url = updateUrlParameter(url, 'limit', '100');
                url = updateUrlParameter(url, 'folder', uploader.settings.multipart_params.folder);
                url = updateUrlParameter(url, 'search', file.name.substr(0, file.name.lastIndexOf('.')) + ' (');

                promise = $.ajax({
                    type: 'GET',
                    url: url
                }).done(function (response) {
                    return makeUnique(file, response, uploader)
                });

                promises.push(promise);
            }
        });

        if (promises) {
            $.when.apply(kQuery, promises).then(function () {
                uploader.start();
            });
        }
    }
    else {
        uploader.start();
    }
}

})(kQuery);