ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

    $('[data-ed-remove-avatar]').on('click', function() {
        
		EasyDiscuss.ajax('admin/views/user/removeAvatar', {
			'userid' : <?php echo $profile->id; ?>
		}).done(function(avatar, message) {
			// Done
			$('[data-ed-remove-avatar]').html(message);
			$('[data-ed-remove-avatar]').addClass("disabled");
			$("#avatar").attr('src', avatar);

		});
    });

	//$('#signature').expandingTextarea();
});