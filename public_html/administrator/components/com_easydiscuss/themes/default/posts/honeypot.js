ed.require(['edq', 'easydiscuss', 'admin/src/table'], function($, EasyDiscuss) {

$('[data-view-item]').on('click', function(event) {

	event.preventDefault();
	event.stopPropagation();

	var id = $(this).data('id');

	EasyDiscuss.dialog({
		"content": EasyDiscuss.ajax('admin/views/posts/honeypot', {"id": id})
	});

});

});
