ed.require(['edq', 'admin/src/table'], function($, reports) {

	var setTask = function(taskValue) {
		$('[data-ed-form-task]').val(taskValue);
	};

	var getSelectedItems = function() {
		var selected = [];

		$('[data-ed-table]').find('[data-ed-table-checkbox]:checked').each(function(i, el) {
			selected.push($(el).val());
		});

		return selected;
	};

	var validateSelectedItems = function() {
		var ids = getSelectedItems();

		if (ids.length <= 0) {
			alert('Please at least select a single post to be deleted');
			return false;
		}

		return true;
	};

	// Delete selected posts
	$('[data-ed-action="posts.delete"]').on('click', function() {

		if (validateSelectedItems()) {
			EasyDiscuss.dialog({
				"content": EasyDiscuss.ajax('admin/views/reports/deleteConfirm'),
				"bindings": {
					"{cancelButton} click": function() {
						EasyDiscuss.dialog.close();
					},
					"{deleteButton} click": function() {
						$.Joomla('submitform', ['deletePosts']);
					}
				}
			});
		}
	});

	// Publish selected posts
	$('[data-ed-action="publish"]').on('click', function() {
		if (validateSelectedItems()) {
			$.Joomla('submitform', ['publish']);
		}
	});

	// Unpublish selected posts
	$('[data-ed-action="unpublish"]').on('click', function() {
		if (validateSelectedItems()) {
			$.Joomla('submitform', ['unpublish']);
		}
	});

	$.Joomla('submitbutton', function(action) {
		$.Joomla('submitform', [action]);
	});

	$('[data-reports-preview]').on('click', function() {
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('admin/views/reports/preview', {
				"id" : $(this).data('id')
			})
		});
	});
});
