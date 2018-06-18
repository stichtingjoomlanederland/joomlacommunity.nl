ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$('[data-linkedin-login]').on('click', function() {

		var width = 447;
		var height = 660;

		// Get the top and left 
		var top = (screen.height / 2) - (height / 2);
		var left = (screen.width / 2) - (width / 2);
		
		var url = '<?php echo $authorizationURL;?>';

		window.open(url, '', 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top);
	});

	var oauthURIinput = $('[data-oauthuri-input]');
	var oauthURIbutton = $('[data-oauthuri-button]');

	oauthURIbutton.on('click', function() {

		// change tooltip display word
		$(this).attr('data-original-title', '<?php echo JText::_('COM_ED_COPIED_TOOLTIP')?>').tooltip('show');

		// retrieve the input id
		var oauthInputId = $(this).siblings().attr('id');
		var selectedText = document.getElementById(oauthInputId);

		selectedText.select();
		document.execCommand("Copy");
	});

	// change back orginal value after mouse out
	oauthURIbutton.on('mouseout', function() {

		// change tooltip display word
		$(this).attr('data-original-title', '<?php echo JText::_('COM_ED_COPY_TOOLTIP')?>').tooltip('show');
	});

	window.doneLogin = function() {
		window.location.reload();
	};
});