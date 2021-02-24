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

	<?php if ($view == 'ask') { ?>
	wrapper = $('[data-ed-attachments]');
	<?php } ?>

	// Clone the form once
	var clonedForm = wrapper.find('[data-ed-attachment-form]').clone();

	// Get the attachment limit
	var limitEnabled = <?php echo $this->config->get('enable_attachment_limit'); ?>;
	var limit = <?php echo $this->config->get('attachment_limit'); ?>;

	// maximum upload file size in bytes
	var maxSize = <?php echo $this->config->get('attachment_maxsize') * 1024 * 1024 ; ?>;
	var info = wrapper.find('[data-ed-attachment-info]');
	var list = wrapper.find('[data-ed-attachments-list]');


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

		var filesize = 0;

		if ($(fileInput)[0] != undefined && $(fileInput)[0].files[0] != undefined) {
			filesize = $(fileInput)[0].files[0].size;
		}

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

		// file upload validation
		var errorMsg = '';
		var supported = '<?php echo $this->config->get('main_attachment_extension'); ?>';

		if (!isExtensionAllowed(file)) {
			errorMsg = '<?php echo JText::_('COM_EASYDISCUSS_FILE_ATTACHMENTS_INVALID_EXTENSION', true); ?>'.replace('%1s', filename);
			errorMsg = errorMsg.replace('%2s', supported);
		}

		if (filesize > 0 && maxSize > 0 && filesize > maxSize) {
			errorMsg = '<?php echo JText::_('COM_ED_FILE_ATTACHMENTS_EXCEEDED_MAXSIZE', true); ?>'.replace('%1s', filename);
		}

		if (errorMsg) {
			var fullErrorMsg = '<div class="t-text--danger">' + errorMsg + '</div>';
			info.html(fullErrorMsg);

			// remove existing file input form
			form.remove();

			// reset the form
			resetAttachmentForm();

			return false;
		}

		// Set the file title
		var title = form.find('[data-item-title]');
		title.html(file.title);

		// Display the actions
		var actions = form.find('[data-item-actions]');
		actions.removeClass('t-d--none');

		// If not image type, hide the insert link
		if (file.type != 'image') {
			var insertLink = form.find('[data-item-actions]').children('[data-insert]');

			insertLink.remove();
		}

		// Remove the form class since this is no longer a form
		form.removeClass('ed-attachment-form');

		// Add it into the list
		form.appendTo(list);

		var itemCount = list.children();

		// if reached the limit, don't reset form
		if (itemCount.length < limit || !limitEnabled) {
			// Once it is added, we want to attach a new form to the list
			resetAttachmentForm();

			return;
		}
		
		info.html('<?php echo JText::_('COM_EASYDISCUSS_EXCEED_ATTACHMENT_LIMIT') ?>');
	};

	var resetAttachmentForm = function() {
		var form = clonedForm.clone();

		// Re-append a new form
		wrapper.append(form);
	};

	var newFile = wrapper.find('[data-ed-attachment-form]').find('[data-attachment-item-input]');

	/**
	 * When user selects a new file to attach
	 */
	$(document)
		.off('change.easydiscuss.attachment', newFile.selector)
		.on('change.easydiscuss.attachment', newFile.selector, function() {

		var element = $(this);
		var form = element.parents('[data-ed-attachment-form]');

		// Insert a new item on the result
		insertAttachment(form);
	});

	// When a reply form is edited / replied, reset the form
	$(document)
		.off('composer.form.reset', '[data-ed-composer-form]')
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


	/**
	 * Removing attachment item
	 */
	$(document)
		.off('click.remove.attachment', '[data-ed-attachment-item-remove]')
		.on('click.remove.attachment', '[data-ed-attachment-item-remove]', function() {
		var element = $(this);
		var item = element.parents('[data-attachment-wrapper]');
		var attachmentsList = item.parents('[data-ed-attachments-list]');
		var id = element.data('id');
		var self = this;

		this.getTotalItems = function() {
			return attachmentsList.children().length;
		};

		this.removeItem = function(row) {
			row.remove();

			var totalItems = self.getTotalItems();
			var diff = limit - totalItems;

			if (diff == 1 && limitEnabled) {
				resetAttachmentForm();

				info.html('<?php echo JText::sprintf('COM_EASYDISCUSS_ATTACHMENTS_INFO', $allowedExtensions); ?>');
			}
		};

		if (!id) {
			this.removeItem(item);
			return;
		}

		var self = this;

		// If the user is editing a post and trying to delete an attachment, we should warn them first.
		EasyDiscuss.dialog({
			"content": EasyDiscuss.ajax('site/views/attachments/confirmDelete', {"id": id}),
			"bindings": {
				"{submitButton} click": function() {

					// Hide the dialog
					EasyDiscuss.dialog().close();

					// Remove the item
					EasyDiscuss.ajax('site/views/attachments/delete', {
						"id": id
					}).done(function() {
						self.removeItem(item);
					});
				}
			}
		});
	});

	/**
	 * Inserting an attachment into the editor
	 */
	$(document)
		.off('click.insert.attachment', '[data-ed-attachments] [data-ed-attachments-list] [data-insert]')
		.on('click.insert.attachment', '[data-ed-attachments] [data-ed-attachments-list] [data-insert]', function() {
		var element = $(this);
		var item = element.parents('[data-attachment-wrapper]');
		var title = item.find('[data-item-title]').html();

		var editor = $('[data-ed-editor]');
		var value = editor.val();
		
		value += '[attachment]' + title + '[/attachment]';

		$('[data-ed-editor]').val(value);
	});
});
