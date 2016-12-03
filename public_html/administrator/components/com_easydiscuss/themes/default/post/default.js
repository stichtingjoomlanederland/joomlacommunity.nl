ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

    window.insertVideoCode = function(videoURL , caretPosition , elementId) {

        if (videoURL.length == 0) {
            return false;
        }

        var textarea = $('textarea[name=' + elementId + ']');
        var tag = '[video]' + videoURL + '[/video]';

        // If this is at the first position, we don't want to do anything here.
        if (caretPosition == 0) {

            $(textarea).val(tag);
            EasyDiscuss.dialog().close();
            return true;
        }

        var contents = $(textarea).val();

        $(textarea).val(contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length));
    };

    window.insertPhotoCode = function(photoURL , caretPosition , elementId) {

        if (photoURL.length == 0) {
            return false;
        }

        var textarea = $('textarea[name=' + elementId + ']');
        var tag = '[img]' + photoURL + '[/img]';

        // If this is at the first position, we don't want to do anything here.
        if (caretPosition == 0) {

            $(textarea).val(tag);
            EasyDiscuss.dialog().close();
            return true;
        }

        var contents = $(textarea).val();

        $(textarea).val(contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length));
    };

    window.insertLinkCode = function(linkURL , linkTitle, caretPosition , elementId) {

        if (linkURL.length == 0) {
            return false;
        }

        if (linkTitle.length == 0) {
            linkTitle = 'Title';
        }

        var textarea = $('textarea[name=' + elementId + ']');
        var tag = '[url=' + linkURL + ']'+ linkTitle +'[/url]';

        // If this is at the first position, we don't want to do anything here.
        if (caretPosition == 0) {

            $(textarea).val(tag);
            EasyDiscuss.dialog().close();
            return true;
        }

        var contents = $(textarea).val();

        $(textarea).val(contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length));
    };
});


<?php if ($this->config->get('main_location_discussion') || $this->config->get('main_location_reply')) { ?>
    
// Location support
ed.require(['edq', 'site/vendors/gmaps', self.getGmapsUrl()], function($, GMaps) {

    var wrapper = $('[data-ed-location]');

    if (wrapper.length <= 0) {
        return;
    }

    var map = wrapper.find('[data-ed-location-map]');

    $.each(map, function(i, item) {

        var iMap = $(item);

        var latitude = iMap.data('latitude');
        var longitude = iMap.data('longitude');

        var gmap = new GMaps({
                                el: item,
                                lat: latitude,
                                lng: longitude,
                                zoom: <?php echo $this->config->get('main_location_default_zoom');?>,
                                mapType: '<?php echo $this->config->get('main_location_map_type');?>',
                                width: '100%',
                                height: '200px'
                    });

        // Add the marker on the map
        gmap.addMarker({
          lat: latitude,
          lng: longitude,
          title: 'Lima'
        });

    });

});

function getGmapsUrl() {
    var gmapsApiKey = "<?php echo $this->config->get('main_location_gmaps_key'); ?>";

    var gmapsUrl = 'https://maps.google.com/maps/api/js?language=<?php echo $this->config->get("main_location_language");?>';

    if (gmapsApiKey) {
        var gmapsUrl = 'https://maps.google.com/maps/api/js?key='+ gmapsApiKey +'&language=<?php echo $this->config->get("main_location_language");?>';
    }

    return gmapsUrl;
}
<?php } ?>