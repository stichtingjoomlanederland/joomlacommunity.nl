/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - [year] Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

jQuery(document).ready(function ($) {
    jQuery('.js--start').on('click', function (e) {
        jQuery('.js--start').addClass('disabled').attr('disabled', 'disabled');
        jQuery('.progress').removeClass('hidden');

        diagnostics(1);

        function diagnostics(step) {
            jQuery.ajax({
                url: 'index.php?option=com_pwtacl&task=diagnostics.runDiagnostics&step=' + step,
                dataType: 'json',
                success: function (a) {
                    var total = a.data.total,
                        items = a.data.items,
                        html = "",
                        stepclass = '.step' + step;

                    if (items) for (var action in items) {
                        for (var type in items[action]) {
                            for (var id in items[action][type]) {
                                var item = items[action][type][id];
                                html += '<tr>';
                                html += '<td><span class="typeofchange label label-' + item.label + '">' + item.action + '</span></td>';
                                html += '<td><span class="' + item.icon + '"></span>' + item.object + '</td>';
                                html += '<td>' + item.title + '<br><small>' + item.name + '</small></td>';
                                html += '<td>';

                                for (var field in item.changes) {
                                    var change = item.changes[field];
                                    if (change.old) {
                                        html += '<div class="btn-group"><span class="btn btn-small disabled">' + field + '</span><span class="btn btn-small btn-danger">' + change.old + '</span><span class="btn btn-small btn-success">' + change.new + '</span></div>';
                                    }
                                }

                                html += '</td>';
                                html += '<td>' + item.id + '</td>';
                                html += '</tr>';
                            }
                        }

                        jQuery(stepclass + ' table').removeClass('hidden');
                        jQuery(stepclass + ' tbody').html(html);
                    }

                    jQuery('.progress .bar').attr('style', 'width:' + 100 / 14 * step + '%');
                    jQuery(stepclass + ' .accordion-toggle').attr('href', '#step' + step).removeClass('nopointer');
                    jQuery(stepclass + ' h3').removeClass('muted').addClass('text-success');
                    jQuery(stepclass + ' .js-step-done').removeClass('hidden');
                    if (total) {
                        jQuery(stepclass + ' .js-assets-fixed').removeClass('hidden');
                        jQuery(stepclass + ' .js-assets-fixed-number').html(total);
                    }

                    step++;
                    if (step <= 14) {
                        setTimeout(function () {
                            diagnostics(step);
                        }, 50)
                    } else {
                        jQuery('.completed').removeClass('hidden');
                        jQuery('.progress').removeClass('active').removeClass('progress-striped');
                        jQuery('.quickscan-issues').addClass('hidden');
                        jQuery('.quickscan-noissues').removeClass('hidden');
                    }
                },
                error: function (data) {
                    console.log('error' + data);
                }
            });
        }
    });
});