ed.require(['edq'], function($) {

	$('[ed-data-profile-sidebar-nav]').children(':first').addClass('active');

	$('.editProfileTabsContent').children(':first').addClass('active');

	// Javascript to enable link to tab
	var url = document.location.toString();

	if (url.match('#')) {
		$('[ed-data-profile-sidebar-nav] a[href="#' + url.split('#')[1] + '"]').tab('show');
		window.scrollTo(0, 0);
	} 

	// Change hash for page-reload
	$('[ed-data-profile-sidebar-nav] a').on('shown.bs.tab', function (e) {
		if(history.pushState) {
			history.pushState(null, null, e.target.hash);
		}
		else {
			location.hash = e.target.hash;
		}
	})

// --- Location Start --- //
<?php if ($this->config->get('layout_profile_showlocation')) { ?>
ed.require(['edq', 'easydiscuss', 'site/vendors/gmaps', 'selectize'], function($, EasyDiscuss, GMaps) {

	var locationTab = $('[data-ed-location-tab]');
	var tabClicked = false;

	// Render google maps
	var uid = $.uid('ext');

	window[uid] = function() {
		$.___GoogleMaps.resolve();

		// Directly render the maps if the url is pointing to edit location page.
		if (url.match('#') && url.split('#')[1] == 'edit-location') {
			renderMap("<?php echo $profile->latitude;?>", "<?php echo $profile->longitude;?>");
			tabClicked = true;
		}
	};

	// Try to initialize google maps
	if (!$.___GoogleMaps) {

		// Get google maps api key.
		var apiKey = '<?php echo $this->config->get("main_location_gmaps_key"); ?>';

		var gmapsUrl = 'https://maps.googleapis.com/maps/api/js?language=en&callback=' + uid;

		// Append api keys inside the url.
		if (apiKey) {
			var gmapsUrl = 'https://maps.googleapis.com/maps/api/js?key='+ apiKey +'&language=en&callback=' + uid;
		}

		$.___GoogleMaps = $.Deferred();

		// If google maps doesn't exist yet.
		if (window.google === undefined || window.google.maps === undefined) {
			ed.require([gmapsUrl]);
		} else {
			$.___GoogleMaps.resolve();
		}
	}    

	locationTab.live('click', function(){

		if (tabClicked == true) {
			return;
		}

		<?php if ($profile->hasLocation()) { ?>
		// If the post has a location we need to render the map
		renderMap("<?php echo $profile->latitude;?>", "<?php echo $profile->longitude;?>");
		<?php } ?>

		tabClicked = true;
	});

	// Apply selectize on location input
	var composer = $('[data-ed-location-form]');
	var addressInput = composer.find('[data-ed-location-address]');

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

		var map = $('[data-ed-location-map]');

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

		var addressInput = $('[data-ed-location-address]');
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
	$.___GoogleMaps.done(function() {

		var geocoder = new google.maps.Geocoder();
		var hasGeolocation = navigator.geolocation !== undefined;

		var autoDetectButton = $('[data-ed-location-detect]');
		
		autoDetectButton.on('click', function() {

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

					control.addOption(locations);

					// Open up the suggestions
					control.open();
				});

			}, function() {

			});

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

			// Render the map
			if (reloadMap) {
				renderMap(lat, lng);
			}

			// Set has location
			addressInput.parents('[data-ed-location-form]').addClass('has-location');
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
		}
	});
});
<?php } ?>
// --- Location End --- //
});
