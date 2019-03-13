ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	var userId = <?php echo $profile->id; ?>;
	var buttonRemove = $('[data-ed-removeBadge]');

	buttonRemove.live('click', function() {
		if (confirm('<?php echo JText::_('COM_EASYDISCUSS_CONFIRM_REMOVE_BADGE', true); ?>')) {
			var badgeId = $(this).data('id');
			var element = $(this).parents('li');

			EasyDiscuss.ajax('admin/views/user/deleteBadge', {
				"badgeId" : badgeId,
				"userId" : userId
			}).done(function(state, message) {
				$(element).remove();
				if (buttonRemove.children().length < 1) {
					element.find('.badgeList .emptyList').show();
				}

				element.find('[data-ed-message]').html(message).removeClass('hidden').addClass('active');
			}).fail(function(message) {
				console.log(message);
			})
		}
	});

	var buttonSaveMessage = $('[data-ed-saveMessage]');

	buttonSaveMessage.live('click', function() {
		var badgeId = $(this).data('id');
		var element = $(this).parents('li');
		var customMessage = element.find('#customMessage').val();

		EasyDiscuss.ajax('admin/views/user/customMessage', {
			"badgeId" : badgeId,
			"customMessage" : customMessage,
			"userId" : userId
		}).done(function(state,message) {
			element.find('[data-ed-message]').html(message).removeClass('hidden').addClass('active');
		}).fail(function(state, message) {
				console.log(message);
		})
	});
});

