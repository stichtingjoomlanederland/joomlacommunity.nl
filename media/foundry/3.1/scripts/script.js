FD31.plugin("script", function($) {

/**
 * jquery.script
 * Script injection utility built on top $.Deferred() backbone.
 * https://github.com/jstonne/jquery.script
 *
 * Copyright (c) 2012 Jensen Tonne
 * www.jstonne.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

$.script = (function(){

    var self = function(options) {

        if (options===undefined) {
            return;
        }

        if (typeof options==="string") {
            options = {
                url: options
            }
        }

        var script = new Script(options);

        return script;
    };

    var Script = function(options) {

        var script = $.extend(this, options);

        script.manager = $.Deferred();

        $.extend(script, script.manager.promise());

        script.load();
    };

    var head = document.getElementsByTagName("head")[0],
        baseElement = document.getElementsByTagName("base")[0];

    $.extend(Script.prototype, {

        timeout: 7000,

        retry: 3,

        retryCount: 1,

        type: "text/javascript",

        async: false,

        charset: "UTF-8",

        verbose: false,

        insert: function() {

            var node = this.node;
            head.appendChild(node);
        },

        remove: function() {

            var node = this.node;

            // This prevents IE7-8 locking up.
            setTimeout(function(){

                // Handle memory leak in IE
                node.onload = node.onerror = node.onreadystatechange = null;

                try {
                    head.removeChild(node);
                } catch(e) {};

            }, 1000);
        },

        load: function() {

            var script = this,
                node;

            script.endTime = undefined;

            script.startTime = new Date();

            script.node = node = document.createElement('script');

            script.insert();

            // Create a reference to these proxied functions,
            // so that we can detach them from event listeners.
            script._ready = $.proxy(script.ready, script);
            script._error = $.proxy(script.error, script);

            // On IE9, addEventListener() does not necessary fire the onload event after
            // the script is loaded, attachEvent() method behaves correctly.
            if (node.attachEvent && !$.browser.opera) {
                node.attachEvent("onreadystatechange", script._ready);
                node.attachEvent("onerror"           , script._error); // IE9 only.
            } else {
                node.addEventListener("load"         , script._ready, false);
                node.addEventListener("error"        , script._error, false);
            }

            $(node).attr({
                type    : script.type,
                async   : script.async,
                charset : script.charset,
                src     : script.url
            });

            script.monitor();
        },

        monitor: function() {

            var script = this;

            if (script.retryCount > script.retry) {

                script._error();

                return;
            }

            setTimeout(function() {

                if (script.state()!=="resolved") {

                    if (script.verbose) {
                        console.warn('$.script: Load timeout. [Retry: ' + script.retryCount + ']', script);
                    }

                    script.remove();

                    script.retryCount++;

                    script.load();
                }

            }, script.timeout * script.retryCount);

        },

        ready: function(event) {

            var script = this,
                node = script.node;

            // if (script.verbose) {
            //     console.info('$.script: Loaded' + (($.browser.msie) ? ' ' + script.url.replace($.scriptPath, '') + ' ': ''), script);
            // }

            if (event.type==="load" || /loaded|complete/.test(node.readyState)) {

                script.complete.call(script, event);

                script.manager.resolve(script);
            }
        },

        error: function(event) {

            var script = this;

            if (script.verbose) {
                console.error('$.script: Unable to load ', script);
            }

            script.complete.call(script, event);

            script.remove();

            script.manager.reject(script);
        },

        complete: function(event) {

            var script = this,
                node = script.node;

            script.endTime = new Date();

            if (node.detachEvent && !$.browser.opera) {
                node.detachEvent("onreadystatechange", script._ready);
                node.detachEvent("onerror"           , script._error);
            } else {
                node.removeEventListener("load"      , script._ready, false);
                node.removeEventListener("error"     , script._error, false);
            }
        }

    });

    return self;

})();

});