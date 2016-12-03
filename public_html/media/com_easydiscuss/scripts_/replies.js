EasyDiscuss.module('replies', function($) {

	var module = this;

	EasyDiscuss.require()
	.view('comment.form')
	.script('comments', 'votes', 'location')
	.language(
		'COM_EASYDISCUSS_REPLY_LOADING_MORE_COMMENTS',
		'COM_EASYDISCUSS_REPLY_LOAD_ERROR')
	.done(function() {

		EasyDiscuss.Controller(
			'Replies',
			{
				defaultOptions:
				{
					termsCondition: null,

					sort: null,

					'{replyItem}': '.discussReplyItem'
				}
			},
			function(self)
			{
				return {

					init: function() {

						// Implement reply items.
						self.initItem(self.replyItem());
					},

					initItem: function(item, reinit) {

						item.implement(
								EasyDiscuss.Controller.Reply.Item,
								{
									controller: {
										parent: self
									},
									reinit: reinit,
									'termsCondition': self.options.termsCondition,

									enableMap: self.options.enableMap
								}
							);
					},

					addItem: function(html, reinit) {

						// Wrap item as jQuery object
						var replyItem = $(html);

						// Prepend/append item based on sorting
						if (self.options.sort == 'latest') {
							replyItem.prependTo(self.element);
						} else {

							if ($('.replyLoadMore').length == 0)
							{
								// If there's no read more on the page, just append it.
								replyItem.appendTo(self.element);
							}
							else
							{
								// check if load more controller exists and if all replies has been loaded
								$('.replyLoadMore').controller().loadedAllReplies && replyItem.appendTo(self.element);
							}
						}

						// Implement reply item
						self.initItem(replyItem, reinit);
					},

					replaceItem: function(id, html) {

						var replyItem = $(html);

						self.replyItem('[data-id=' + id + ']')
							.replaceWith(replyItem);

						self.initItem(replyItem);
					}
				};
			}

		);

		EasyDiscuss.Controller(
			'Reply.Item',
			{
				defaultOptions:
				{
					// Properties
					id: null,
					termsCondition: null,
					reinit: null,

					// Views
					view:
					{
						commentForm: 'comment.form'
					},

					// Elements
					'{addCommentButton}' : '.addComment',
					'{commentFormContainer}': '.commentFormContainer',
					'{commentNotification}'	: '.commentNotification',
					'{commentsList}'	: '.commentsList',
					'{commentLoadMore}'	: '.commentLoadMore',

					'{editReplyButton}' : '.editReplyButton',
					'{cancelReplyButton}' : '.cancel-reply',
					'{composerContainer}' : '.discuss-editor',
					'{composer}' : '.discuss-composer',

					'{alertMessage}': '.alertMessage',

					'{postLocation}'	: '.postLocation',
					'{locationData}'	: '.locationData'
				}
			},
			function(self )
			{
				return {
					init: function()
					{
						self.options.id = self.element.data('id');

						if(self.locationData().length > 0) {
							var mapOptions = $.parseJSON(self.locationData().val());
							self.postLocation().implement("EasyDiscuss.Controller.Location.Map", mapOptions);
						}

						// Implement comments list.
						self.commentsList().implement(EasyDiscuss.Controller.Comment.List);

						// Implement comment pagination.
						self.commentLoadMore().length > 0 && self.commentLoadMore().implement(EasyDiscuss.Controller.Comment.LoadMore, {
							controller: {
								list: self.commentsList().controller()
							}
						});

						if (self.options.reinit) {

							var postLocation = self.element.find('.postLocation');

							if (postLocation.length > 0) {

								var options = $.parseJSON(postLocation.find('.locationData').val());

								EasyDiscuss.require()
									.script('location')
									.done(function($) {
										postLocation.implement(
											'EasyDiscuss.Controller.Location.Map',
											options
										);
									});
							}

							if (EasyDiscuss.main_syntax_highlighter) {
								EasyDiscuss
									.require()
									.script('syntaxhighlighter')
									.done(function($) {
										self.element.find('.discuss-content-item pre').each(function(i, e) {
										hljs.highlightBlock(e);
									});
								});
							}

							self.find('.discuss-vote')
								.implement(
									EasyDiscuss.Controller.Votes,
									{
										viewVotes: EasyDiscuss.view_votes
									}
								);

							// Implement likes controller
							self.find('.attachmentsItem').implement(
								EasyDiscuss.Controller.Attachments.Item,
								{
								}
							);
						}
					},

					'{editReplyButton} click': function()
					{
						self.edit();
					},

					alert: function(message, type, hideAfter) {

						if (type === undefined) type = 'info';

						self.removeAlert();

						$('<div class="alert alertMessage"></div>')
							.addClass('alert-' + type)
							.html(message)
							.prependTo(self.composerContainer());

						if (hideAfter) {

							setTimeout(function() {

								self.alertMessage()
									.fadeOut('slow', function() {

										self.removeAlert();
									});
							}, hideAfter);
						}
					},

					removeAlert: function() {

						self.alertMessage().remove();
					},

					edit: function() {

						self.editReplyButton()
							.addClass('btn-loading');

						// Remove any existing composer
						EasyDiscuss.ajax('site.views.post.editReply', {id: self.options.id})
							.done(function(id, composer) {

								self.composer().remove();

								// Insert composer
								self.composerContainer()
									.append(composer);

								// Initialize composer
								discuss.composer.init('.' + id);
							})
							.fail(function() {

								self.alert('Unable to load composer.', 'error', 3000);
							})
							.always(function() {

								self.editReplyButton()
									.removeClass('btn-loading');
							});
					},

					'{composer} save': function(el, event, html) {

						var replyItem = $(html).filter('.discussReplyItem');

						if (replyItem.length > 0) {

							// self.composer().remove();

							self.parent.replaceItem(self.options.id, replyItem);

							var replies = $('.discussionReplies').controller();

							replies.initItem(replyItem, true);
						}
					},

					'{composer} cancel': function() {

						self.composer().remove();
					},

					'{addCommentButton} click': function()
					{
						// Retrieve the comment form and implement it.
						var commentForm = self.view.commentForm({
							'id'	: self.options.id
						});

						$(commentForm).implement(
							EasyDiscuss.Controller.Comment.Form,
							{
								container: self.commentFormContainer(),
								notification: self.commentNotification(),
								commentsList: self.commentsList(),
								loadMore: self.commentLoadMore(),
								termsCondition: self.options.termsCondition
							}
						);

						self.commentFormContainer().html(commentForm).toggle();
					}
				};
			}
		);

		EasyDiscuss.Controller(
			'Replies.LoadMore',
			{
				defaultOptions:
				{
					id: null,
					sort: null
				}
			},
			function(self)
			{
				return {
					init: function() {
						self.loadedAllReplies = false;
					},

					'{self} click': function(el) {
						if (el.enabled()) {

							// Disable load more button
							el.disabled(true);

							// Set button to loading mode
							el.addClass('btn-loading').html($.language('COM_EASYDISCUSS_REPLY_LOADING_MORE_COMMENTS'));

							// Call for more reply
							EasyDiscuss.ajax('site.views.post.getReplies', {
								id: self.options.id,
								start: self.list.replyItem().length,
								sort: self.options.sort
							}).done(function(html, nextCycle) {

								html = $(html);

								html.appendTo(self.list.element);

								var items = html.filter('li').find('.discussReplyItem');

								// var items = $(html).filter('li');

								// Implement reply controller
								items.implement(
									EasyDiscuss.Controller.Reply.Item,
									{
										controller: {
											parent: self.list
										},
										'termsCondition': self.list.options.termsCondition,
										'reinit': true
									}

								);

								// Check if there are more replies to load
								if (nextCycle) {
									el.enabled(true);
								} else {
									el.hide();
									self.loadedAllReplies = true;
								}
							}).fail(function() {
								el.addClass('btn-danger').html($.language('COM_EASYDISCUSS_REPLY_LOAD_ERROR'));
							}).always(function() {
								el.removeClass('btn-loading');
							});
						}
					}
				};
			}
		);

		module.resolve();
	});
});
