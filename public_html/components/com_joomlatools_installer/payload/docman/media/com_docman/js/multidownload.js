"use strict";

kQuery(function($) {
    var csrf_token = Docman.token;
    var item_checkbox_selector = '.k-js-item-select';
    var item_row_selector = '.docman_item';
    var download_button_selector = '.k-js-multi-download';
    var download_button = $(download_button_selector);
    var download_spinner = $('<span class="k-loader" style="display: none">Loading...</span>');

    // if there are multiple buttons for the grid such as delete
    var has_delete_button = kQuery('.k-js-toolbar').find('[id="toolbar-delete"]').length === 1;

    $(item_checkbox_selector).each(function(i, checkbox) {
        var $checkbox = $(checkbox);

        if ($checkbox.data('storageType') !== 'file') {
            $checkbox.parent().ktooltip({
                title: Koowa.translate('Remote files cannot be downloaded in batch'),
                placement: 'right',
                delay: {show: 200, hide: 50},
                container: '.k-ui-namespace'
            });

            if (!has_delete_button) {
                $checkbox.prop('disabled', true);
            }
        } else if (!$checkbox.data('canDownload')) {
            $checkbox.parent().ktooltip({
                title: Koowa.translate('You are not authorized to download the selected file'),
                placement: 'right',
                delay: {show: 200, hide: 50},
                container: '.k-ui-namespace'
            });

            if (!has_delete_button) {
                $checkbox.prop('disabled', true);
            }
        }

    });


    download_button.ktooltip({
        title: Koowa.translate('Preparing download'),
        placement: 'bottom',
        delay: {show: 200, hide: 50},
        trigger: 'manual',
        container: '.k-ui-namespace'
    });

    var startSpinner = function () {
        download_button.ktooltip('show');
        download_spinner.css('display', '');
    };
    var stopSpinner = function () {
        download_button.ktooltip('hide');
        download_spinner.css('display', 'none');
    };

    var enableButton = function() {
        download_button.removeClass('k-is-disabled disabled');
    };

    var disableButton = function () {
        download_button.addClass('k-is-disabled disabled');
    };
    var setButtonStatus = function(){
        var checked = $(item_checkbox_selector + ':checked');
        if(checked.length) {
            var count = 0;
            $.each(checked, function (index, checkbox) {
                var $checkbox = $(checkbox);

                if ($checkbox.data('canDownload') && $checkbox.data('storageType') === 'file') {
                    count++;
                }
            });

            if (count) {
                enableButton();
            } else {
                disableButton();
            }

        } else {
            disableButton();
        }
    };

    var isButtonEnabled = function () {
        return !download_button.hasClass('k-is-disabled');
    };

    download_button.append(download_spinner);
    download_button.css('');

    setButtonStatus();

    $('body').on('click', item_checkbox_selector, setButtonStatus)
        .on('click', item_row_selector, setButtonStatus);

    download_button.on('click', function (event) {
        event.preventDefault();

        if (!isButtonEnabled()) {
            return;
        }

        var items = $(item_checkbox_selector + ':checked');

        if (items.length) {

            var ids = [];

            $.each(items, function (index, checkbox) {
                var $checkbox = $(checkbox);

                if ($checkbox.data('canDownload')) {
                    ids.push($checkbox.data('id'));
                }
            });

            if (ids) {
                $.ajax({
                    method: 'post',
                    url: '?view=download&'+$.param({id: ids}),
                    dataType: 'json',
                    data: {
                        _action: 'compress',
                        csrf_token: csrf_token
                    },
                    beforeSend: function () {

                        startSpinner();
                        disableButton();
                    }
                }).done(function(response) {
                    if (typeof response === 'object' && response.archive) {

                        var location = window.location.href.replace(window.location, '');
                        var concat   = location.indexOf('?') !== -1 ? '&' : '?';
                        window.location.href = location+concat+$.param({view: 'download', archive: response.archive});

                        enableButton();
                        stopSpinner();
                    }
                }).fail(function () {
                    enableButton();
                    stopSpinner();
                });
            }

        }
    });
});