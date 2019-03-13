<?php
if ($post->isQuestion() && $operation != 'replying' && !$this->config->get('main_location_discussion')) {
	return;
}

if (($post->isReply() || $operation == 'replying') && !$this->config->get('main_location_reply') ) {
	return;
}
?>
ed.require(['edq', 'easydiscuss', 'site/vendors/gmaps', 'selectize', self.getGmapsUrl()], function($, EasyDiscuss, GMaps) {

	// Apply selectize on location input
	var composer = $('[<?php echo $editorId;?>]');
	var addressInput = composer.find('[data-ed-location-address]');

	// When a reply form is edited / replied, reset the form
	$(document)
	.on('composer.form.reset', '[data-ed-composer-form]', function(){

		var form = $(this);

		// Clear location if there is any
		var location = form.find('[data-ed-location-address]');

		if (location.length > 0) {

			// Clear the map
			var removeLocation = form.find('[data-ed-location-remove]');

			removeLocation.click();
		}
	});

	var removeAddressButton = $('[data-ed-location-remove]');

	removeAddressButton.live('click', function() {
		var parent = $(this).parents('[data-ed-location-form]');

		removeLocation(parent);
	});

	var removeLocation = function(wrapper) {

		var addressInput = wrapper.find('[data-ed-location-address]');

		// Remove the location
		wrapper.removeClass('has-location');

		// Reset the input
		var selectize = addressInput[0].selectize;
		selectize.clear();
	};

	var renderMap = function(lat, lng) {

		var map = composer.find('[data-ed-location-map]');

		var gmap = new GMaps({
								el: map[0],
								lat: lat,
								lng: lng,
								'width': '100%',
								'height': '250px'
					});

		gmap.addMarker({
			lat: lat,
			lng: lng,
			draggable: true,
			dragend: function(obj) {
				var lat = obj.latLng.lat();
				var lng = obj.latLng.lng();

				// Update the location
				setLocation(lat, lng);

				GMaps.geocode({
					"lat": lat,
					"lng": lng,
					callback: function(results, status) {
						// Set the new address
						setAddress(results[0]);
					}
				});
			}
		});

		return;
	};

	var setAddress = function(row) {

		var addressInput = composer.find('[data-ed-location-address]');
		var selectize = addressInput[0].selectize;

		addressInput.val(row.formatted_address);

		// Clear the current input
		var obj = {
					'latitude': row.geometry.location.lat(),
					'longitude': row.geometry.location.lng(),
					'name': row.address_components[0].long_name,
					'address': row.formatted_address,
					'fulladdress': row.formatted_address,
					'reloadMap': "0"
				};

		selectize.addOption(obj);
		selectize.addItem(obj.address);
	};

	var setLocation = function(lat, lng) {

		var latitudeInput = $('[data-ed-location-latitude]');
		var longitudeInput = $('[data-ed-location-longitude]');

		latitudeInput.val(lat);
		longitudeInput.val(lng);
	};


	// Defer instantiation of controller until Google Maps library is loaded.
	var geocoder = new google.maps.Geocoder();
	var hasGeolocation = navigator.geolocation !== undefined;

	var autoDetectButton = $('[data-ed-location-detect]');

	<?php if ($post->hasLocation()) { ?>
	// If the post has a location we need to render the map
	renderMap("<?php echo $post->latitude;?>", "<?php echo $post->longitude;?>");
	<?php } ?>

	autoDetectButton.on('click', function() {

	autoDetectButton.html('<i class="fa fa-spinner"></i>');
		navigator.geolocation.getCurrentPosition(function(position) {

			var latitude = position.coords.latitude;
			var longitude = position.coords.longitude;

			geocoder.geocode({
				location: new google.maps.LatLng(latitude, longitude)
			}, function(result) {

				var locations = [];
				var control = addressInput[0].selectize;

				$.each(result, function(i, row) {

					// Format the output
					locations.push({
						'latitude': row.geometry.location.lat(),
						'longitude': row.geometry.location.lng(),
						'name': row.address_components[0].long_name,
						'address': row.formatted_address,
						'fulladdress': row.formatted_address,
						'reloadmap': "1"
					});

				});
				autoDetectButton.html('<i class="fa fa-location-arrow"></i>');
				control.addOption(locations);

				// Open up the suggestions
				control.open();
			});

		}, function() {

		});

	});

	addressInput.selectize({
		persist: false,
		openOnFocus: true,
		createOnBlur: false,
		create: false,
		delimiter: "||",
		valueField: 'address',
		labelField: 'address',
		searchField: 'address',
		maxItems: 1,
		hideSelected: true,
		closeAfterSelect: true,
		selectOnTab: true,
		options: [],
		onItemAdd: function(value, item) {

			// Get the option data
			var lat = $(item).data('lat');
			var lng = $(item).data('lng');
			var reloadMap = $(item).data('reloadmap') == 1;

			// Set the location
			setLocation(lat, lng);

			// Set has location
			addressInput.parents('[data-ed-location-form]').addClass('has-location');

			// Render the map
			if (reloadMap) {
				renderMap(lat, lng);
			}
		},
		load: function(query, callback) {

			// If the query was empty, don't do anything here
			if (!query.length) {
				return callback();
			}

			// Run an ajax call for suggestions
			EasyDiscuss.ajax('site/views/location/geocode', {
				"address": query
			}).done(function(locations) {
				callback(locations);

			});
		},
		render: {
			item: function(data, escape) {

				return '<div class="item" data-reloadmap="' + data.reloadmap + '" data-lng="' + data.longitude + '" data-lat="' + data.latitude + '">' + escape(data.address) + '</div>';
			},
			option: function(item, escape) {
				return '<div>' +
					'<span class="title">' +
						'<span class="name">' + escape(item.address) + '</span>' +
					'</span>' +
				'</div>';
			}
		},
		score: function (search) {
			return function(item) {
				return 1;
			};
		}
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
