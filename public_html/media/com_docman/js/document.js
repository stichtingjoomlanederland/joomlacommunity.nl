
(function($) {

if (typeof Docman === 'undefined') {
    Docman = {};
}

var humanizeFileName = function(name) {
    // strip extension
    name = name.substr(0, name.lastIndexOf('.'));

    // Replace - _ . with space character
    name = name.replace(/[\-_.]/g, ' ');

    // Trim the whitespaces
    name = $.trim(name.replace(/[\s]{2,}/g, ' '));

    // First character uppercase
    name = name.charAt(0).toUpperCase()+name.substr(1);

    return name;
};

$(function($) {
	var form           = $('.k-js-form-controller'),
        controller     = form.data('controller');

    var int = setInterval(function() {
        if (controller.store) {
            clearInterval(int);

            Koowa.EntityStore.createFormBinding(controller.store, 'title', form);

            var entity = controller.store.state.entity,
                setFileAndFolder = function(path) {
                    var properties = {
                        folder: path.substr(0, path.lastIndexOf('/')),
                        file  : path.substr(path.lastIndexOf('/')+1),
                        extension: path.substr(path.lastIndexOf('.')+1).toLowerCase()
                    };

                    if (!entity.title) {
                        properties.title = humanizeFileName(properties.file);
                    }

                    controller.store.commit('setProperty', properties);
                },
                setIcon = function(extension) {
                    var element = $('#params_icon');

                    if (extension && element.val() === 'default') {
                        if (element.val().indexOf('icon:') !== 0) {
                            /** @namespace Docman.icon_map */
                            $.each(Docman.icon_map, function(key, value) {
                                if ($.inArray(extension, value) !== -1) {
                                    element.val(key).trigger('change');
                                }
                            });
                        }
                    }
                };

            setFileAndFolder(entity.storage_path);
            setIcon(entity.extension);

            controller.store.watch(function(state) {
                return state.entity.storage_path;
            }, setFileAndFolder);

            controller.store.watch(function(state) {
                return state.entity.extension;
            }, setIcon);
        }
    }, 100);


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

    
})(kQuery);