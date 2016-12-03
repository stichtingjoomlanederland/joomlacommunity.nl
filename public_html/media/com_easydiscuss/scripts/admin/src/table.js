ed.define('admin/src/table', ['edq'], function($) {

	var form = $('[data-ed-form]');
	var formTask = $('[data-ed-form-task]');

	// If there is no table, just skip this.
	if (form.length < 1) {
		return;
	}

	// Implement table.state
	$(document)
		.on('click', '[data-ed-table-state]', function() {

			var task = $(this).data('task');

			// Find the parent <tr>
			var parent = $(this).parents('tr');

			// Check the checkbox
			parent.find('[data-ed-table-checkbox]').attr('checked', 'checked');
			
			// Set the form's task.
			formTask.val(task);

			form.submit();
		});

	// When a checkbox is checked, we also need to update the boxchecked value
	$(document)
		.on('change', '[data-ed-table-checkbox]', function() {

			var checked = $(this).is(':checked');

			if (checked) {
				$('[data-ed-table-boxchecked]').val('1');
			}
		});

	// Implement table.filter
	$(document)
		.on('change', '[data-ed-table-filter]', function() {

			$.Joomla('submitform');
		});

	// Implement table.search
	$(document)
		.on('change', '[data-ed-table-search]', function() {

			$.Joomla('submitform');
		});

	$(document)
		.on('click', '[data-ed-table-search-submit]', function() {
			$.Joomla('submitform');
		});

	$(document)
		.on('click', '[data-ed-table-search-reset]', function() {

			// Reset the search input value
			$('[data-ed-table-search]').val('');

			$.Joomla('submitform');
		});
});