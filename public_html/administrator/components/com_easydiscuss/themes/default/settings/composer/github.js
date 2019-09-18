ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$('[data-gist-login]').on('click', function() {
		var width = 580;
		var height = 500;

		// Get the top and left 
		var top = (screen.height / 2) - (height / 2);
		var left = (screen.width / 2) - (width / 2);
		
		var url = 'index.php?option=com_easydiscuss&controller=autoposting&task=request&type=github';

		window.open(url, '', 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top);
	});

	window.doneLogin = function() {
		window.location = "<?php echo rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easydiscuss&view=settings&layout=composer&active=github'; ?>";
	};
});