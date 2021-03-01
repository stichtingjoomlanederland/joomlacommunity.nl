
kQuery.validator.addMethod("storagepath", function(value) {
    return /^[0-9A-Za-z:_\-\/\\\.]+$/.test(value);
}, Koowa.translate('Folder names can only contain letters, numbers, dash, underscore or colons'));

kQuery(function($) {
    var extensions = $('#allowed_extensions'),
        tag_list = extensions.tagsInput({
            removeWithBackspace: false,
            width: '100%',
            height: '100%',
            animate: true,
            defaultText: Koowa.translate('Add another extension...')
        }),
        filetypes = extensions.data('filetypes'),
        labels = {
            'audio': Koowa.translate('Audio files'),
            'archive': Koowa.translate('Archive files'),
            'document': Koowa.translate('Documents'),
            'image': Koowa.translate('Images'),
            'video': Koowa.translate('Video files')
        },
        list = $('.k-js-extension-groups'),
        group = $('.k-js-extension-preset');

    $.each(filetypes, function(key, value) {
        var label = labels[key],
            el = $(group).clone();

        el.find('.k-js-extension-preset-label').text(label);
        el.find('button').data('extensions', value);
        list.append(el);
        el.show();
    });

    list.on('click', 'button', function(e) {
        e.preventDefault();

        var el = $(this),
            method = (el.hasClass('k-js-add') ? 'addTag' : 'removeTag'),
            extensions = el.data('extensions');

        $.each(extensions, function(i, extension) {
            tag_list[method](extension, {unique: true, mark_input: false});
        });
    });

    var evt = function() {
        var value = $('#maximum_size').val()*1048576;

        $('<input type="hidden" name="maximum_size" />').val(value).appendTo($('.k-js-form-controller'));
    };

    $('.k-js-form-controller').on('k:beforeApply', evt).on('k:beforeSave', evt);

    $('#advanced-permissions-toggle').on('click', function(e){
        e.preventDefault();

        $.magnificPopup.open({
            items: {
                src: $('#advanced-permissions'),
                type: 'inline'
            }
        });
    });

    $('.edit_document_path').click(function(event) {
        var $this = $(this);

        event.preventDefault();

        $this.parent().siblings('input').prop('disabled', false);
        $this.parents('.k-input-group').removeClass('k-input-group');
        $this.remove();
    });

    var checkbox      = $('.file_size_checkbox'),
        max_size      = $('#maximum_size'),
        last_value    = null,
        checkboxEvent = function() {
            var checked = checkbox.find('input').prop('checked');
            max_size.prop('disabled', checked);

            if (checked) {
                last_value = max_size.val();
                max_size.val('');
            } else if (last_value) {
                max_size.val(last_value);
            }
        };

    checkbox.change(checkboxEvent);
    checkboxEvent();
});

kQuery(function ($) {
    var csrf_token = document.querySelector('input[name="csrf_token"]').getAttribute('value');
    var buttons = {
        '.k-js-refresh-license': {
            tooltip: Koowa.translate('Refreshing license…'),
            payload: {
                _action: 'refresh_license',
                csrf_token: csrf_token
            },
            error: 'Could not refresh support license',
            done: () => {
                window.location.reload(true);
            }
        }
    }

    for (let [button, data] of Object.entries(buttons)) {

        let actionButton = $(button);
        let spinner = $('<span class="k-loader" style="display: none">Loading…</span>');

        actionButton.append(spinner);

        actionButton.ktooltip({
            title: data.tooltip,
            placement: 'bottom',
            delay: {show: 200, hide: 50},
            trigger: 'manual',
            container: '.k-ui-namespace'
        });

        actionButton.click(function (event) {
            event.preventDefault();

            $.ajax({
                method: 'post',
                url: $('.k-js-form-controller').attr('action'),
                dataType: 'json',
                data: data.payload,
                beforeSend: function () {
                    actionButton.addClass('k-is-disabled disabled').ktooltip('show');
                    spinner.css('display', '');
                }
            }).fail(function() {
                alert(data.error);
            }).done(function() {
                data.done ? data.done() : null;
            }).always(function () {
                actionButton.append('<span class="k-icon-check" style="color: green" aria-hidden="true"></span>');
                actionButton.removeClass('k-is-disabled disabled').ktooltip('hide');
                spinner.css('display', 'none');
                setTimeout(function () {
                    var check = actionButton.children('.k-icon-check');
                    check.hide(function() {
                        check.remove();
                    });
                }, 2000);
            });
        })
    }
});