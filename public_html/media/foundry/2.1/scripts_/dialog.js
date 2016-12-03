(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
/**
 * jquery.dialog.
 * jQuery dialog with extensible transitions,
 * iframe & ajax content support.
 *
 * Copyright (c) 2011 Jason Ramos
 * www.stackideas.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

$.require()
 .library(
    'mvc/controller',
    'ui/core',
    'ui/position',
    'throttle-debounce',
    'easing'
 )
 .stylesheet(
    'dialog/default'
 )
 .template(
    'dialog/default'
 )
 .done(function() {
$.Controller(
    'dialog',
    {
        defaults: {

            title: '',
            content: '',
            buttons: {},

            customClass: "",

            width: 'auto',
            height: 'auto',

            css: {
                position: 'absolute',
                width: 'auto',
                height: 'auto',
                zIndex: 10001
            },

            position: {
                my: 'center center',
                at: 'center center',
                of: window
            },

            offset: 100,

            // TODO: Overlay to be part of EJS
            showOverlay: true,
            overlay: {
                css: {
                    background: '#000',
                    opacity: '0.4',
                    position: 'fixed',
                    top: 0,
                    left: 0,
                    zIndex: 10000,
                    width : '100%',
                    height: '100%',
                    display: 'none'
                }
            },

            body: {
                css: {
                    minWidth: 400,
                    minHeight: 200,

                    // If the dialog content immediate element contains margin,
                    // the margin will bleed outside of the parent container
                    // if overflow: 'auto' is not assigned.
                    overflow: 'auto'
                }
            },

            iframe: {
                css: {
                    width: '100%',
                    height: '100%',
                    border: 'none'
                }
            },

            transition: {
                show: 'zoom',
                hide: 'zoom'
            },

            beforeShow: function(){},
            afterShow : function(){},
            beforeHide: function(){},
            afterHide : function(){},

            dialogHead   : '.dialog-head',
            dialogBody   : '.dialog-body',
            dialogFooter : '.dialog-footer',
            dialogTitle  : '.dialog-title',
            dialogContent: '.dialog-content',
            dialogButtons: '.dialog-buttons',
            dialogLoader : '.dialog-loader',
            closeButton  : '.dialog-closeButton'
        }
    },
    function(self){ return {
        ready: false,

        contentReady: false,

        init: function()
        {
            var _this = this;

            this.setInitOptions(this.options);

            this.initElement = this.element.clone();

            this.element.finalizeContent = function(){ return _this.finalizeContent.apply(_this) };

            // Experimental optimization
            // Make a reference to all the static elements on init.
            $.each([
                'dialogHead',
                'dialogBody',
                'dialogFooter',
                'dialogTitle',
                'dialogContent',
                'dialogButtons',
                'dialogLoader'
            ], function(i, name)
            {
                _this[name] = _this.find(_this.options[name]);
            });

            if (this.options.content===undefined) {
                return;
            }

            this.display();
        },

        setInitOptions: function(options)
        {
            var _this = this;

            this.initOptions = $.extend(true, {}, options);

            // Remove callbacks
            $.each(['beforeShow', 'afterShow', 'beforeHide', 'afterHide'], function(i, name)
            {
                _this.initOptions[name] = function(){};
            });
            this.initOptions.title   = null;
            this.initOptions.content = null;
            this.initOptions.buttons = null;
        },

        // Update is called during subsequent $.dialog() calls.
        // A default behaviour of any controller class.
        update: function(options)
        {
            if (options)
            {
                var options = $.extend(true, {}, this.initOptions, options);

                this.setInitOptions(options);

                this.display(options);
            }

            return this;
        },

        displayQueue: [],

        display: function(options)
        {
            if (this.resizing)
                return this.displayQueue.push(options);

            if ($.isPlainObject(options))
                this.options = $.extend(true, {}, this.Class.defaults, options);

            if (!this.ready)
            {
                // TODO: Overlay should be part of dialog template
                if (this.options.showOverlay)
                    this.createOverlay();
            }

            this.contentReady = this.options.content===null;

            var transition = this.options.transition;
            if (typeof transition=='string')
                this.options.transition = { show: transition, hide: transition };

            // Determine content type
            var _this = this;

            this.contentType = 'html';

            if ($.isUrl(this.options.content))
                this.contentType = 'iframe';

            if ($.isDeferred(this.options.content))
                this.contentType = 'deferred';

            switch (this.contentType)
            {
                case 'html':
                    this.hideLoader();
                    this.show();
                    break;

                case 'iframe':
                    var iframe = $(document.createElement('iframe')),
                        iframeUrl = this.options.content,
                        iframeCss = this.options.iframe.css,
                        iframeButtons = this.options.buttons,
                        dialogContent = this.dialogContent,
                        iframeOptions = $.extend(true, {}, this.options, {
                            content: iframe,
                            buttons: iframeButtons
                        });

                    this.showLoader(function()
                    {
                        onIframeLoaded = (function()
                        {
                            return function(event)
                            {
                                // Expose dialog object to iframe
                                // Inside a try catch because does not work on cross-site domain,
                                // and url checking takes a lot more code to write.
                                try { iframe[0].contentWindow.parentDialog = _this; } catch(err) {};

                                _this.update(iframeOptions);
                            };
                        })();

                        iframe
                            .appendTo(dialogContent)
                            .css(iframeCss)
                            .css({position: 'absolute', visibility: 'hidden'})
                            .one('load', onIframeLoaded)
                            .attr('src', iframeUrl);
                    });

                    break;

                case 'deferred':
                    var ajax = this.options.content,
                        contentOptions = this.options;

                    this.showLoader(function() {

                        ajax.done(function(html) {

                            _this.update($.extend(true, {}, contentOptions, {content: html}));
                        });
                    });

                    break;
            }
        },

        loading: false,

        showLoader: function(callback)
        {
            var _this = this;

            var showLoaderOverlay = function()
            {
                _this.dialogLoader
                    .show()
                    .css(
                    {
                        width: _this.dialogBody.width(),
                        height: _this.dialogBody.height()
                    })
                    .position({
                        my: 'top left',
                        at: 'top left',
                        of: _this.options.dialogContent
                    });

                return callback && callback();
            };

            return (this.ready) ? showLoaderOverlay() : this.update(
            {
                title: this.options.title,
                content: '',
                width: this.initOptions.body.css.minWidth,
                height: this.initOptions.body.css.minHeight,
                afterShow: showLoaderOverlay
            });
        },

        hideLoader: function()
        {
            this.dialogLoader.hide();
        },

        autoSize: function()
        {
            return this.initOptions.css.width=='auto' || this.initOptions.css.height=='auto';
        },

        finalizeContent: function()
        {
            var _this = this;

            this.element
                .css(this.options.css);

            this.dialogContent
                .css(this.options.body.css);

            if (this.options.title!==null)
                this.dialogTitle
                    .html(this.options.title);

            if (this.options.content!==null)
            {
                var isIframe = $(this.options.content).is('iframe');

                this.dialogBody
                    .toggleClass('type-iframe', isIframe);

                if (isIframe)
                {
                    // This is a replacement for jQuery's .siblings().
                    // The difference is that this code will go through
                    // not only html elements but also other types of dom nodes,
                    // e.g. text nodes.
                    var parent = this.options.content.parent()[0];

                    var i = 0;
                    while(parent.childNodes.length > 1)
                    {
                        var node = parent.childNodes[i];
                        if (node!==_this.options.content[0])
                            parent.removeChild(node);
                        i++;
                    }

                    this.options.content
                        .css({position: 'relative', visibility: 'visible'});
                } else {
                    this.dialogContent
                        .html(this.options.content);
                }
            }

            if (!this.contentReady)
                this.finalizeButtons();

            return this.element;
        },

        finalizeSize: function(fast)
        {
            var refElement = this.refElement;

            if (!fast)
            {
                refElement = this.refElement = this.initElement.clone().removeClass('global').insertAfter(this.element);
                refTitle   = refElement.find(this.options.dialogTitle);
                refBody    = refElement.find(this.options.dialogBody);
                refFooter  = refElement.find(this.options.dialogFooter);
                refButtons = refElement.find(this.options.dialogButtons);

                refElement
                    .css(this.initOptions.css)
                    .css({display: 'block'});

                refTitle
                    .html(this.options.title);

                if (!$.isEmptyObject(this.options.buttons))
                {
                    refFooter.show();
                    refButtons.append('<button>test</button>');
                } else {
                    refFooter.hide();
                }

                // Pass 1: Readjust dialog body's dimension based on dialog's content
                var isIframe = $(this.options.content).is('iframe');

                var refContent = (isIframe) ? document.createElement('div') : this.options.content;

                this.options.body.css.width  = (isIframe && this.options.width=='auto')  ? this.options.content.contents().width() : this.options.width;
                this.options.body.css.height = (isIframe && this.options.height=='auto') ? this.options.content.contents().height() : this.options.height;

                refBody
                    .css(this.options.body.css)
                    .toggleClass('type-iframe', isIframe)
                    .html(refContent);

                this.options.body.css.width  = refBody.width();
                this.options.body.css.height = refBody.height();

                // Pass 2: Re-adjust dialog's dimension based on window's dimension
                var offset         = this.options.offset,
                    width          = refElement.width(),
                    height         = refElement.height(),
                    maxWidth       = $(window).width() - offset,
                    maxHeight      = $(window).height() - offset,
                    widthExceeded  = width > maxWidth,
                    heightExceeded = height > maxHeight;

                this.options.css.width  = (widthExceeded) ? maxWidth : width;
                this.options.css.height = (heightExceeded) ? maxHeight : height;

                // Pass 3: Readjust dialog body's dimension based on readjusted dialog's dimension
                this.options.body.css.width  -= (width  - this.options.css.width);
                this.options.body.css.height -= (height - this.options.css.height);
                this.options.body.css.minWidth = this.options.body.css.minHeight = 'auto';

                // Pass 4: Decide scrollbar visiblity based on readjusted dialog body's dimension.
                refBody.css(this.options.body.css);

                this.options.body.css.overflowX = (!widthExceeded  || isIframe || refBody[0].scrollWidth  <= this.options.body.css.width)  ? 'auto' : 'scroll';
                this.options.body.css.overflowY = (!heightExceeded || isIframe || refBody[0].scrollHeight <= this.options.body.css.height) ? 'auto' : 'scroll';

                // Clean up
                refBody.html('');
            }

            // Pass 5: Readjust position based on final dialog dimension
            refElement
                .css(this.options.css)
                //  FF3 can't retrieve css positions when element is on display: none;
                // .initialPosition(this.options.position, true);
                .css('visibility', 'hidden')
                .position(this.options.position);

            this.options.css.top  = refElement.css('top');
            this.options.css.left = refElement.css('left');
        },

        finalizeButtons: function()
        {
            var dialogFooter  = this.dialogFooter,
                dialogButtons = this.dialogButtons;

            dialogButtons.empty();

            if ($.isEmptyObject(this.options.buttons))
            {
                dialogFooter.hide();
                return;
            }

            $.each(this.options.buttons, function(i, button)
            {
                var events = $.extend({}, button);
                delete events.name;

                $(document.createElement('button'))
                    .attr('type', 'button')
                    .html(button.name)
                    .bind(events)
                    .appendTo(dialogButtons);
            });

            dialogFooter.show();
        },

        show: function(callback)
        {
            var _this = this;

            if (this.ready && this.resizing) return;

            if (this.refElement)
                this.refElement.remove();

            if (!this.contentReady)
                this.options.beforeShow.apply(this);

            this.resizing = true;

            this.finalizeSize();

            this.element.addClass('resizing');

            this.transition[this.options.transition.show].show
                .apply(this, [function()
                {
                    if (!_this.ready)
                    {
                        _this.bind(window, 'resize scroll', $.debounce(150, function(){ _this.refresh() }));
                        _this.ready = true;
                    }

                    if (!_this.contentReady)
                    {
                        if (callback) callback.apply(_this);
                        _this.options.afterShow.apply(_this);
                    }

                    _this.element.removeClass('resizing');

                    _this.contentReady = true;

                    // Let the dialog container wrap to dialog content's final natural size,
                    // so we can avoid all the tedious box model issues.
                    _this.element.css({width: 'auto', height: 'auto'});

                    if (_this.displayQueue.length > 0)
                    {
                        setTimeout(function(){
                            _this.resizing = false;
                            _this.display(_this.displayQueue.shift());
                        }, 500);
                    } else {
                        _this.resizing = false;
                    }

                }]);
        },

        hide: function(callback)
        {
            var _this = this;

            if (!this.contentReady)
                this.options.beforeHide.apply(this);

            this.transition[this.options.transition.hide].hide
                .apply(this, [function()
                {
                    if (callback) callback.apply(_this);
                    _this.options.afterHide.apply(_this);
                }]);
        },

        refresh: function()
        {
            if (this.closing)
                return;

            var _this = this;
            this.finalizeSize(true);

            this.resizing = true;

            this.transition[this.options.transition.show].show.apply(this, [function()
            {
                _this.resizing = false;

                // Let the dialog container wrap to dialog content's final natural size,
                // so we can avoid all the tedious box model issues.
                _this.element.css({width: 'auto', height: 'auto'});
            }]);
        },

        // TODO: Transition parameters
        transition:
        {
            none: {
                show: function(callback)
                {
                    // TODO: Fix IE7 z-index issue.

                    if (!this.ready)
                        this.overlay.show();

                    this.element
                        .finalizeContent()
                        .show(0, callback);
                },

                hide: function(callback)
                {
                    this.overlay.hide();
                    this.element.hide(0, callback);
                }
            },

            fade: {
                show: function(callback)
                {
                    var _this = this;

                    if (!this.ready)
                        this.overlay.fadeOut(0).fadeIn('normal');

                    if (!this.contentReady)
                    {
                        this.element
                            .fadeOut((!this.contentReady) ? 0 : 'fast', function()
                            {
                                _this.element
                                    .finalizeContent()
                                    .fadeIn('normal', 'easeInCubic', callback);
                            });

                    } else {
                        this.element.finalizeContent();
                        return callback && callback();
                    }
                },

                hide: function(callback)
                {
                    this.overlay.fadeOut('normal', 'easeOutCubic');
                    this.element.fadeOut('normal', 'easeOutCubic', callback);
                }
            },

            zoom: {
                show: function(callback)
                {
                    var _this = this,
                        dialogBody = this.dialogBody,
                        dialogFooter = this.dialogFooter;

                    if (!this.ready)
                    {
                        this.overlay.fadeIn('fast');

                        this.element
                            .finalizeContent()
                            .css(
                            {
                                top: parseInt(this.options.css.top) + (parseInt(this.options.css.height) / 2),
                                left: parseInt(this.options.css.left) + (parseInt(this.options.css.width) / 2),
                                width: 0,
                                height: 0
                            });
                    }

                    if (!this.contentReady)
                    {
                        dialogBody.css({opacity: 0});
                        dialogFooter.css({opacity: 0});
                    }

                    this.element
                        .animate(
                        {
                            top   : this.options.css.top,
                            left  : this.options.css.left,
                            width : this.options.css.width,
                            height: this.options.css.height
                        }, 'normal', 'easeInCubic',
                        function()
                        {
                            _this.element.finalizeContent();

                            dialogBody.css({opacity: 1});
                            dialogFooter.css({opacity: 1});

                            // TODO: Circular reference. IE8 show() callback gets executed when closing. Double check if its fixed.
                            return callback && callback();
                        });
                },
                hide: function(callback)
                {
                    var _this = this;

                    this.element
                        .animate(
                            {
                                top: parseInt(this.options.css.top) + (parseInt(this.options.css.height) / 2),
                                left: parseInt(this.options.css.left) + (parseInt(this.options.css.width) / 2),
                                width: 0,
                                height: 0
                            }, 'normal', 'easeOutCubic',
                            function()
                            {
                                _this.overlay.fadeOut('fast');
                                return callback && callback();
                            });
                }
            }
        },

        overlay: $('<div></div>'),

        createOverlay: function()
        {
            var _self = this;

            this.overlay
                .css(this.options.overlay.css)
                .click(function()
                {
                    _self.close();
                })
                .appendTo('body');
        },

        closing: false,

        close: function()
        {
            if (this.closing) return;

            this.closing = true;

            this.hide(function()
            {
                this.element.remove();
                this.refElement.remove();
            });
        },

        "{closeButton} click": function()
        {
            this.close();
        },

        content: function()
        {
            var dialogContent = this.dialogContent;
            var iframe = dialogContent.find("> iframe");
            return (iframe.length > 0) ? iframe.contents() : dialogContent;
        }
    }}
);
$.dialog = function(options) {

    // When dialog is loaded via iframe
    if (window.parentDialog) {
        return window.parentDialog.update(options);
    }

    var dialog = $('.foundryDialog.global'),
        controller = dialog.controllers("dialog")[0];

    // Return dialog controller if no options given
    if (arguments.length < 1 && controller) {
        return controller;
    }

    // Create dialog if element not exist.
    if (dialog.length < 1) {
        dialog = $($.View('dialog/default')).addClass('global').addClass(options.customClass).appendTo('body');
    }

    // Translate options shorthand
    if (typeof options === "string" || $.isDeferred(options)) {

    	var afterShow = arguments[1];

    	options = {
    		content: options,
    		afterShow: ($.isFunction(afterShow)) ? afterShow : $.noop
    	}
    }

    // If it is an existing dialog
    if (controller) {

        controller.update(options);

    } else {

        controller = dialog.implement('dialog', options, function(){}).controllers("dialog")[0];
    }

    return controller;
};

    module.resolve();

 });
// module body: end

}; 
// module factory: end

dispatch("dialog")
.containing(moduleFactory)
.to("Foundry/2.1 Modules");

}());