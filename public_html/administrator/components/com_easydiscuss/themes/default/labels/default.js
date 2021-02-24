ed.require(['edq'], function($) {
	$.Joomla('submitbutton', function(action) {
		if (action == 'add') {
            window.location = '<?php echo JURI::base();?>index.php?option=com_easydiscuss&view=labels&layout=form';

            return;
        }

		$.Joomla('submitform', [action]);
	});
});