ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$.Joomla('submitbutton', function(action) {

		if (action == 'resetRank') {

			EasyDiscuss.ajax('admin/views/ranks/resetRank', {
				'userid' : "<?php echo $profile->id; ?>"
			})
			.done(function(result, count) {

				EasyDiscuss.dialog({
					"content": "User rank has been reset successfully"
				});
			});

			return;
		}

		$.Joomla('submitform', [action]);
	});

});
