ed.require(['edq'], function($) {

	$.Joomla('submitbutton', function(action) {

		var selected = new Array;

		$('[data-ed-table]').find('input[name=cid\\[\\]]:checked').each(function(i, el) {
			selected.push($(el).val());
		});

		if (action == 'assignBadge') {

			window.insertBadge = function(badgeId, userIds) {

				EasyDiscuss.ajax('admin/controllers/user/assignBadge', {
					"badgeId": badgeId, 
					"userIds": userIds
				}).done(function(redirectURL) {
						EasyDiscuss.dialog().close();
						window.location = redirectURL;
				});
			}

			EasyDiscuss.dialog({
				content: EasyDiscuss.ajax("admin/views/users/assignBadge", {"cid": selected})
			});

			return;
		}

		return $.Joomla('submitform', [action]);
	});
});