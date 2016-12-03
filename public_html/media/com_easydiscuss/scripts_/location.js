EasyDiscuss.module('location', function($) {

  var module = this;

  // require: start
  EasyDiscuss.require()
	.library(
		'throttle-debounce',
		'ui/autocomplete'
	)
	.done(function() {

        // controller: start

	EasyDiscuss.Controller(

		'Location.Form',

		{
			defaultOptions: {

				language: 'en',

				initialLocation: null,

				mapType: 'ROADMAP',
				height: '250px',
				width: '100%',

				'{locationInput}': '.locationInput',
				'{locationLatitude}': '.locationLatitude',
				'{locationLongitude}': '.locationLongitude',
				'{latitudeDisplay}'	: '.latitudeDisplay',
				'{longitudeDisplay}' : '.longitudeDisplay',
				'{locationMap}': '.locationMap',
				'{autoDetectButton}': '.autoDetectButton',
				'{locationCoords}'	: '.locationCoords',
				'{removeLocationButton}'	: '.removeLocationButton'
			}
		},

		function(self) { return {


			init: function() {

				var mapReady = $.uid('ext');

				window[mapReady] = function() {
					$.___GoogleMaps.resolve();
				};

				if (!$.___GoogleMaps) {

					$.___GoogleMaps = $.Deferred();

					EasyDiscuss.require()
						.script(
							{prefetch: false},
							'https://maps.googleapis.com/maps/api/js?sensor=true&language=' + self.options.language + '&callback=' + mapReady
						);
				}

				// Defer instantiation of controller until Google Maps library is loaded.
				$.___GoogleMaps.done(function() {
					self._init();
				});
			},

			_init: function(el, event) {

				self.geocoder = new google.maps.Geocoder();

				self.hasGeolocation = navigator.geolocation !== undefined;

				if (!self.hasGeolocation) {
					self.autoDetectButton().remove();
				} else {
					self.autoDetectButton().show();
				}

				self.locationInput()
					.autocomplete({

						delay: 300,

						minLength: 0,

						source: self.retrieveSuggestions,

						select: function(event, ui) {

							self.locationInput()
								.autocomplete('close');

							self.setLocation(ui.item.location);
						}
					})
					.prop('disabled', false);

				self.autocomplete = self.locationInput().autocomplete('widget');

				self.autocomplete
					.addClass('location-suggestion');

				var initialLocation = $.trim(self.options.initialLocation);

				if (initialLocation) {

					self.getLocationByAddress(

						initialLocation,

						function(location) {

							self.setLocation(location[0]);
						}
					);

				}

				self.busy(false);
			},

			busy: function(isBusy) {
				self.locationInput().toggleClass('loading', isBusy);
			},

			getUserLocations: function(callback) {
				self.getLocationAutomatically(
					function(locations) {
						self.userLocations = self.buildDataset(locations);
						callback && callback(locations);
					}
				);
			},

			getLocationByAddress: function(address, callback) {

				self.geocoder.geocode(
					{
						address: address
					},
					callback);
			},

			getLocationByCoords: function(latitude, longitude, callback) {

				self.geocoder.geocode(
					{
						location: new google.maps.LatLng(latitude, longitude)
					},
					callback);
			},

			getLocationAutomatically: function(success, fail) {

				if (!navigator.geolocation) {
					return fail('ERRCODE', 'Browser does not support geolocation or do not have permission to retrieve location data.');
				}

				navigator.geolocation.getCurrentPosition(
					// Success
					function(position) {
						self.getLocationByCoords(position.coords.latitude, position.coords.longitude, success);
					},
					// Fail
					fail
				);
			},

			renderMap: function(location, tooltipContent) {

				self.busy(true);

				self.locationMap().css('width' , self.options.width).css('height' , self.options.height).show();

				var map	= new google.maps.Map(
					self.locationMap()[0],
					{
						zoom: 15,
						center: location.geometry.location,
						mapTypeId: google.maps.MapTypeId[self.options.mapType],
						disableDefaultUI: true
					}
				);

				var marker = new google.maps.Marker(
					{
						draggable: true,
						position: location.geometry.location,
						center: location.geometry.location,
						title: location.formatted_address,
						map: map
					}
				);

				var infoWindow = new google.maps.InfoWindow({ content: tooltipContent });

				google.maps.event.addListener(map, 'tilesloaded', function() {
					infoWindow.open(map, marker);
					self.busy(false);
				});

				// Add listener event when drag is end so we can update the latitude and longitude.
				google.maps.event.addListener(marker, 'dragend', function(event ) {

					self.getLocationByCoords(this.getPosition().lat() , this.getPosition().lng() , function(locations) {

						// now we get the user specified location. lets update the input.
						var address = locations[0].formatted_address;
						self.locationInput().val(address);

					});

					// Update the new latitude and longitude values.
					self.locationLatitude().val(this.getPosition().lat());
					self.locationLongitude().val(this.getPosition().lng());

					// Update the new latitude and longitude display values. This is not the input.
					self.latitudeDisplay().html(this.getPosition().lat());
					self.longitudeDisplay().html(this.getPosition().lng());
					self.locationCoords().addClass('showCoords');
				});

			},

			setLocation: function(location) {

				if (!location) return;

				self.locationResolved = true;

				self.lastResolvedLocation = location;

				self.locationInput()
					.val(location.formatted_address);

				self.locationLatitude()
					.val(location.geometry.location.lat());

				self.latitudeDisplay()
					.html(location.geometry.location.lat());

				self.longitudeDisplay()
					.html(location.geometry.location.lng());

				self.locationCoords().addClass('showCoords');

				self.locationLongitude()
					.val(location.geometry.location.lng());

				self.renderMap(location, location.formatted_address);
			},

			removeLocation: function() {

				self.locationResolved = false;

				self.locationInput().val('');

				self.locationLatitude().val('');

				// Remove the display values
				self.latitudeDisplay().html('');

				self.longitudeDisplay().html('');

				self.locationCoords().removeClass('showCoords');
				self.locationLongitude().val('');

				self.locationMap().hide();
			},

			buildDataset: function(locations) {

				var dataset = $.map(locations, function(location) {
					return {
						label: location.formatted_address,
						value: location.formatted_address,
						location: location
					};
				});

				return dataset;
			},

			retrieveSuggestions: function(request, response) {

				self.busy(true);

				var address = request.term,

					respondWith = function(locations) {
						response(locations);
						self.busy(false);
					};

				// User location
				if (address == '') {

					respondWith(self.userLocations || []);

				// Keyword search
				} else {

					self.getLocationByAddress(address, function(locations) {

						respondWith(self.buildDataset(locations));
					});
				}
			},

			suggestUserLocations: function() {

				if (self.hasGeolocation && self.userLocations) {

					self.removeLocation();

					self.locationInput()
						.autocomplete('search', '');
				}

				self.busy(false);
			},

			'{locationInput} blur': function() {

				// Give way to autocomplete
				setTimeout(function() {

					var address = $.trim(self.locationInput().val());

					// Location removal
					if (address == '') {

						self.removeLocation();

					// Unresolved location, reset to last resolved location
					} else if (self.locationResolved) {

						if (address != self.lastResolvedLocation.formatted_address) {

							self.setLocation(self.lastResolvedLocation);
						}
					} else {
						self.removeLocation();
					}

				}, 250);
			},

			'{autoDetectButton} click': function() {

				self.busy(true);

				if (self.hasGeolocation && !self.userLocations) {

					self.getUserLocations(self.suggestUserLocations);

				} else {

					self.suggestUserLocations();
				}
			},

			'{removeLocationButton} click' : function()
			{
				self.removeLocation();
			}

		}}
	);

	EasyDiscuss.Controller(

		'Location.Map',

		{
			defaultOptions: {
				animation: 'drop',
				language: 'en',
				useStaticMap: false,
				disableMapsUI: true,

				// fitBounds = true will disobey zoom
				// single location with fitBounds = true will set zoom to max (by default from Google)
				// locations.length == 1 will set fitBounds = false unless explicitly specified
				// locations.length > 1 will set fitBounds = true unless explicitly specified
				zoom: 5,
				fitBounds: null,

				minZoom: null,
				maxZoom: null,

				// location in center has to be included in locations array
				// center will default to first object in locations
				// latitude and longitude always have precedence over address
				// {
				// 	"latitude": latitude,
				// 	"longitude": longitude,
				// 	"address": address
				// }
				center: null,

				// address & title are optional
				// latitude and longitude always have precedence over address
				// title will default to geocoded address
				// first object will open info window
				// [
				// 	{
				// 		"latitude": latitude,
				// 		"longitude": longitude,
				// 		"address": address,
				// 		"title": title
				// 	}
				// ]
				locations: [],

				// Default map type to be road map. Can be overriden.
				mapType: 'ROADMAP',

				width: 500,
				height: 400,

				'{locationMap}': '.locationMap',
				'{removeLocation}' : '.removeLocation'
			}
		},

		function(self) { return {

			init: function()
			{
				self.mapLoaded = false;

				var mapReady = $.uid('ext');

				window[mapReady] = function() {
					$.___GoogleMaps.resolve();
				};

				if (self.options.useStaticMap == true) {
					var language = '&language=' + String(self.options.language);
					var dimension = '&size=' + String(self.options.width) + 'x' + String(self.options.height);
					var zoom = '&zoom=' + String(self.options.zoom);
					var center = '&center=' + String(parseFloat(self.options.locations[0].latitude).toFixed(6)) + ',' + String(parseFloat(self.options.locations[0].longitude).toFixed(6));
					var maptype = '&maptype=' + google.maps.MapTypeId[self.options.mapType];
					var markers = '&markers=';
					var url = 'https://maps.googleapis.com/maps/api/staticmap?sensor=false' + language + dimension;

					if (self.options.locations.length == 1) {
						markers += String(parseFloat(self.options.locations[0].latitude).toFixed(6)) + ',' + String(parseFloat(self.options.locations[0].longitude).toFixed(6));

						url += zoom + center + maptype + markers;
					} else {
						var temp = new Array();
						$.each(self.options.locations, function(i, location) {
							temp.push(String(parseFloat(location.latitude).toFixed(6)) + ',' + String(parseFloat(location.longitude).toFixed(6)));
						});
						markers += temp.join('|');

						url += markers + maptype;
					}

					self.locationMap().show().html('<img src="' + url + '" />');
					self.busy(false);
				} else {
					var mapReady = $.uid('ext');

					window[mapReady] = function() {
						$.___GoogleMaps.resolve();
					};

					if (!$.___GoogleMaps) {

						$.___GoogleMaps = $.Deferred();

						EasyDiscuss.require()
							.script(
								{prefetch: false},
								'https://maps.googleapis.com/maps/api/js?sensor=true&language=' + self.options.language + '&callback=' + mapReady
							);
					}

					// Defer instantiation of controller until Google Maps library is loaded.
					$.___GoogleMaps.done(function() {
						self._init();
					});
				}
			},

			_init: function() {

				// initialise fitBounds according to locations.length
				if (self.options.fitBounds === null) {
					if (self.options.locations.length == 1) {
						self.options.fitBounds = false;
					} else {
						self.options.fitBounds = true;
					}
				}

				// initialise disableMapsUI value to boolean
				self.options.disableMapsUI = Boolean(self.options.disableMapsUI);

				// initialise all location object
				self.locations = new Array();
				$.each(self.options.locations, function(i, location) {
				    if (location.latitude != 'null' && location.longitude != 'null') {
						self.locations.push(new google.maps.LatLng(location.latitude, location.longitude));
					}
				});

				if (self.locations.length > 0) {
					self.renderMap();
				}

				self.busy(false);
			},

			busy: function(isBusy) {
				self.locationMap().toggleClass('loading', isBusy);
			},

			renderMap: function() {
				self.busy(true);

				self.locationMap().show();

				var latlng;

				if (self.options.center) {
					latlng = new google.maps.LatLng(center.latitude, center.longitude);
				} else {
					latlng = self.locations[0];
				}

				self.map = new google.maps.Map(
					self.locationMap()[0],
					{
						zoom: parseInt(self.options.zoom),
						minZoom: parseInt(self.options.minZoom),
						maxZoom: parseInt(self.options.maxZoom),
						center: latlng,
						mapTypeId: google.maps.MapTypeId[self.options.mapType],
						disableDefaultUI: self.options.disableMapsUI
					}
				);

				google.maps.event.addListener(self.map, 'tilesloaded', function() {
					if (self.mapLoaded == false) {
						self.mapLoaded = true;
						self.loadLocations();
					}
				});
			},

			loadLocations: function() {
				self.bounds = new google.maps.LatLngBounds();
				self.infoWindow = new Array();

				var addLocations = function() {
					$.each(self.locations, function(i, location) {
						self.bounds.extend(location);
						var placeMarker = function() {
							self.addMarker(location, self.options.locations[i]);
						};

						setTimeout(placeMarker, 100 * (i + 1));
					});

					if (self.options.fitBounds) {
						self.map.fitBounds(self.bounds);
					}
				};

				setTimeout(addLocations, 500);
			},

			addMarker: function(location, info) {
				if (!location) return;

				var marker = new google.maps.Marker(
					{
						position: location,
						map: self.map
					}
				);

				marker.setAnimation(google.maps.Animation.DROP);
				self.addInfoWindow(marker, info);
			},

			addInfoWindow: function(marker, info) {
				var content = info.content;

				if (!content) {
					content = info.address;
				}

				var infoWindow = new google.maps.InfoWindow();
				infoWindow.setContent(content);
				self.infoWindow.push(infoWindow);

				if (self.options.locations.length > 1) {
					google.maps.event.addListener(marker, 'click', function() {
						$.each(self.infoWindow, function(i, item) {
							item.close();
						});
						infoWindow.open(self.map, marker);
					});
				} else {
					google.maps.event.addListener(marker, 'click', function() {
						infoWindow.open(self.map, marker);
					});

					infoWindow.open(self.map, marker);
				}

				// custom hack for postmap module
				if (info.ratingid) {
					google.maps.event.addListener(infoWindow, 'domready', function() {
						$.each(info.ratingid, function(i, rid) {
							eblog.ratings.setup('ebpostmap_' + rid + '-ratings' , true, 'entry');
							$('#ebpostmap_' + rid + '-ratings').removeClass('ui-state-disabled');
							$('#ebpostmap_' + rid + '-ratings-form').find('.blog-rating-text').hide();
							$('#ebpostmap_' + rid + '-ratings .ratings-value').hide();
						});
					});
				}
			},
			'{removeLocation} click' : function(element )
			{
				disjax.loadingDialog();
				disjax.load('location' , 'confirmRemoveLocation' , self.element.data('id').toString());
			}
		}}
	);

        module.resolve();

        // controller: end

	});
  // require: end
});
