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

var shlBootstrap = shlBootstrap || function ($) {
    var tmp = {
        updateBootstrap: function () {
            $("*[rel=tooltip]").tooltip();
            $("select").chosen({
                disable_search_threshold: 10,
                allow_single_deselect: !0
            });
            $(".radio.btn-group label").addClass("btn");
            $(".btn-group label:not(.active)").click(function () {
                var label = $(this);
                var input = $("#" + label.attr("for"));
                if (!input.prop('checked')) {
                    label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
                    if (input.val() == '') {
                        label.addClass('active btn-primary');
                    } else if (input.val() == 0) {
                        label.addClass('active btn-danger');
                    } else {
                        label.addClass('active btn-success');
                    }
                    input.prop('checked', true);
                }
            });
            $(".btn-group input[checked=checked]").each(function () {
                if ($(this).val() == '') {
                    $("label[for=" + $(this).attr('id') + "]").addClass('active btn-primary');
                } else if ($(this).val() == 0) {
                    $("label[for=" + $(this).attr('id') + "]").addClass('active btn-danger');
                } else {
                    $("label[for=" + $(this).attr('id') + "]").addClass('active btn-success');
                }
            });
        },
        canOpenModal: !0,
        modals: {},
        modalTemplate: "<div class='shmodal hide ' id='{%selector%}'><div class='shmodal-header'><button type='button' class='close' data-dismiss='modal'>×</button>{%title%}</div><div id='{%selector%}-container'></div></div>",
        selectedIdsUrl: "",
        setSelectedIdsUrl: function (e) {
            shlBootstrap.selectedIdsUrl = e
        },
        getModalUrl: function (e) {
            return e + shlBootstrap.selectedIdsUrl
        },
        closeModal: function () {
            var $closeModalButton = $("div.shmodal-header button.close");
            $closeModalButton.click()
        },
        setModalTitleFromModal: function (title) {
            var $modalTitle = window.parent.jQuery("div.shmodal-header:visible");
            $modalTitle.html("<h3>" + title + "</h3>")
        },
        registerModal: function (modalDefinition) {
            var storedDefinition = {};
            storedDefinition = $.extend({
                selector: "",
                title: "",
                url: "",
                width: .5,
                height: .5,
                onclose: "",
                footer: "",
                content: "",
                onDisplay: '',
                backdrop: !0,
                keyboard: !1
            }, modalDefinition);
            shlBootstrap.modals[storedDefinition.selector] = storedDefinition;
        },
        renderModal: function (index, modalDefinition) {

            var modalContent = shlBootstrap.modalTemplate.replace(new RegExp("{%selector%}", "g"), modalDefinition.selector);
            var title = modalDefinition.title ? "<h3>" + modalDefinition.title + "</h3>" : "&nbsp;";
            modalContent = modalContent.replace("{%title%}", title);
            $(modalContent).appendTo("#shl-modals-container");
            $("#" + modalDefinition.selector).on("show", function () {
                if (!shlBootstrap.canOpenModal) {
                    return !1;
                }
                var modalDef = shlBootstrap.modals[this.id];
                var width = modalDef.width < 1 ? $(window).width() * modalDef.width : modalDef.width;
                var height = modalDef.height < 1 ? $(window).height() * modalDef.height : modalDef.height;
                var url = shlBootstrap.getModalUrl(modalDef.url);
                var $modal = jQuery("#" + modalDef.selector);
                var bodyContent;
                if (modalDef.content) {
                    bodyContent = modalDef.content;
                }
                else {
                    bodyContent = '<iframe class="iframe" src="' + url + '" height="' + height + '" width="' + width + '" ></iframe>'
                }
                jQuery("#" + modalDef.selector + "-container").html('<div class="shmodal-body" style="height:' + height + "px; width:" + width + 'px;">' + bodyContent + '</div>' + modalDef.footer);
                var l = $modal.height(), s = $modal.width(), i = jQuery(window).height(), d = jQuery(window).width(), c = (d - s) / 2, h = (i - l) / 2;
                jQuery("#" + modalDef.selector).css({
                    "margin-top": h,
                    top: "0"
                });
                jQuery("#" + modalDef.selector).css({"margin-left": c, left: "0"});
                if (modalDef.onDisplay && typeof window[modalDef.onDisplay] == 'function') {
                    window[modalDef.onDisplay](this);
                }
            });
            $("#" + modalDefinition.selector).on("hide", function () {
                var modal = shlBootstrap.modals[this.id];
                modal.onclose && modal.onclose(), jQuery("#" + this.id + "-container").innerHTML = ""
            });
            $("#" + modalDefinition.selector).modal({keyboard: modalDefinition.keyboard, backdrop: modalDefinition.backdrop, show: !1});

        },
        renderModals: function () {
            $.each(shlBootstrap.modals, shlBootstrap.renderModal)
        },
        inputCounters: {},
        registerInputCounter: function (counterOptions) {
            var defaults = {
                maxCharacterSize: -1,
                originalStyle: "badge-success",
                warningStyle: "badge-warning",
                errorStyle: "badge-important",
                warningNumber: 20,
                errorNumber: 40,
                displayFormat: "#left",
                style: "shl-char-counter",
                title: ""
            };
            counterOptions = $.extend(defaults, counterOptions);
            shlBootstrap.inputCounters[counterOptions.selector] = counterOptions;
        },
        renderInputCounters: function () {
            $.each(shlBootstrap.inputCounters, shlBootstrap.renderInputCounter)
        },
        renderInputCounter: function (index, counterDef) {
            $("#" + counterDef.selector).textareaCount(counterDef)
        },
        onReady: function () {
            $("<div id='shl-modals-container'></div>").appendTo("body"), shlBootstrap.renderModals(), shlBootstrap.renderInputCounters()
        }
    };
    jQuery(document).ready(tmp.onReady);
    return tmp;
}(jQuery);

