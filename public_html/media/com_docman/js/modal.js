/**
 * DOCman Modal
 *
 * Behaviors related to ComDocmanTemplateHelperModal
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @requires    Koowa, Koowa.Class
 */

(function($){

/** @namespace Docman */
if (typeof Docman === 'undefined') { //noinspection JSUndeclaredVariable
    Docman = {};
}

/** @namespace Docman.Modal */
Docman.Modal = {};

/**
 * @mixin
 * @extends Koowa.Class
 */
Docman.Modal.Class = Koowa.Class.extend(
    {
        /**
         * @property {object} element - Cached jQuery object of the html input element storing the value
         */
        element: false,

        /**
         * @namespace
         * @property {object}  options                - The default option values.
         * @property {string}  options.id             - The default id of the html input element.
         */
        options: {
            id: false
        },

        initialize: function(options){

            this.setOptions(options);

            this.element = $('#' + this.options.id);

            var self = this;
            Docman.Modal.request_map[options.callback] = self.callback.bind(self);
        }
    }
);

/**
 * @class Docman.Modal.Icon
 * @extends Docman.Modal.Class
 */
Docman.Modal.Icon = Docman.Modal.Class.extend({

    /**
     * @namespace
     * @property {object}  options                   - The default option values.
     * @property {string}  options.id                - The default id of the html input element.
     * @property {string}  options.custom_icon_path  - Custom icon path, icon:// parsed by php to the custom icon folder root url.
     * @property {string}  options.blank_icon_path   - Full url to a blank png for failed select fallbacks.
     */
    options: {
        custom_icon_path: "icon://",
        blank_icon_path: "media://system/images/blank.png"
    },
    initialize: function(options){

        //noinspection JSUnresolvedFunction
        /** Call parent construct */
        this.supr(options);

        var preview = $('#' + this.options.id + '-preview'),
            font_preview = $('#' + this.options.id + '-font-preview'),
            value = '',
            self = this,
            icon_path = this.options.custom_icon_path,
            dropdown = preview.parent(),
            event = function(){
                var el = $(this),
                    value = el.val();
                if (value.substr(0, 5) === 'icon:' || !value) {
                    value = (value ? icon_path + '/' + value.substr(5) : self.options.blank_icon_path);

                    preview.attr('src', value);
                    preview.css('display', 'inline');
                    font_preview.css('display', 'none');
                } else {
                    var classes = font_preview.attr('class').split(' ');

                    $.each(classes, function(i, cls) {
                        if (cls.substr(0, 16) === 'k-icon-document-') {
                            font_preview.removeClass(cls);
                        }
                    });

                    font_preview.addClass('k-icon-document-'+value);

                    preview.css('display', 'none');
                    font_preview.css('display', 'inline-block');
                }

                //Breaks on Joomla 3.0 due to no event argument being passed to Dropdown.toggle
                //dropdown.dropdown('toggle');
                //Workaround
                if(dropdown.parent().hasClass('open')) dropdown.trigger('click');
            };

        this.element.closest('ul').find('.k-js-document-icon-selector').click(function(e){
            e.preventDefault();

            $('#'+self.options.id).val($(this).attr('data-value')).trigger('change');
        });

        this.element.on('change', event);
    },
    /**
     * Callback event fired by the iframe handler when a file is selected
     * @param {string} selected
     */
    callback: function(selected){
        this.element.val('icon:'+selected).trigger('change');

        $.magnificPopup.close();
    }
});

/**
 * @class Docman.Modal.Thumbnail
 * @extends Docman.Modal.Class
 */
Docman.Modal.Thumbnail = Docman.Modal.Class.extend({

    AUTOMATIC: 0,

    /**
     * @namespace
     * @property {object}  options                        - The default option values.
     * @property {string}  options.id                     - The default id of the html input element.
     * @property {boolean} options.automatic              - Enable if possible to generate thumbnail automatically.
     * @property {string}  options.image_folder  - URL path to the thumbnails location.
     */
    cache: {},
    options: {
        supported_extensions: [],
        automatic_switch: false,
        automatic_path: '',
        automatic_element_options: {
            type: 'hidden',
            name: 'automatic_thumbnail'
        },
        container_element: '.thumbnail-picker',
        image_folder: ''
    },
    state: {
        value: '',
        automatic: {
            supported_format: true,
            local_file: true,
            preview: false
        }
    },
    initialize: function(options){
        this.supr(options);

        var self = this,
            form = $(this.element[0].form),
            data = form.data('docman:thumbnail:data');

        this.state.value = this.element.val();
        this.state.initial_value = this.state.value;
        this.state.automatic.image = this.options.automatic_path;
        this.state.automatic.storage = $('.js-document-path').data('current');
        this.automatic_switch = this.options.automatic_switch;

        /* Extend the instance with any properties in the data store */
        if(data) {
            $.extend(true, this.state, data);
        }

        this.container_element = this.element.closest(this.options.container_element);

        this.input_container = $('.k-js-input-container', this.container_element);

        this._setButtons();
        this._setInputs();
        this._setHelpMessages();

        form.on('docman:thumbnail:change', function(event, eventData){
            $.extend(true, self.state, eventData);

            var value = self.state.value,
                active;

            switch (self.active) {
                case 'automatic':
                    value = self.state.automatic.image;

                    // A Change button doesn't make sense for automatic thumbnails
                    $('.thumbnail-change', self.container_element).css('display', 'none');
                    break;
                case 'custom':
                    // Custom thumbnails can be changed, show a button
                    $('.thumbnail-change', self.container_element).css('display', '');

                    // Reset cache if a custom image is selected
                    self.cache = {};
                    self.messages.all.hide();
                    break;
                case 'none':
                    break;
            }

            active = self.buttons[self.active];
            active.prop('checked', true);

            var input = self.input_container.find('input[name="'+self.element.attr('name')+'"]');

            input.filter('[data-type="'+self.active+'"]').val(value);

            self.input_container.find('input').prop('disabled', true)
                .filter('[data-type="'+self.active+'"]').prop('disabled', false);


            self._setPreview();
            self._setHelpMessages();
        });

        form.data('docman:thumbnail', this);

        /* Trigger the handler to finish initial setup */
        form.trigger('docman:thumbnail:change');
    },
    enableAutomatic: function() {
        this.buttons.automatic.prop('disabled', false);

        this.messages.unsupported_format.hide();
        this.messages.unsupported_location.hide();

        if (this.state.automatic.source && this.cache.source === this.state.automatic.source) {
            this.cache.source = null;

            if (this.cache.automatic_image) {
                this.state.automatic.image = this.cache.automatic_image;
                this.cache.automatic_image = null;
            }

            if (this.cache.automatic_preview) {
                this.state.automatic.preview = this.cache.automatic_preview;
                this.cache.automatic_preview = null;
            }
        }
    },
    disableAutomatic: function(cache) {
        if (this.active === 'automatic') {
            this.cache.source            = cache.source  || this.state.automatic.source;
            this.cache.automatic_image   = cache.image   || this.state.automatic.image;
            this.cache.automatic_preview = cache.preview || this.state.automatic.preview;

            // Change the current selection to none.
            this.buttons.none.trigger('click');
        }

        this.buttons.automatic.prop('disabled', true);
    },
    setSource: function(state) {
        var cache            = $.extend(true, {}, this.state.automatic),
            supported_format = true,
            local_file       = true;

        state = state || {};
        state.automatic = state.automatic || {};

        $.extend(true, this.state, state);

        if (this.state.automatic.extension) {
            supported_format = $.inArray(this.state.automatic.extension, this.options.supported_extensions) !== -1;
        }

        if (this.state.automatic.storage && this.state.automatic.storage !== 'local') {
            local_file = false;
        }

        if (local_file) {
            if (supported_format) {
                this.enableAutomatic();

                // Switch from none to auto if auto switch is enabled, then disable it.
                if (this.active == 'none' && this.automatic_switch) {
                    this.buttons.automatic.trigger('click');
                    this.automatic_switch = false;
                }

                if (this.active === 'automatic') {
                    this._setPreview();
                }
            } else {

                this.messages.unsupported_format.show();

                // If the document has an automatic thumbnail even though the format is not supported now
                // we should still show it as automatic
                if (this.state.value != this.state.automatic.image) {
                    this.disableAutomatic(cache);
                } else {
                    this._setPreview();
                }

            }
        } else {
            this.messages.unsupported_format.hide();
            this.messages.unsupported_location.show();
            this.disableAutomatic(cache);
        }

        if (state.source) {
            this.state.automatic.source = state.source;
        }

    },
    _setPreview: function() {
        var value   = this.state.value,
            preview = $('.thumbnail-preview', this.container_element),
            image   = $('.thumbnail-image', this.container_element);

        if(this.active == 'automatic' || this.active == 'custom') {
            preview.css('display', '');

            var preview_url;

            if (this.active == 'custom') {
                // Custom thumbnail.
                preview_url = this.options.image_folder + value;
            } else {
                // Automatic thumbnail.
                preview_url = this.state.file_url;

                if (this.state.automatic.preview && this.state.automatic.preview.indexOf('generated/') === 0) {
                    // Use preview iff its path matches the one from image.
                    if (this.state.automatic.preview == this.state.automatic.image) {
                        preview_url = this.state.automatic.preview;
                    }
                }
            }

            if (preview_url && this._isValidFormat(preview_url)) {
                if (preview_url.indexOf('generated/') === 0) {
                    preview_url = this.options.image_folder + preview_url;
                }

                $('.k-js-yes-thumbnail').attr('src', preview_url).removeClass('k-is-hidden');
                $('.k-js-no-thumbnail').addClass('k-is-hidden');
                //image.css('background-image', 'url("' + preview_url + '")').removeClass('placeholder');
            } else {
                $('.k-js-yes-thumbnail').attr('src', '').addClass('k-is-hidden');
                $('.k-js-no-thumbnail').removeClass('k-is-hidden');
                //image.css('background-image', '').addClass('placeholder');
            }
        } else { // none
            preview.css('display', 'none');

            $('.k-js-yes-thumbnail').attr('src', '').addClass('k-is-hidden');
            $('.k-js-no-thumbnail').removeClass('k-is-hidden');
            //image.css('background-image', '').addClass('placeholder');
        }
    },
    /**
     * Set help messages based on the current value
     */
    _setHelpMessages: function() {
        this.messages = {
            all: $('.thumbnail-info .k-js-alert', this.container_element),
            unsupported_format: $('.thumbnail-info .automatic-unsupported-format', this.container_element),
            unsupported_location: $('.thumbnail-info .automatic-unsupported-location', this.container_element)
        };
    },
    _setButtons: function() {
        var self    = this;

        this.buttons = {
            automatic: $('.k-js-thumbnail-automatic', this.container_element),
            custom: $('.k-js-thumbnail-custom', this.container_element),
            none: $('.k-js-thumbnail-none', this.container_element),
            change: $('.k-js-thumbnail-change', this.container_element)
        };

        this.buttons.automatic.on('click', function(e){
            self.active = 'automatic';
            self.change({value: self.state.automatic.image});
        });

        this.buttons.change.on('click', function(event){
            event.preventDefault();

            self.buttons.custom.trigger('click', event);
        });

        this.buttons.none.on('click', function(event){
            self.active = 'none';
            self.change({value: ''});
        });

        this.buttons.custom.on('click', function(event) {
            self.active = 'custom';

            $.magnificPopup.open({
                items: {
                    src: kQuery(this).attr('data-href'),
                    type: 'iframe'
                },
                mainClass: 'koowa_dialog_modal'
            });
        });

        /* This is to fix double border bug in the button group due to a hidden input element */
        //this.buttons.custom.append(this.element);

        var active;

        // Set active button.
        if (this.state.value) {
            if (this.state.value == this.state.automatic.image && this.state.automatic.storage == 'file') {
                active = 'automatic';
            } else {
                active = 'custom';
            }
        } else {
            active = 'none';
        }

        this.active = active;
    },
    _setInputs: function() {
        var input_container = this.input_container;

        if (this.buttons.automatic.length) {
            // automatic_thumbnail=1 for automatic
            $('<input />', $.extend({}, this.options.automatic_element_options, {
                'data-type': 'automatic',
                value: 1
            })).appendTo(input_container);

            // name=image value=generated/foobar.png for automatic
            $('<input />', {
                'data-type': 'automatic',
                name: this.element.attr('name'),
                //value: this.options.automatic_path,
                type: 'hidden'
            }).appendTo(input_container);

            $('<input />', {
                'data-type': 'automatic',
                name: 'automatic_thumbnail',
                value: 1,
                type: 'hidden'
            }).appendTo(input_container);
        }

        // automatic_thumbnail=0 for custom and none
        var input = $('<input />', $.extend({}, this.options.automatic_element_options, {
            'data-type': 'custom',
            value: '0'
        })).appendTo(input_container)
            .clone().attr('data-type', 'none').appendTo(input_container);

        // name=image value="" for none
        $('<input />', {
            'data-type': 'none',
            name: this.element.attr('name'),
            value: '',
            type: 'hidden'
        }).appendTo(input_container);
    },
    /**
     * Private shortcut API to check thumbs format for live previews
     * @param path
     * @returns {boolean}
     * @private
     */
    _isValidFormat: function(path){
        var extension = path.substr(path.lastIndexOf('.')+1).toLowerCase();

        return $.inArray(extension, ['jpg', 'jpeg', 'gif', 'png']) !== -1;
    },
    /**
     * Shortcut API for updating the state of the thumbnail widget
     * @param data
     */
    change: function(data){
        return this.element.closest('form').trigger('docman:thumbnail:change', data);
    },

    /**
     * Callback event fired by the iframe handler when a file is selected
     * @param {string} selected
     */
    callback: function(selected){

        this.change({name: this.element.attr('name'), value: selected});

        $.magnificPopup.close();
    }
});

/**
 * Global request map, used in iframe and JSONP style callbacks
 * @memberOf Docman.Modal
 * @type {object}
 */
Docman.Modal.request_map = {};

})(kQuery);