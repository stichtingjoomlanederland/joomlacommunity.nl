/**
 * Akeeba Backup
 *
 * @package    akeeba
 * @copyright  Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license    GNU GPL version 3 or, at your option, any later version
 **/

if (typeof akeeba == 'undefined')
{
    var akeeba = {};
}

if (typeof akeeba.System == 'undefined')
{
    akeeba.System = {};
    akeeba.System.notification = {
        iconURL:                ''
    };
    akeeba.System.params = {
        AjaxURL:                '',
        useIFrame:              false,
        errorCallback:          akeeba.System.defaultErrorHandler,
        iFrame:                 null,
        iFrameCallbackError:    null,
        iFrameCallbackSuccess:  null,
        password:				''
    };
    akeeba.System.translations = [];
    akeeba.System.modalDialog = null;
}

(function($){
    /**
     * An extremely simple error handler, dumping error messages to screen
     *
     * @param  error  The error message string
     */
    akeeba.System.defaultErrorHandler = function (error)
    {
        alert ("An error has occurred\n" + error);
    };

    /**
     * Poor man's AJAX, using IFRAME elements
     *
     * @param  data             An object with the query data, e.g. a serialized form
     * @param  successCallback  A function accepting a single object parameter, called on success
     */
    akeeba.System.doIframeCall = function(data, successCallback, errorCallback)
    {
        akeeba.System.params.iFrameCallbackSuccess = successCallback;
        akeeba.System.params.iFrameCallbackError = errorCallback;
        akeeba.System.params.iFrame = document.createElement('iframe');

        $(akeeba.System.params.iFrame)
            .css({
                'display'		: 'none',
                'visibility'	: 'hidden',
                'height'		: '1px'
            })
            .attr('onload','akeeba.System.iframeCallback')
            .insertAfter('#response-timer');

        var url = akeeba.System.params.AjaxURL + '&' + $.param(data);

        $(akeeba.System.params.iFrame).attr('src',url);
    };

    /**
     * Poor man's AJAX, using IFRAME elements: the callback function
     */
    akeeba.System.iframeCallback = function()
    {
        // Get the contents of the iFrame
        var iframeDoc = null;

        if (akeeba.System.params.iFrame.contentDocument)
        {
            iframeDoc = akeeba.System.params.iFrame.contentDocument; // The rest of the world
        }
        else
        {
            iframeDoc = akeeba.System.params.iFrame.contentWindow.document; // IE on Windows
        }

        var msg = iframeDoc.body.innerHTML;

        // Dispose of the iframe
        $(akeeba.System.params.iFrame).remove();

        akeeba.System.params.iFrame = null;

        // Start processing the message
        var junk = null;
        var message = "";

        // Get rid of junk before the data
        var valid_pos = msg.indexOf('###');

        if( valid_pos == -1 )
        {
            // Valid data not found in the response
            msg = 'Invalid AJAX data: ' + msg;

            if(akeeba.System.params.iFrameCallbackError == null)
            {
                if(akeeba.System.params.errorCallback != null)
                {
                    akeeba.System.params.errorCallback(msg);
                }
            }
            else
            {
                akeeba.System.params.iFrameCallbackError(msg);
            }

            return;
        } else if( valid_pos != 0 )
        {
            // Data is prefixed with junk
            junk = msg.substr(0, valid_pos);
            message = msg.substr(valid_pos);
        }
        else
        {
            message = msg;
        }

        message = message.substr(3); // Remove triple hash in the beginning

        // Get of rid of junk after the data
        var valid_pos = message.lastIndexOf('###');
        message = message.substr(0, valid_pos); // Remove triple hash in the end

        try
        {
            var data = JSON.parse(message);
        }
        catch(err)
        {
            var msg = err.message + "\n<br/>\n<pre>\n" + message + "\n</pre>";

            if(akeeba.System.params.iFrameCallbackError == null)
            {
                if(akeeba.System.params.errorCallback != null)
                {
                    akeeba.System.params.errorCallback(msg);
                }
            }
            else
            {
                akeeba.System.params.iFrameCallbackError(msg);
            }

            return;
        }

        // Call the callback function
        akeeba.System.params.iFrameCallbackSuccess(data);
    };

    /**
     * Performs an AJAX request and returns the parsed JSON output.
     * akeeba.System.params.AjaxURL is used as the AJAX proxy URL.
     * If there is no errorCallback, the global akeeba.System.params.errorCallback is used.
     *
     * @param  data             An object with the query data, e.g. a serialized form
     * @param  successCallback  A function accepting a single object parameter, called on success
     * @param  errorCallback    A function accepting a single string parameter, called on failure
     * @param  useCaching       Should we use the cache?
     * @param  timeout          Timeout before cancelling the request (default 60s)
     */
    akeeba.System.doAjax = function (data, successCallback, errorCallback, useCaching, timeout)
    {
        if (akeeba.System.params.useIFrame)
        {
            akeeba.System.doIframeCall(data, successCallback, errorCallback);

            return;
        }

        if (useCaching == null)
        {
            useCaching = true;
        }

        // We always want to burst the cache
        var now = new Date().getTime() / 1000;
        var s = parseInt(now, 10);
        var microtime = Math.round((now - s) * 1000) / 1000;
        data._cacheBustingJunk = microtime;

        if(timeout == null)
        {
            timeout = 600000;
        }

        var structure =
        {
            type: "POST",
            url: akeeba.System.params.AjaxURL,
            cache: false,
            data: data,
            timeout: timeout,
            success: function(msg)
            {
                // Initialize
                var junk = null;
                var message = "";

                // Get rid of junk before the data
                var valid_pos = msg.indexOf('###');

                if (valid_pos == -1)
                {
                    // Valid data not found in the response
                    msg = akeeba.System.sanitizeErrorMessage(msg);
                    msg = 'Invalid AJAX data: ' + msg;

                    if (errorCallback == null)
                    {
                        if(akeeba.System.params.errorCallback != null)
                        {
                            akeeba.System.params.errorCallback(msg);
                        }
                    }
                    else
                    {
                        errorCallback(msg);
                    }

                    return;
                }
                else if( valid_pos != 0 )
                {
                    // Data is prefixed with junk
                    junk = msg.substr(0, valid_pos);
                    message = msg.substr(valid_pos);
                }
                else
                {
                    message = msg;
                }

                message = message.substr(3); // Remove triple hash in the beginning

                // Get of rid of junk after the data
                var valid_pos = message.lastIndexOf('###');
                message = message.substr(0, valid_pos); // Remove triple hash in the end

                try
                {
                    var data = JSON.parse(message);
                }
                catch(err)
                {
                    message = akeeba.System.sanitizeErrorMessage(message);
                    var msg = err.message + "\n<br/>\n<pre>\n" + message + "\n</pre>";

                    if (errorCallback == null)
                    {
                        if(akeeba.System.params.errorCallback != null)
                        {
                            akeeba.System.params.errorCallback(msg);
                        }
                    }
                    else
                    {
                        errorCallback(msg);
                    }

                    return;
                }

                // Call the callback function
                successCallback(data);
            },
            error: function(Request, textStatus, errorThrown)
            {
                var text = Request.responseText ? Request.responseText : '';
                var message = '<strong>AJAX Loading Error</strong><br/>HTTP Status: '+Request.status+' ('+Request.statusText+')<br/>';

                message = message + 'Internal status: '+textStatus+'<br/>';
                message = message + 'XHR ReadyState: ' + Request.readyState + '<br/>';
                message = message + 'Raw server response:<br/>' + akeeba.System.sanitizeErrorMessage(text);

                if (errorCallback == null)
                {
                    if(akeeba.System.params.errorCallback != null)
                    {
                        akeeba.System.params.errorCallback(message);
                    }
                }
                else
                {
                    errorCallback(message);
                }
            }
        };

        if(useCaching)
        {
            var ajaxManager = window.jQuery.manageAjax;

            if (typeof ajaxManager == "undefined")
            {
                ajaxManager = $.manageAjax;
            }

            if (typeof ajaxManager == "undefined")
            {
                ajaxManager = akeeba.jQuery.manageAjax;
            }

            ajaxManager.add('akeeba-ajax-profile', structure);
        }
        else
        {
            akeeba.jQuery.ajax( structure );
        }
    };

    /**
     * Sanitize a message before displaying it in an error dialog. Some servers return an HTML page with DOM modifying
     * JavaScript when they block the backup script for any reason (usually with a 5xx HTTP error code). Displaying the
     * raw response in the error dialog has the side-effect of killing our backup resumption JavaScript or even completely
     * destroy the page, making backup restart impossible.
     *
     * @param {string} msg The message to sanitize
     *
     * @returns {string}
     */
    akeeba.System.sanitizeErrorMessage = function(msg)
    {
        if (msg.indexOf("<script") > -1)
        {
            msg = "(HTML containing script tags)";
        }

        return msg;
    };

    /**
     * Performs an AJAX request to the restoration script (restore.php)
     * @param data
     * @param successCallback
     * @param errorCallback
     * @return
     */
    akeeba.System.doEncryptedAjax = function (data, successCallback, errorCallback)
    {
        json = JSON.stringify(data);

        if (akeeba.System.params.password.length > 0)
        {
            json = AesCtr.encrypt(json, akeeba.System.params.password, 128);
        }

        var post_data = { json: json };

        var structure =
        {
            type:    "POST",
            url:     akeeba.System.params.AjaxURL,
            cache:   false,
            data:    post_data,
            timeout: 600000,
            success: function (msg)
            {
                // Initialize
                var junk = null;
                var message = "";

                // Get rid of junk before the data
                var valid_pos = msg.indexOf('###');

                if (valid_pos == -1)
                {
                    // Valid data not found in the response
                    msg = 'Invalid AJAX data: ' + msg;

                    if (errorCallback == null)
                    {
                        if (akeeba.System.params.errorCallback != null)
                        {
                            akeeba.System.params.errorCallback(msg);
                        }
                    }
                    else
                    {
                        errorCallback(msg);
                    }

                    return;
                }
                else if (valid_pos != 0)
                {
                    // Data is prefixed with junk
                    junk = msg.substr(0, valid_pos);
                    message = msg.substr(valid_pos);
                }
                else
                {
                    message = msg;
                }

                message = message.substr(3); // Remove triple hash in the beginning

                // Get of rid of junk after the data
                valid_pos = message.lastIndexOf('###');
                message = message.substr(0, valid_pos); // Remove triple hash in the end

                // Decrypt if required
                if (akeeba.System.params.password.length > 0)
                {
                    message = AesCtr.decrypt(message, akeeba.System.params.password, 128);
                }

                try
                {
                    var data = JSON.parse(message);
                }
                catch (err)
                {
                    errorMessage = err.message + "\n<br/>\n<pre>\n" + message + "\n</pre>";
                    if (errorCallback == null)
                    {
                        if (akeeba.System.params.errorCallback != null)
                        {
                            akeeba.System.params.errorCallback(errorMessage);
                        }
                    }
                    else
                    {
                        errorCallback(errorMessage);
                    }
                    return;
                }

                // Call the callback function
                successCallback(data);
            },
            error:   function (Request, textStatus, errorThrown)
            {
                var message = 'AJAX Loading Error: ' + textStatus;
                if (errorCallback == null)
                {
                    if (akeeba.System.params.errorCallback != null)
                    {
                        akeeba.System.params.errorCallback(message);
                    }
                }
                else
                {
                    errorCallback(message);
                }
            }
        };

        $.ajax(structure);
    };


    /**
     * Creates a modal box based on the data object. The keys to this object are:
     * - title          The title of the modal dialog, skip to not create a title
     * - body           The body content of the dialog. Not applicable if href is defined
     * - href           A URL to open in an IFrame inside the body
     * - iFrameHeight   The height of the IFrame, applicable if href is set
     * - iFrameWidth    The width of the IFrame, applicable if href is set
     * - OkLabel        The label of the OK (primary) button
     * - CancelLabel    The label of the Cancel button
     * - OkHandler      Run this when the OK button is pressed, before closing the modal
     * - CancelHandler  Run this when the Cancel button is pressed, after closing the modal
     * - showButtons    Set to false to not show the buttons
     *
     * Alternatively you can pass a reference to an element. In this case we expect that the rel attribute of the element
     * contains a JSON-encoded string of the data object.
     *
     * @param   data  The configuration data (see above)
     */
    akeeba.System.modal = function(data)
    {
        try {
            rel = $(data).attr('rel');
            data = JSON.parse(rel);
        }
        catch (e)
        {}

        // Outer modal markup
        var modalWrapper = $(document.createElement('div')).addClass('akeeba-bootstrap modal fade').attr({
            tabindex: '-1', role: 'dialog', 'aria-hidden': true
        });
        var modalDialog = $(document.createElement('div')).addClass('modal-dialog').appendTo(modalWrapper);
        var modalContent = $(document.createElement('div')).addClass('modal-content').appendTo(modalDialog);

        // Modal Header
        if (typeof(data.title) !== 'undefined')
        {
            var modalHeader = $(document.createElement('div')).addClass('modal-header').appendTo(modalContent);
            var headerCloseButton = $(document.createElement('button')).addClass('close').attr({
                'data-dismiss': "modal", 'aria-hidden': true
            }).html('&times;').appendTo(modalHeader);
            var modalHeaderTitle = $(document.createElement('h4')).addClass('modal-title').html(data.title)
                .appendTo(modalHeader);

            // Assign events
            if (typeof(data.CancelHandler) !== 'undefined')
            {
                headerCloseButton.click(function(e){
                    var callback = data.CancelHandler;
                    modalWrapper.modal('hide');
                    callback(modalWrapper);
                    e.preventDefault();
                })
            }
        }

        // Modal body
        var modalBody = $(document.createElement('div')).addClass('modal-body').appendTo(modalContent);

        if (typeof(data.href) === 'undefined')
        {
            // HTML body
            modalBody.html(data.body);
        }
        else if(data.href.substr(0, 1) == '#')
        {
            $(data.href).clone().appendTo(modalBody);
        }
        else
        {
            var iFrame = $(document.createElement('iframe')).attr({
                src: data.href,
                width: '100%',
                height: 400,
                frameborder: 0,
                allowtransparency: "true"
            }).appendTo(modalBody);

            if (typeof(data.iFrameHeight) !== 'undefined')
            {
                iFrame.attr('height', data.iFrameHeight);
            }

            if (typeof(data.iFrameWidth) !== 'undefined')
            {
                iFrame.attr('width', data.iFrameWidth);
            }
        }

        // Should I show the buttons?
        var showButtons = true;

        if (typeof(data.showButtons) !== 'undefined')
        {
            showButtons = data.showButtons;
        }

        // Modal buttons
        if (showButtons)
        {
            // Create the modal footer
            var modalFooter = $(document.createElement('div')).addClass('modal-footer').appendTo(modalContent);

            // Get the button labels
            var okLabel = akeeba.System.translations['UI-MODAL-OK'];
            var cancelLabel = akeeba.System.translations['UI-MODAL-CANCEL'];

            if (typeof(data.OkLabel) !== 'undefined')
            {
                okLabel = data.OkLabel;
            }

            if (typeof(data.CancelLabel) !== 'undefined')
            {
                cancelLabel = data.CancelLabel;
            }

            // Create buttons
            var cancelButton = $(document.createElement('button')).addClass('btn btn-default').attr({
                type: 'button', 'data-dismiss': 'modal'
            }).html(cancelLabel).appendTo(modalFooter);

            var okButton = $(document.createElement('button')).addClass('btn btn-primary').attr({
                type: 'button'
            }).html(okLabel).appendTo(modalFooter);

            // Assign handlers
            if (typeof(data.CancelHandler) !== 'undefined')
            {
                cancelButton.click(function(e){
                    var callback = data.CancelHandler;
                    modalWrapper.modal('hide');
                    callback(modalWrapper);
                    e.preventDefault();
                })
            }

            if (typeof(data.OkHandler) !== 'undefined')
            {
                okButton.click(function(e){
                    var callback = data.OkHandler;
                    modalWrapper.modal('hide');
                    callback(modalWrapper);
                    e.preventDefault();
                })
            }
            else
            {
                okButton.click(function(e){
                    modalWrapper.modal('hide');
                    e.preventDefault();
                });
            }

            // Hide unnecessary buttons
            if (okLabel.trim() == '')
            {
                okButton.css('display', 'none');
            }

            if (cancelLabel.trim() == '')
            {
                cancelButton.css('display', 'none');
            }
        }

        // Show modal
        akeeba.System.modalDialog = modalWrapper;

        modalWrapper.modal({
            keyboard: false,
            backdrop: 'static'
        });
    };

    /**
     * Requests permission for displaying desktop notifications
     */
    akeeba.System.notification.askPermission = function()
    {
        if (window.Notification == undefined)
        {
            return;
        }

        if (window.Notification.permission == 'default')
        {
            window.Notification.requestPermission();
        }
    };

    /**
     * Displays a desktop notification with the given title and body content. Chrome and Firefox will display our custom
     * icon in the notification. Safari will not display our custom icon but will place the notification in the iOS /
     * Mac OS X notification centre. Firefox displays the icon to the right of the notification and its own icon on the
     * left hand side. It also plays a sound when the notification is displayed. Chrome plays no sound and displays only
     * our icon on the left hand side.
     *
     * The notifications have a default timeout of 5 seconds. Clicking on them, or waiting for 5 seconds, will dismiss
     * them. You can change the timeout using the timeout parameter. Set to 0 for a permanent notification.
     *
     * @param  title        string  The title of the notification
     * @param  bodyContent  string  The body of the notification (optional)
     */
    akeeba.System.notification.notify = function(title, bodyContent, timeout)
    {
        if (window.Notification == undefined)
        {
            return;
        }

        if (window.Notification.permission != 'granted')
        {
            return;
        }

        if (timeout == undefined)
        {
            timeout = 5000;
        }

        if (bodyContent == undefined)
        {
            body = '';
        }

        var n = new window.Notification(title, {
            'body': bodyContent,
            'icon': akeeba.System.notification.iconURL
        });

        if (timeout > 0)
        {
            setTimeout(function(notification) {
                return function()
                {
                    notification.close();
                }
            }(n), timeout);
        }
    };

//=============================================================================
// 							I N I T I A L I Z A T I O N
//=============================================================================
//Custom no easing plug-in
    $.extend($.easing, {
        none: function(fraction, elapsed, attrStart, attrDelta, duration) {
            return attrStart + attrDelta * fraction;
        }
    });

    $(document).ready(function(){
        // Create an AJAX manager
        var ajaxManager = window.jQuery.manageAjax;

        if (typeof ajaxManager == "undefined")
        {
            var ajaxManager = $.manageAjax;
        }

        if (typeof ajaxManager == "undefined")
        {
            var ajaxManager = akeeba.jQuery.manageAjax;
        }

        var akeeba_ajax_manager = ajaxManager.create('akeeba_ajax_profile', {
            queue: true,
            abortOld: false,
            maxRequests: 1,
            preventDoubbleRequests: false,
            cacheResponse: false
        });
    });

}(akeeba.jQuery));