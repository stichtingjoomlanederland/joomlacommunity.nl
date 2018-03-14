
/** @namespace Docman */
if (typeof Docman === 'undefined') { //noinspection JSUndeclaredVariable
    Docman = {};
}

(function($) {
    var debounce = function(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };

    $(function() {

        Docman.File = Vue.extend({
            data: function() {
                return {
                    last_remote: '',
                    last_file: '',
                    error_message: '',
                    remote_streams: [],
                    temporary_folder : 'tmp'
                }
            },
            mounted: function () {
                var vm = this;

                if (this.isRemote) {
                    this.last_remote = this.entity.storage_path;
                } else if (this.isLocal) {
                    this.last_file = this.entity.storage_path;
                }

                // Callback to the file selector
                Docman.onSelectFile = function(selected) {
                    vm.$store.commit('setProperty', {storage_path: selected, storage_type: 'file'});
                    vm.updateSelectedFile();

                    if (typeof $.magnificPopup !== 'undefined' && $.magnificPopup.instance) {
                        $.magnificPopup.close();
                    }
                };

                $('.docman-uploader').on('uploader:uploaded', function(event, data) {
                    if (data.file && data.file.status === plupload.DONE) {
                        var uploader = $(this).uploader('instance'),
                            folder = uploader.options.multipart_params.folder,
                            name   = data.file.name,
                            path   = ((folder && folder !== '') ? folder+'/' : '')+name;

                        vm.$store.commit('setProperty', {storage_path: path, storage_type: 'file'});
                    }
                }).on('uploader:create', function() {
                    var button = $('.js-more-button');

                    if (button.data('enabled')) {
                        button.show();
                        $('.k-upload__content').after(button);
                    }
                }).on('uploader:ready', function() {
                    if (vm.isLocal) {
                        vm.updateSelectedFolder();
                        vm.updateSelectedFile();
                        vm.updateExistingFileButtonLink();
                    }
                });

                var beforeSend = function() {
                    var type  = vm.entity.storage_type,
                        value = vm.entity.storage_path,
                        form   = $(this);

                    $('<input type="hidden" name="storage_type" />').val(type).appendTo(form);
                    $('<input type="hidden" name="storage_path" />').val(value).appendTo(form);
                };

                $('.k-js-form-controller').on('koowa:beforeApply', beforeSend)
                    .on('koowa:beforeSave', beforeSend)
                    .on('koowa:beforeSave2new', beforeSend)
                    .on('koowa:validate', function(args) {
                        var path = vm.entity.storage_path;

                        vm.error_message = '';

                        if (vm.isLocal) {
                            var uploader = $('.docman-uploader').uploader('instance').getUploader();

                            if (uploader.state === plupload.STARTED) {
                                vm.error_message = Koowa.translate('Please wait for the upload to finish before saving the document');

                                return false;
                            }

                            if (uploader.files.length === 0) {
                                vm.error_message = Koowa.translate('Please select a file first');

                                return false;
                            }
                        } else {
                            var scheme = null,
                                matches = path.match(/^([a-zA-Z0-9\-]+):/);

                            if (matches) {
                                scheme = matches[1];

                                // If scheme is in the array it will not be redirected by browser
                                // Therefore we need to check if it's enabled
                                if (scheme && typeof vm.remote_streams[scheme] !== 'undefined') {
                                    if (!vm.remote_streams[scheme]) {
                                        vm.error_message = Koowa.translate('Invalid remote link. This link type is not supported by your server.')

                                        return false;
                                    }
                                }
                            } else {
                                vm.error_message = Koowa.translate('Your link should either start with http:// or another protocol');

                                return false;
                            }

                        }

                        return true;
                    });
            },
            computed: Vuex.mapState({
                selected_file: function() {
                    return this.entity.storage_path.substr(this.entity.storage_path.lastIndexOf('/')+1);
                },
                selected_folder: function() {
                    if (this.entity._isNew && !this.entity.storage_path) {
                        return this.temporary_folder;
                    } else {
                        return this.entity.storage_path.substr(0, this.entity.storage_path.lastIndexOf('/'));
                    }
                },
                isRemote: function() {
                    return this.entity.storage_type === 'remote';
                },
                isLocal: function() {
                    return this.entity.storage_type === 'file' || !this.entity.storage_type;
                },
                entity: 'entity'
            }),
            methods: {
                updateSelectedFile: function() {
                    if (this.selected_file) {
                        var size = this.entity.size;
                        var uploader = $('.docman-uploader').uploader('instance');

                        var tmp_file     = new plupload.File({
                            'name': this.selected_file,
                            'size' : size || 0
                        });

                        tmp_file.loaded = size || 0;
                        tmp_file.status = plupload.DONE;
                        tmp_file.percent = 100;
                        tmp_file.destroy = function() {};

                        uploader.getUploader().addFile(tmp_file);
                    }
                },
                updateSelectedFolder: function() {
                    var uploader = $('.docman-uploader').uploader('instance');
                    uploader.options.multipart_params.folder = this.selected_folder;
                },
                updateRemotePath: debounce(function(event) {
                    this.last_remote = event.target.value;

                    this.$store.commit('setProperty', {storage_type: 'remote', storage_path: event.target.value});
                }, 300),
                switchToFile: function(event) {
                    if (this.isRemote || !this.entity.storage_type) {
                        this.last_remote = this.entity.storage_path;

                        this.$store.commit('setProperty', {storage_type: 'file', storage_path: this.last_file});
                    }
                },
                switchToRemote: function(event) {
                    if (this.isLocal || !this.entity.storage_type) {
                        this.last_file = this.entity.storage_path;

                        this.$store.commit('setProperty', {storage_type: 'remote', storage_path: this.last_remote});
                    }
                },
                updateExistingFileButtonLink: function() {
                    var link   = $('.js-more-button').find('a'),
                        href   = link.attr('href'),
                        folder = (this.selected_folder === this.temporary_folder ? '' : this.selected_folder) || '';

                    href = href.replace(/\&folder=(.*?)&/i, '&folder='+folder+'&');
                    href = href.replace(/\&file=(.*?)&/i, '&file='+(this.selected_file || '')+'&');

                    link.attr('href', href);
                }
            },
            watch: {
                'entity.storage_path': function() {
                    if (this.isLocal) {
                        this.updateExistingFileButtonLink();
                        this.updateSelectedFolder();
                    }
                }
            }
        });

    });

})(kQuery);
