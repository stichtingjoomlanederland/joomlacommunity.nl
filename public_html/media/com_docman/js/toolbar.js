kQuery(function($) {
    var grid   = $('.k-js-grid-controller'),
        controller = grid.data('controller'),
        buttons  = controller.buttons,
        no_action_buttons = $('#toolbar-move, #toolbar-batch, #toolbar-assign, #toolbar-remove');

    if (no_action_buttons.length) {
        buttons.push(no_action_buttons);
    }

    controller.toolbar.find('a.toolbar').ktooltip({
        placement: 'bottom'
    });

    grid.on('koowa:afterValidate', function() {
        var message  = 'You are not authorized to perform the %s action on these items',
            selected = Koowa.Grid.getAllSelected(),
            actions  = {
                'delete': 'core.delete',
                'edit': 'core.edit'
            },
            checkAction = function(action, selected) {
                var result = true;

                if (selected.length === 0) {
                    return false;
                }

                if (!action) {
                    return true;
                }

                selected.each(function() {
                    var permissions = $(this).data('permissions'),
                        joomla_action = actions[action];

                    if (!permissions || result == false) {
                        return;
                    }

                    result = permissions[joomla_action] || true;
                });

                return result;
            };

        buttons.each(function() {
            var button = $(this),
                action = button.data('action');

            /*if (button.hasClass('k-is-unauthorized')) {
                button.addClass('k-is-disabled');
                button.attr('data-original-title', message.replace('%s', action));
                return;
            }*/

            if (checkAction(action, selected) && selected.length > 0) {
                button.removeClass('k-is-disabled');
                button.attr('data-original-title', '');
            } else {
                button.addClass('k-is-disabled');
                if(selected.length > 0) {
                    button.attr('data-original-title', message.replace('%s', action));
                }
            }
        });

        return true;
    });

    grid.trigger('koowa:afterValidate');
});