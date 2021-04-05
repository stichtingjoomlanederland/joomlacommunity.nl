ed.require(['edq'], function($) {
	$.Joomla('submitbutton', function(action){
		if (action == 'migrators.purge' && confirm('<?php echo JText::_("COM_ED_CONFIRM_PURGE_HISTORY", true); ?>')) {
			$.Joomla('submitform', ['purge']);
			return;
		}
		// we only want to handle purge button from the toolbar.
		return false;
	});
});