<?php if ($this->config->get('main_syntax_highlighter')) { ?>
// Prism support
ed.require(['site/vendors/prism'], function() {
	Prism.highlightAll();
});
<?php } ?>

<?php if ($this->config->get('main_location_discussion') || $this->config->get('main_location_reply')) { ?>
// Location support
ed.require(['edq', 'site/vendors/gmaps', self.getGmapsUrl()], function($, GMaps) {

	var wrapper = $('[data-ed-location]');

	if (wrapper.length <= 0) {
		return;
	}

	var map = wrapper.find('[data-ed-location-map]');

	$.each(map, function(i, item) {

		var iMap = $(item);

		var latitude = iMap.data('latitude');
		var longitude = iMap.data('longitude');

		var gmap = new GMaps({
								el: item,
								lat: latitude,
								lng: longitude,
								zoom: <?php echo $this->config->get('main_location_default_zoom');?>,
								mapType: '<?php echo $this->config->get('main_location_map_type');?>',
								width: '100%',
								height: '200px'
					});

		// Add the marker on the map
		gmap.addMarker({
		  lat: latitude,
		  lng: longitude,
		  title: 'Lima'
		});

	});

});

function getGmapsUrl() {
	var gmapsApiKey = "<?php echo $this->config->get('main_location_gmaps_key'); ?>";

	var gmapsUrl = 'https://maps.google.com/maps/api/js?language=<?php echo $this->config->get("main_location_language");?>';

	if (gmapsApiKey) {
		var gmapsUrl = 'https://maps.google.com/maps/api/js?key='+ gmapsApiKey +'&language=<?php echo $this->config->get("main_location_language");?>';
	}

	return gmapsUrl;
}
<?php } ?>


<?php if ($this->config->get('main_likes_discussions') || $this->config->get('main_likes_replies')) { ?>
ed.require(['edq', 'site/src/like'], function($) {
});
<?php } ?>

// Comment form
<?php if ($this->config->get('main_commentpost') || $this->config->get('main_comment')) { ?>
ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	var addCommentButton = $('[data-ed-toggle-comment]'),
		submitButton = $('[data-ed-comment-submit]'),
		loadMoreButton = $('[data-ed-comment-load-more]');

	addCommentButton.live('click', function() {
		$(this).siblings('[data-ed-comment-form]').toggleClass('t-hidden');
	});

	submitButton.live('click', function() {

		$(this).attr('disabled');

		var wrapper = $(this).parents('[data-ed-comments-wrapper]');
		var id = wrapper.data('id');
		var commentMessage = wrapper.find('[data-ed-comment-message]');
		var commentList = wrapper.find('[data-ed-comment-list]');

		var tncCheckbox = wrapper.find('[data-ed-comment-tnc-checkbox]:checked').length > 0;

		EasyDiscuss.ajax('site/views/comment/save', {
			"id": id,
			"comments": commentMessage.val(),
			"tncCheckbox": tncCheckbox
		}).done(function(content) {
			wrapper.removeClass('is-empty');

			<?php if ($this->config->get('main_comment_ordering') == 'asc') { ?>
			commentList.append(content);
			<?php } else { ?>
			commentList.prepend(content);
			<?php } ?>

			commentMessage.val('')
			$(this).attr('disabled', false);

		}).fail(function(message) {
			EasyDiscuss.dialog({
				"content": message
			});
		});

		return true;
	});

	loadMoreButton.live('click', function() {
		var wrapper = $(this).parents('[data-ed-comments-wrapper]');
		var id = wrapper.data('id');
		var commentList = wrapper.find('[data-ed-comment-list]');
		var totalCurrent = commentList.find('[data-ed-comment-item]').length;
		var loadButton = $(this);

		EasyDiscuss.ajax('site/views/post/getComments', {
			"id": id,
			"start": totalCurrent
		}).done(function(content, nextCycle) {
			commentList.append(content);
			
			if (!nextCycle) {
				loadButton.hide();
			}
		});

	});

	// delete comment
	$(document)
		.on('click', '[data-ed-comments-delete]', function() {

			var parent = $(this).parents('[data-ed-comment-item]');
			var id = parent.data('id');

			EasyDiscuss.dialog({
				content: EasyDiscuss.ajax('site/views/comment/confirmDelete', { "id": id }),
				bindings: {
					"{submitButton} click": function() {
						EasyDiscuss.ajax('site/views/comment/delete', {
							"id": id
						}).done(function(content) {

							if (content == false) {
								var wrapper = parent.parents('[data-ed-comments-wrapper]');
								wrapper.addClass('is-empty');
							};

							parent.remove();

							// Hide the dialog
							EasyDiscuss.dialog().close();
						});
					}
				}
			})
		});

	// Convert comment into a reply.

	var convertButton = $('[data-comment-convert-link]');

	convertButton.live('click', function() {
		var id = $(this).parents('[data-ed-comment-item]').data('id');
		var postId = $(this).parents('[data-ed-comments-wrapper]').data('id');

		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/comment/confirmConvert', {"id": id, "postId": postId}),
			bindings: {
				"{submitButton} click" : function() {
					EasyDiscuss.ajax('site/controllers/comments/convert', {
						"id": id,
						"postId": postId
					}).done(function() {
						window.location.reload(1);
					});
				}
			}
		})

	});



	// show term and conditions
	$(document)
		.on('click', '[data-ed-comment-tnc-link]', function() {
			EasyDiscuss.dialog({
				content: EasyDiscuss.ajax('site/views/comment/showTnc', {
				})
			})
		});
});

