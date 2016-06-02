/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.3.1.490
 * @date        2016-05-18
 */

/*! Copyright Weeblr llc @_YEAR_@ - Licence: http://www.gnu.org/copyleft/gpl.html GNU/GPL */

;
(function (_app, window, document, $) {
    "use strict";

    var $msgArea;
    var editorName;
    var product = "products.wbamp";
    var version = "1";
    var $helpButton;
    var helpserverUrl = 'https://weeblr.com/index.php?option=com_wbdoc&product=' + product + '&version=' + version + '&view=document&tmpl=component&embed=true&Itemid=337&doc_url=';
    var helpDisplayed = false;
    var helpCache = {};
    var $helpSpinnerContainer;
    var displayedTab = '';
    var _debug = false;

    /**
     * Implementation
     */

    /**
     * Actually insert a tag in the currently active editor
     * @param source
     */
    function insertTag(tagType) {
        try {
            if (processors[tagType]) {
                processors[tagType]();
            }
            else {
                throw "Unknow tag type " + tagType;
            }
            shlBootstrap.closeModal();
        } catch (e) {
            console.debug(e);
            displayMessage('Internal error, unable to insert tag: ' + e);
        }
    }

    var processors = {
        /**
         * {wbamp-meta name="doc_type" content="NewsArticle"}
         */
        "doctype": function () {
            var newValue = $('#wbamp_editor_form_doctype_params_document_type').val();
            newValue = newValue && newValue.trim();
            if (newValue) {
                var tag = '{wbamp-meta name="doc_type" content="' + newValue + '"}\n';
                prependTagToContent(tag, new RegExp('\n?{wbamp-meta name="doc_type"[^}]+}'));
            }
        },

        /**
         * {wbamp-meta name="image" url="" height="123" width="456"}
         */
        "docimage": function () {
            var url = $('#wbamp_editor_form_docimage_params_page_image_url').val();
            url = url && url.trim();
            if (url) {
                var width = getInt($('#wbamp_editor_form_docimage_params_page_image_width').val());
                var height = getInt($('#wbamp_editor_form_docimage_params_page_image_height').val());
                var tag = '{wbamp-meta name="image" url="' + url + '" width="' + width + '" height="' + height + '"}\n';
                prependTagToContent(tag, new RegExp('{wbamp-meta name="image"[^}]+}\n?'));
            }
        },

        /**
         * {wbamp-meta name="author" content="Yannick Gaultier" type="Person"}
         */
        "docauthor": function () {
            var author = $('#wbamp_editor_form_docauthor_params_document_author').val();
            author = author && author.trim();
            if (author) {
                var type = $('#wbamp_editor_form_docauthor_params_document_author_type').val();
                type = type && type.trim();
                var tag = '{wbamp-meta name="author" type="' + type + '" content="' + author + '"}\n';
                prependTagToContent(tag, new RegExp('{wbamp-meta name="author"[^}]+}\n'));
            }
        },

        "start_amp_show": function () {
            processors.hideshow('start', 'show');
        },
        "end_amp_show": function () {
            processors.hideshow('end', 'show');
        },
        "start_amp_hide": function () {
            processors.hideshow('start', 'hide');
        },
        "end_amp_hide": function () {
            processors.hideshow('end', 'hide');
        },

        /**
         *
         * @param tagType
         */
        hideshow: function (position, action) {
            var tag = '{wbamp-' + action + ' ' + position + '}';
            insertAtCurrentPos(tag);
        }
    }

    /**
     * Get an int from a string after basic clean up
     *
     * @param value
     * @returns {*}
     */
    function getInt(value) {
        value = value && value.trim();
        var raw = parseInt(value);
        return isNaN(raw) ? 0 : raw;
    }

    /**
     * Insert some content at the current cursor
     * position in the current editor
     *
     * @param value
     */
    function insertAtCurrentPos(value) {
        if (value) {
            jInsertEditorText(value, editorName);
        }
    }

    /**
     * Prepend some content at the start of the current
     * editor content.
     * Existing similar content is removed, if a regExp
     * object is passed
     * @param regExp
     * @param newValue
     */
    function prependTagToContent(newValue, regExp) {
        if (newValue) {
            var html = wbampEditorPluginGetContent();
            // new doc type, wipe out any preexisting
            if (regExp) {
                html = html.replace(regExp, '');
            }
            html = newValue + html;

            // set back updated content
            wbampEditorPluginSetContent(html);
        }
    }

    /**
     * Show designated tab, and hide all others
     *
     * @param tabId
     */
    function showTab(tabId) {
        // search new
        var $newTab = $('#wbamp-editor-tab-' + tabId);
        // found, display it and hide others
        // if not do nothing
        if ($newTab.length) {
            if (displayedTab) {
                var $oldTab = $('#wbamp-editor-tab-' + displayedTab);
                if ($oldTab.length) {
                    $oldTab.removeClass('current').addClass('hide');
                }
            }
            wbAmpEditorUpdateFooterButton(tabId);

            // update the selected element if we
            // are returning after closing the modal
            var select = $('#wbamp-option-select');
            select.val(tabId);
            select.trigger("liszt:updated");

            displayedTab = tabId;
            $newTab.removeClass('hide').addClass('current');
            if (helpDisplayed) {
                // update help with new tab
                helpDisplayed = false;
                showHelp();
            }
        }
    }

    /**
     * Output to console, with a global on/off switch
     * @param text
     */
    function log(text) {
        _debug && console.log(text + ' - ' + Date.now());
    }

    /**
     * Proxy method to relay the user click on
     * our editor button, that will trigger the
     * modal popup to be shown.
     * Workaround for Joomla built-in TinyMCE
     * way of embedding extended buttons in the TMCE toolbar
     * which forces to use built-in modals
     */
    function clickRelay(currentEditorName, elementId, tabId, event) {
        if (event) {
            event.preventDefault();
        }
        editorName = currentEditorName;
        // open our modal
        $(elementId).on('shown', function () {

            // hide or show the InsertTag button
            // depending on selected tab
            wbAmpEditorUpdateFooterButton(tabId);

            // manage help: restore whatever
            // help was displayed if popup
            // was opened previously
            $helpButton = null;
            displayedTab = displayedTab || tabId;
            showTab(displayedTab);
        }).modal();
    }

    /**
     * Insert an alert box in the modal, then hide it
     * after a (long) timeout
     *
     * @param msg
     */
    function displayMessage(msg) {
        $msgArea = $msgArea || $('.wbamp-editor-msg-area');
        var $content = $('<div class="alert alert-error" id="wbamp-editor-msg-content">' + msg + '</div>');
        $content.prependTo($msgArea);
        setTimeout(function () {
            jQuery('#wbamp-editor-msg-content').slideUp(300);
        }, 12000);
    }

    /**
     * Help display management
     */
    function showHelp() {
        $helpButton = $helpButton || $('#wbamp-editor-help-button');
        var helpId = $helpButton.attr('data-helpid');

        if (helpDisplayed) {
            closeHelp();
            return;
        }

        // have we fetched this help before?
        if (helpCache[helpId]) {
            displayHelp(helpId, helpCache[helpId]);
        }
        else {
            // create a script tag and insert it
            var url = helpserverUrl + product + '/' + version + '/' + helpId;
            showSpinner();

            jQuery.ajax(
                {
                    "url": url,
                    "error": function (jqXHR, textStatus, errorThrown) {
                        hideSpinner();
                        console.error('Error fetching documentation page ' + helpId);
                        console.error(textStatus);
                    },
                    "success": function (data, textStatus, jqXHR) {
                        displayHelp(helpId, data);
                        hideSpinner();
                    }
                });

        }
    }

    /**
     * Show a spinner while the remote help content is fetched,
     * along with its parent container
     */
    function showSpinner() {
        $helpSpinnerContainer = $helpSpinnerContainer || $('.wbamp-help-frame-loader-container');
        $helpSpinnerContainer.show();
        weeblrApp.spinner.start('wbamp-editor-help-spinner');
    }

    /**
     * Hide a previously displayed spinner, including its container
     */
    function hideSpinner() {
        $helpSpinnerContainer = $helpSpinnerContainer || $('.wbamp-help-frame-loader-container');
        weeblrApp.spinner.stop('wbamp-editor-help-spinner');
        $helpSpinnerContainer.hide();
    }

    /**
     * Opens the iframe (rather its container) used to display the help content
     */
    function openHelp() {
        helpDisplayed = true;
        $('#wbamp-help-frame').fadeIn();
        $helpButton = $helpButton || $('#wbamp-editor-help-button');
        $helpButton.addClass('displayed');
    }

    /**
     * Closes the iframe (rather it's container) used to display the help content
     */
    function closeHelp() {
        helpDisplayed = false;
        $('#wbamp-help-frame').slideUp();
        $helpButton = $helpButton || $('#wbamp-editor-help-button');
        $helpButton.removeClass('displayed');
    }

    /**
     * Displays some help content by injecting it into an (existing) iframe
     * and calling another method to show the iframe
     *
     * @param helpId
     * @param helpData
     */
    function displayHelp(helpId, helpData) {
        // if not in cache already, cache data
        if (helpData && !helpCache[helpId]) {
            helpCache[helpId] = helpData;
        }

        var theFrame = document.getElementById('wbamp-help-frame');

        var theDoc;
        if (theFrame.document) {
            theDoc = theFrame.document;
        }
        else if (theFrame.contentWindow) {
            theDoc = theFrame.contentWindow.document;
        }

        // inject in iframe document
        theDoc.open();
        theDoc.writeln(helpData);
        theDoc.close();

        // finally open the frame
        openHelp();

    }

    /**
     * Public interface
     */
    _app.wbampeditor = {
        insertTag: insertTag,
        showTab: showTab,
        clickRelay: clickRelay,
        showHelp: showHelp,
        displayHelp: displayHelp
    };

    return _app;

})
(window.wblib = window.wblib || {}, window, document, jQuery);


