jQuery(document).ready(function ($) {
    $.setAutomationReload = function () {
        $('[acym-automation-reload]').each(function () {
            $(this).on('change', function () {
                let selectReload = $(this);
                setTimeout(function () {
                    let params = selectReload.attr('acym-automation-reload');
                    let decodedParams = JSON.parse(params);

                    if (!acym_helper.empty(decodedParams['plugin'])) {
                        decodedParams = [decodedParams];
                    }

                    decodedParams.forEach(function (oneDecodedParams) {
                        let postParams = {
                            'ctrl': 'dynamics',
                            'task': 'trigger',
                            'plugin': oneDecodedParams['plugin'],
                            'trigger': oneDecodedParams['trigger']
                        };

                        if (oneDecodedParams['name']) {
                            postParams['name'] = oneDecodedParams['name'];
                            postParams['value'] = $('[name="' + oneDecodedParams['name'] + '"]').val();
                        }

                        if (oneDecodedParams['params']) {
                            for (let [identifier, name] of Object.entries(oneDecodedParams['params'])) {
                                postParams[identifier] = name;
                            }
                        }

                        if (oneDecodedParams['paramFields']) {
                            for (let [identifier, name] of Object.entries(oneDecodedParams['paramFields'])) {
                                postParams[identifier] = $('[name="' + name + '"]').val();
                            }
                        }

                        $.ajax({
                            type: 'POST',
                            url: ACYM_AJAX_URL,
                            data: postParams,
                            success: function (result) {
                                let $container = $(oneDecodedParams['change']);
                                $container.html(result);

                                acym_helperSelect2.setSelect2();
                                acym_helperSelect2.setAjaxSelect2();
                                acym_helperTooltip.setTooltip();
                                $container
                                    .closest('.acym__automation__one__filter.acym__automation__one__filter__classic')
                                    .find(
                                        '.acym__automation__inserted__filter input, .acym__automation__inserted__filter textarea, .acym__automation__inserted__filter select')
                                    .on('change', function () {
                                        $.reloadCounters($container);
                                    });
                            }
                        });
                    });
                }, 100);
            });
        });
    };
});
