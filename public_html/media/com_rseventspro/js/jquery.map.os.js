(function($) {
	'use strict';

	$.fn.rsjoomlaomap = function(options) {
		var base = this;
		
		base.el = this;
		
		base.markers = [];
		
		base.init = function() {
			base.options = $.extend({},$.fn.rsjoomlaomap.defaultOptions, options);
			
			// Initialize the Geocoder.
			base.geocoder = L.esri.Geocoding.geocodeService(); // http://esri.github.io/esri-leaflet/api-reference/services/geocode-service.html
			
			// Initialize the Bounds.
			base.latlngbounds = L.latLngBounds();
			
			base.inputAddress = base.options.address.length ? $('#'+base.options.address) : false;
			base.inputCoords = base.options.coordinates.length ? $('#'+base.options.coordinates) : false;
			base.multiMarks = base.options.markers != null ? base.options.markers.length > 1 : false;
			
			if (base.options.pinpointBtn) {
				base.pinpointBtn = $('#'+base.options.pinpointBtn);
			}
			
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
			}
		}
		
		// Initialize map.
		base.initMap = function() {
			base.map = L.map(document.getElementById(base.el.prop('id')), {
				zoom: base.options.zoom,
				zoomControl: base.options.zoomControl,
				scrollWheelZoom: base.options.scrollwheel,
			});
			
			var tileLayer = L.tileLayer(base.options.tileLayer, {
				maxZoom: 18,
				attribution: base.options.attribution,
				id: base.options.tileType,
				accessToken: base.options.accessToken
			});
			
			tileLayer.addTo(base.map);
		};
		
		// Initialize marker.
		base.initMarker = function() {
			if (!base.options.markerDisplay)
				return;
			
			if (base.options.markers) {
				if (base.options.markers.length) {
					$(base.options.markers).each(function(i, el) {
						if (el.position) {
							base.markers[i] = L.marker(base.createLatLng(el.position), {
								title: el.title,
								draggable: false
							});
							
							if (el.content) {
								base.markers[i].on('click', function(event) {
									base.markers[i].unbindPopup();
									base.markers[i].bindPopup(base.stripslashes(el.content)).openPopup();
								});
							}
							
							if (el.icon) {
								base.markers[i].setIcon(L.icon({ iconUrl: el.icon }));
							}
							
							base.markers[i].addTo(base.map);
							
							if (base.multiMarks) {
								base.latlngbounds.extend(base.markers[i].getLatLng());
							}
						} else if (el.address) {
							base.geocoder.geocode().text(el.address).run(function(error, result) {
								if (typeof error == 'undefined' && result.results.length > 0) {
									base.markers[i] = L.marker(result.results[0].latlng, {
										title: el.title,
										draggable: false
									});
									
									if (el.content) {
										base.markers[i].on('click', function(event) {
											base.markers[i].unbindPopup();
											base.markers[i].bindPopup(base.stripslashes(el.content)).openPopup();
										});
									}
									
									if (el.icon) {
										base.markers[i].setIcon(L.icon({ iconUrl: el.icon }));
									}
									
									base.markers[i].addTo(base.map);
									
									if (base.multiMarks) {
										base.latlngbounds.extend(base.markers[i].getLatLng());
									}
								}
							});
						} else {
							base.markers[i] = null;
						}
					});
				} else {
					base.map.setView(base.createLatLng(base.options.center));
				}
			} else {
				base.marker = L.marker([0, 0], {
					draggable: base.options.markerDraggable,
				});
				
				base.marker.addTo(base.map);
			}
		};
		
		base.initPositions = function() {
			if (base.options.markers) {
				if (base.multiMarks) {
					setTimeout( function() {
						if (base.latlngbounds.getCenter().lat.toString() != 0 && base.latlngbounds.getCenter().lng.toString() != -180) {
							base.map.setView(base.latlngbounds.getCenter());
							base.map.fitBounds(base.latlngbounds);
						} else {
							base.map.setView(base.createLatLng(base.options.center));
						}
					}, 2000);
				} else {
					$(base.options.markers).each(function(i, el) {
						if (el.position) {
							base.map.setView(base.createLatLng(el.position));
							base.markers[i].setLatLng(base.createLatLng(el.position));
						} else if (el.address) {
							base.geocoder.geocode().text(el.address).run(function(error, result) {
								if (typeof error == 'undefined' && result.results.length > 0) {
									base.map.setView(result.results[0].latlng);
									base.markers[i].setLatLng(result.results[0].latlng);
								}
							});
						}
					});
				}
			}
		};
		
		// Initialize the map and marker position.
		base.initPos = function() {
			// 1st priority: the lat and lng options.
			if ( parseFloat(base.options.lat) || parseFloat(base.options.lng) )
			{
				base.setPos(L.latLng(base.options.lat, base.options.lng));
			}
			// 2nd priority: the coordinates input value.
			else if ( base.inputCoords.val() )
			{
				base.setPos(base.createLatLng(base.inputCoords.val()));
			}
			// 3rd priority: the address input value.
			else if ( base.inputAddress.val() )
			{
				base.geocoder.geocode().text(base.inputAddress.val()).run(function(error, result){
					if (typeof error == 'undefined' && result.results.length > 0) {
						base.setPos(result.results[0].latlng);
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
				base.setPos( L.latLng(0,0) );
			}
		};
		
		// Set the map and marker positon.
		base.setPos = function(latLng) {
			base.map.setView(latLng);
				
			if (base.options.markerDisplay) {
				base.marker.setLatLng(latLng);
			}
		};
		
		// Create lat & lng from string
		base.createLatLng = function(string) {
			string = string.split(',');
			return L.latLng(string[0],string[1]);
		};
		
		// Add a on drag end event to the marker.
		base.setMarkerOnDragEnd = function() {
			if (!base.options.markerDisplay)
				return;
			
			if (base.options.markers) 
				return;
			
			base.marker.on('dragend', function(event) {
				base.inputCoords.val( base.marker.getLatLng().lat.toFixed(7) +','+ base.marker.getLatLng().lng.toFixed(7) );
				
				base.geocoder.reverse().latlng(base.marker.getLatLng()).run(function(error, result) {
					if (typeof error == 'undefined') {
						base.inputAddress.val(result.address.Match_addr);
						
						if (typeof base.options.markerOnDragEnd != 'function')
							return;
							
						// Call the user defined on drag event.
						base.options.markerOnDragEnd(result);
					} else {
						base.inputAddress.val('');
					}
				});
			});
		};
		
		// Add a on key up event to the address input.
		base.inputAddressOnKeyUp = function() {
			var addressSelector = base.options.radiusSearch ? base.radiusLocation : base.inputAddress;
			
			$(addressSelector).bind('keyup', function() {
				addressSelector.parent().find('.' + base.options.resultsWrapperClass).remove();
					
				if ( $.trim( addressSelector.val() ) ) {	
					base.geocoder.geocode().text(addressSelector.val()).run(function(error, result){
						if (typeof error == 'undefined' && result.results.length > 0) {
							
							var results_wrapper = $('<div class="' + base.options.resultsWrapperClass + '"><ul class="' + base.options.resultsClass + '"></ul></div>');
							addressSelector.after(results_wrapper);
								
							$(result.results).each(function(index, item) {
								var li = $('<li>' + item.text + '</li>').click(function() {
									addressSelector.val(item.text);
									
									if (base.options.radiusSearch) {
										base.map.panTo(item.latlng);
									} else {
										base.inputCoords.val( item.latlng.lat.toFixed(7) + ',' + item.latlng.lng.toFixed(7) );
										base.setPos(item.latlng);
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
						base.setPos( L.latLng(0, 0) );
					}
				}
			});
		};
		
		// Add a pin-point trigger
		base.pinpoint = function() {
			var pinpoint = function() {
				if ($.trim(base.inputAddress.val())) {
					base.geocoder.geocode().text(base.inputAddress.val()).run(function(error, result){
						if (typeof error == 'undefined' && result.results.length > 0) {
							base.setPos(result.results[0].latlng);
							base.inputCoords.val( result.results[0].latlng.lat.toFixed(7) + ',' + result.results[0].latlng.lng.toFixed(7) );
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
					var coords = base.createLatLng(coordinatesString);
					
					if (!isNaN(coords.lat) && !isNaN(coords.lng)) {
						base.setPos(coords);
						
						base.geocoder.reverse().latlng(base.marker.getLatLng()).run(function(error, result) {
							if (typeof error == 'undefined') {
								base.inputAddress.val(result.address.Match_addr);
							} else {
								base.inputAddress.val('');
							}
						});
					}
				}
			}
			
			$(base.inputCoords).on('input', inputCoordsOnChange);
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
				base.geocoder.geocode().text(base.radiusLocation.val()).run(function(error, result) {
					if (typeof error == 'undefined' && result.results.length > 0) {
						if (base.track == 0) {
							base.map.setView(result.results[0].latlng);
							var searchCenter = result.results[0].latlng;
							base.processCircle(searchCenter);
							if (typeof type != 'undefined' && type == 'initial') {
								base.processCreateMarkers(result.results[0].latlng.lat, result.results[0].latlng.lng);
							}
							base.track++;
						}
					}
				});
			} else {
				var initialLocation = L.latLng(coords.initialLat, coords.initialLong);
				if (initialLocation) {
					base.map.setView(initialLocation);
					var searchCenter = initialLocation;
					base.processCircle(searchCenter);
					
					base.geocoder.reverse().latlng(initialLocation).run(function(error, result) {
						if (typeof error == 'undefined') {
							if (result) {
								base.radiusLocation.val(result.address.Match_addr);
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
				base.circle.remove();
			}
			
			base.circle = L.circle(searchCenter, {
				radius: radius,
				color: '#000000',
				fillColor: base.options.fillColor,
				fillOpacity: 0.35,
				weight: 1
			});
			
			base.circle.addTo(base.map);
			
			var bounds = L.latLngBounds();
			var foundMarkers = 0;
			
			for (var i = 0; i < base.markers.length; i++) {
				if ( L.latLng(base.markers[i].getLatLng()).distanceTo(searchCenter) < radius ) {
					bounds.extend( base.markers[i].getLatLng() );
					base.markers[i].addTo(base.map);
					foundMarkers++;
				} else {
					base.markers[i].remove();
				}
			}
			
			if (foundMarkers > 0) {
				if ( bounds.getNorthEast().equals( bounds.getSouthWest() ) ) {
					var extendPoint1 = L.latLng( bounds.getNorthEast().lat + 0.001, bounds.getNorthEast().lng + 0.001 );
					var extendPoint2 = L.latLng( bounds.getNorthEast().lat - 0.001, bounds.getNorthEast().lng - 0.001 );
					bounds.extend(extendPoint1);
					bounds.extend(extendPoint2);
				}
			 
				base.map.fitBounds(bounds);
			} else {
				base.map.fitBounds( base.circle.getBounds() );
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
			var marker = L.marker(base.createLatLng(element.coords));
			marker.addTo(base.map);
			
			if (element.icon) {
				base.markers[i].setIcon(L.icon({ iconUrl: element.icon }));
			}
			
			if ($('#rsepro-map-results').length) {
				var $table 	= $('#rsepro-map-results');
				var $row 	= $('<tr>');
				
				// Image
				var $image 	= $(element.image);
				var $cell 	= $('<td>').append($image);
				$row.append($cell);
				
				// Info
				var $button = $('<button type="button" class="btn btn-block"><i class="fa fa-map-marker"></i></button>').click(function(e){ 
					e.preventDefault();
					base.map.setView(base.createLatLng(element.coords));
					marker.fire('click');
					$('html, body').animate({
						scrollTop: $(base.el).offset().top
					}, 1000);
				});
				
				var $cell = $('<td>').append(element.info);
				$cell.append($button);
				
				$row.append($cell);
				
				// Append row
				$table.append($row);
				
				$table.parent().show();
			}
			
			marker.on('click', function(event) {
				marker.unbindPopup();
				marker.bindPopup(base.stripslashes(element.content)).openPopup();
			});
			
			base.markers.push(marker);
		};
		
		// Clear markers.
		base.clearMarkers = function() {
			for (var i = 0; i < base.markers.length; i++) {
				base.markers[i].remove();
			}
			
			base.markers = [];
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
				
				base.geocoder.geocode().text(base.radiusLocation.val()).run(function(error, result) {
					var radiusValue = base.radiusValue.val() == '' ? 100 : base.radiusValue.val();
					var filters	= $('.rsepro-filter-filters input').length ? '&' + $('.rsepro-filter-filters input').serialize() : '';
					var data = 'Itemid='+ parseInt($('#rsepro-itemid').text()) + filters + '&unit='+ base.radiusUnit.val() + '&radius=' + radiusValue;
					
					if (typeof error == 'undefined' && result.results.length > 0) {
						data = data + '&startpoint=' + result.results[0].latlng.lat + ',' + result.results[0].latlng.lng;
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
						// Create the markers.
						$(json).each(function(i, el) {
							base.createMarker(el);
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
		
		base.stripslashes = function(str) {
			return (str + '').replace(/\\(.?)/g, function (s, n1) {
				switch (n1) {
					case '\\':
						return '\\';
					case '0':
						return '\u0000';
					case '':
						return '';
					default:
						return n1;
				}
			});
		};
		
		base.init();
		
		return base;
	};
	
	// Set the default options
	$.fn.rsjoomlaomap.defaultOptions = {
		address:				'',
		center:					null,
		lat:					null,
		lng:					null,
		coordinates:			'',
		pinpointBtn:			null,
		markers: 				null,
		zoom: 					5,
		scrollwheel:			false,
		zoomControl:			true,
		inputAddress:			null,
		inputCoords:			null,
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
		resultsClass:			'rsepro-map-results'
	};
})(jQuery);