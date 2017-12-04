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
