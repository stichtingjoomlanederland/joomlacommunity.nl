kQuery(function($) {

    new Docman.GroupDialog({
        view: '.js-usergroup-modal-assign',
        button: '.js-usergroup-action',
        group_selector: '.js-usergroup-groups',
        open_button: '#toolbar-assign',
        group_field: 'assign_group'
    });

    new Docman.GroupDialog({
        view: '.js-usergroup-modal-remove',
        button: '.js-usergroup-action',
        group_selector: '.js-usergroup-groups',
        open_button: '#toolbar-remove',
        group_field: 'remove_group'
    });
});

var Docman = Docman || {};

(function($) {

Docman.GroupDialog = Koowa.Class.extend({

    target_group: null,

    initialize: function(options) {
        this.supr();

        options = {
            group_field: options.group_field || 'groups',
            view: $(options.view),
            group_selector: $(options.group_selector, options.view),
            button: $(options.button, options.view),
            open_button: $(options.open_button)
        };

        this.setOptions(options);
        this.attachEvents();
    },
    attachEvents: function() {

        var self = this;

        if (this.options.open_button) {
            $(this.options.open_button).click(function(event) {
                event.preventDefault();
                self.show();
            });
        }

        if (this.options.view.find('form')) {
            this.options.view.find('form').submit(function(event) {
                event.preventDefault();
                self.submit();
            });
        }

        if (this.options.group_selector) {
            this.options.group_selector.on('change', function(e) {
                self.options.button.prop('disabled', !$(this).val());
            });
        }
    },
    show: function() {

        var options = this.options,
        count = Koowa.Grid.getAllSelected().length;

        if (options.open_button.hasClass('k-is-unauthorized') || !count) {
            return;
        }

        $.magnificPopup.open({
            items: {
                src: $(options.view),
                type: 'inline'
            }
        });
    },
    hide: function() {
        $.magnificPopup.close();
    },
    submit: function() {

        var controller = $('.k-js-grid-controller').data('controller'),
            selected = this.options.group_selector.val(),
            field    = this.options.group_field,
            context = {};

        if (selected && Koowa.Grid.getAllSelected().length) {
            context.validate = true;
            context.data = {};
            context.data[field] = selected;
            context.data[controller.token_name] = controller.token_value;
            context.action = 'edit';
            controller.trigger('execute', [context]);
        }
    }
  });

})(window.kQuery);
