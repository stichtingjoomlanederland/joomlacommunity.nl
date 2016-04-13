ed.require(['edq','bootstrap.colorpicker'], function($) {

    $.Joomla('submitbutton', function(action) {

        if (action == 'cancel') {
            window.location.href = '<?php echo JURI::root();?>administrator/index.php?option=com_easydiscuss&view=priorities';
            return;
        }

        $.Joomla('submitform', [action]);
    });

	$('[data-ed-priority-colorpicker]').colorpicker();
});