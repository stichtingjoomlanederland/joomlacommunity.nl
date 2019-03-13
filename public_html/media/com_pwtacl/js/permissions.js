/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - [year] Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

// 1 = Allowed
// 0 = Denied
// 9 = Not set

jQuery(document).ready(function ($) {
    var action = $('.action.edit');
    options = Joomla.getOptions('pwtacl');

    if (action) {
        action.click(function (e) {
            e.preventDefault();
            var assetid = parseInt($(this).attr('data-assetid')),
                action = $(this).attr('data-action'),
                groupid = parseInt($(this).attr('data-groupid')),
                parentid = parseInt($(this).attr('data-parentid')),
                setting = parseInt($(this).attr('data-setting')),
                setting_calculated = parseInt($(this).attr('data-setting-calculated')),
                setting_parent = parseInt($(this).attr('data-setting-parent')),
                newsetting = null,
                newsettingchilds = null,
                newcalculatedsetting = null;

            // Double-check to prevent Super User access revoking...
            if (assetid === 1 && action == 'core.admin' && setting === 1) {
                var retVal = confirm(options.superuseralert);
                if (retVal == false) {
                    return false;
                }
            }

            // Parent not-set
            if (setting_calculated === 9) {

                // Not-set -> Allowed
                if (setting === 9) {
                    newsetting = 1;
                    newsettingchilds = 1;
                    newcalculatedsetting = 1;

                    $(this).addClass('allowed');
                    $(this).find('span').removeClass().addClass('icon-ok');
                }
            }

            // Parent allowed
            else if (setting_calculated === 1) {

                // Allowed -> Denied
                if (setting_parent === 1 && setting === 1) {
                    newsetting = 0;
                    newsettingchilds = 0;
                    newcalculatedsetting = 0;

                    $(this).removeClass('allowed').addClass('denied');
                    $(this).find('span').removeClass('icon-ok').addClass('icon-not-ok');
                }

                // Allowed -> Not-set
                else if (setting === 1) {
                    newsetting = 9;
                    newsettingchilds = 9;
                    newcalculatedsetting = 9;

                    $(this).removeClass('allowed');
                    $(this).find('span').removeClass().addClass('icon-not-ok');
                }


                // Inherited Allowed -> Denied
                if (setting === 9) {
                    newsetting = 0;
                    newsettingchilds = 0;
                    newcalculatedsetting = 0;

                    $(this).addClass('denied');
                    $(this).find('span').removeClass().addClass('icon-not-ok');
                }
            }

            // Parent denied
            else if (setting_calculated === 0) {

                // Denied -> Not-set (allowed)
                if (setting_parent === 1 && setting === 0) {
                    newsetting = 9;
                    newsettingchilds = 1;
                    newcalculatedsetting = 1;

                    $(this).removeClass('denied');
                    $(this).find('span').removeClass().addClass('icon-ok');
                }

                // Denied -> Not-set
                else if (setting === 0) {
                    newsetting = 9;
                    newsettingchilds = 9;
                    newcalculatedsetting = 9;

                    $(this).removeClass('denied');
                }

                // Allowed -> Locked
                if (setting === 1) {
                    newsetting = 9;
                    newcalculatedsetting = 0;

                    $(this).removeClass('conflict');
                    $(this).find('span').removeClass().addClass('icon-not-ok');
                }
            }

            // Set new asset settings
            $(this).attr('data-setting', newsetting);
            $(this).attr('data-setting-calculated', newcalculatedsetting);

            if (newsetting !== null) {
                saveAction(assetid, action, groupid, newsetting);
            }

            if (newsettingchilds !== null) {
                setChilds(assetid, action, newsettingchilds, newsetting, groupid);
            }
        });
    }

    function setChilds(assetid_parent, action, setting_calculated, setting_parent, groupid) {

        $('td[data-parentid="' + assetid_parent + '"][data-action="' + action + '"]').each(function () {

            var setting = parseInt($(this).attr('data-setting')),
                assetid = parseInt($(this).attr('data-assetid'));

            if (setting_calculated === 9) {

                if (setting === 9) {
                    $(this).attr('data-setting-calculated', setting_calculated);
                    $(this).find('span').removeClass().addClass('icon-not-ok');
                }

                if (setting === 0) {
                    $(this).attr('data-setting', 9);
                    $(this).removeClass('denied');
                    $(this).find('span').removeClass().addClass('icon-not-ok');
                    saveAction(assetid, action, groupid, 9);
                }
            }

            else if (setting_calculated === 1) {
                if (setting === 9) {
                    $(this).attr('data-setting-calculated', setting_calculated);
                    $(this).find('span').removeClass().addClass('icon-ok');
                }

                if (setting === 1) {
                    $(this).attr('data-setting-calculated', setting_calculated);
                    $(this).attr('data-setting', 9);
                    $(this).removeClass('allowed');
                    saveAction(assetid, action, groupid, 9);
                }
            }

            else if (setting_calculated === 0) {

                if (setting_parent === 0 && setting === 9) {
                    $(this).attr('data-setting-calculated', setting_calculated);
                    $(this).attr('data-setting', 9);
                    $(this).find('span').removeClass().addClass('icon-lock');
                }

                else if (setting === 9) {
                    $(this).attr('data-setting-calculated', setting_calculated);
                    $(this).attr('data-setting', 9);
                    $(this).find('span').removeClass().addClass('icon-not-ok');
                }

                if (setting === 0) {
                    $(this).attr('data-setting', 9);
                    $(this).removeClass('denied');
                    $(this).find('span').removeClass().addClass('icon-lock');
                    saveAction(assetid, action, groupid, 9);
                }
            }

            // Set parent asset setting
            $(this).attr('data-setting-parent', setting_parent);

            setChilds(assetid, action, setting_calculated, setting_parent);
        });
    }

    function saveAction(assetid, action, groupid, setting) {
        request = {
            'assetid': assetid,
            'action': action,
            'groupid': groupid,
            'setting': setting
        };

        $.ajax({
            type: 'POST',
            data: request,
            url: 'index.php?option=com_pwtacl&task=assets.saveAction',
            success: function (response) {
                if (assetid === 1 && action == 'core.admin') {
                    location.reload();
                }
            }
        });
    }

    // Fixed table headers
    $.fn.fixedHeader = function (options) {
        var config = {
            topOffset: 82
        };

        if (options) {
            $.extend(config, options);
        }

        return this.each(function () {
            var o = $(this),
                $win = $(window),
                $head = $('thead', o),
                isFixed = 0,
                headTop = $head.length && $head.offset().top - config.topOffset;

            function processScroll() {
                if (!o.is(':visible')) return;
                if ($('thead.header-copy').length) {
                    $('thead.header-copy th').each(function (i, th) {
                        $(th).width($($head.find('th')[i]).width());
                    });
                    var i, scrollTop = $win.scrollTop();
                }
                var t = $head.length && $head.offset().top - config.topOffset;
                if (!isFixed && headTop != t) {
                    headTop = t;
                }
                if (scrollTop >= headTop && !isFixed) {
                    isFixed = 1;
                } else if (scrollTop <= headTop && isFixed) {
                    isFixed = 0;
                }
                isFixed ? $('thead.header-copy', o).show().offset({left: $head.offset().left}) : $('thead.header-copy', o).hide();
            }

            $win.on('scroll', processScroll);

            $head.clone(true).addClass('header-copy header-fixed').css({
                'position': 'fixed',
                'top': config['topOffset']
            }).appendTo(o);
            o.find('thead.header-copy');

            o.find('thead > tr > th').each(function (i, h) {
                var w = $(h).width();
                o.find('thead.header-copy> tr > th:eq(' + i + ')').width(w)
            });

            processScroll();
        });
    };

    $('.table-fixed-header').fixedHeader();

    $('[data-toggle=additional]').click(function (e) {
        e.preventDefault();
        $(this.getAttribute('data-target')).toggle();
        $(this).find('span').toggleClass('icon-arrow-right icon-arrow-down');
    });
});

jQuery(window).on('resize', function () {
    jQuery('.header-copy').remove();
    jQuery('.table-fixed-header').fixedHeader();
});
