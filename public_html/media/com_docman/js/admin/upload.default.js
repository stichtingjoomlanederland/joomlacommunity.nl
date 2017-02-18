
var DOCman = DOCman || {};

kQuery(function($) {

    DOCman.BatchForm = Koowa.Class.extend({
        batch_form: null,
        document_form_container: null,

        form_template: null,

        save_button: null,
        cancel_button: null,

        uploader: null,

        upload_in_progress: false,

        // used to decide whether to reload the page or not
        documents_created: false,

        humanize_titles: true,

        options: {
            onBeforeInitialize: function(instance) {},
            onAfterInitialize: function() {},

            selected_category: null,
            selected_files: [],

            show_uploader: true,

            refresh_parent_on_close: true,

            batch_form_selector: '.k-js-batch-form',
            form_selector: '.k-js-document-form',
            form_container_selector: '.k-js-form-container',
            title_input_selector: '.k-js-title',
            name_input_selector:  '.k-js-filename',
            remove_file_selector: '.k-js-remove-file',
            form_template_selector: '.k-js-document-form-template',
            save_selector: '.k-js-save',
            cancel_selector: '.k-js-cancel',
            humanize_selector: '.k-js-humanize-titles',
            uploader_selector: '.docman-batch-uploader',
            continue_selector: '.k-js-continue',
            upload_warning_selector: '.k-js-upload-warning',
            close_modal_selector: '.k-js-close-modal'
        },

        initialize: function(options) {
            this.supr();

            this.setOptions(options);

            if (this.options.onBeforeInitialize) {
                this.options.onBeforeInitialize.call(this, this);
            }

            this.batch_form = $(this.options.batch_form_selector);
            this.form_template = doT.template($(this.options.form_template_selector).text());
            this.document_form_container = $(this.options.form_container_selector);

            this.save_button = $(this.options.save_selector);
            this.cancel_button = $(this.options.cancel_selector);

            this.uploader = $(this.options.uploader_selector);

            this.category_selector = this.batch_form.find('select[name="docman_category_id"]');

            if (this.options.selected_category) {
                this.category_selector
                    .val(this.options.selected_category)
                    .prop('disabled', true);

                if (!this.category_selector.val()) {
                    this.disableBatchValues();
                    this.hideUploader();
                    this.batch_form.prepend($('<div />', {
                        class: 'alert alert-error',
                        html: Koowa.translate('You are not permitted to create documents in this category')
                    }));
                }
            }
            else if (!this.category_selector.find('option').length) {
                this.disableBatchValues();
                this.hideUploader();
                this.batch_form.prepend($('<div />', {
                    class: 'alert alert-error',
                    html: Koowa.translate('You are not permitted to create documents in any category')
                }));

            }

            if (!this.options.show_uploader) {
                this.hideUploader();
            }

            if (this.options.selected_files) {
                var self = this;
                $.each(this.options.selected_files, function(i, path) {
                    self.renderFile({
                        path: path
                    });
                });
            }

            this.attachEvents();

            if (this.options.onAfterInitialize) {
                this.options.onAfterInitialize(this);
            }

            if (!this.document_form_container.find('form').length) {
                this.save_button.prop('disabled', true);
            }
        },
        callUploader: function() {
            if (this.uploader.uploader('instance')) {
                return this.uploader.uploader(arguments);
            }

            return false;
        },
        hideUploader: function() {
            this.uploader.parent().remove();
        },
        showSuccessMessage: function() {
            $('.k-js-success-message').show();

            if (this.isOpenInModal()) {
                $('.k-js-close-modal-container').show();
            }
        },
        hideSuccessMessage: function() {
            $('.k-js-success-message').hide();
        },
        attachEvents: function() {
            var self = this;

            $(this.options.close_modal_selector).click(function(e) {
                e.preventDefault();

                self.hide();
            });

            $(this.options.humanize_selector).change(function() {
                self.humanize_titles = (this.value == 1);
                this.value == 1 ? self.humanize() : self.dehumanize();
            });

            this.uploader.on('uploader:uploaded', function(event, data) {
                var file = data.file,
                    uploader = data.uploader;

                if (file.status === plupload.DONE && typeof data.result.response !== 'undefined') {
                    var result = data.result.response.entities[0];

                    self.renderFile(result);

                    var remaining = uploader.files.length - (uploader.total.uploaded + uploader.total.failed);
                    if (remaining === 0) {
                        self.enableBatchValues();
                        self.document_form_container.show();
                        self.save_button.removeProp('disabled');
                    }
                }
            });

            this.uploader.on('uploader:started', function() {
                self.upload_in_progress = true;
            });

            this.uploader.on('uploader:stopped', function() {
                self.upload_in_progress = false;
            });

            self.document_form_container.on('click', self.options.remove_file_selector, function(e) {
                e.preventDefault();

                var el = $(e.currentTarget).closest(self.options.form_selector);
                el.animate({
                    opacity: 0.2,
                    height: 0
                }, 300, function(){
                    el.remove();

                    if (!self.document_form_container.find('form').length) {
                        self.save_button.prop('disabled', true);
                    }
                });


            });

            var getDefaults = function() {
                var defaults = self.flattenArray(self.batch_form.serializeArray());
                defaults.push({name: self.category_selector.attr('name'), value: self.category_selector.val()});

                return defaults;
            };

            self.save_button.click(function(event) {
                event.preventDefault();

                if (self.save_button.prop('disabled') === true) {
                    return;
                }

                if (!self.document_form_container.find('form').length) {
                    return;
                }

                if (self.upload_in_progress) {
                    self.callUploader('notify', 'error', Koowa.translate('Please wait for the upload to finish first'));
                    return;
                }

                var defaults = getDefaults(),
                    chain = [],
                    callChain = function() {
                    if (chain.length) {
                        chain.pop().call();
                    }
                };

                self.callUploader('clearQueue');
                self.callUploader('disable');

                self.save_button.prop('disabled', true);

                self.hideSuccessMessage();

                self.disableBatchValues();

                $(self.options.form_selector)
                    .find(self.options.title_input_selector).attr('readonly', 'readonly').end()
                    .each(function(i, form) {
                        var f = $(form);

                        if (f.find(self.options.continue_selector).length) {
                            return;
                        }

                        chain.unshift(function() {

                            $.ajax({
                                url: f.attr('action')+'&view=document&id=&slug=',
                                type: 'POST',
                                dataType: 'json',
                                data: self.getPostData(f, defaults)
                            }).done(function(response, eventStatus, xhr){
                                if (xhr.status == 201) {
                                    self.documents_created = true;

                                    var item = response.entities[0],
                                        link = item.links.self.href.replace(/&amp;/g, '&'),
                                        text = Koowa.translate('Continue editing this document: {document}');

                                    f.empty();

                                    link += '&format=html&id='+item.id;

                                    if (link.search('administrator/') === -1) {
                                        link += '&layout=form';
                                    }

                                    f.append($('<div class="k-js-continue k-form-row__item k-form-row__item--label" />').append($('<a />', {
                                        'href': link,
                                        'text': text.replace('{document}', item.title),
                                        'target': '_blank'
                                    })));
                                }
                            }).always(function(){
                                callChain();
                            });
                        });
                    });

                    chain.unshift(function() {
                        //self.save_button.removeProp('disabled');

                        self.showSuccessMessage();

                        self.enableBatchValues();

                        self.callUploader('enable');
                    });

                callChain();
            });

            self.cancel_button.click(function(event) {
                event.preventDefault();

                if (!$(self.options.form_selector).find(self.options.title_input_selector).length || confirm(Koowa.translate('You will lose all unsaved data. Are you sure?'))) {
                    self.hide();
                }
            });

            if (self.options.refresh_parent_on_close && self.isOpenInModal()) {
                var instance = window.parent.kQuery.magnificPopup.instance;

                if (instance.ev) {
                    instance.ev.on('mfpClose', function() {
                        if (self.documents_created) {
                            window.parent.location.reload();
                        }
                    });
                }
            }
        },
        isOpenInModal: function() {
            return (window !== window.parent && window.parent.kQuery && window.parent.kQuery.magnificPopup && window.parent.kQuery.magnificPopup.instance);
        },
        renderFile: function(file) {
            var filename = file.name || file.path.substring(file.path.lastIndexOf('/')+1),
                title = file.title || (this.humanize_titles ? this.humanizeString(filename) : filename);

            var html = this.form_template({
                title: title,
                filename: filename,
                storage_path: file.path
            });

            this.document_form_container.find(this.options.upload_warning_selector).remove();

            this.document_form_container.append(html);
        },
        hide: function() {
            if (window.parent.kQuery && window.parent.kQuery.magnificPopup && window.parent.kQuery.magnificPopup.instance) {
                window.parent.kQuery.magnificPopup.close();
            }
        },
        flattenArray: function(items){

            var object = [];
            $.each(items, function(i, item) {
                object.push(item);
            });

            return object;
        },
        getPostData: function(form, defaults) {

            var values  = this.flattenArray($(form).serializeArray());

            $.each(defaults, function(index, d) {
                if ( !values[d.name] || values[d.name] === '' ) {
                    if ( typeof d.value !== 'undefined' ) {
                        values.push(d);
                    }
                }
            });

            values['storage_type'] = 'file';

            return values;
        },
        humanizeString: function(string) {
            string = string.substring(0, string.lastIndexOf('.'));

            var last_slash = string.lastIndexOf('/');
            if (last_slash) {
                string = string.substring(last_slash+1);
            }

            string = string.replace(/[_\-\.]/g, ' ');

            string = string[0].toUpperCase()+string.substr(1);

            return string;
        },
        dehumanizeString: function(string) {
            var last_slash = string.lastIndexOf('/');
            if (last_slash) {
                string = string.substring(last_slash+1);
            }

            return string;
        },
        humanize: function() {
            var self = this;

            $(this.options.form_selector).each(function(i, el) {
                var $el = $(el),
                    filename = $el.find(self.options.name_input_selector).val(),
                    input    = $el.find(self.options.title_input_selector);

                if (input.val() === self.dehumanizeString(filename)) {
                    input.val(self.humanizeString(filename));
                }
            });
        },
        dehumanize: function() {
            var self = this;

            $(this.options.form_selector).each(function(i, el) {
                var $el = $(el),
                    filename = $el.find(self.options.name_input_selector).val(),
                    input    = $el.find(self.options.title_input_selector);

                if (input.val() === self.humanizeString(filename)) {
                    input.val(self.dehumanizeString(filename));
                }
            });
        },
        enableBatchValues: function() {
            var inputs = this.batch_form.find('select, input');
            if (this.options.selected_category) {
                inputs = inputs.not(this.category_selector);
            }

            inputs.removeAttr('disabled');
        },
        disableBatchValues: function() {
            this.batch_form.find('select, input').prop('disabled', true);
        }
    });
});
