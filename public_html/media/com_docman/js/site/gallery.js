"use strict";

(function($) {


    $.simpleGallery = function(element, options) {

        var defaults = {
                debounce: 100,
                container: {
                    'class': 'koowa_media_contents'
                },
                item: {
                    'class': '.koowa_media__item',
                    'width': 220
                },
                prefix: {
                    'class': 'columns-'
                },
                label: {
                    'class': '.koowa_media__item__label'
                }
            },
            plugin = this,
            $element = $(element);

        plugin.settings = {};

        plugin.debounce = function(func, wait) {
            var timeout;
            return function() {
                var context = this, args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    timeout = null;
                    func.apply(context, args);
                }, wait);
            };
        };

        plugin.init = function() {

            plugin.settings = $.extend(true, {}, defaults, options);

            // Variables
            var container = $element.find('.' + plugin.settings.container['class']),
                prefix = plugin.settings.prefix['class'],
                labelClass = plugin.settings.label['class'],
                item =  $element.find(plugin.settings.item['class']),
                itemWidth = plugin.settings.item['width'];

            // Remove classes
            $.fn.removeClassPrefix = function (prefix) {
                this.each( function ( i, it ) {
                    var classes = it.className.split(" ").map(function (item) {
                        return item.indexOf(prefix) === 0 ? "" : item;
                    });
                    it.className = classes.join("");
                });
                return this;
            };


            // Equalize the height of each item in a row
            function equalizeLabelHeights(elements) {
                var minheight = 0;

                elements.each(function() {
                    var height = $(this).outerHeight();

                    if (height > minheight) {
                        minheight = height;
                    }
                });

                elements.css({'height': minheight});
            }

            var per_row_old = 0;

            // Add classes to the gallery container
            function applyStyling() {
                if (itemWidth) {
                    var width = $element.width();

                    $element.find(labelClass).css('height', 'auto');

                    // Remove all classes if screen is small
                    if (width < itemWidth) {
                        container.removeClassPrefix(prefix);
                    }

                    // Add column classes and fix the label heights per row
                    var per_row = parseInt(width / itemWidth, 10) + 1,
                        children = container.find(plugin.settings.item['class']),
                        i, count;

                    per_row_old = per_row;

                    container.removeClassPrefix(prefix).addClass(prefix+per_row);

                    if ( per_row_old != per_row ) {
                        children.find(labelClass).removeAttr('style');
                    }

                    for (i = 0, count = 1; i < children.length; i += per_row, count++) {
                        children.slice(i, i+per_row).removeClassPrefix('row').addClass('row' + count);
                        equalizeLabelHeights(container.find('.row' + count + ' ' + labelClass));
                    }
                }

                // Set the initialized class if not already set
                if ( !$element.hasClass('gallery-initialized') ) {
                    $element.addClass('gallery-initialized');
                }
            }

            // Run as fast as possible
            $(document).on('ready', plugin.debounce(applyStyling, plugin.settings.debounce));

            // Run on resize as well
            $(window).on('resize', plugin.debounce(applyStyling, plugin.settings.debounce));

        };

        plugin.init();
    };


    // add the plugin to the jQuery.fn object
    $.fn.simpleGallery = function(options) {
        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {
            // if plugin has not already been attached to the element
            if (undefined == $(this).data('simpleGallery')) {
                // create a new instance of the plugin
                var plugin = new $.simpleGallery(this, options);
                // in the jQuery version of the element
                // store a reference to the plugin object
                $(this).data('simpleGallery', plugin);
            }
        });
    }

})(kQuery);
