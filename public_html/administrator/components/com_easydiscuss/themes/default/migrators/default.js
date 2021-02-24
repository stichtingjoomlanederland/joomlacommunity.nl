ed.require(['edq'], function($) {
	$.Joomla('submitbutton', function(action){

        if (action == 'migrators.purge' && confirm('<?php echo JText::_("COM_ED_CONFIRM_PURGE_HISTORY", true); ?>')) {
        	$.Joomla('submitform', ['purge']);
        	return;
        }

        $.Joomla('submitform', [action]);
	});

});