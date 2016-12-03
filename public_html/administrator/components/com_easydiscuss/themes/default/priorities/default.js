ed.require(['edq'], function($) {
	
	$.Joomla('submitbutton', function(action) {

        if (action == 'add') {
            window.location.href = '<?php echo JURI::root();?>administrator/index.php?option=com_easydiscuss&view=priorities&layout=form';
            return;
        }

		$.Joomla('submitform', [action]);
	});
});