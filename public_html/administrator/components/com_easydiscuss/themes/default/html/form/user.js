ed.require(['edq'], function($) {

	var removeButton = $('[data-user-browser] [data-remove]');
	var browseButton = $('[data-user-browser] [data-browse]');

	window.selectUser = function(id, name) {
		$('#<?php echo $id;?>-placeholder').val(name);
		$('#<?php echo $id;?>').val(id);

		EasyDiscuss.dialog().close();
	}

	removeButton.on('click', function() {
		var button = $(this);
		var parent = button.parents('[data-user-browser]');

		// Reset the form
		parent.find('input[type=hidden]').val('');
		parent.find('input[type=text]').val('');
	});

	browseButton.on('click', function() {
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('admin/views/users/browse')
		});
	});
});
