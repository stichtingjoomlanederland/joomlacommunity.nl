/**
 * Shlib - programming library
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier 2016
 * @package      shlib
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      0.3.1.580
 * @date         2016-08-25
 */

/*! Copyright Weeblr llc @_YEAR_@ - Licence: http://www.gnu.org/copyleft/gpl.html GNU/GPL */

;
(function (_app, window, document, $) {
    "use strict;"

    var autoSetupSelector = '.wbTip';
    var tipsMode = 'popover';

    /**
     * Setup tips for a group of elements,
     * based on a text selector
     *
     * @param selector
     */
    function setupTips(selector) {
        selector = selector || autoSetupSelector;
        try {
            $(selector).each(setupTip);
        } catch (e) {
            console.log('wbLib: error setting up help tips: ' + e.message);
        }

        return _app.tips;
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
        switch (tipsMode) {
            case 'popover':
                // J 3.6.1 +, using popovers
                var originalTitle = $label.data('content');
                if (originalTitle) {
                    $label.off('mouseenter mouseleave');
                    appendTip($element, originalTitle);
                }
                break;
            case 'tips':
                // legacy mode, pre J 3.6.1, suing bootstrap tips
                var originalTitle = $label.attr('title');
                if (!originalTitle) {
                    originalTitle = $label.data('original-title');
                }
                if (originalTitle) {
                    $label.removeClass('hasTooltip').attr('title', '');
                    appendTip($element, originalTitle);
                }
                break;
            default:
                console.log('wblib: invalid tips mode ' + _app.tips.tipsMode);
        }
    }

    /**
     * Append a tip element to an input
     *
     * @param $element
     * @param title
     */
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
        return _app.tips;
    }

    function setTipsMode(mode) {
        tipsMode = mode;
        return _app.tips;
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

    // interface
    _app.tips = _app.tips || {};
    _app.tips.setAutoSetupSelector = setAutoSetupSelector;
    _app.tips.setupTips = setupTips;
    _app.tips.setupTip = setupTip;
    _app.tips.hideTips = toggleTips;
    _app.tips.showTips = toggleTips;
    _app.tips.setTipsMode = setTipsMode; // from J 3.6.1 and up

    return _app;
})
(window.weeblrApp = window.weeblrApp || {}, window, document, jQuery);
