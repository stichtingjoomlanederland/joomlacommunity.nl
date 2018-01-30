(function($) {

/** @namespace Docman */
if (typeof Docman === 'undefined') { //noinspection JSUndeclaredVariable
    Docman = {};
}

var urlParam = function(name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    return results ? results[1] : 0;
};
var itemid = urlParam('Itemid'),
    url_prefix = '?option=com_docman&slug=&'+(itemid ? 'Itemid='+itemid+'&' : '');

Docman.AccessBox = Vue.extend({
    data: function() {
        return {
            entity_type: 'document',
            allowed_groups: [],
            default_preset: null,
            current_value: 0,
            selected_groups: null,
            selected_access: 0,
            active: null
        }
    },
    mounted: function() {
        var vm = this,
            access_selector   = $(vm.$el).find('.k-js-access-selector'),
            group_selector    = $(vm.$el).find('.k-js-group-selector'),
            category_selector = $('#docman_category_id');

        // If there is only one option it's auto selected without trigger. So the category value in Vuex will be 0
        if (category_selector.find('option').length === 1) {
            category_selector.trigger('change');
        }

        access_selector.select2({theme: "bootstrap"});

        if (this.current_value == 0 || !this.current_value) {
            this.active = 'inherit';
        } else if (this.current_value > 0) {
            this.active = 'presets';
        }
        else {
            this.active = 'groups';
        }

        vm.selected_access   = access_selector.val();
        vm.selected_groups   = group_selector.val();

        access_selector.on('change', function () {
            vm.selected_access = $(this).val();
        });

        group_selector.on('change', function () {
            vm.selected_groups = $(this).val();
        });
    },
    created: function () {},
    methods: {
        updateAllowedGroups: function() {
            var vm = this;

            if (this.active === 'inherit' && this.selected_category) {
                this.allowed_groups = [
                    '<em>'+Koowa.translate('Calculating')+'</em>'
                ];

                // empty slug is needed so only id is used in frontend
                $.getJSON(url_prefix+'view=category&fields=access&format=json&id='+this.selected_category)
                    .then(function(data) {
                        var entity = data.entities[0];

                        if (entity) {
                            if (entity.access > 0) {
                                var level = Docman.viewlevels[entity.access];

                                return $.Deferred().resolve({entities: [Docman.viewlevels[entity.access]]});
                            }
                            else {
                                return $.getJSON(url_prefix+'view=level&fields=group_list&format=json&id='+Math.abs(entity.access));
                            }
                        }
                    }).then(function(data) {
                        var level = data && data.entities ? data.entities[0] : {};

                        vm.allowed_groups = level && level.group_list ? level.group_list : {};
                    });
            }
            else {
                var level = vm.active === 'inherit' ? vm.default_preset : vm.selected_access;

                vm.allowed_groups = Docman.viewlevels[level] ? Docman.viewlevels[level].group_list : [];
            }
        }
    },
    computed: Vuex.mapState({
        selected_category: function() {
            return this.entity._name === 'document' ? this.entity.docman_category_id : this.entity.parent_id;
        },
        entity: 'entity'
    }),
    watch: {
        selected_access: function() {
            this.updateAllowedGroups();
        },
        selected_category: function() {
            this.updateAllowedGroups();
        },
        selected_groups: function(newVal) {
            if (!newVal) {
                this.active = 'inherit';
            }
        },
        active: function () {
            this.updateAllowedGroups();
        }
    }
});

})(window.kQuery);