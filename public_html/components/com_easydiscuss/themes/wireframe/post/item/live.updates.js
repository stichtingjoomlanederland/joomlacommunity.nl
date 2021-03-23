ed.require(['edq', 'easydiscuss', 'eventsource', 'toastr'], function($, EasyDiscuss) {

	// Default container for ED toastr for live update
	var div = '<div id="ed"></div>';
	$(div).addClass('ed-toastr ed-toast-container toast-bottom-right').appendTo('body');

	var postWrapper = $('[data-ed-post-wrapper]');
	var id = postWrapper.data('id');

	// Get its current limitstart
	var pagination = postWrapper.find('[data-ed-pagination-list]');
	var limitstart = pagination.length > 0 ? pagination.find('li.active').data('limitstart') : '';

	// Customize the popup message first
	toastr.options = {
		"closeButton": true,
		"newestOnTop": false,
		"onclick": null,
		"toastClass": "ed-toast__item",
		"containerId": "ed",
		"closeHtml": '<div class="ed-toast__header"><button type="button" class="toast-close-button" role="button">×</button></div>',
		"closeClass": 'ed-toast__header-action',
		"contentWrapper": '<div class="ed-toast"></div>'
	};

	var showPopUpMessage = function(message, type, options, selector) {
		toastr.options.type = type;
		toastr.options.avatar = '';
		toastr.options.background = '#F9F9FA';

		if ((type == 'reply' || type == 'comment') && options) {
			toastr.options.avatar = '<div class="o-avatar-status-icon">' + options['avatarHTML']
					+ '<div class="o-avatar-status-icon__icon"><div class="o-avatar o-avatar--icon o-avatar--rounded t-bg--600">'
					+ '<i class="far fa-comment-dots fa-fw t-text--100"></i></div></div></div>';
		}

		if (selector) {
			toastr.options.scrollTo = selector;

			if (type == 'answer' && options['isAnswer']) {
				toastr.options.background = '#E6F8EF';
			}
		}

		toastr.custom(message);
	};

	var updates = function(data) {
		data.forEach(function(obj) {
			var data = JSON.parse(obj);

			var replyWrapper = postWrapper.find('[data-ed-post-replies-wrapper]');
			var commentWrapper = postWrapper.find('[data-ed-comments-wrapper][data-id="' + data.id + '"]');
			var commentList = commentWrapper.find('[data-ed-comment-list]');
			var itemWrapper = postWrapper.find('[data-ed-post-item][data-id="' + data.id + '"]');

			// Update the comments of the reply
			if (data.hasNewComments) {
				commentWrapper.find('[data-ed-comment-container]').removeClass('t-d--none');
				commentWrapper.find('[data-ed-comment-list-wrapper]').removeClass('is-empty');

				data.newComments.forEach(function(comment) {
					isAsc ? commentList.append(comment) : commentList.prepend(comment);

					var id = $(comment).data('id');
					var options = [];

					if (data.newCommentMessage[id]) {
						options['avatarHTML'] = data.commenterAvatar[id];
					}

					var selector = itemWrapper.find('[data-ed-comment-item][data-id="' + id + '"]');

					showPopUpMessage(data.newCommentMessage[id], 'comment', options, selector);
				});
			}

			if (data.isQuestion) {
				if (hasAnswer != data.hasAnswer) {
					hasAnswer = data.hasAnswer;
					var selector = '';
					var options = [];

					if (data.hasAnswer) {
						currentAnswerId = data.answerId;

						postWrapper.addClass('is-resolved');

						// Hide the reply since it has been marked as answer
						postWrapper.find('[data-ed-post-item][data-id="' + data.answerId + '"]').addClass('t-d--none');

						// Update the answer of the post
						answerWrapper.html(data.answer);

						options['isAnswer'] = true;

						selector = postWrapper.find('[data-ed-post-item][data-id="' + currentAnswerId + '"]').find('[data-ed-reply-card-wrapper]');
					} else {
						postWrapper.removeClass('is-resolved');

						// Remove the answer of the post
						answerWrapper.find('.is-answer').remove();

						// Show the reply back since it is not an answer anymore
						var nonAnswerItem = postWrapper.find('[data-ed-post-item][data-id="' + currentAnswerId + '"]');
						nonAnswerItem.removeClass('t-d--none');

						options['isAnswer'] = false;

						selector = nonAnswerItem.find('[data-ed-reply-card-wrapper]');
					}

					showPopUpMessage(data.answerMessage, 'answer', options, selector);
				}

				// Update the total replies of the post
				postWrapper.find('[data-ed-post-reply-counter]').html(data.totalReplies);

				// Update the replies of the post
				if (data.hasNewReplies) {
					data.replies.forEach(function(reply) {
						if (data.isAnswer) {
							return;
						}

						replyWrapper.removeClass('is-empty');

						data.repliesSort == 'oldest' ? replyList.append(reply) : replyList.prepend(reply); 

						var id = $(reply).data('id');
						var options = [];

						if (data.replyerAvatar[id]) {
							options['avatarHTML'] = data.replyerAvatar[id];
						}

						var selector = postWrapper.find('[data-ed-post-item][data-id="' + id + '"]').find('[data-ed-reply-card-wrapper]');

						showPopUpMessage(data.newReplyMessage[id], 'reply', options, selector);
					});
				}
			}
		});
	};

	// Get the current question and replies id on the page
	var ids = [];
	ids.push(id);

	postWrapper.find('[data-ed-reply-item]').each(function() {
		var replyId = $(this).data('id');

		ids.push(replyId);
	});

	var isAsc = <?php echo $this->config->get('main_comment_ordering') == 'asc' ? 1 : 0; ?>;
	var replyList = postWrapper.find('[data-ed-post-replies]');

	// The current values
	var answerWrapper = postWrapper.find('[data-ed-post-answer-wrapper]');
	var hasAnswer = answerWrapper.find('.is-answer').length > 0 ? true : false;
	var currentAnswerId = hasAnswer ? answerWrapper.find('[data-ed-reply-item]').data('id') : 0;

	<?php if ($this->config->get('system_single_polling')) { ?>
	var url = window.ed_site + 'index.php?option=com_easydiscuss&view=post&layout=updates&mode=SSE&ids=' + ids;
	var sse = new EventSource(url);

	// Get the live updates of the discussion entry
	sse.addEventListener('updates', function(event) {
		var data = JSON.parse(event.data);

		// Perform the updates
		updates(data);
	});
	<?php } else { ?>
		var interval = <?php echo (int) $this->config->get('system_polling_interval'); ?>;

		// Just in case, the user set it to zero, we set it to 5 seconds
		if (!interval) {
			interval = 5;
		}

		setInterval(function() {
			EasyDiscuss.ajax('site/views/post/updates', {
				"ids": ids,
				"interval": interval
			}).done(function(obj) {
				ids = obj.ids;

				updates(obj.data);
			});
		}, interval * 1000);
	<?php } ?>
});