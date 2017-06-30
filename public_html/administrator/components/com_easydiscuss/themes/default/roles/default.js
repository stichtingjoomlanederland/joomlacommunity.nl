ed.require(['edq', 'admin/src/table'], function($) {

	$.Joomla('submitbutton', function(action) {

        if (action == 'add') {

            window.location = '<?php echo JURI::base();?>index.php?option=com_easydiscuss&view=roles&layout=form';
            return;
        }

		if (action != 'remove' || confirm('<?php echo JText::_("COM_EASYDISCUSS_ROLES_DELETE_CONFIRM", true); ?>')) {
			$.Joomla( 'submitform' , [action] );
		}
	});
});