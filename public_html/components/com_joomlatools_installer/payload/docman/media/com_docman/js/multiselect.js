/**
 * Multiselect
 *
 * @copyright   Copyright (C) 2020 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

(function($) {

    $.fn.multiselect = function(options) {
        var containers  = this;
        var lastChecked = null;

        if (containers.attr('type') != 'checkbox') {
            checkboxes = containers.find(':checkbox');
        } else {
            checkboxes = containers;
        }
        
        containers.on('click', function(e) {
            var element = $(e.target);

            if (element.attr('type') == 'checkbox')
            {
                if (!lastChecked) {
                    lastChecked = element;
                    return;
                }
                
                if (e.shiftKey)
                {
                    start = checkboxes.index(element);
                    end   = checkboxes.index(lastChecked);

                    checkboxes.slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', lastChecked.prop('checked'));
                }
        
                lastChecked = this;
            }
        }).on('mousedown', function(e) {
            if (e.shiftKey) {
                // Prevent selecting of text by Shift+click
                e.preventDefault();
            }
        });
    }

})(kQuery);