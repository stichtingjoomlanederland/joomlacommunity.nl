ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$.Joomla('submitbutton', function(action){

		if ( action != 'remove' || confirm('<?php echo JText::_('COM_EASYDISCUSS_CONFIRM_DELETE_POSTS', true); ?>')) {
			$.Joomla( 'submitform' , [action] )
		}
	});

});
