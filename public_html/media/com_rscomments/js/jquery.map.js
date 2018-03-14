var RSCLocation = {};

RSCLocation.$ = jQuery.noConflict();
RSCLocation.geocoder = new google.maps.Geocoder();

RSCLocation.init = function() {
	RSCLocation.addressLookup();
	
	RSCLocation.$('#rsc_detect_btn').on('click', function() {
		RSCLocation.detectAddress();
	});
}

RSCLocation.detectAddress = function() {
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			var coordinates = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			
			RSCLocation.geocoder.geocode({'latLng': coordinates}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					var inputBtn = RSCLocation.$('#rsc_location');
					var results_wrapper = RSCLocation.$('<div class="rsc-results-wrapper"><ul class="rsc-results"></ul></div>');
					inputBtn.after(results_wrapper);
					
					RSCLocation.$(results).each(function(index, item) {
						var li = RSCLocation.$('<li>' + item.formatted_address + '</li>').click(function() {
							inputBtn.val(item.formatted_address);
							RSCLocation.$('#rsc_coordinates').val( item.geometry.location.lat().toFixed(7) + ',' + item.geometry.location.lng().toFixed(7) );
							results_wrapper.remove();
						});
						
						results_wrapper.find('ul').append(li);  
					});
					
					RSCLocation.$(document).click( function(event) {
						if (RSCLocation.$(event.target).parents().index(results_wrapper) == -1 ) {
							results_wrapper.remove();
						}
					});
				}
			});
		});
	}
}

RSCLocation.addressLookup = function() {
	var inputBtn = RSCLocation.$('#rsc_location');
	
	inputBtn.on('keyup', function() {
		inputBtn.parent().find('.rsc-results-wrapper').remove();
		
		if (RSCLocation.$.trim(inputBtn.val())) {
			RSCLocation.geocoder.geocode( {address: inputBtn.val()}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					var results_wrapper = RSCLocation.$('<div class="rsc-results-wrapper"><ul class="rsc-results"></ul></div>');
					inputBtn.after(results_wrapper);
					
					RSCLocation.$(results).each(function(index, item) {
						var li = RSCLocation.$('<li>' + item.formatted_address + '</li>').click(function() {
							inputBtn.val(item.formatted_address);
							RSCLocation.$('#rsc_coordinates').val( item.geometry.location.lat().toFixed(7) + ',' + item.geometry.location.lng().toFixed(7) );
							results_wrapper.remove();
						});
						
						results_wrapper.find('ul').append(li);  
					});
					
					RSCLocation.$(document).click( function(event) {
						if (RSCLocation.$(event.target).parents().index(results_wrapper) == -1 ) {
							results_wrapper.remove();
						}
					});
				}
			});
		}
	});
}