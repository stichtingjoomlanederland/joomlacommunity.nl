(function($) {
	'use strict';

	$.fn.rsjoomlamap = function(options) {
		var base = this;
		
		base.el = this;
		
		base.init = function() {
			base.options = $.extend({},$.fn.rsjoomlamap.defaultOptions, options);
			
			// Initialize the Geocoder.
			base.geocoder = new google.maps.Geocoder();
			
			// Initialize the Bounds.
			base.latlngbounds = new google.maps.LatLngBounds();
			
			base.inputAddress = $('#'+base.options.address);
			base.inputCoords = $('#'+base.options.coordinates);
			base.multiMarks = base.options.markers != null ? base.options.markers.length > 1 : false;
			
			if (base.options.pinpointBtn) {
				base.pinpointBtn = $('#'+base.options.pinpointBtn);
			}
			
			base.directionsDisplay	= new google.maps.DirectionsRenderer({draggable: true});
			base.directionsService	= new google.maps.DirectionsService();
			base.directionsBtn		= $('#'+base.options.directionsBtn);
			base.directionsFrom		= $('#'+base.options.directionsFrom);
			base.directionNoResults	= base.options.directionNoResults;
			
			// Radius search values
			if (base.options.radiusSearch) {
				base.radiusLocation	= $('#'+base.options.radiusLocationId);
				base.radiusValue	= $('#'+base.options.radiusValueId);
				base.radiusUnit		= $('#'+base.options.radiusUnitId);
				base.radiusLoader	= $('#'+base.options.radiusLoaderId);
				base.radiusBtn		= $('#'+base.options.radiusBtnId);
				
				base.circle = null;
				base.markers = [];
				base.cache = [];
			}
			
			base.initMap();
			
			if (base.options.radiusSearch) {
				base.setRadiusPos();
				base.inputAddressOnKeyUp();
				base.bindRadiusSearch();
			} else {
				base.initMarker();
				
				if (!base.options.markers) {
					base.initPos();
				} else {
					base.initPositions();
				}
				
				base.setMarkerOnDragEnd();
				base.inputAddressOnKeyUp();
				base.inputCoordsOnChange();
				base.pinpoint();
				base.getDirections();
				base.panToCenter();
			}
		}
		
		// Initialize map.
		base.initMap = function() {
			base.map = new google.maps.Map(document.getElementById(base.el.prop('id')), {
				zoom: base.options.zoom,
				mapTypeId: base.options.mapType,
				streetViewControl: base.options.streetViewControl,
				scrollwheel: base.options.scrollwheel,
				zoomControl: base.options.zoomControl,
				styles: base.options.styles
			});
		};
		
		// Initialize marker.
		base.initMarker = function() {
			if (!base.options.markerDisplay)
				return;
			
			if (base.options.markers) {
				if (base.options.markers.length) {
					$(base.options.markers).each(function(i, el) {
						var themarker = 'marker' + i;
						
						if (el.position) {
							base.themarker = new google.maps.Marker({
								map: base.map,
								title: el.title,
								position : base.createLatLng(el.position),
								draggable: false,
								icon: base.options.markerIcon
							});
							
							if (el.content) {
								var content = el.content;
								var infowindow = new google.maps.InfoWindow();
							
								google.maps.event.addListener(base.themarker,'click', (function(themarker,content,infowindow) {
									return function() {
										infowindow.setContent(content);
										infowindow.open(base.map,themarker);
									};
								})(base.themarker,content,infowindow));
								
								google.maps.event.addListener(base.map, 'click', function() {
									infowindow.close();
								});
							}
							
							if (base.multiMarks) {
								base.latlngbounds.extend(base.themarker.getPosition());
							}
						} else if (el.address) {
							base.geocoder.geocode({'address': el.address}, function(results, status) {
								if (status == google.maps.GeocoderStatus.OK) {
									var lat = parseFloat(results[0].geometry.location.lat().toFixed(7));
									var lon = parseFloat(results[0].geometry.location.lng().toFixed(7));
									
									base.themarker = new google.maps.Marker({
										map: base.map,
										title: el.title,
										draggable: false,
										icon: base.options.markerIcon
									});
									
									base.themarker.setPosition(new google.maps.LatLng(lat,lon));
									
									if (el.content) {
										var content = el.content;
										var infowindow = new google.maps.InfoWindow();
									
										google.maps.event.addListener(base.themarker,'click', (function(themarker,content,infowindow) {
											return function() {
												infowindow.setContent(content);
												infowindow.open(base.map,themarker);
											};
										})(base.themarker,content,infowindow));
										
										google.maps.event.addListener(base.map, 'click', function() {
											infowindow.close();
										});
									}
								}
							});
						} else {
							base.themarker = null;
						}
					});
				} else {
					base.map.setCenter(base.createLatLng(base.options.center));
				}
			} else {			
				base.marker = new google.maps.Marker({
					map: base.map,
					draggable: base.options.markerDraggable,
					icon: base.options.markerIcon
				});
			}
		};
		
		base.initPositions = function() {
			if (base.options.markers) {
				if (base.multiMarks) {
					if (base.latlngbounds.getCenter().lat().toString() != 0 && base.latlngbounds.getCenter().lng().toString() != -180) {
						base.map.setCenter(base.latlngbounds.getCenter());
						base.map.fitBounds(base.latlngbounds);
					} else {
						base.map.setCenter(base.createLatLng(base.options.center));
					}
				} else {
					$(base.options.markers).each(function(i, el) {
						var themarker = 'marker' + i;
						base.map.setCenter(base.createLatLng(el.position));
						base.themarker.setPosition(base.createLatLng(el.position));
					});
				}
			}
		};
		
		// Initialize the map and marker position.
		base.initPos = function() {
			// 1st priority: the lat and lng options.
			if ( parseFloat(base.options.lat) || parseFloat(base.options.lng) )
			{
				base.setPos( new google.maps.LatLng(base.options.lat, base.options.lng) );
			}
			// 2nd priority: the coordinates input value.
			else if ( base.inputCoords.val() )
			{
				base.setPos(base.createLatLng(base.inputCoords.val()));
			}
			// 3rd priority: the address input value.
			else if ( base.inputAddress.val() )
			{
				base.geocoder.geocode( {address: base.inputAddress.val()}, function(results, status) 
				{
					if (status == google.maps.GeocoderStatus.OK) 
					{
						base.setPos(results[0].geometry.location);
					}
				});
			}
			// 4th priority: locations coordinates
			else if (base.options.locationCoordonates) 
			{
				base.setPos(base.createLatLng(base.options.locationCoordonates));
			}
			// 5th priority: base.options.center
			else if (base.options.center) 
			{
				base.setPos(base.createLatLng(base.options.center));
			}
			else {
				base.setPos( new google.maps.LatLng(0,0) );
			}
		};
		
		// Set the map and marker positon.
		base.setPos = function(latLng) {
			base.map.setCenter(latLng);
			
			if (base.options.markerDisplay) {
				base.marker.setPosition(latLng);
			}
		};
		
		// Create lat & lng from string
		base.createLatLng = function(string) {
			string = string.split(',');
			return new google.maps.LatLng(string[0],string[1]);
		};
		
		// Add a on drag end event to the marker.
		base.setMarkerOnDragEnd = function() {
			if (!base.options.markerDisplay)
				return;
			
			if (base.options.markers) 
				return;
			
			google.maps.event.addListener(base.marker, 'dragend', function() {
				base.geocoder.geocode({latLng: base.marker.getPosition()}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						base.inputCoords.val(base.marker.getPosition().toUrlValue());
						base.inputAddress.val(results[0].formatted_address);
							
						if (typeof base.options.markerOnDragEnd != 'function') {
							return;
						}
						
						// Call the user defined on drag event.
						base.options.markerOnDragEnd(results[0]);
					} else {
						base.inputAddress.val('');
					}
				});
			});
		};
		
		// Add a on key up event to the address input.
		base.inputAddressOnKeyUp = function() {
			var addressSelector = base.options.radiusSearch ? base.radiusLocation : base.inputAddress;
			
			addressSelector.on('keyup', function() {
				addressSelector.parent().find('.' + base.options.resultsWrapperClass).remove();
				
				if ($.trim(addressSelector.val())) {
					base.geocoder.geocode( {address: addressSelector.val()}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							var results_wrapper = $('<div class="' + base.options.resultsWrapperClass + '"><ul class="' + base.options.resultsClass + '"></ul></div>');
							addressSelector.after(results_wrapper);
							
							$(results).each(function(index, item) {
								var li = $('<li>' + item.formatted_address + '</li>').click(function() {
									if (base.options.radiusSearch) {
										addressSelector.val(item.formatted_address);
										base.map.setCenter(item.geometry.location);
									} else {
										addressSelector.val(item.formatted_address);
										base.inputCoords.val( item.geometry.location.lat().toFixed(7) + ',' + item.geometry.location.lng().toFixed(7) );
										base.setPos(item.geometry.location);
									}
									
									results_wrapper.remove();
								});
								
								results_wrapper.find('ul').append(li);  
							});
							
							$(document).click( function(event) {
								if( $(event.target).parents().index(results_wrapper) == -1 ) {
									results_wrapper.remove();
								}
							});
						}
					});
				} else {
					if (!base.options.radiusSearch) {
						base.inputCoords.val('');
						base.setPos( new google.maps.LatLng(0, 0) );
					}
				}
			});
		};
		
		// Add a pin-point trigger
		base.pinpoint = function() {
			var pinpoint = function() {
				if ($.trim(base.inputAddress.val())) {
					base.geocoder.geocode({address: base.inputAddress.val()}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							base.setPos(results[0].geometry.location);
							base.inputCoords.val(results[0].geometry.location.lat().toFixed(7) + ',' + results[0].geometry.location.lng().toFixed(7) );
						}
					});
				}
			}
			
			if (typeof base.pinpointBtn != 'undefined') {
				base.pinpointBtn.on('click', pinpoint);
			}
		};
		
		// 	Add a on change event to the coordinates input.
		base.inputCoordsOnChange = function() {
			var inputCoordsOnChange = function() {
				if (base.inputCoords.val() == '') {
					return;
				}
				
				var coordinatesString = base.inputCoords.val();
				if (coordinatesString.indexOf(',') != -1) {					
					base.geocoder.geocode({latLng: base.marker.getPosition()}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							base.inputAddress.val(results[0].formatted_address);
						} else {
							base.inputAddress.val('');
						}
					});
					
					base.setPos(new google.maps.LatLng(base.inputCoords.val()));
				}
			}
			
			base.inputCoords.on('keyup change', inputCoordsOnChange);
		};
		
		// 	Get directions button
		base.getDirections = function() {
			if (base.options.directionsPanel && base.directionsFrom && base.directionsBtn) {
				var getDirections = function() {
					base.directionsDisplay.setMap(base.map);
					base.directionsDisplay.setPanel(document.getElementById(base.options.directionsPanel));
					
					var request = {
						origin: base.directionsFrom.val(),
						destination: base.createLatLng(base.options.locationCoordonates),
						travelMode: google.maps.TravelMode.DRIVING
					};
					
					base.directionsService.route(request, function(response, status) {
						if (status == google.maps.DirectionsStatus.OK) {
							$('#rsepro-map-directions-error').css('display','none');
							base.directionsDisplay.setDirections(response);
						} else {
							base.directionsDisplay.setMap(null);
							$('#rsepro-map-directions-error').html(base.directionNoResults);
							$('#rsepro-map-directions-error').css('display','');
							$('#rsepro-directions-panel').html('');
						}
					});
				}
				
				base.directionsBtn.on('click', getDirections);
			}
		};
		
		base.panToCenter = function() {
			var panToCenter = function(position) {
				var currentzoom = base.map.getZoom();
				if (currentzoom <= 2) {
					base.map.panTo(position);
					base.map.setZoom(5);
				}
			}
			
			google.maps.event.addListener(base.map, 'click', function(e) {
				panToCenter(e.latLng);
			});
		};
		
		
		// Set the default position
		base.setRadiusPos = function() {
			var coords = {
				initialLat  : '',
				initialLong : ''
			};		
			base.process('initial');
			
			if (navigator.geolocation && base.options.use_geolocation) {
				navigator.geolocation.getCurrentPosition(function(position) {
					coords.initialLat 	= position.coords.latitude;
					coords.initialLong 	= position.coords.longitude;
					base.process('initial', coords)
				}, function() {
					base.process('initial');
				});
			}
		};
		
		// track the load of the map
		base.track = 0;
		
		base.process = function(type, coords) {	
			var use_geocoder = true;
			if (typeof coords != 'undefined') {
				use_geocoder = false;
			}
			
			if (use_geocoder) {
				base.geocoder.geocode({'address': base.radiusLocation.val()}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						if (base.track == 0) {
							base.map.setCenter(results[0].geometry.location);
							var searchCenter = results[0].geometry.location;
							base.processCircle(searchCenter);
							if (typeof type != 'undefined' && type == 'initial') {
								base.processCreateMarkers(results[0].geometry.location.lat(), results[0].geometry.location.lng());
							}
							base.track++;
						}
					}
				});
			} else {
				var initialLocation = new google.maps.LatLng(coords.initialLat, coords.initialLong);
				if (initialLocation) {
					base.map.setCenter(initialLocation);
					var searchCenter = initialLocation;
					base.processCircle(searchCenter);
					base.geocoder.geocode({'latLng': initialLocation}, function(results, status){
						if (status == google.maps.GeocoderStatus.OK) {
							 if (results[1]) {
								base.radiusLocation.val(results[1].formatted_address);
								if (typeof type != 'undefined' && type == 'initial') {
									base.processCreateMarkers(coords.initialLat, coords.initialLong);
								}
							 }
						}
					});
					base.track++;
				}
			}
		};
		
		base.processCircle = function (searchCenter) {
			var unit_value = base.radiusUnit.val() == 'miles' ? 1609.34 : 1000;
			var radiusValue = base.radiusValue.val() == '' ? 100 : base.radiusValue.val();
			var radius = parseInt(radiusValue, 10) * unit_value;
			
			if (base.circle) {
				base.circle.setMap(null);
			}
			
			base.circle = new google.maps.Circle({
				center:	searchCenter,
				radius: radius,
				fillOpacity: 0.35,
				fillColor: base.options.circleColor,
				map: base.map
			});
			
			var bounds = new google.maps.LatLngBounds();
			var foundMarkers = 0;
				
			for (var i = 0; i < base.markers.length; i++) {
				if (google.maps.geometry.spherical.computeDistanceBetween(base.markers[i].getPosition(), searchCenter) < radius) {
					bounds.extend( base.markers[i].getPosition() );
					base.markers[i].setMap(base.map);
					foundMarkers++;
				} else {
					base.markers[i].setMap(null);
				}
			}
			
			if (foundMarkers > 0) {
				if (bounds.getNorthEast().equals( bounds.getSouthWest())) {
					var extendPoint1 = new google.maps.LatLng( bounds.getNorthEast().lat() + 0.001, bounds.getNorthEast().lng() + 0.001 );
					var extendPoint2 = new google.maps.LatLng( bounds.getNorthEast().lat() - 0.001, bounds.getNorthEast().lng() - 0.001 );
					bounds.extend(extendPoint1);
					bounds.extend(extendPoint2);
				}
			 
				base.map.fitBounds(bounds);
			} else {
				base.map.fitBounds(base.circle.getBounds());
			}
		};
		
		base.processCreateMarkers = function (lat, lng) {
			base.radiusLoader.css('display', '');
			var radiusValue = base.radiusValue.val() == '' ? 100 : base.radiusValue.val();
			var filters	= $('.rsepro-filter-filters input').length ? '&' + $('.rsepro-filter-filters input').serialize() : '';
			var data = 'Itemid='+ parseInt($('#rsepro-itemid').text()) + filters + '&unit='+ base.radiusUnit.val() + '&radius=' + radiusValue + '&startpoint=' + lat + ',' + lng;
			var rse_root = typeof rsepro_root != 'undefined' ? rsepro_root : '';
			
			$.ajax({
				type: 'post',
				dataType: 'json',
				url: rse_root + 'index.php?option=com_rseventspro&task=rseventspro.getajaxmap',
				data: data,
				success: function(json) {
					
					if ($('#rsepro-map-results').length) {
						$('#rsepro-map-results').empty().parent().hide();
					}
					
					try {
						json.length;
						base.initInfoWindow();
						
						// Create the markers.
						$(json).each(function(i, element) {
							base.createMarker(element);
						});
					} catch(e) {}
					base.radiusLoader.css('display', 'none');
				}
			});
		};
		
		// Create marker
		base.createMarker = function(element) {
			var marker = new google.maps.Marker({
				position: base.createLatLng(element.coords),
				map: base.map,
				icon: base.options.markerIcon
			});
			
			if ($('#rsepro-map-results').length) {
				var $table 	= $('#rsepro-map-results');
				var $row 	= $('<tr>');
				
				// Image
				var $image 	= $(element.image);
				var $cell 	= $('<td>').append($image);
				$row.append($cell);
				
				// Title + Location
				var $title		= $(element.link)
				var $location 	= $('<p>').text(element.address);
				var $cell 		= $('<td>').append($title).append($location);
				$row.append($cell);
				
				// Button
				var $cell 	= $('<td>');
				var $button = $('<button type="button" class="btn"><i class="fa fa-map-marker"></i></button>').click(function(e){ 
					e.preventDefault();
					base.map.setCenter(base.createLatLng(element.coords));
					google.maps.event.trigger(marker, 'click');
					$('html, body').animate({
						scrollTop: $(base.el).offset().top
					}, 1000);
				});
				$cell.append($button);
				$row.append($cell);
				
				// Append row
				$table.append($row);
				
				$table.parent().show();
			}
			
			google.maps.event.addListener(marker, 'click', function() {
				base.infoWindow.setContent(element.content);
				base.infoWindow.open(base.map, marker);	
			});
			
			base.markers.push(marker);
		};
		
		// Clear markers.
		base.clearMarkers = function() {
			for (var i = 0; i < base.markers.length; i++) {
				base.markers[i].setMap(null);
			}
			
			base.markers = [];
		};
		
		// Initialize the info window.	
		base.initInfoWindow = function() {
			base.infoWindow = new google.maps.InfoWindow();	
		};
		
		base.bindRadiusSearch = function() {
			base.radiusBtn.on('click',function() {
				var errors = false;
				
				// Remove errors
				base.radiusLocation.parents('.control-group').removeClass('error');
				base.radiusValue.parents('.control-group').removeClass('error');
					
				// Validate location
				if (!$.trim( base.radiusLocation.val())) {
					base.radiusLocation.parents('.control-group').addClass('error');
					errors = true;
				}
					
				// Validate radius
				if ( !/^\d+$/.test( base.radiusValue.val() ) || parseInt( base.radiusValue.val(), 10 ) <= 0 ) {
					base.radiusValue.parents('.control-group').addClass('error');
					errors = true;
				}
					
				// Stop the execution of the function if there are errors
				if (errors) {
					$('html,body').animate({scrollTop: $('#adminForm').offset().top});
					return;
				}
				
				base.radiusLoader.css('display','');
				base.clearMarkers();
				
				base.geocoder.geocode( {'address': base.radiusLocation.val()}, function(results, status) {
					var radiusValue = base.radiusValue.val() == '' ? 100 : base.radiusValue.val();
					var filters	= $('.rsepro-filter-filters input').length ? '&' + $('.rsepro-filter-filters input').serialize() : '';
					var data = 'Itemid='+ parseInt($('#rsepro-itemid').text()) + filters + '&unit='+ base.radiusUnit.val() + '&radius=' + radiusValue;
					
					if (status == google.maps.GeocoderStatus.OK) {						
						data = data + '&startpoint=' + results[0].geometry.location.lat() + ',' + results[0].geometry.location.lng();
						base.createAjaxRequest(data);
					} else {
						base.createAjaxRequest(data);
					}
				});
			});
		};
		
		base.createAjaxRequest = function (data) {
			var rse_root = typeof rsepro_root != 'undefined' ? rsepro_root : '';
			$.ajax({
				type: 'post',
				dataType: 'json',
				url: rse_root + 'index.php?option=com_rseventspro&task=rseventspro.getajaxmap',
				data: data,
				success: function(json) {
					if ($('#rsepro-map-results').length) {
						$('#rsepro-map-results').empty().parent().hide();
					}
					
					try {
						json.length;
						base.initInfoWindow();
						
						// Create the markers.
						$(json).each(function(i, element) {
							base.createMarker(element);
						});
					} catch(e) {}
					
					base.track = 0;
					base.process();
					base.radiusLoader.css('display','none');
				}
			});
		};
		
		base.setOptions = function (data) {
			base.map.setOptions(data);
		};
		
		if (typeof google != 'undefined') {
			base.init();
		}
		
		return base;
	};
	
	// Set the default options
	$.fn.rsjoomlamap.defaultOptions = {
		address:				'',
		center:					null,
		lat:					null,
		lng:					null,
		coordinates:			'',
		pinpointBtn:			null,
		markers: 				null,
		zoom: 					5,
		mapType: 				google.maps.MapTypeId.ROADMAP, // See: https://developers.google.com/maps/documentation/javascript/maptypes#BasicMapTypes
		streetViewControl:		false,
		scrollwheel:			false,
		zoomControl:			true,
		inputAddress:			null,
		inputLat:				null,
		inputLng:				null,
		inputCoords:			null,
		directionsPanel:		null,
		directionsBtn: 			null,
		directionsFrom: 		null,
		directionNoResults:		'No directions were found.',
		markerDisplay: 			true,
		markerDraggable: 		false,
		markerOnDragEnd: 		null,
		markerIcon:				'',
		radiusSearch: 			0,
		use_geolocation: 		0,
		circleColor:			'#ff8080',
		radiusLocationId:		'rsepro-location',
		radiusValueId:			'rsepro-radius',
		radiusUnitId:			'rsepro-unit',
		radiusLoaderId: 		'rsepro-loader',
		radiusBtnId:	 		'rsepro-radius-search',
		resultsWrapperClass:	'rsepro-map-results-wrapper',
		resultsClass:			'rsepro-map-results',
		styles:					[]
	};
})(jQuery);