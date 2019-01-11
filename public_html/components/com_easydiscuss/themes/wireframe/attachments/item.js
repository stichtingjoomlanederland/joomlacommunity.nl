ed.require(['edq', 'easydiscuss', 'jquery.fancybox'], function($, EasyDiscuss) {

	// Bind the delete attachment buttons
	var attachmentWrapper = $('[data-ed-attachment-item]');

	$(document)
		.on('click', '[data-ed-attachment-delete]', function() {
				
			var id = $(this).parents(attachmentWrapper.selector).data('id');
			var parent = $(this).parents(attachmentWrapper.selector);

			EasyDiscuss.dialog({
				content: EasyDiscuss.ajax('site/views/attachments/confirmDelete', { "id": id }),
				bindings: {
					"{submitButton} click": function() {

						// Remove the attachment
						parent.remove();

						// When the delete button is clicked, call the ajax method to remove the attachment
						EasyDiscuss.ajax('site/views/attachments/delete', {
							"id": id
						}).done(function() {

							// Hide the dialog
							EasyDiscuss.dialog().close();
						});
					}
				}
			})
		});

	// Apply fancybox
	var attachmentPreview = $('[data-ed-attachment-preview]');

	attachmentPreview.fancybox({
		type: 'image',
		wrapCSS: 'fancy--ed-image',

		helpers: {
			overlay: null
		}
	});

});


