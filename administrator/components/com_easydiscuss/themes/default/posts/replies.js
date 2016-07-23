ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	// Prism support
	<?php if ($this->config->get('main_syntax_highlighter')) { ?>
	ed.require(['site/vendors/prism'], function() {
	    Prism.highlightAll();
	});
	<?php } ?>

	$.Joomla('submitbutton', function(action){

		if ( action != 'remove' || confirm('<?php echo JText::_('COM_EASYDISCUSS_CONFIRM_DELETE_POSTS', true); ?>')) {
			$.Joomla( 'submitform' , [action] )
		}
	});
});
