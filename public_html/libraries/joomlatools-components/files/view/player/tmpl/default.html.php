<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */
defined('KOOWA') or die;
?>


<?= helper('behavior.koowa'); ?>

<ktml:style src="assets://files/css/plyr.css" />
<ktml:script src="assets://files/js/plyr/plyr.js" />
<script>
    kQuery(function($){
        var recorded_plays = [];

        plyr.setup();

        $(document).on('playing', function(event) {
            if (typeof event.detail !== 'undefined' && typeof event.detail.plyr !== 'undefined') {
                var plyr = event.detail.plyr;

                // If they've played over 3 seconds, then consider it played
                // This is the same timing convention used by Facebook, Instagram, and Twitter
                setTimeout(function() {
                    if (!plyr.isPaused()) {
                        var media = $(plyr.getMedia());

                        var category = media.data('category');
                        var action = 'Play ' + plyr.getType();

                        var title = media.data('title') || '';
                        var id = parseInt(media.data('media-id'), 10) || 0;

                        if (recorded_plays.indexOf(title) === -1) {
                            recorded_plays.push(title);

                            if (typeof window.GoogleAnalyticsObject !== 'undefined' && typeof window[window.GoogleAnalyticsObject] !== 'undefined') {
                                window[window.GoogleAnalyticsObject]('send', 'event', category, action, title, id);
                            }
                            else if (typeof _gaq !== 'undefined' && typeof _gat !== 'undefined') {
                                if (_gat._getTrackers().length) {
                                    _gaq.push(function () {
                                        var tracker = _gat._getTrackers()[0];
                                        tracker._trackEvent(category, action, title, id);
                                    });
                                }
                            }
                        }
                    }
                }, 3000);
            }
        });
    });
</script>
