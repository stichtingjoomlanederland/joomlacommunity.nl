ed.require(['edq', 'easydiscuss', 'admin/src/table'], function($, EasyDiscuss) {

	window.deleteConfirm = function() {
		if( confirm( '<?php echo JText::_( 'COM_EASYDISCUSS_SPOOLS_CONFIRM_DELETE');?>' ) )
		{
			return true;
		}
		return false;
	}

	window.purgeConfirm	= function(){
		if( confirm( '<?php echo JText::_( 'COM_EASYDISCUSS_SPOOLS_CONFIRM_PURGE');?>' ) ) {
			return true;
		}
		return false;
	}

	$.Joomla('submitbutton' , function(action){

		if (action == 'purge') {
			if (!purgeConfirm()) {
				return false;
			}
		}

		if (action == 'remove') {
			if (!deleteConfirm()) {
				return false;
			}
		}

		$.Joomla( 'submitform' , [action] );
	});

	$('[data-mailer-preview]').on('click', function() {

        EasyDiscuss.dialog({
            content: EasyDiscuss.ajax('admin/views/spools/preview', {"id" : $(this).data('id')})
        });
    });
});