ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$('#category_id').on('change', function() {
		submitform();
	});

	$.Joomla('submitbutton', function(action){

		if (action == 'showMove') {

			EasyDiscuss.dialog({
				content: EasyDiscuss.ajax("admin/views/posts/showMoveDialog"),
				bindings: {
					"{moveButton} click": function() {
						Joomla.submitbutton('movePosts');
						EasyDiscuss.dialog().close();
					}
				}
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


	$('[data-moderate-dialog]').on('click', function() {

		var id = $(this).data('id');

		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('admin/views/posts/showApproveDialog', { "id": id }),
			bindings: {
				"{approveButton} click": function() {
					EasyDiscuss.ajax('admin/controllers/posts/publish', {
						"cid": [id]
					}).done(function(content) {

						// // Hide the dialog
						EasyDiscuss.dialog().close();
						// refresh current page
						location.reload();
					});
				},

				"{rejectButton} click": function() {
					EasyDiscuss.ajax('admin/controllers/posts/unpublish', {
						"cid": [id]
					}).done(function(content) {

						// // Hide the dialog
						EasyDiscuss.dialog().close();
						// refresh current page
						location.reload();
					});
				}
			}
		});

	});

	// attempt to load pagination via ajax.
	$(document).ready(function() {

		<?php if (!$browse) { ?>

		EasyDiscuss.ajax('admin/views/posts/pagination', {
			"type" : "questions",
			"search": "<?php echo $search; ?>",
			"state": "<?php echo $filter; ?>",
			"category": "<?php echo $categoryId; ?>",
			'limitstart': "<?php echo $limitstart; ?>"
		}).done(function(content) {
			$('[data-questions-pagination]')
				.removeClass('is-loading')
				.html(content);
		});

		<?php } ?>
	});

});
