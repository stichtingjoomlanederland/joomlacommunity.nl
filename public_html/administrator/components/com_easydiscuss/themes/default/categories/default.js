ed.require(['edq'], function($) {
	$.Joomla('submitbutton', function(action) {
		if (action != 'remove' || confirm('<?php echo JText::_('COM_EASYDISCUSS_ARE_YOU_SURE_CONFIRM_DELETE', true); ?>')) {
			$.Joomla( 'submitform' , [action] );
		}

		if (action == 'add') {
			window.location = "<?php echo JURI::root();?>administrator/index.php?option=com_easydiscuss&view=categories&layout=form";
			return;
		}
	});
});