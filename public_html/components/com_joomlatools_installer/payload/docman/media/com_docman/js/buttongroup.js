/**
 * DOCman Buttongroup
 *
 * Adds logic and behavior to buttongroups
 *
 * @copyright	Copyright (C) 2007 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @requires    Koowa.Class
 */

var Docman = Docman || {};

(function($){

    Docman.Buttongroup = Koowa.Class.extend({

        getDefaults: function(){

            var defaults = {
                    element: '',
                    active_class: 'k-is-active'
                };

            return $.extend(true, {}, defaults); // get the defaults from the parent and merge them
        },

        initialize: function(options){

            this.options = $.extend(true, this.getDefaults(), options);

            this.element = $(this.options.element);

            var self      = this,
                custom    = self.element.next().find('.k-js-custom-amount'),
                buttons   = self.element.next().find('.k-js-buttongroup-button');

            custom.on((window.addEventListener ? 'input' : 'keyup'), function() {
                var value = $(this).val();
                if (value) {
                    buttons.removeClass(self.options.active_class);
                    self.element.val(value);
                }
            });

            buttons.on('click', function() {
                if ($(this).hasClass(self.options.active_class)) {
                    buttons.removeClass(self.options.active_class);

                    self.element.val('');
                } else {
                    buttons.removeClass(self.options.active_class);
                    $(this).addClass(self.options.active_class);

                    custom.val('');
                    self.element.val(this.value);
                }
            });
        }
    });
}(window.kQuery));