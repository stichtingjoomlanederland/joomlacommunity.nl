
/** @namespace Docman */
if (typeof Docman === 'undefined') { //noinspection JSUndeclaredVariable
    Docman = {};
}

(function($) {

var urlParam = function(name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    return results ? results[1] : 0;
};
var itemid = urlParam('Itemid'),
    url_prefix = '?option=com_docman&slug=&'+(itemid ? 'Itemid='+itemid+'&' : '');

$(function() {

    Docman.Scanner = Vue.extend({
        data: function() {
            return {
                hasDocumentContents: false,
                hasPendingScan: false,
                isConnectEnabled: false,
                scannableExtensions: []
            }
        },
        created: function () {
            this.checkDocumentContents();
        },
        computed: Vuex.mapState({
            isIndexable: function() {
                return $.inArray(this.entity.extension, this.scannableExtensions) !== -1;
            },
            isRemote: function() {
                return this.entity.storage_type === 'remote';
            },
            entity: 'entity'
        }),
        methods: {
            checkPendingScan: function() {
                if (!this.entity._isNew && !this.hasDocumentContents) {
                    var vm = this,
                        url = url_prefix+'view=scans&limit=1&format=json&identifier='+this.entity.uuid;

                    $.getJSON(url).then(function(data) {
                        var entity = data.entities[0];

                        if (entity) {
                            vm.hasPendingScan = true;
                        }
                    });
                }
            },
            checkDocumentContents: function() {
                if (!this.entity._isNew) {
                    var vm = this,
                        url = url_prefix+'view=document_contents&limit=1&format=json&id='+this.entity.id;

                    $.getJSON(url).then(function(data) {
                        var entity = data.entities[0];

                        if (entity) {
                            vm.hasDocumentContents = true;
                        }
                    }).then(function() {
                        vm.checkPendingScan();
                    });
                }
            }
        },
        watch: {}
    });

});

})(kQuery);
