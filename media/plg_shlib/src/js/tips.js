/**
 * Shlib - programming library
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier 2015
 * @package      shlib
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      0.3.1.540
 * @date         2016-07-18
 */

/*! Copyright Weeblr llc @_YEAR_@ - Licence: http://www.gnu.org/copyleft/gpl.html GNU/GPL */

;
(function (_app, window, document, $) {
    "use strict;"

    var autoSetupSelector = '.wbTip';

    /**
     * Setup tips for a group of elements,
     * based on a text selector
     *
     * @param selector
     */
    function setupTips(selector) {
        try {
            $(selector).each(setupTip);
        } catch (e) {
            console.log('wbLib: error setting up help tips: ' + e.message);
        }
    }

    /**
     * Setup a tip for a given element
     *
     * @param element
     */
    function setupTip(index, element) {
        var $element = $(element);
        var labelId = '#' + element.id + '-lbl';
        var $label = $(labelId);
        var originalTitle = $label.attr('title');
        if (originalTitle) {
            $label.removeClass('hasTooltip').attr('title', '');
            appendTip($element, originalTitle);
        }
    }

    function appendTip($element, title) {
        var $newTip = $('<span class="wbtip-wrapper"><span type="button" class="wbtip-content">' + prepareTitle(title) + '</span></span>');
        var $controls = $element.parent();
        $newTip.appendTo($controls);
    }

    /**
     * Drop the initial title created in standard tooltip
     *
     * @param title
     * @returns {*}
     */
    function prepareTitle(title) {
        var lineBreakPos = title.indexOf('<br />');
        if (lineBreakPos != -1) {
            title = title.substr(lineBreakPos + 6);
        }
        return title;
    }

    /**
     * Set the jQuery selector to use to
     * auto setup tips at onReady event
     *
     * @param selector
     */
    function setAutoSetupSelector(selector) {
        autoSetupSelector = selector;
    }

    /**
     * Auto-setup at on ready
     */
    function onReady() {
        try {
            setupTips(autoSetupSelector);
        }
        catch (e) {
            console.log('Error setting up help tips: ' + e.message);
        }
    }

    /**
     * Hide/shows tips based
     *
     * @param state
     */
    function toggleTips(state) {
        if (state == 'show') {
            $('.wbtip-wrapper').show()
            $('.wbtip-switch.wbtip-show').hide();
            $('.wbtip-switch.wbtip-hide').show();
        }
        else {
            $('.wbtip-wrapper').hide();
            $('.wbtip-switch.wbtip-show').show();
            $('.wbtip-switch.wbtip-hide').hide();
        }
    }

    $(document).ready(onReady);

    // interface
    _app.tips = _app.tips || {};
    _app.tips.setAutoSetupSelector = setAutoSetupSelector;
    _app.tips.setupTips = setupTips;
    _app.tips.setupTip = setupTip;
    _app.tips.hideTips = toggleTips;
    _app.tips.showTips = toggleTips;

    return _app;
})
(window.weeblrApp = window.weeblrApp || {}, window, document, jQuery);
