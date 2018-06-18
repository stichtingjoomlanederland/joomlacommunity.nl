ed.require(['edq'], function($) {
	
	<?php if ($this->config->get('main_work_displaytimediff')) { ?>
	var days = [
			'<?php echo JText::_('SUNDAY', true); ?>',
			'<?php echo JText::_('MONDAY', true); ?>',
			'<?php echo JText::_('TUESDAY', true); ?>',
			'<?php echo JText::_('WEDNESDAY', true); ?>',
			'<?php echo JText::_('THURSDAY', true); ?>',
			'<?php echo JText::_('FRIDAY', true); ?>',
			'<?php echo JText::_('SATURDAY', true); ?>'
	];

	var currentTime = new Date();
	var currentDay = days[currentTime.getDay()];

	$('[data-user-day]').html(currentDay);
	
	var updateUserClock = function() {
		var currentTime = new Date();
		var currentHours = currentTime.getHours();
		var currentMinutes = currentTime.getMinutes();
		var currentSeconds = currentTime.getSeconds();

		// Pad the minutes and seconds with leading zeros, if required
		currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
		currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;

		<?php if ($this->config->get('main_work_hourformat') == '12') { ?>
			// Choose either "AM" or "PM" as appropriate
			var timeOfDay = ( currentHours < 12 ) ? "AM" : "PM";

			// Convert the hours component to 12-hour format if needed
			currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;

			// Convert an hours component of "0" to "12"
			currentHours = ( currentHours == 0 ) ? 12 : currentHours;

			$('[data-user-hour]').html(currentHours);
			$('[data-user-minute]').html(currentMinutes);
			$('[data-user-second]').html(currentSeconds);
			$('[data-user-meridiem]').html(timeOfDay);
		<?php } ?>


		<?php if ($this->config->get('main_work_hourformat') == '24') { ?>	
			$('[data-user-hour]').html(currentHours);
			$('[data-user-minute]').html(currentMinutes);
			$('[data-user-second]').html(currentSeconds);
		<?php } ?>
	};

	var updateServerClock = function() {
		Date.withOffset = function(offset) {
			// create Date object for current location
			d = new Date();

			// Convert to milisec
			// add local time zone offset
			// get UTC time in msec
			utc = d.getTime() + (d.getTimezoneOffset() * 60000);

			// create new Date object for different city
			// using supplied offset
			nd = new Date(utc + (3600000*offset));

			return nd;
		};

		var offset = <?php echo $offset;?>;

		var currentTime = Date.withOffset(offset);
		var currentHours = currentTime.getHours();
		var currentMinutes = currentTime.getMinutes();
		var currentSeconds = currentTime.getSeconds();

		// Pad the minutes and seconds with leading zeros, if required
		currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
		currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;

		
		<?php if ($this->config->get('main_work_hourformat') == '12') { ?>
			// Choose either "AM" or "PM" as appropriate
			var timeOfDay = (currentHours < 12) ? "AM" : "PM";

			// Convert the hours component to 12-hour format if needed
			currentHours = (currentHours > 12) ? currentHours - 12 : currentHours;

			// Convert an hours component of "0" to "12"
			currentHours = (currentHours == 0) ? 12 : currentHours;
			
			$('[data-server-hour]').html(currentHours);
			$('[data-server-minute]').html(currentMinutes);
			$('[data-server-second]').html(currentSeconds);
			$('[data-server-meridiem]').html(timeOfDay);
		<?php } ?>


		<?php if ($this->config->get('main_work_hourformat') == '24') { ?>	
			$('[data-server-hour]').html(currentHours);
			$('[data-server-minute]').html(currentMinutes);
			$('[data-server-second]').html(currentSeconds);
		<?php } ?>
	};

	setInterval(function() {
		// Set user's clock time
		updateUserClock();

		// Set support's clock time
		updateServerClock();
	}, 1000);

	<?php } ?>

});