<?php } ?>


<?php if ($this->config->get('main_allowvote') || $this->config->get('main_allowquestionvote')) { ?>
ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	var voteButton = $('[data-ed-vote-button]');
	var counter = $('[data-ed-vote-counter]');
	var voteWrapper = $('[data-ed-post-vote]');

	voteButton.on('click.ed.vote', function() {

		// <this> is select when you click that button
		var id = $(this).parents(voteWrapper.selector).data('id');

		// Trigger the undo button
		var voteUndo = $(this).parents(voteWrapper.selector).find('[data-ed-vote-undo]');
		var direction = $(this).data('direction');

		var counterEle = $(this).siblings(counter.selector);

		EasyDiscuss.ajax('site/views/votes/add', {
			'id': id,
			'type': direction
		}).done(function(total) {

			// show vote undo link 
			voteUndo.removeClass('t-hidden');

			// update the vote count
			counterEle.text(total);

		}).fail(function(message) {
			EasyDiscuss.dialog({
				"content": message
			});
		});

	});

	// Vote undo LINK
	var voteUndoButton = $('[data-ed-vote-undo]');

	voteUndoButton.on('click.ed.vote.undo', function() {

		// <this> is select when you click that button
		var id = $(this).parents(voteWrapper.selector).data('id');
		var counterEle = $(this).siblings(counter.selector);

		EasyDiscuss.ajax('site/views/votes/undo', {
			'id': id
		}).done(function(total, successMsg) {

			// update the vote count
			counterEle.text(total);
			voteUndoButton.addClass('t-hidden');
			
			EasyDiscuss.dialog({
				"content": successMsg
			});            

		}).fail(function(failMsg) {
			EasyDiscuss.dialog({
				"content": failMsg
			});
		});

	});    
});
<?php } ?>

<?php if ($this->config->get('main_favorite')) { ?>
ed.require(['edq', 'site/src/favourite'], function($) {
});
<?php } ?>


<?php if ($print) { ?>
window.print();
<?php } ?>

