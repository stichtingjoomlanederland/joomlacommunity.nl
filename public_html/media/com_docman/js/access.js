(function($) {

/** @namespace Docman */
if (typeof DOCman === 'undefined') { //noinspection JSUndeclaredVariable
    DOCman = {};
}

// TODO translations

DOCman.Usergroups = Koowa.Class.extend({
    active: null,
    previous: null,
    getOptions: function() {
        return {
            category: '#category',
            form:     '.k-js-form-controller',
            entity:   'category'
        };
    },
    initialize: function(element, options) {
        var self = this;

        this.supr();
        this.setOptions(options);

        this.element = element = $(element);

        this.elements = {
            access:  element.find('.access_selector'),
            groups:  element.find('.group_selector'),
            inherit: element.find('input[name="inherit"]'),
            who_can_see: element.find('ul.who-can-see'),
            who_can_see_container: element.find('.who-can-see-container'),
            category: $(self.options.category),
            form: $(self.options.form),
            buttons: {
                all: $('.k-js-access-button', element),
                inherit: $('.k-js-access-inherit', element),
                groups: $('.k-js-access-groups', element),
                presets: $('.k-js-access-presets', element)
            },
            tabs: {
                all: $('.k-js-access-tab', element),
                groups: $('.k-js-access-tab-groups', element),
                presets: $('.k-js-access-tab-presets', element)
            }
        };

        var selected = element.data('selected');

        if (selected == 0) {
            this.switchTo('inherit');
        } else if (selected > 0) {
            this.switchTo('presets');
        }
        else {
            this.switchTo('groups');
        }

        this.elements.category.on('change', $.proxy(self.updateWhoCanSee, self));
        this.elements.access.on('change', $.proxy(self.updateWhoCanSee, self));

        this.elements.groups.on('change', function () {
            if (!$(this).val()) self.switchTo('inherit');
        });

        this.elements.buttons.inherit.on('click', function() {
            self.switchTo('inherit');
        });

        this.elements.buttons.groups.on('click', function() {
            self.switchTo('groups');
        });

        this.elements.buttons.presets.on('click', function() {
            self.switchTo('presets');
        });

        if (this.elements.form) {
            var beforeSend = function() {
                var value  = self.getValue(),
                    form   = $(this);

                if (self.active === 'inherit') {
                    $('<input type="checkbox" name="inherit" value="1" checked />').appendTo(form);
                } else if (typeof value !== 'object') {
                    $('<input type="hidden" name="groups" />').val(value).appendTo(form);
                } else if (value) {
                    $.each(value, function(i, group) {
                        $('<input type="hidden" name="groups[]" />').val(group).appendTo(form);
                    });
                }
            };

            this.elements.form.on('koowa:beforeApply', beforeSend)
                             .on('koowa:beforeSave', beforeSend)
                             .on('koowa:beforeSave2new', beforeSend);
        }
    },
    setGroupsFromList: function(list) {
        var self = this;

        this.elements.who_can_see.empty();

        $.each(list, function(id, title) {
            self.elements.who_can_see.append($('<li>', {html: title}));
        });
    },
    updateWhoCanSee: function() {
        var self = this,
            is_inherited   = self.elements.buttons.inherit.prop('checked'),
            category_id    = self.elements.category.val(),
            inherit_label  = self.elements.buttons.inherit.next('label'),
            inherit_string = Koowa.translate('Use default');

        if (category_id) {
            if (this.options.entity === 'document') {
                inherit_string = Koowa.translate('Inherit from category');
            } else {
                inherit_string = Koowa.translate('Inherit from parent category');
            }
        }

        inherit_label.text(inherit_string);

        if (is_inherited && category_id) {

            self.setGroupsFromList(['<em>'+Koowa.translate('Calculating')+'</em>']);

            var urlParam = function(name) {
                var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
                return results ? results[1] : 0;
            };
            var itemid = urlParam('Itemid'),
                url_prefix = '?option=com_docman&slug=&';

            if (itemid) {
                url_prefix += 'Itemid='+itemid+'&';
            }

            // empty slug is needed so only id is used in frontend
            $.getJSON(url_prefix+'view=category&fields=access&format=json&id='+category_id)
                .then(function(data) {
                    var entity = data.entities[0];

                    if (entity) {
                        if (entity.access > 0) {
                            var level = DOCman.viewlevels[entity.access];

                            return $.Deferred().resolve({entities: [DOCman.viewlevels[entity.access]]});
                        }
                        else {
                            return $.getJSON(url_prefix+'view=level&fields=group_list&format=json&id='+Math.abs(entity.access));
                        }
                    }
                }).then(function(data) {
                var level = data && data.entities ? data.entities[0] : {};

                self.setGroupsFromList(level && level.group_list ? level.group_list : {});
            });
        }
        else {
            var level = is_inherited ? self.element.data('default-id') : self.elements.access.val();

            self.setGroupsFromList(DOCman.viewlevels[level] ? DOCman.viewlevels[level].group_list : []);
        }
    },
    switchTo: function(active, event) {
        this.previous = self.active;
        this.active   = active;

        this.elements.buttons[active].prop('checked', true);

        if (active === 'inherit') {
            this.switchToInherit(event);
        }
        else if (active === 'presets') {
            this.switchToPresets(event);
        }
        else if (active === 'groups') {
            this.switchToGroups(event);
        }
    },
    switchToInherit: function() {
        this.elements.access.prop('disabled', true);
        this.elements.groups.prop('disabled', true);

        this.elements.who_can_see_container.css('display', 'block');

        this.updateWhoCanSee();

        this.elements.tabs.all.hide();
    },
    switchToGroups: function(event) {
        this.elements.access.prop('disabled', true);
        this.elements.groups.prop('disabled', false);

        this.elements.who_can_see_container.css('display', 'none');

        this.updateWhoCanSee();

        this.elements.tabs.all.hide();
        this.elements.tabs.groups.show();
    },
    switchToPresets: function() {
        this.elements.access.prop('disabled', false);
        this.elements.groups.prop('disabled', true);

        this.elements.who_can_see_container.css('display', 'block');

        this.updateWhoCanSee();

        this.elements.tabs.all.hide();
        this.elements.tabs.presets.show();
    },
    getValue: function() {
        var value = null;

        if (this.active === 'inherit') {
            value = 0;
        }
        else if (this.active === 'groups') {
            value = this.elements.groups.val();
        }
        else if (this.active === 'presets') {
            value = this.elements.access.val();
        }

        return value;
    }
});

})(window.kQuery);