!function (e) {
    e.fn.textareaCount = function (t, r) {
        function a() {
            return v.html(o()), "undefined" != typeof r && r.call(this, l()), !0
        }

        function o() {
            var e = p.val(), r = e.length;
            if (t.maxCharacterSize > 0) {
                r >= t.maxCharacterSize && (e = e.substring(0, t.maxCharacterSize));
                var a = d(e), o = t.maxCharacterSize - a;
                if (i() || (o = t.maxCharacterSize), r > o) {
                    var l = this.scrollTop;
                    p.val(e.substring(0, o)), this.scrollTop = l
                }
                v.removeClass(t.warningStyle), v.removeClass(t.originalStyle), r > t.errorNumber ? v.addClass(t.errorStyle) : r > t.warningNumber ? v.addClass(t.warningStyle) : v.addClass(t.originalStyle), f = p.val().length + a, i() || (f = p.val().length), b = h(c(p.val())), g = t.errorNumber - f
            } else {
                var a = d(e);
                f = p.val().length + a, i() || (f = p.val().length), b = h(c(p.val()))
            }
            return n()
        }

        function n() {
            var e = t.displayFormat;
            return e = e.replace("#input", f), e = e.replace("#words", b), m > 0 && (e = e.replace("#max", m), e = e.replace("#left", g)), e
        }

        function l() {
            var e = {input: f, max: m, left: g, words: b};
            return e
        }

        function s(e) {
            return e.next(".charleft")
        }

        function i() {
            var e = navigator.appVersion;
            return -1 != e.toLowerCase().indexOf("win") ? !0 : !1
        }

        function d(e) {
            for (var t = 0, r = 0; r < e.length; r++)"\n" == e.charAt(r) && t++;
            return t
        }

        function c(e) {
            var t = e + " ", r = /^[^A-Za-z0-9]+/gi, a = t.replace(r, ""), o = rExp = /[^A-Za-z0-9]+/gi, n = a.replace(o, " "), l = n.split(" ");
            return l
        }

        function h(e) {
            var t = e.length - 1;
            return t
        }

        var u = {
            maxCharacterSize: -1,
            originalStyle: "originalTextareaInfo",
            warningStyle: "warningTextareaInfo",
            errorStyle: "errorTextareaInfo",
            warningNumber: 20,
            errorNumber: 40,
            displayFormat: "#input characters | #words words",
            title: ""
        }, t = e.extend(u, t), p = e(this);
        p.wrap("<div class='shl-char-counter-wrapper'></div>"), e("<div class='charleft badge " + t.style + "' " + (t.title ? "title='" + t.title + "'" : "") + ">&nbsp;</div>").insertAfter(p);
        var v = s(p);
        v.addClass(t.originalStyle);
        var f = 0, m = t.maxCharacterSize, g = 0, b = 0;
        p.bind("keyup", function (e) {
            a()
        }).bind("mouseover", function (e) {
            setTimeout(function () {
                a()
            }, 10)
        }).bind("paste", function (e) {
            setTimeout(function () {
                a()
            }, 10)
        }), a()
    }
}(jQuery);

