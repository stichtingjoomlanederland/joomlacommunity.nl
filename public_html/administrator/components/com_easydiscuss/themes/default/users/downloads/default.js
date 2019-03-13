ed.require(['edq', 'admin/src/table'], function($) {

	$.Joomla('submitbutton', function(action) {
		if ((action == 'purgeAll')) {
			if (confirm('<?php echo JText::_("COM_ED_USER_DOWNLOAD_PURGE_ALL_CONFIRMATION", true); ?>')) {
				$.Joomla('submitform', [action]);
				return;
			}
		}

		if ((action == 'removeRequest')) {
			if (confirm('<?php echo JText::_("COM_ED_USER_DOWNLOAD_DELETE_CONFIRMATION", true); ?>')) {
				$.Joomla('submitform', [action]);
				return;
			}
		}

		$.Joomla( 'submitform' , [action] );
		return;
	});
});
