ed.require(['edq', 'site/src/location'], function($, App) {

	App.execute('[data-ed-location-form]', {

		<?php if ($post->address) { ?>
			initialLocation: "<?php echo $post->address;?>",
		<?php } ?>

		height: '250px',
		width: '100%',
		mapType: 'ROADMAP',
		language: "<?php echo $this->config->get('main_location_language');?>",

		"{locationInput}": "input[name=address]",
		"{locationLatitude}": "input[name=latitude]",
		"{locationLongitude}": "input[name=longitude]"
	});
});
