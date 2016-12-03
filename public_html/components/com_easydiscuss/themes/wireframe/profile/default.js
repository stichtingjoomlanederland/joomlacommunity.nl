ed.require(['edq', 'easydiscuss', 'site/src/profile'], function($, EasyDiscuss, profile) {

    // Initialize the profile app.
    profile.execute('[data-profile-item]');

<?php if ($profile->latitude && $profile->longitude) { ?>
	function initialize() {

		var mapCanvas = document.getElementById('ed-user-map');
		var lat = <?php echo $profile->latitude; ?>;
		var lng = <?php echo $profile->longitude; ?>;
		var latLng = {lat: lat, lng: lng};

		var mapOptions = {
		  center: new google.maps.LatLng(lat, lng),
		  zoom: 15,
		  mapTypeId: google.maps.MapTypeId.ROADMAP
		}

		var map = new google.maps.Map(mapCanvas, mapOptions)

		var marker = new google.maps.Marker({
			position: latLng,
			map: map
		})
	}

	google.maps.event.addDomListener(window, 'load', initialize);    
<?php } ?>
    
	$('[data-ed-profile-compose]').on('click', function() {

	    // It needs to contain the user id otherwise this will not work
    	var id = $(this).data('userid');

	    // Displays the dialog to start a conversation.
	    EasyDiscuss.dialog({
	        content: EasyDiscuss.ajax('site/views/conversation/compose', {
	            "id": id
	        }),
	        bindings: {
	            "init": function() {
	                console.log('initializing');
	            }
	        }
	    });
	});
});
