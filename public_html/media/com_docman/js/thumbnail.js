/**
 * Thumbnail selector
 *
 * @copyright	Copyright (C) 2007 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @requires    Koowa, Koowa.Class
 */



(function($){

    var imageEditor = null;

    this.ThumbnailBox = Vue.extend({
        data: function() {
            return {
                editor: {
                    site: null,
                },
                automatic: {
                    exists: false,
                    enabled: true,
                    path: '',
                    extensions: []
                },
                download_in_progress: false,
                download_in_progress_error: false,
                hasConnectSupport: false,
                isAdmin: false,
                connect_token: null,
                csrf_token: null,
                image_container: null,
                image_folder: null,
                web_preview: null,
                active: null,
                previous_active: null,
                cache: {
                    storage_path: null,
                    storage_type: null,
                    image: null
                },
                links: {
                    web: null,
                    custom: null,
                    save_web_image: null,
                    preview_automatic_image: null
                }
            }
        },
        mounted: function() {
            var vm = this;

            imageEditor = Docman.ImageEditorFactory.getEditor(null, {
                'connectToken': this.connect_token,
                'site': this.editor.site,
                'onSaveImage': function() {
                    imageEditor.forceReloadImage(vm.preview_url);
                }
            });

            this.cache.image  = this.entity.image;
            this.cache.storage_path = this.entity.storage_path;
            this.cache.storage_type = this.entity.storage_type;

            window.onSelectImage = function(selected) {
                if (selected.substr(0, 4) === 'web_') {
                    vm.active = 'web';
                } else {
                    vm.active = 'custom';
                }

                vm.$store.commit('setProperty', {image: selected});

                if (typeof $.magnificPopup !== 'undefined' && $.magnificPopup.instance) {
                    $.magnificPopup.close();
                }
            };

            if (this.automatic.enabled && this.entity.image == this.automatic.path) {
                this.active = 'automatic';
            }
            else if (this.entity.image && this.entity.image !== '') {
                if (this.entity.image.indexOf('web_') === 0) {
                    this.active = 'web';
                } else {
                    this.active = 'custom';
                }
            } else {
                this.active = 'none';
            }

            if (vm.hasConnectSupport) {
                window.addEventListener('message', function(event) {
                    if (event.origin.match(/^https:\/\/(.*?)\.joomlatools.(?:com|xyz)$/)) {
                        if (!event.data.urls) {
                            return;
                        }

                        if (typeof $.magnificPopup !== 'undefined' && $.magnificPopup.instance) {
                            $.magnificPopup.close();
                        }

                        vm.web_preview = event.data.urls.thumb;

                        vm.download_in_progress = true;

                        $('.k-js-form-controller').on('k:validate', function(args) {
                            if (vm.download_in_progress) {
                                vm.download_in_progress_error = true;

                                return false;
                            }

                            vm.download_in_progress_error = false;
                        });

                        $.ajax({
                            url: vm.links.save_web_image+'&container='+vm.image_container,
                            method: 'POST',
                            timeout: 20000,
                            data: {
                                'csrf_token': vm.csrf_token,
                                'overwrite': 1,
                                'name': 'web_'+event.data.id+'.jpg',
                                'file': event.data.urls.regular
                            }
                        }).done(function(response) {
                            vm.download_in_progress = false;
                            vm.download_in_progress_error = false;

                            if (typeof response == 'object' && typeof response.entities == 'object' && response.entities.length == 1) {
                                var entity = response.entities[0];

                                vm.$store.commit('setProperty', {image: entity.path});
                            }

                            vm.web_preview = false;
                        }).fail(function() {
                            vm.download_in_progress = false;
                            vm.download_in_progress_error = false;

                            vm.web_preview = false;
                            vm.$store.commit('setProperty', {image: ''});
                        });
                    }
                });
            }

        },
        created: function () {},
        methods: {
            editImage: function() {
                if (this.hasConnectSupport && this.entity.image) {
                    imageEditor.loadImage(this.image_container+'://'+this.entity.image);
                }
            },
            changeCustom: function() {
                var vm = this;

                $.magnificPopup.open({
                    items: {
                        src: vm.links.custom+'&callback=onSelectImage&container='+vm.image_container,
                        type: 'iframe'
                    },
                    mainClass: 'koowa_dialog_modal'
                });
            },
            openPicker: function() {
                var vm = this;

                $.magnificPopup.open({
                    items: {
                        src: vm.links.web+'?token='+vm.connect_token,
                        type: 'iframe'
                    },
                    mainClass: 'koowa_dialog_modal'
                });
            }
        },
        computed: Vuex.mapState({
            selected_file: function() {
                return this.entity.storage_path.substr(this.entity.storage_path.lastIndexOf('/')+1);
            },
            selected_folder: function() {
                return this.entity.storage_path.substr(0, this.entity.storage_path.lastIndexOf('/'));
            },
            selected_extension: function() {
                return this.entity.storage_path.substr(this.entity.storage_path.lastIndexOf('.')+1).toLowerCase();
            },
            preview_url: function() {
                var result;

                if (this.active === 'none') {
                    result = false;
                }
                else if (this.active === 'custom' && this.entity.image) {
                    result = this.image_folder+'/'+this.entity.image;
                }
                else if (this.active === 'automatic') {
                    if (this.automatic.exists) {
                        result = this.image_folder + '/' + this.automatic.path;
                    } else if (this.isLocal && this.isImage && this.selected_file) {
                        result = this.links.preview_automatic_image+'&folder='+this.selected_folder+'&name='+this.selected_file;
                    }
                }
                else if (this.active === 'web') {
                    result = this.web_preview || (this.entity.image ? this.image_folder+'/'+this.entity.image : false);
                }

                return result;
            },
            isRemote: function() {
                return this.entity.storage_type === 'remote';
            },
            isLocal: function() {
                return this.entity.storage_type === 'file';
            },
            isSupported: function() {
                return $.inArray(this.selected_extension, this.automatic.extensions) !== -1;
            },
            isImage: function() {
                return $.inArray(this.selected_extension, ['jpg', 'png', 'gif']) !== -1;
            },
            hasAutomaticSupport: function() {
                return this.isLocal && $.inArray(this.selected_extension, this.automatic.extensions) !== -1;
            },
            isDifferentFile: function() {
                return !(this.cache.storage_type == this.entity.storage_type && this.cache.storage_type != this.entity.storage_type);
            },
            entity: 'entity'
        }),
        watch: {
            'entity.storage_path': function() {
                if (this.isLocal && this.hasAutomaticSupport && this.active === 'none') {
                    this.active = 'automatic';
                }

                if ((!this.hasAutomaticSupport || this.isRemote) && this.active === 'automatic') {
                    this.active = 'none';
                }
            },
            'entity.storage_type': function() {
                if (this.isRemote && this.active === 'automatic') {
                    this.active = 'none';
                }
            },
            'active': function(newVal, oldVal) {
                this.previous_active = oldVal;

                if (newVal === 'custom' && oldVal === 'none' && (!this.entity.image || this.entity.image === '')) {
                    if (this.cache.image) {
                        this.entity.image = this.cache.image;
                    } else {
                        this.changeCustom();
                    }
                }

                if (newVal === 'web' && oldVal) {
                    if (!this.entity.image || this.entity.image.substring(0, 4) !== 'web_') {
                        this.openPicker();
                    }
                }

                if (newVal === 'none') {
                    this.cache.image = this.entity.image;
                    this.entity.image = null;
                }
            }
        }
    });

})(kQuery);