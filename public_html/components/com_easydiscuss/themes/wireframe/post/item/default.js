<?php if ($this->config->get('main_syntax_highlighter')) { ?>
ed.require(['site/vendors/prism'], function() {
	Prism.highlightAll();
});
<?php } ?>


<?php if ($this->config->get('main_favorite')) { ?>
ed.require(['edq', 'site/src/favourites'], function($) {
});
<?php } ?>

<?php if ($this->config->get('main_likes_discussions') || $this->config->get('main_likes_replies')) { ?>
ed.require(['edq', 'site/src/likes'], function($) {});
<?php } ?>

<?php if ($this->config->get('main_commentpost') || $this->config->get('main_comment')) { ?>
ed.require(['edq', 'site/src/comments'], function($) {
});
<?php } ?>


<?php if ($print) { ?>
window.print();
<?php } ?>

<?php if ($this->config->get('main_ratings')) { ?>
ed.require(['edq', 'site/src/ratings'], function($) {
});
<?php } ?>

ed.require(['edq', 'easydiscuss', 'site/src/actions', 'site/src/polls', 'eventsource', 'toastr'], function($, EasyDiscuss) {
	var postWrapper = $('[data-ed-post-wrapper]');

	/**
	 * Triggered when a vote is casted
	 */
	$(document).on('easydiscuss.voted', function(event, id, direction, total, isReply) {
		if (!isReply) {
			postWrapper.find('[data-meta-votes]').text(total);
		}
	});

	/**
	 * Triggered when like is casted
	 */
	$(document).on('easydiscuss.likes', function(event, id, task, total, isReply) {
		if (!isReply) {
			postWrapper.find('[data-meta-likes]').text(total);
		}
	});
});


ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	// This is the alert for post view
	var alertMessage = $('[data-ed-post-notifications]');
	var alertSubmitMessage = $('[data-ed-post-submit-reply-notifications]');

	// Bind the lock buttons
	window.clearAlertMessages = function() {
		alertMessage
			.removeClass('alert-danger')
			.removeClass('alert-success')
			.removeClass('alert-info');
	};
});