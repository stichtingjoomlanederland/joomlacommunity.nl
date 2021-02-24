ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$.Joomla('submitbutton', function(action){
		var selected = [];

		$('input[name=cid\\[\\]]:checked').each(function() {
			var value = $(this).val();
			selected.push(value);
		});

		if (action == 'reject') {
			reject(selected);
			return;
		}

		if (action == 'remove') {
			remove(selected);
			return;
		}

		if (action == 'publish') {
			approve(selected);
			return;
		}
	});

	function reject(ids) {
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('admin/views/posts/confirmReject', { "ids": ids }),
		});
	};

	function approve(ids) {
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('admin/views/posts/confirmApprove', { "ids": ids }),
		});
	};

	function remove(ids) {
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('admin/views/posts/confirmDelete', { "ids": ids }),
		});
	}

	$('[data-pending-reject]').on('click', function() {
		var id = $(this).data('id');
		reject([id]);
	});

	$('[data-pending-approve]').on('click', function() {
		var id = $(this).data('id');
		approve([id]);
	});


});
