ed.require(['edq', 'jquery.joomla'], function($) {

    $.Joomla('submitbutton', function(task) {

        if (task == 'cancel') {
            window.location = '<?php echo JURI::base();?>index.php?option=com_easydiscuss&view=emails';

            return;
        }

        $.Joomla('submitform', [task]);
        return false;
    })
});