<?php if ($this->config->get('main_ratings')) { ?>
ed.require(['edq', 'site/src/ratings'], function($) {
});
<?php } ?>


ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	// This is the actions wrapper
	var actionsWrapper = $('[data-ed-post-actions-bar]');
	var postWrapper = $('[data-ed-post-wrapper]');

	// This is the alert for post view
	var alertMessage = $('[data-ed-post-notifications]');
	var alertSubmitMessage = $('[data-ed-post-submit-reply-notifications]');

	// Bind question and answers buttons
	var qnaButtons = $('[data-ed-post-qna]');

	var quoteButton = $('[data-ed-post-quote]');

	// Moderation buttons
	var moderateButtons = $('[data-ed-post-moderation]');

	quoteButton.live('click', function() {

		var rawContent = $(this).find('input.raw_message').val();

		var editorType = "<?php echo $this->config->get('layout_editor'); ?>";



		if (editorType == 'bbcode') {

			var quoteContent = '[quote]' + rawContent + '[/quote]';

			var editor = $('[data-ed-editor]');

				 // update editor content.
			editor.val(quoteContent);
			editor.focus();

		} else {

			var quoteContent = '<blockquote>' + rawContent + '</blockquote><br />';

			if (window.CKEDITOR) {
				try {
					window.CKEDITOR.instances['dc_content'].setData(quoteContent);
				} catch (e) {}
			} else {
				window.jInsertEditorText(quoteContent, 'dc_content');
			}
		}
	});


	qnaButtons.live('click', function() {
		var id = $(this).parents(actionsWrapper.selector).data('id');
		var task = $(this).data('task');

		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/post/' + task, {
				"id": id
			})
		});
	});

	// Bind the feature buttons
	var featureButtons = $('[data-ed-post-feature]');

	featureButtons.live('click', function() {
		var id = $(this).parents(actionsWrapper.selector).data('id');
		var task = $(this).data('task');

		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/post/' + task, {
				"id": id
			})
		});
	});

	// Bind the move post buttons
	var moveButton = $('[data-ed-post-move]');

	moveButton.live('click', function() {
		var id = $(this).parents(actionsWrapper.selector).data('id');

		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/post/move', {
				"id": id
			})
		})
	});

	moderateButtons.live('click', function() {
		var id = $(this).parents(actionsWrapper.selector).data('id');
		var task = $(this).data('task');

		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/post/' + task, {
				"id": id
			})
		})
	})	

