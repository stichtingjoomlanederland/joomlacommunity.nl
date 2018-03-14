EasyDiscuss.module('comments' , function($) {

	var module = this;

	EasyDiscuss.require()
	.language(
		'COM_EASYDISCUSS_TERMS_PLEASE_ACCEPT',
		'COM_EASYDISCUSS_COMMENT_SUCESSFULLY_ADDED',
		'COM_EASYDISCUSS_COMMENT_LOAD_MORE',
		'COM_EASYDISCUSS_COMMENT_LOADING_MORE_COMMENTS',
		'COM_EASYDISCUSS_COMMENT_LOAD_ERROR'
	)
	.done(function() {

		EasyDiscuss.Controller(
			'Comment.List',
			{
				defaultOptions:
				{
					// Elements
					'{commentItems}'	: '.commentItem'
				}
			},
			function(self )
			{
				return {
					init: function()
					{
						// Implement controller on each comment item.
						self.commentItems().implement(EasyDiscuss.Controller.Comment.List.Item);
					}
				};
			}
		);

		EasyDiscuss.Controller(
			'Comment.List.Item',
			{
				defaultOptions:
				{
					// Properties.
					id: null,

					postId : null,

					// Elements
					'{deleteCommentLink}'	: '.deleteComment',
					"{convertCommentLink}"	: "[data-comment-convert-link]"
				}
			},
			function(self )
			{
				return {
					init: function()
					{
						self.options.id	= self.element.data('id');
						self.options.postId = self.element.data( 'post-id' );
					},

					'{deleteCommentLink} click' : function()
					{
						disjax.loadingDialog();
						disjax.load('comment' , 'confirmDelete' , self.options.id + '');
					},

					"{convertCommentLink} click" : function()
					{
						disjax.loadingDialog();
						disjax.load('comment' , 'confirmConvert' , self.options.id + '' , self.options.postId + '' );
					} 
				};
			}
		);

		EasyDiscuss.Controller(
			'Comment.LoadMore',
			{
				defaultOptions:
				{
					id: null,
					currentCount: 0
				}
			},
			function(self )
			{
				return {
					init: function()
					{
						// self.list is the list controller

						self.options.id = self.element.data('postid');

						self.doneLoading = false;
					},

					'{self} click': function(el)
					{
						if (el.enabled()) {
							el.disabled(true);

							self.element
								.addClass('btn-loading')
								.html($.language('COM_EASYDISCUSS_COMMENT_LOADING_MORE_COMMENTS'));

							EasyDiscuss.ajax('site.views.post.getComments', {
								id: self.options.id,
								start: self.list.commentItems().length
							}).done(function(html, nextCycle) {
								var elements = $(html).filter('li');

								elements.implement(EasyDiscuss.Controller.Comment.List.Item);

								self.list.element.append(elements);

								if (!nextCycle) {
									self.doneLoading = true;
									self.element.hide();
								} else {
									self.element.html($.language('COM_EASYDISCUSS_COMMENT_LOAD_MORE'));
								}

								el.enabled(true);
							}).fail(function() {
								self.element
									.addClass('btn-danger')
									.html($.language('COM_EASYDISCUSS_COMMENT_LOAD_ERROR'));
							}).always(function() {
								self.element.removeClass('btn-loading');
							});
						}
					}
				};
			});

		EasyDiscuss.Controller(
			'Comment.Form',
			{
				defaultOptions:
				{
					// Properties
					container: null,
					notification: null,
					commentsList: null,
					loadMore: null,
					termsCondition: null,

					// Elements
					'{commentMessage}'	: '.commentMessage',
					'{postId}'	: '.postId',
					'{commentTnc}'	: '.commentTnc',
					'{saveButton}'	: '.saveButton',
					'{cancelButton}': '.cancelButton',
					'{termsLink}'	: '.termsLink',
					'{commentLoader}'	: '.commentLoader'
				}
			},
			function(self)
			{
				return {

					init: function()
					{
					},

					resetForm: function()
					{
						// Reset the text area to empty.
						self.commentMessage().val('');

						// Reset the tnc checkbox.
						self.commentTnc().prop('checked' , false);
					},

					'{termsLink} click' : function()
					{
						// Load the terms and condition dialog.
						disjax.load('comment' , 'tnc');
					},

					'{cancelButton} click' : function()
					{
						self.options.container.toggle();
					},

					'{saveButton} click' : function()
					{
						if (!self.commentTnc().is(':checked') && self.options.termsCondition)
						{
							self.options.notification.html($.language('COM_EASYDISCUSS_TERMS_PLEASE_ACCEPT')).addClass('alert alert-error');
							return false;
						}

						// Let's try to post an ajax call now to save the comment.
						EasyDiscuss.ajax('site.views.comment.save' ,
						{
							'comment'	: self.commentMessage().val(),
							'id'	: self.postId().val(),
							'tnc'	: '1'
						},
						{
							beforeSend: function()
							{
								self.commentLoader().show();
							}
						})
						.done(function( html )
						{
							// Set the notification message.
							self.options.notification.html($.language('COM_EASYDISCUSS_COMMENT_SUCESSFULLY_ADDED')).removeClass('alert alert-error').addClass('alert alert-success');

							// Clear the comment form.
							self.resetForm();

							// Hide the comment form.
							self.options.container.hide();

							// Hide the comment loader
							self.commentLoader().hide();

							// Add a comment count
							EasyDiscuss.commentsCount = EasyDiscuss.commentsCount === undefined ? 1 : EasyDiscuss.commentsCount + 1;

							// Implement comment item controller
							// $( html ).implement( EasyDiscuss.Controller.Comment.List.Item );

							if (self.options.loadMore.length < 1 || self.options.loadMore.controller().doneLoading)
							{
								// Append the result to the page.
								$( html ).appendTo( self.options.commentsList )
									.addController( "EasyDiscuss.Controller.Comment.List.Item" );
							}

						})
						.fail(function(text )
						{
							// Append error message and display error.
							self.options.notification.html(text).addClass('alert alert-error');

							// Hide the comment loader
							self.commentLoader().hide();
						});
					}
				};
			}

		);

		module.resolve();
	});
});
