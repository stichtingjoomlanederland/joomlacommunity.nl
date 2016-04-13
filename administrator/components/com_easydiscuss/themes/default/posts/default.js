ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$('#category_id').on('change', function() {
		submitform();
	});

	$.Joomla('submitbutton', function(action){

		if (action == 'showMove') {

			EasyDiscuss.dialog({
				content: EasyDiscuss.ajax("admin/views/posts/showMoveDialog")
			});

			return;
		}

		if (action == 'movePosts') {

			var newCategory 	= $('#new_category' ).val();

			if( newCategory == 0 )
			{
				$( '#new_category_error' )
					.html( '<?php echo JText::_( 'COM_EASYDISCUSS_PLEASE_SELECT_CATEGORY' );?>' )
					.show();
				return false;
			}

			$( '#adminForm input[name=move_category]' ).val( newCategory );
		}

		if ( action != 'remove' || confirm('<?php echo JText::_('COM_EASYDISCUSS_CONFIRM_DELETE_POSTS', true); ?>')) {
			$.Joomla( 'submitform' , [action] )
		}
	});
});