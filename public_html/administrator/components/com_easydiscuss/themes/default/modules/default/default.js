ed.require(['edq'], function($) {

	$.Joomla('submitbutton' , function(task) {

		if (task == 'discover') {
			window.location = 'index.php?option=com_easydiscuss&view=modules&layout=discover';
			return false;
		}

		if (task == 'uninstall') {
			var selected = [];

			$('[data-table-grid]').find('input[name=cid\\[\\]]:checked').each(function(i , el ){
				selected.push($(el).val());
			});

			EasyDiscuss.dialog({
				content: EasyDiscuss.ajax('admin/views/dialogs/render', {
					'file' : 'admin/modules/dialog.delete'
				}),
				bindings: {
					"{submitButton} click": function() {
						$.Joomla('submitform', [task]);
					}
				}
			});

			return;
		}

		$.Joomla('submitform', [task]);

	});


});