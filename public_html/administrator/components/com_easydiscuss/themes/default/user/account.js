ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {
	
	$('[data-ed-reset-rank]').on('click', function() {
        
        $(".resetMessage").addClass("discuss-loader");

		EasyDiscuss.ajax('admin/views/ranks/resetRank',
		{
			'userid' : <?php echo $profile->id; ?>
		})
		.done(function(result, count)
		{
			// Done
			$('[data-ed-reset-rank]').html('Reset Successfully');
			$('[data-ed-reset-rank]').addClass("disabled");

		})
		.fail(function(message )
		{
			// show error message
			$('[data-ed-reset-rank]').html('Failed to reset rank');
		});
    });

    $('[data-ed-remove-avatar]').on('click', function() {
        
		EasyDiscuss.ajax('admin/views/user/removeAvatar',
		{
			'userid' : <?php echo $profile->id; ?>
		})
		.done(function(avatar, message)
		{
			// Done
			$('[data-ed-remove-avatar]').html(message);
			$('[data-ed-remove-avatar]').addClass("disabled");
			$("#avatar").attr('src', avatar);

		});
    });

	//$('#signature').expandingTextarea();
	});