Komento.module("location", function($) {

var module = this;

// require: start
Komento.require()
	.library(
		"ui/autocomplete"
	)
	.done(function(){

// controller: start

Komento.Controller(

	"Location.Form.Simple",

	{
		defaultOptions: {

			language: 'en',

			initialLocation: null,

			"{locationInput}": ".locationInput",

			"{locationLatitude}": ".locationLatitude",

			"{locationLongitude}": ".locationLongitude",

			"{autoDetectButton}": ".autoDetectButton"
		}
	},

	function(self) { return {
		init: function() {
			// Reset values
			self.locationInput().val($.language('COM_KOMENTO_COMMENT_WHERE_ARE_YOU'));
			self.locationLongitude().val('');
			self.locationLatitude().val('');

			// Bind one time focus on locationInput
			self.locationInput().one('focus', function() {
				self.locationInput().val('');
			});

			var mapReady = $.uid("ext");

			window[mapReady] = function() {
				$.___GoogleMaps.resolve();
			}

			if (!$.___GoogleMaps) {

				$.___GoogleMaps = $.Deferred();

				if(window.google === undefined || window.google.maps === undefined) {
					Komento.require()
						.script(
							{prefetch: false},
							"https://maps.googleapis.com/maps/api/js?sensor=true&language=" + self.options.language + "&callback=" + mapReady
						);
				} else {
					$.___GoogleMaps.resolve();
				}
			}

			// Defer instantiation of controller until Google Maps library is loaded.
			$.___GoogleMaps.done(function() {
				self._init();
			});
		},

		_init: function() {

			self.geocoder = new google.maps.Geocoder();

			self.hasGeolocation = navigator.geolocation!==undefined;

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
							.autocomplete("close");

						self.setLocation(ui.item.location);
					}
				})
				.prop("disabled", false);

			// self.autocomplete = self.locationInput().autocomplete("widget");

			// self.autocomplete.addClass("location-suggestion");

			self.locationInput().addClass("location-suggestion");

			var initialLocation = $.trim(self.options.initialLocation);

			if (initialLocation) {

				self.getLocationByAddress(

					initialLocation,

					function(location) {

						self.setLocation(location[0]);
					}
				);

			};

			self.busy(false);
		},

		busy: function(isBusy) {
			self.locationInput().toggleClass("loading", isBusy);
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
				return fail("ERRCODE", "Browser does not support geolocation or do not have permission to retrieve location data.")
			}

			navigator.geolocation.getCurrentPosition(
				// Success
				function(position) {
					self.getLocationByCoords(position.coords.latitude, position.coords.longitude, success)
				},
				// Fail
				fail
			);
		},

		setLocation: function(location) {

			if (!location) return;

			self.locationResolved = true;

			self.lastResolvedLocation = location;

			self.locationInput()
				.val(location.formatted_address);

			self.locationLatitude()
				.val(location.geometry.location.lat());

			self.locationLongitude()
				.val(location.geometry.location.lng());
		},

		removeLocation: function() {

			self.locationResolved = false;

			self.locationInput()
				.val('');

			self.locationLatitude()
				.val('');

			self.locationLongitude()
				.val('');
		},

		buildDataset: function(locations) {

			var dataset = $.map(locations, function(location){
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
			if (address=="") {

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
					.autocomplete("search", "");
			}

			self.autoDetectButton().text($.language('COM_KOMENTO_FORM_LOCATION_AUTODETECT'));

			self.busy(false);
		},

		"{locationInput} blur": function() {

			// Give way to autocomplete
			setTimeout(function(){

				var address = $.trim(self.locationInput().val());

				// Location removal
				if (address=="") {

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

		"{autoDetectButton} click": function() {
			self.busy(true);

			self.autoDetectButton().text($.language('COM_KOMENTO_FORM_LOCATION_DETECTING'));

			if (self.hasGeolocation && !self.userLocations) {

				self.getUserLocations(self.suggestUserLocations);

			} else {

				self.suggestUserLocations();
			}
		},

		"{locationInput} keypress": function(el) {
			el.keypress(function(e) {
				if(e.which == 13) return false;
			});
		}
	}}
);

module.resolve();

// controller: end

	});
// require: end
});