ed.require(['edq', 'chosen'], function($) {
	// Bind the post merge buttons
	var mergeButton = $('[data-ed-post-merge]');

	mergeButton.live('click', function() {
		var id = $(this).parents(actionsWrapper.selector).data('id');

		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/post/mergeForm', {
				"id": id
			}).done(function() {
				var timeout = 150;
				setTimeout(function() {

					var suggest = $('[data-field-suggest]');

					suggest.chosen({
						width: "95%"
					});

				}, timeout);
			})
		})
	})
});

	// Bind the post branch buttons
	var branchButton = $('[data-ed-post-branch]');


	branchButton.live('click', function() {

		var id = $(this).parents(actionsWrapper.selector).data('id');

		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/post/branchForm', {
				"id": id
			})
		});

	});

	// Bind the post delete button
	var deleteButton = $('[data-ed-post-delete]');

	deleteButton.live('click', function() {
		var id = $(this).parents(actionsWrapper.selector).data("id");

		EasyDiscuss.dialog({
			"content": EasyDiscuss.ajax('site/views/post/confirmDelete', {
				"id": id
			})
		});
	});

	// Bind the ban user buttons
	var banUserButton = $('[data-ed-post-ban-user]');

	banUserButton.live('click', function() {

		var postId = $(this).parents(actionsWrapper.selector).data('id');

		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/post/banForm', {
				"id": postId
			})
		});
	});

	// Bind the post report button
	var reportButton = $('[data-ed-post-report]');

	reportButton.live('click', function() {

		// Get the post id
		var postId = $(this).parents(actionsWrapper.selector).data('id');

		// Display the dialog to report
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/reports/dialog', {
				"id": postId
			})
		});
	});

	// Bind the lock buttons
	window.clearAlertMessages = function() {
		alertMessage
			.removeClass('alert-danger')
			.removeClass('alert-success')
			.removeClass('alert-info');
	};

	// Bind lock / unlock buttons
	var lockButtons = $('[data-ed-post-lock-buttons]');

	lockButtons.live('click', function() {

		var id = $(this).parents(actionsWrapper.selector).data('id');
		var task = $(this).data('task');
		var namespace = 'site/views/post/' + task;

		// Add a class to the wrapper
		if (task == 'lock') {
			postWrapper.addClass('is-locked');
		} else {
			postWrapper.removeClass('is-locked');
		}

		// We don't need a dialog here.
		EasyDiscuss.ajax(namespace, {
			"id": id
		}).done(function(message) {

			// Clear notification messages
			window.clearAlertMessages();

			alertMessage
				.html(message)
				.addClass('o-alert o-alert--success');

		}).fail(function(message) {

			// Clear notification messages
			window.clearAlertMessages();

			// Append the failed message on notifications
			alertMessage
				.html(message)
				.addClass('o-alert o-alert--danger');
		});

	});

	// Bind the resolve / unresolve buttons
	var resolveButton = $('[data-ed-post-resolve]');

	resolveButton.live('click', function() {

		var id = $(this).parents(actionsWrapper.selector).data('id');
		var task = $(this).data('task');
		var namespace = 'site/views/post/' + task;

		// // If we are resolving a post, we need to set is-resolved class
		// if (task == 'resolve') {
		// 	postWrapper.addClass('is-resolved');
		// } else {
		// 	postWrapper.removeClass('is-resolved');
		// }

		// EasyDiscuss.ajax(namespace, {
		// 	"id": id
		// })
	
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax(namespace, {
				"id": id
			})
		});	
	});


	// reply accept button
	qnaButtons.live('click', function() {
		var id = $(this).parents(actionsWrapper.selector).data('id');
		var task = $(this).data('task');

		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/post/' + task, {
				"id": id
			})
		});
	});


	// Bind the post status buttons
	var statusButton = $('[data-ed-post-status]');

	statusButton.live('click', function() {

		var id = $(this).parents(actionsWrapper.selector).data('id');
		var status = $(this).data('status');

		EasyDiscuss.ajax('site/views/post/status', {
			"id": id,
			"status": status
		}).done(function(status, message) {
			// Display the message
			alertMessage
				.html(message)
				.addClass('alert alert-success');
		});

	});

	// Bind the edit reply button
	var editReplyButton = $('[data-ed-edit-reply]');

	editReplyButton.live('click', function() {

		var wrapper = $(this).parents('[data-ed-reply-item]');
		var id = wrapper.data('id');
		var seq = wrapper.find('[data-ed-post-reply-seq]').data('ed-post-reply-seq');
		var editorArea = wrapper.find('[data-ed-reply-editor]');

		EasyDiscuss.ajax('site/views/post/editReply', {
			"id": id,
			"seq": seq
		}).done(function(form) {

			// Append the form into the content area.
			editorArea.html(form);
		});

	});

	// Bind the lock poll button
	var lockPollsButton = $('[data-ed-post-poll-lock]');

	lockPollsButton.live('click', function() {

		var postId = $(this).parents('[data-ed-polls]').data('post-id');

		// This could be a question or a reply
		var wrapper = $(this).parents('[data-ed-post-item]');

		var pollsWrapper = wrapper.find('[data-ed-polls]');

		EasyDiscuss.ajax('site/views/polls/lock', {
			'id': postId
		}).done(function() {

			// Set the polls wrapper to be locked
			wrapper.addClass('is-lockpoll');

			// We need to disable all the polls choices
			pollsWrapper.find('[data-ed-poll-choice-checkbox]')
				.attr('disabled', 'disabled');
		});
	});

	// Bind the unlock poll button
	var unlockPollsButton = $('[data-ed-post-poll-unlock]');


	unlockPollsButton.live('click', function() {

		var postId = $(this).parents('[data-ed-polls]').data('post-id');

		// This could be a question or a reply
		var wrapper = $(this).parents('[data-ed-post-item]');

		var pollsWrapper = wrapper.find('[data-ed-polls]');

		EasyDiscuss.ajax('site/views/polls/unlock', {
			'id': postId
		}).done(function() {

			// Remove the locked class
			wrapper.removeClass('is-lockpoll');

			// We need to disable all the polls choices
			pollsWrapper.find('[data-ed-poll-choice-checkbox]')
				.removeAttr('disabled', 'disabled');

		});
	});


});
