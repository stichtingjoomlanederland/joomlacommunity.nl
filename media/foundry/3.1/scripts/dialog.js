(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
$.require() 
 .script("ui/position","easing") 
 .done(function() { 
var exports = function() { 

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

 $.template('dialog/default', '<div class="foundryDialog global"><div class="dialog-wrap"><div class="in"><div class="dialog-loader"><div class="loader-img"></div></div><div class="dialog-head"><span class="dialog-title"></span><a class="dialog-closeButton">Close</a></div><div class="dialog-body dialog-content"></div><div class="dialog-footer"><div class="dialog-loading"></div><div class="dialog-buttons"></div></div></div></div></div>');

$.Controller(
    'Dialog',
    {
        pluginName: "dialog",

        defaultOptions: {

            title: '',
            content: undefined,
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
                    minWidth: 0,
                    minHeight: 0,

                    // If the dialog content immediate element contains margin,
                    // the margin will bleed outside of the parent container
                    // if overflow: 'auto' is not assigned.
                    overflow: 'auto'
                }
            },

            loader: {
                width: 300,
                height: 100,
            },

            loading: "",

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

            escapeKey: true,

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
            dialogLoading: '.dialog-loading',
            "{closeButton}"  : '.dialog-closeButton'
        }
    },
    function(self){ return {
        ready: false,

        contentReady: false,

        init: function()
        {
            self.setInitOptions(self.options);

            self.initElement = self.element.clone();

            self.element.finalizeContent = function(){ return self.finalizeContent.apply(self, arguments) };

            // Experimental optimization
            // Make a reference to all the static elements on init.
            $.each([
                'dialogHead',
                'dialogBody',
                'dialogFooter',
                'dialogTitle',
                'dialogContent',
                'dialogButtons',
                'dialogLoader',
                'dialogLoading'
            ], function(i, name)
            {
                self[name] = self.find(self.options[name]);
            });

            if (self.options.content===undefined) {
                return;
            }

            self.display();
        },

        "{self} destroyed": function() {
            $(document).off("keydown.foundry.dialog");
        },

        setInitOptions: function(options)
        {
            self.initOptions = $.extend(true, {}, options);

            // Remove callbacks
            $.each(['beforeShow', 'afterShow', 'beforeHide', 'afterHide'], function(i, name){
                self.initOptions[name] = function(){};
            });

            $.each(['title', 'content', 'buttons', 'loading', 'selectors', 'bindings'], function(i, name){
                self.initOptions[name] = null;
            });
        },

        // Update is called during subsequent $.dialog() calls.
        // A default behaviour of any controller class.
        update: function(options)
        {
            if (options)
            {
                // Prevent binder from updating dialog
                if (options.hasOwnProperty("{binder}")) return;

                var options = $.extend(true, {}, self.initOptions, options);

                // Remember certain options like width, height throughout
                // the lifetime of the dialog, but discard those that aren't
                // like title, content, buttons, selectors, bindings & transitions.
                self.setInitOptions(options);

                self.display(options);
            }

            return self;
        },

        determineContentType: function(content) {

            if (/<dialog>(.*?)/.test(content)) {
                return 'dialog';
            }

            if ($.isUrl(content)) {
                return 'iframe';
            }

            if ($.isDeferred(content)) {
                return 'deferred';
            }

            return 'html';
        },

        displayQueue: [],

        display: function(options)
        {

            if (self.resizing) {
                return self.displayQueue.push(options);
            }

            if ($.isPlainObject(options)) {
                self.options = $.extend(true, {}, self.Class.defaults, options);
            }

            if (!self.ready)
            {
                // TODO: Overlay should be part of dialog template
                if (self.options.showOverlay)
                    self.createOverlay();
            }

            var options = self.options,
                content = options.content;

            // Determine if content is ready
            self.contentReady = content===null;

            // Determine content type
            self.contentType = self.determineContentType(content);

            // Normalize transition
            var transition = options.transition;
            if ($.isString(transition)) {
                options.transition = { show: transition, hide: transition };
            }

            switch (self.contentType)
            {
                case 'html':
                    self.hideLoader();
                    self.show();
                    break;

                case 'iframe':
                    var iframe = $(document.createElement('iframe')),
                        iframeUrl = content,
                        iframeCss = options.iframe.css,
                        iframeButtons = options.buttons,
                        dialogContent = self.dialogContent,
                        iframeOptions = $.extend(true, {}, options, {
                            content: iframe,
                            buttons: iframeButtons
                        });

                    self.showLoader(function()
                    {
                        onIframeLoaded = (function()
                        {
                            return function(event)
                            {
                                // Expose dialog object to iframe
                                // Inside a try catch because does not work on cross-site domain,
                                // and url checking takes a lot more code to write.
                                try { iframe[0].contentWindow.parentDialog = self; } catch(err) {};

                                self.update(iframeOptions);
                            };
                        })();

                        // Prevent scrollbar from showing in loading dialog
                        self.dialogBody
                            .addClass('type-iframe');

                        iframe
                            .appendTo(dialogContent)
                            .css(iframeCss)
                            .css({position: 'absolute', visibility: 'hidden'})
                            .one('load', onIframeLoaded)
                            .attr('src', iframeUrl);
                    });

                    break;

                case 'deferred':
                    var ajax = content;

                    self.showLoader(function() {

                        ajax.done(function(html) {

                            if (!html) return;

                            var newOptions = ($.isString(html)) ? {content: html} : html;

                            // Do not proceed if there are no new content to update.
                            if (!newOptions.content) return;

                            self.update($.extend(true, {}, options, newOptions));
                        })
                        .fail(function(message, type){

                            var title =
                                    $.isString(type) ?
                                        $.String.capitalize(type) :
                                        "Error",

                                content =
                                    $.isString(message) ?
                                        message :
                                        "There was an error loading content.";

                            self.update({
                                title: title,
                                content: content
                            });
                        });
                    });
                    break;


                // Test with:
                // '<dialog><after_show type="javascript">function(){console.log("after_show_executed");}</after_show><selectors type="json">{"{OKButton}": ".okButton"}</selectors><width>400</width><height>200</height><show_overlay>0</show_overlay><title>foobar_title</title><content>foobar_content</content><buttons>foobar_buttons</buttons></dialog>'

                case 'dialog':
                    var xmlOptions = $.buildHTML(content),
                        newOptions = {};

                    $.each(xmlOptions.children(), function(i, node){

                        var node = $(node),
                            key  = $.String.camelize(this.nodeName.toLowerCase()),
                            val  = node.html(),
                            type = node.attr("type");

                        switch (type) {
                            case "json":
                                try {
                                    val = $.parseJSON(val);
                                } catch(e) {};
                                break;

                            case "javascript":
                                try {
                                    val = eval('(function($){ return ' + $.trim(val) + ' })(' + $.globalNamespace + ')');
                                } catch(e) {};
                                break;

                            case "text":
                                val = node.text();
                                break;
                        }

                        // Automatically convert numerical values
                        if ($.isNumeric(val)) val = parseFloat(val);

                        // console.log(type, key, val);

                        newOptions[key] = val;
                    });

                    self.update($.extend(true, {}, options, newOptions));
                    break;
            }
        },

        loading: function(flag) {

            var el = self.element;

            if (flag===undefined) {
                return el.hasClass("loading");
            }

            if (flag===false) {
                el.removeClass("loading");
                return;
            }

            if (flag===true) {
                flag = self.options.loading || "";
            }

            if ($.isString(flag)) {
                el.addClass("loading");
                self.dialogLoading.html(flag);
            }
        },

        showLoader: function(callback)
        {
            var originalWidth = self.options.width,
                originalHeight = self.options.height;

            var showLoaderOverlay = function()
            {
                self.dialogLoader
                    .show()
                    .css(
                    {
                        width: self.dialogBody.outerWidth(true),
                        height: self.dialogBody.outerHeight(true)
                    })
                    .position({
                        my: 'top left',
                        at: 'top left',
                        of: self.options.dialogContent
                    });

                // Reset width & height
                self.options.width = originalWidth;
                self.options.height = originalHeight;

                return callback && callback();
            };

            return (self.ready) ? showLoaderOverlay() : self.update(
            $.extend({
                title: self.options.title,
                content: '',
                width: self.initOptions.body.css.minWidth,
                height: self.initOptions.body.css.minHeight,
                afterShow: showLoaderOverlay
            }, self.options.loader));
        },

        hideLoader: function()
        {
            self.element.removeClass("loading");
            self.dialogLoader.hide();
        },

        autoSize: function()
        {
            return self.initOptions.css.width=='auto' || self.initOptions.css.height=='auto';
        },

        finalizeContent: function(finale)
        {
            var options = self.options,
                content = options.content;

            self.element
                .css(options.css);

            self.dialogContent
                .css(options.body.css);

            if (self.refreshing) return;

            if (options.title!==null)
                self.dialogTitle
                    .html(options.title);

            if (content!==null)
            {
                var isIframe = !$.isString(content) && $(content).is('iframe');

                self.dialogBody
                    .toggleClass('type-iframe', isIframe);

                if (isIframe)
                {
                    // This is a replacement for jQuery's .siblings().
                    // The difference is that this code will go through
                    // not only html elements but also other types of dom nodes,
                    // e.g. text nodes.
                    var parent = content.parent()[0];

                    var i = 0;
                    while(parent.childNodes.length > 1)
                    {
                        var node = parent.childNodes[i];
                        if (node!==content[0])
                            parent.removeChild(node);
                        i++;
                    }

                    content
                        .css({position: 'relative', visibility: 'visible'});
                } else {

                    // Finalize content might be called multiple times during different
                    // transition states. But only the last one should be inserted
                    // with the embedded scripts.

                    var finalContent;

                    if (finale) {
                        finalContent = content;

                        // Store the stripped version into the options
                        options.content = self.stripScript(finalContent);
                    } else {
                        finalContent = self.stripScript(content);
                    }

                    // This is inserted with scripts
                    self.dialogContent
                        .html(finalContent);
                }
            }

            if (!self.contentReady)
                self.finalizeButtons();

            return self.element;
        },

        stripScript: function(html) {

            if (!$.isString(html)) return html;

            var content = $($.parseHTML(html));

            content.find("script").remove();

            return content;
        },

        finalizeSize: function(fast)
        {
            var refElement = self.refElement,
                options = self.options,
                body = options.body,
                content = options.content,
                css = options.css,
                bodyCss = body.css;

            // Create a new ref element
            if (refElement===undefined) {
                refElement = self.refElement = self.initElement.clone().removeClass('global').insertAfter(self.element);

            // Because it could be detached at this moment
            } else {
                refElement.insertAfter(self.element);
            }

            if (!fast)
            {
                refTitle   = refElement.find(options.dialogTitle);
                refBody    = refElement.find(options.dialogBody);
                refFooter  = refElement.find(options.dialogFooter);
                refButtons = refElement.find(options.dialogButtons);

                refElement
                    .css(self.initOptions.css)
                    .css({display: 'block'});

                refTitle
                    .html(options.title);

                if (!$.isEmptyObject(options.buttons))
                {
                    refFooter.show();
                    refButtons.append('<button>test</button>');
                } else {
                    refFooter.hide();
                }

                // Pass 1: Readjust dialog body's dimension based on dialog's content
                var isIframe = !$.isString(content) && $(content).is('iframe');

                var refContent = (isIframe) ? document.createElement('div') : self.stripScript(content);

                bodyCss.width  = (isIframe && options.width=='auto')  ? content.contents().width() : options.width;
                bodyCss.height = (isIframe && options.height=='auto') ? content.contents().height() : options.height;

                refBody
                    .css(bodyCss)
                    .toggleClass('type-iframe', isIframe)
                    .html(refContent);

                var refBodyWidth = refBody.width(),
                    refBodyHeight = refBody.height();

                if (refBody.css("boxSizing")!=="border-box") {
                    bodyCss.width  = refBodyWidth;
                    bodyCss.height = refBodyHeight;
                }

                if (bodyCss.width < bodyCss.minWidth) {
                    bodyCss.width = bodyCss.minWidth;
                }

                if (bodyCss.height < bodyCss.minHeight) {
                    bodyCss.height = bodyCss.minHeight;
                }

                // Pass 2: Re-adjust dialog's dimension based on window's dimension
                var offset         = options.offset,
                    width          = refElement.width(),
                    height         = refElement.height(),
                    maxWidth       = $(window).width() - offset,
                    maxHeight      = $(window).height() - offset,
                    widthExceeded  = width > maxWidth,
                    heightExceeded = height > maxHeight;

                css.width  = (widthExceeded) ? maxWidth : width;
                css.height = (heightExceeded) ? maxHeight : height;

                // Pass 3: Readjust dialog body's dimension based on readjusted dialog's dimension

                if (bodyCss.width!=="auto") bodyCss.width  -= (width  - css.width);
                if (bodyCss.width <= 0) bodyCss.width = "auto";

                if (bodyCss.height!=="auto") bodyCss.height -= (height - css.height);
                if (bodyCss.height <= 0) bodyCss.height = "auto";

                bodyCss.minWidth = bodyCss.minHeight = 'auto';

                // Pass 4: Decide scrollbar visiblity based on readjusted dialog body's dimension.
                refBody.css(bodyCss);

                bodyCss.overflowX = (!widthExceeded  || isIframe || refBody[0].scrollWidth  <= bodyCss.width)  ? 'auto' : 'scroll';
                bodyCss.overflowY = (!heightExceeded || isIframe || refBody[0].scrollHeight <= bodyCss.height) ? 'auto' : 'scroll';

                // Clean up
                refBody.html('');

            }

            // Pass 5: Readjust position based on final dialog dimension
            refElement
                .css(css)
                //  FF3 can't retrieve css positions when element is on display: none;
                // .initialPosition(options.position, true);
                .css('visibility', 'hidden')
                .position(options.position);

            css.top  = refElement.css('top');
            css.left = refElement.css('left')
        },

        finalizeButtons: function()
        {
            var dialogFooter  = self.dialogFooter,
                dialogButtons = self.dialogButtons;

            dialogButtons.empty();

            var buttons = self.options.buttons;

            // No buttons
            if ($.isEmptyObject(buttons)) {
                dialogFooter.hide();
                return;
            }

            // Buttons as html
            if ($.isString(buttons)) {
                dialogButtons.html(buttons);
            }

            // Programmable buttons
            if ($.isArray(buttons)) {
                $.each(buttons, function(i, button) {
                    var events = $.extend({}, button),
                        classNames  = button.classNames ? button.classNames : '';
                    delete events.name;
                    delete events.classNames;

                    $(document.createElement('button'))
                        .attr('type', 'button')
                        .addClass( classNames )
                        .html(button.name)
                        .bind(events)
                        .appendTo(dialogButtons);
                });
            }

            dialogFooter.show();
        },

        show: function(callback)
        {
            if (self.ready && self.resizing) return;

            var options = self.options;

            if (self.refElement) {
                self.refElement.remove();
            }

            if (!self.contentReady) {
                options.beforeShow.apply(self);
            }

            $(document).off("keydown.foundry.dialog");

            if (options.escapeKey) {
                $(document).on("keydown.foundry.dialog", function(event){
                    if (event.keyCode==27) {
                        self.close();
                    }
                });
            }

            self.resizing = true;

            self.finalizeSize();

            self.element.addClass('resizing');

            $(window).trigger("dialogTransitionStart");

            self.transition[options.transition.show].show
                .apply(self, [function()
                {
                    if (!self.ready)
                    {
                        self.on("resize.dialog scroll.dialog", window, $._.debounce(function(){ self.refresh() }, 150));
                        self.ready = true;
                    }

                    if (!self.contentReady)
                    {
                        if (callback) callback.apply(self);
                        options.afterShow.apply(self);
                    }

                    // Detach shadow dialog
                    self.refElement.detach();

                    self.element.removeClass('resizing');

                    self.contentReady = true;

                    // Let the dialog container wrap to dialog content's final natural size,
                    // so we can avoid all the tedious box model issues.
                    self.element.css({width: 'auto', height: 'auto'});

                    // Remove old binder
                    self.removePlugin("binder");

                    // Create new bindings
                    var selectors = options.selectors,
                        bindings  = options.bindings;

                    if (selectors && bindings) {

                        var binder = $.Controller(
                            $.uid("dialogBinder"),
                            {defaultOptions: selectors},
                            function(self){ return bindings }
                        );

                        self.addPlugin("binder", binder);
                    }

                    if (self.displayQueue.length > 0)
                    {
                        setTimeout(function(){
                            self.resizing = false;
                            self.display(self.displayQueue.shift());
                        }, 500);
                    } else {
                        self.resizing = false;

                        $(window).trigger("dialogTransitionEnd");
                    }

                }]);
        },

        "{self} removePlugin": function(el, event, name, plugin) {

            if (plugin===undefined) return;

            delete $.Controller[plugin.Class.fullName];
        },

        hide: function(callback)
        {
            if (!self.contentReady)
                self.options.beforeHide.apply(self);

            $(window).trigger("dialogTransitionStart");

            self.transition[self.options.transition.hide].hide
                .apply(self, [function()
                {
                    if (callback) callback.apply(self);
                    self.options.afterHide.apply(self);

                    $(window).trigger("dialogTransitionEnd");
                }]);
        },

        refresh: function()
        {
            if (self.closing) return;

            if (self.resizing) return;

            self.finalizeSize(true);

            self.resizing = true;

            self.refreshing = true;

            $(window).trigger("dialogTransitionStart");

            self.transition[self.options.transition.show].show.apply(self, [function()
            {
                $(window).trigger("dialogTransitionEnd");

                self.resizing = false;

                self.refreshing = false;

                // Let the dialog container wrap to dialog content's final natural size,
                // so we can avoid all the tedious box model issues.
                self.element.css({width: 'auto', height: 'auto'});
            }]);
        },

        // TODO: Transition parameters
        transition:
        {
            none: {
                show: function(callback)
                {
                    // TODO: Fix IE7 z-index issue.

                    if (!self.ready)
                        self.overlay.show();

                    self.element
                        .finalizeContent()
                        .show(0, callback);
                },

                hide: function(callback)
                {
                    self.overlay.hide();
                    self.element.hide(0, callback);
                }
            },

            fade: {
                show: function(callback)
                {
                    if (!self.ready)
                        self.overlay.fadeOut(0).fadeIn('normal');

                    if (!self.contentReady)
                    {
                        self.element
                            .fadeOut((!self.contentReady) ? 0 : 'fast', function()
                            {
                                self.element
                                    .finalizeContent()
                                    .fadeIn('normal', 'easeInCubic', callback);
                            });

                    } else {
                        self.element.finalizeContent();
                        return callback && callback();
                    }
                },

                hide: function(callback)
                {
                    self.overlay.fadeOut('normal', 'easeOutCubic');
                    self.element.fadeOut('normal', 'easeOutCubic', callback);
                }
            },

            zoom: {
                show: function(callback)
                {
                    var dialogBody = self.dialogBody,
                        dialogFooter = self.dialogFooter,
                        css = self.options.css;

                    if (!self.ready)
                    {
                        self.overlay.fadeIn('fast');

                        self.element
                            .finalizeContent()
                            .css(
                            {
                                top: parseInt(css.top) + (parseInt(css.height) / 2),
                                left: parseInt(css.left) + (parseInt(css.width) / 2),
                                width: 0,
                                height: 0
                            });
                    }

                    if (!self.contentReady)
                    {
                        dialogBody.css({opacity: 0});
                        dialogFooter.css({opacity: 0});
                    }

                    self.element
                        .animate(
                        {
                            top   : css.top,
                            left  : css.left,
                            width : css.width,
                            height: css.height
                        }, 'normal', 'easeInCubic',
                        function()
                        {
                            self.element.finalizeContent(true);

                            dialogBody.css({opacity: 1});
                            dialogFooter.css({opacity: 1});

                            // TODO: Circular reference. IE8 show() callback gets executed when closing. Double check if its fixed.
                            return callback && callback();
                        });
                },
                hide: function(callback)
                {
                    var css = self.options.css;

                    self.element
                        .animate(
                            {
                                top: parseInt(css.top) + (parseInt(css.height) / 2),
                                left: parseInt(css.left) + (parseInt(css.width) / 2),
                                width: 0,
                                height: 0
                            }, 'normal', 'easeOutCubic',
                            function()
                            {
                                self.overlay.fadeOut('fast');
                                return callback && callback();
                            });
                }
            }
        },

        overlay: $('<div></div>'),

        createOverlay: function()
        {
            self.overlay
                .css(self.options.overlay.css)
                .click(function()
                {
                    self.close();
                })
                .appendTo('body');
        },

        closing: false,

        close: function()
        {
            if (self.closing) return;

            self.closing = true;

            $(document).off("keydown.foundry.dialog");

            self.hide(function()
            {
                self.removePlugin("binder");
                self.element.remove();
                self.refElement && self.refElement.remove();
            });

            return self;
        },

        "{closeButton} click": function()
        {
            self.close();
        },

        content: function()
        {
            var dialogContent = self.dialogContent;
            var iframe = dialogContent.find("> iframe");
            return (iframe.length > 0) ? iframe.contents() : dialogContent;
        },

        messageGroup: function() {

            var dialogBody = self.dialogBody,
                messageGroup = dialogBody.find("> [data-message-group]");

            if (messageGroup.length < 1) {
                messageGroup = $("<div data-message-group></div>").prependTo(dialogBody);
            }

            return messageGroup;
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

        controller = dialog.addController('Dialog', options);
    }

    return controller;
};

}; 

exports(); 
module.resolveWith(exports); 

}); 
// module body: end

}; 
// module factory: end

FD31.module("dialog", moduleFactory);

}());