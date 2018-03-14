;
kQuery(function ($) {

    'use strict';

    $.widget("docman.orderable", {

        options: {
            nested: false,
            items: 'tr',
            handle: 'a.js-sort-handle'
        },

        _create: function () {

            this.sortableParent = null;
            this.sortableRange = null;
            this.children = [];
            this.siblings = [];
            this.originalRowIndex = null;
        },

        _init: function () {

            var self = this;
            var params = this.element.data('params');

            this.form = this.element.closest('form');

            if (params) {
                this.options.nested = params.nested;
            }

            this.element.sortable({
                axis: 'y',
                cursor: 'move',
                handle: this.options.handle,
                items: this.options.items + ':not(.disabled)',
                helper: function (event, ui) {
                    var width = $(this).width();

                    ui.children().each(function () {
                        $(this).width(width);
                    });

                    return ui;
                },
                start: function (event, ui) {

                    self.originalRowIndex = ui.item.index();

                    if (self.options.nested) {
                        self.sortableParent = ui.item.data('parent');
                        self.sortableRange = self.element.find(self.options.items + '[data-parent="' + ui.item.data('parent') + '"]');
                    } else {
                        self.sortableParent = null;
                        self.sortableRange = self.element.find(self.options.items);
                    }

                    self.disableOtherSortableGroups();

                    if (self.options.nested) {
                        self.hideChildren(ui.item);
                        self.element.sortable('refresh');
                    }
                },
                stop: function (event, ui) {

                    var rowIndex = ui.item.index();
                    var ordering = ui.item.data('ordering');
                    var direction = self.originalRowIndex - rowIndex;

                    // if direction > 0, item is moving up on the list
                    if (direction > 0) {
                        var next = $(ui.item).next().data('ordering');
                        var change = next - ordering;
                    } else {

                        var prev = $(ui.item).prev().data('ordering');
                        var change = prev - ordering;
                    }

                    self.reorder(ui.item, change);

                    if (self.options.nested) {
                        self.showChildren(ui.item);
                        self.element.sortable('refresh');
                    }

                    // Remove "style" from children to properly reset the row
                    ui.item.children().each(function () {
                        $(this).removeAttr('style');
                    });

                    self.enableOtherSortableGroups();
                }
            });
        },

        hideChildren: function (item) {

            var self = this;

            this.children[0] = this.getChildren(item);
            this.children[0].hide();

            this.siblings = this.getSiblings(item);

            $.each(this.siblings, function (i, sibling) {
                self.children[i + 1] = self.getChildren($(sibling));
                self.children[i + 1].hide('slow');
            });
        },

        showChildren: function (item) {

            var self = this;

            this.children[0].show('slow');
            item.after($(this.children[0]));
            this.children[0] = null;

            $.each(this.siblings, function (i, sibling) {
                $(sibling).after($(self.children[i + 1]));
                self.children[i + 1].show('slow');
                self.children[i + 1] = null;
            });
        },

        getChildren: function (item) {

            if (this.options.nested) {
                return this.element.find(this.options.items + '[data-parents~="' + item.data('item') + '"]');
            }

            return null;
        },

        getSiblings: function (item) {

            if (this.options.nested) {
                return this.element.find(this.options.items + '[data-level="' + item.data('level') + '"]');
            }

            return this.element.find(this.options.items);
        },

        getNextSibling: function (item) {
            var next = null;
            var siblings = this.getSiblings(item);

            $.each(siblings, function (index, sibling) {
                if (item.data('ordering') > $(sibling).data('ordering')) {
                    console.log(item.data('ordering') + ' > ' + $(sibling).data('ordering'));
                    next = $(sibling);
                    return false;
                }
            });

            return next;
        },

        getPrevSibling: function (item) {

            var prev = null;
            var siblings = this.getSiblings(item);

            $.fn.reverse = [].reverse;

            siblings = siblings.reverse();

            $.each(siblings, function (index, sibling) {
                if (item.data('ordering') < $(sibling).data('ordering')) {
                    console.log(item.data('ordering') + ' < ' + $(sibling).data('ordering'));
                    prev = $(sibling);
                    return false;
                }
            });

            return prev;
        },

        disableOtherSortableGroups: function () {

            if (this.options.nested && this.sortableParent) {
                this.element.find(this.options.items + '[data-parent!="' + this.sortableParent + '"]').addClass('disabled');
                this.element.sortable('refresh');
            }
        },

        enableOtherSortableGroups: function () {
            this.element.find(this.options.items).removeClass('disabled');
            this.element.sortable('refresh');
        },

        reorder: function (item, change) {

            if (change == 0) {
                return;
            }

            var self = this;
            var token_name = this.form.data('controller').token_name;
            var token_value = this.form.data('controller').token_value;
            var data = 'order=' + change + '&' + token_name + '=' + token_value;

            $.ajax({
                method: 'post',
                url: this.form.attr('action') + '&id[]=' + item.data('item'),
                data: data,
                dataType: 'json',
                success: function () {
                    var rows = self.getSiblings(item);
                    rows.each(function (index, row) {
                        $(row).data('ordering', index + 1);
                    });
                }
            });

        }
    });

    var selector = 'tbody[data-behavior="orderable"]';

    if ($(selector).length) {
        $(selector).orderable();
    }
});
