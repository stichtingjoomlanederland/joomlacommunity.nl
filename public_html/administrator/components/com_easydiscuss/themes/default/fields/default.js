ed.require(['edq'], function($) {
    $.Joomla( 'submitbutton' , function(action){

        if (action == 'add') {
            window.location.href = '<?php echo JURI::root();?>administrator/index.php?option=com_easydiscuss&view=customfields&layout=form';

            return;
        }

        if (action == 'remove' && confirm('<?php echo JText::_("COM_EASYDISCUSS_CUSTOMFIELDS_DELETE_CONFIRM", true); ?>')) {
            $.Joomla('submitform', ['delete']);
            return;
        }

        $.Joomla('submitform', [action]);
    });
});