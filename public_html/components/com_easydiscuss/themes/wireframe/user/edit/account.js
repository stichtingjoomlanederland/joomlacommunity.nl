ed.require(['edq'], function($) {

$(document).on('click.upload', '[data-ed-upload-button]', function() {
	$('[data-ed-avatar-input]').click();
});

$(document).on('change', '[data-ed-avatar-input]', function() {
	var file = $('[data-ed-avatar-input]')[0].files[0];
	if (file){

		var tmp = file.name.split('.');
		var filename = file.name;
		var ext = tmp[tmp.length-1];

		if (filename.length > 28) {
			// manually shorten the filename
			filename =filename.replace('.' + ext, '');

			var firstPart = filename.substr(0, 10);
			var lastPart = filename.substr(-5);
			filename = firstPart + '...' + lastPart + '.' + ext;
		}

		$('[data-ed-avatar-filename]')
			.removeClass('t-hidden')
			.html(filename);
	}
});


$(document).on('click', '[data-ed-avatar-remove]', function() {
	EasyDiscuss.dialog({
		content: EasyDiscuss.ajax('site/views/profile/removeAvatar',{
		})
	});
});


$(document).on('click', '[data-ed-mark-allread]', function() {

	EasyDiscuss.ajax('site/views/profile/markAllRead', {
	}).done(function(message) {
		$('[data-ed-allread-status]').html(message);
	});

});

$(document).on('click', '[data-ed-check-alias]', function() {
	var element = $(this);
	var alias = $('[data-ed-alias-input]').val();
	var status = $('[data-ed-alias-status]');
	
	if (alias != '') {

		status.addClass('t-d--none');
		element.addClass('is-loading');

		EasyDiscuss.ajax('site/views/profile/checkAlias', {
			"alias": alias
		}).done(function(exists, message) {

			var className = exists ? 't-text--danger' : 't-text--success';

			status.removeClass('t-d--none')
			status.addClass(className);

			status.show();

			// Set the message
			status.html(message);

			element.removeClass('is-loading');
		});
	}

});

});