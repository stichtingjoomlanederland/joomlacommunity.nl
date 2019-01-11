ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	var options = {
		limit: 0,
		editable: false,
		types: {
			'image': ["jpg","png","gif","jpeg"],
			'archive': ["zip","rar","gz","gzip"],
			'pdf': ["pdf"]
		}
	};

	var allowedExtensions = "<?php echo $allowedExtensions; ?>";

	var wrapper = $('[<?php echo $editorId;?>] [data-ed-attachments]');

	// Clone the form once
	var clonedForm = wrapper.find('[data-ed-attachment-form]').clone();

	// Get the file input
	var fileInput = wrapper.find('[data-attachment-item-input]');

	// Get the attachment limit
	var limitEnabled = <?php echo $this->config->get('enable_attachment_limit'); ?>;
	var limit = <?php echo $this->config->get('attachment_limit'); ?>;

	var info = wrapper.find('[data-ed-attachment-info]');

	var list = wrapper.find('[data-ed-attachments-list]');

	fileInput.live('change', function() {
		var el = $(this);
		var form = el.parents('[data-ed-attachment-form]');

		// Insert a new item on the result
		insertAttachment(form);
	});

	// When a reply form is edited / replied, reset the form
	$(document)
	.on('composer.form.reset', '[data-ed-composer-form]', function(){

		// If there is attachment form, remove it
		wrapper.find('[data-ed-attachment-form]').remove();

		// get back the cloned form
		var form = clonedForm.clone();

		// Re-append a new form
		wrapper.append(form);

		// Reset the info dom
		info.html('<?php echo JText::sprintf('COM_EASYDISCUSS_ATTACHMENTS_INFO', $allowedExtensions); ?>');

	});

	var existInArray = function(val, arr) {
		var exist = false;

		if (arr.length){
			for (var i=0; arr.length > i; i++ ) {
				if (val.toLowerCase() == arr[i].toLowerCase()) {
					exist = true;
					break;
				}
			}
		}

		return exist;
	}

	var getAttachmentType = function(filename) {

		var extension = filename.substr((filename.lastIndexOf('.') + 1));
		var type = 'default';

		// Image type
		if (existInArray(extension, options.types.image)) {
			type = 'image';
		}

		// Archive type
		if (existInArray(extension, options.types.archive)) {
			type = 'archive';
		}

		// Archive type
		if (existInArray(extension, options.types.pdf)) {
			type = 'pdf';
		}

		return type;
	};

	var isExtensionAllowed = function(file) {
		filename = file.title;

		var extension = filename.substr((filename.lastIndexOf('.') + 1));

		var exts = allowedExtensions.split(',');

		var index = $.inArray(extension, exts);

		// extension found. This mean the file extension is supported.
		if (index >= 0) {
			return true;
		}

		return false;
	};

	var insertAttachment = function(form) {

		var fileInput = form.find("input:not(:hidden)");

		// Get the file attributes
		var file = {
			title: fileInput.val(),
			type: getAttachmentType(fileInput.val())
		};

		// Chrome fix
		if (file.title.match(/fakepath/)) {
			file.title = file.title.replace(/C:\\fakepath\\/i, '');
		}

		// reset message.
		info.html('<?php echo JText::sprintf('COM_EASYDISCUSS_ATTACHMENTS_INFO', $allowedExtensions); ?>');

		if (!isExtensionAllowed(file)) {

			var error = '<?php echo JText::_('COM_EASYDISCUSS_FILE_ATTACHMENTS_INVALID_EXTENSION', true); ?>';
			errorMsg = error.replace('%1s', filename);

			fullErrorMsg = '<label class="o-alert o-alert--icon o-alert--danger">' + errorMsg + '</label>';

			info.html(fullErrorMsg);
			return false;
		}

		// Set the file title
		var title = form.children('[data-ed-attachment-item-title]');
		title.html(file.title);

		// Add the attachment type class
		form
			.removeClass('ed-attachment-form')
			.addClass('attachment-type-' + file.type)

		// If not image type, hide the insert link
		if (file.type != 'image') {
			var insertLink = form.children('[data-ed-attachment-item-insert]');
			insertLink.remove();
		}

		// Add it into the list
		form.appendTo(list);

		var itemCount = list.find('.attachment-item');

		// if reached the limit, don't reset form
		if (itemCount.length < limit || !limitEnabled) {
			// Once it is added, we want to attach a new form to the list
			resetAttachmentForm();
		} else {
			info.html('<?php echo JText::_('COM_EASYDISCUSS_EXCEED_ATTACHMENT_LIMIT') ?>');

		}

	};

	var resetAttachmentForm = function() {

		var form = clonedForm.clone();

		// Re-append a new form
		wrapper.append(form);
	};


	// Removing an attachment item
	var removeItem = wrapper.find('[data-ed-attachment-item-remove]');

	removeItem.live('click', function() {

		var item = $(this);
		var itemWrapper = item.parents('.attachment-item');
		var id = item.data('id');

		if (!id) {
			itemWrapper.remove();

			// Get the attachment count
			var itemCount = list.find('.attachment-item');
			var diff = limit - itemCount.length;

			// if it does not reach the limit, add the form
			if (diff == 1 && limitEnabled) {
				// Once it is removed, we want to attach a new form to the list
				resetAttachmentForm();

				info.html('<?php echo JText::sprintf('COM_EASYDISCUSS_ATTACHMENTS_INFO', $allowedExtensions); ?>');
			}
			return;
		}

		EasyDiscuss.dialog({
			"content": EasyDiscuss.ajax('site/views/attachments/confirmDelete', {"id": id}),
			"bindings": {
				"{submitButton} click": function() {

					// Hide the dialog
					EasyDiscuss.dialog().close();

					// Remove the item
					EasyDiscuss.ajax('site/views/attachments/delete', {
						"id": id
					}).done(function(){
						itemWrapper.remove();

						// Get the attachment count
						var itemCount = list.find('.attachment-item');
						var diff = limit - itemCount.length;

						// if it does not reach the limit, add the form
						if (diff == 1 && limitEnabled) {
							// Once it is removed, we want to attach a new form to the list
							resetAttachmentForm();

							info.html('<?php echo JText::sprintf('COM_EASYDISCUSS_ATTACHMENTS_INFO', $allowedExtensions); ?>');
						}
					});
				}
			}
		});
	});

	// Insert an attachment item
	var insertAttachmentItem = wrapper.find('[data-ed-attachment-item-insert]');

	insertAttachmentItem.live('click', function() {

		var editor = $('[data-ed-editor]');
		var value = editor.val();
		var file = $(this).siblings('[data-ed-attachment-item-title]').html();

		value += '[attachment]' + file + '[/attachment]';

		$('[data-ed-editor]').val(value);
	});

});
