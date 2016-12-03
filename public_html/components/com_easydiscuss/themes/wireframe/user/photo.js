ed.require(['edq', 'cropper'], function($) {

	var avatar = $('[data-ed-original-avatar]');
	var avatarContainer = $('[data-ed-original-avatar-container]');
	var button = $('[data-ed-avatar-crop-button]');
	var save = $('[data-ed-avatar-crop-save]');
	var cancel = $('[data-ed-avatar-crop-cancel]');
	var removeAvatar = $('[data-ed-avatar-remove]');
	var loading = $('[data-ed-avatar-loading]');
	var preview = $('[data-ed-avatar-preview]');
	var avatarPreview = $('[data-ed-avatar]');
	var message = $('[data-ed-avatar-crop-alert]');

	button.live('click', function(){

		avatarContainer.removeClass('t-hidden');
		button.hide();
		removeAvatar.hide();
		message.addClass('t-hidden');

		save.show();
		cancel.show();		
		avatar.cropper({
			aspectRatio: 1,
			autoCropArea: 1,
			preview: preview,
			viewMode: 1,
			zoomable: false,
			crop: function(e) {
				showPreview(e);
			}
		});

		function showPreview(coords) {

			avatarPreview.hide();
			preview.show();

			// set coordinates as global variable so our save function can use this informations.
			window.coordinates = coords;
		};
	});

	save.live('click', function(){

		loading.show();

		EasyDiscuss.ajax('site/views/profile/cropPhoto', {
			"x": coordinates.x,
			"y": coordinates.y,
			"w": coordinates.width,
			"h": coordinates.height,
		}).done(function(uri, messageHtml) {

			avatarPreview.attr('src', uri);
			
			avatarContainer.addClass('t-hidden');
			button.show();
			removeAvatar.show();

			save.hide();
			cancel.hide();
			message.removeClass('t-hidden');	
		}).always(function(){
			loading.hide();
		})
	});

	removeAvatar.live('click', function(){
        EasyDiscuss.dialog({
            content: EasyDiscuss.ajax('site/views/profile/removeAvatar',{
            })
        })
	});

	cancel.live('click', function(){

		preview.hide();
		avatarPreview.show();

		avatarContainer.addClass('t-hidden');
		button.show();
		removeAvatar.show();

		save.hide();
		cancel.hide();
	})
});