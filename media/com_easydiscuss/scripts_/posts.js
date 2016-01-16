EasyDiscuss.module('posts', function($) {

	var module = this;

	EasyDiscuss.require()
	.view('comment.form', 'post.notification')
	.script('comments', 'location')
	.language('COM_EASYDISCUSS_NOTIFICATION_NEW_REPLIES',
				'COM_EASYDISCUSS_NOTIFICATION_NEW_COMMENTS',
				'COM_EASYDISCUSS_PLEASE_SELECT_CATEGORY_DESC',
				'COM_EASYDISCUSS_POST_TITLE_CANNOT_EMPTY',
				'COM_EASYDISCUSS_POST_CONTENT_IS_EMPTY'
			)
	.done(function() {

		EasyDiscuss.Controller(
			'Post.Ask',
			{
				defaultOptions:
				{
					// Elements
					'{submitDiscussion}' : '.submitDiscussion'
				}
			},
			function(self)
			{
				return{
					init: function()
					{
					},

					'{submitDiscussion} click' : function()
					{
						// Disable the submit button if it's already pressed to avoid duplicate clicks.

						if ($(this).attr('disabled'))
						{
							return;
						}
						$(this).attr('disabled' , 'disabled');

						var errorString = '';
						var isError = false;

						var selectedCategory = $('.discuss-form *[name=category_id]').val();

						if (selectedCategory == 0 || selectedCategory.length == 0)
						{
							//$( '.categorySelection' ).addClass( 'error' );

							var msg = $.language('COM_EASYDISCUSS_PLEASE_SELECT_CATEGORY_DESC');
							errorString += '<li>' + msg + '</li>';

							isError = true;
						}

						if ($('#ez-title').val() == '' && $('#post-topic-title').val() == '')
						{
							//$( '#ez-title' ).addClass( 'error' );

							var msg = $.language('COM_EASYDISCUSS_POST_TITLE_CANNOT_EMPTY');
							errorString += '<li>' + msg + '</li>';

							isError = true;
						}

						// this discuss.getContent is a function from form.new.php
						var dcReplyContent = discuss.getContent();

						if (dcReplyContent == '')
						{
							//$( '#dc_reply_content' ).addClass( 'error' );

							var msg = $.language('COM_EASYDISCUSS_POST_CONTENT_IS_EMPTY');
							errorString += '<li>' + msg + '</li>';

							isError = true;
						}


						if (isError)
						{
							errorString = '<div class="alert alert-error"><ul class="unstyled">' + errorString + '</ul></div>';
							$('.ask-notification').html('');
							$('.ask-notification').append(errorString);

							$(document).scrollTop($('.ask-notification').offset().top);

							return false;
						}


						// Submit the form now.
						$('#dc_submit').submit();
						return false;
					}
				};
			}
		);

		EasyDiscuss.Controller(
			'Post.Moderator',
			{
				defaultOptions:
				{
					// Elements
					'{moderatorBtn}' : '.moderatorBtn',

					'{postModeratorList}' : '.post-moderator-list',

					'{moderatorList}': '.moderatorList'
				}
			},
			function(self)
			{
				return{
					init: function()
					{
						// default property
						if (self.moderatorBtn().parent('.dropdown_').hasClass('open')) {
							self.loadModeratorList();
						}
					},

					loadModeratorList: function() {

						length	= self.postModeratorList().size();

						if (length > 0) return;

						self.moderatorList().empty();

						// no data found. add loading icon into list
						var loader = '<li style="height:10px;"><div class="discuss-loader" style="margin-left:15px;"></div></li>';

						self.moderatorList().append(loader);

						EasyDiscuss.ajax('site.views.post.getModerators' ,
							{
								'id' : self.element.data('id'),
								'category_id' : self.element.data('category')
							} ,
							{
								success: function(html ) {

									//remove loader li
									self.moderatorList().empty();

									self.moderatorList().append(html);
								},
								fail: function() {
									//do nothing
								}
							});
					},

					'{moderatorBtn} click' : function()
					{
						self.loadModeratorList();
					},

					'{postModeratorList} click' : function(element )
					{
						var userId = $(element).data('userid');
						var postId = $(element).data('postid');

						EasyDiscuss.ajax('site.views.post.ajaxModeratorAssign',
							{
								'userId': userId,
								'postId': postId
							},
							{
								success: function(message) {
									$('.discuss-post-assign').html(message);
								},
								fail: function(message) {
									$('.discuss-post-assign').html(message);
								}
							});

					}


				};
			}
		);

		EasyDiscuss.Controller(
			'Post.Question',
			{
				defaultOptions:
				{
					id: null,

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

					'{postLocation}'	: '.postLocation',
					'{locationData}'	: '.locationData'
				}
			},
			function(self)
			{
				return{
					init: function()
					{
						// Implement comments list.
						self.commentsList().implement(EasyDiscuss.Controller.Comment.List);

						// Implement comment pagination.
						self.commentLoadMore().length > 0 && self.commentLoadMore().implement(EasyDiscuss.Controller.Comment.LoadMore, {
							controller: {
								list: self.commentsList().controller()
							}
						});

						// Initialize post id.
						self.options.id	= self.element.data('id');

						if(self.locationData().length > 0) {
							var mapOptions = $.parseJSON(self.locationData().val());
							self.postLocation().implement("EasyDiscuss.Controller.Location.Map", mapOptions);
						}
					},

					'{addCommentButton} click' : function()
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
			'PostItems',
			{
				defaultOptions: {
					activefiltertype: null,

					'{allPostsFilter}'	: '.allPostsFilter',
					'{newPostsFilter}'	: '.newPostsFilter',
					'{unResolvedFilter}'	: '.unResolvedFilter',
					'{unAnsweredFilter}'	: '.unAnsweredFilter',
					'{resolvedFilter}'		: '[data-filters-resolved]',

					'{sortLatest}'	: '.sortLatest',
					'{sortPopular}'	: '.sortPopular',

					'{ulList}' : 'ul.normal',
					'{loader}' : '.loader',
					'{pagination}' : '.dc-pagination'

				}
			},
			function(self) { return {
				init: function() {
				},

				doSort: function(sortType ) {
					filterType = self.options.activefiltertype;
					self.doFilter(filterType, sortType);
				},

				doFilter: function(filterType, sortType) {
					self.options.activefiltertype = filterType;

					if (sortType === undefined) sortType = 'latest';

					// show loading-bar
					self.loader().toggle();

					// always reset to latest tab.
					self.sortPopular().show();
					self.sortLatest().tab('show');

					self.ulList().children('li').remove();

					EasyDiscuss.ajax('site.views.index.filter' ,
						{
							'filter'	: filterType,
							'sort' : sortType,
							'id' : self.element.data('id'),
							'view': self.element.data('view')
						} ,
						{
							success: function(str, pagination ) {
								// hide loading-bar
								self.loader().toggle();
								self.ulList().append(str);
								self.pagination().html(pagination);
							},
							fail: function() {
								// hide loading-bar
								self.loader().toggle();
							}
						});
				},

				'{resolvedFilter} click' : function()
				{
					$('.filterItem').removeClass( 'active' );
					self.doFilter( 'resolved' );
					self.resolvedFilter().parent().addClass('active');
				},

				'{sortLatest} click': function() {
					self.doSort('latest');
				},

				'{sortPopular} click': function() {
					self.doSort('popular');
				},

				'{allPostsFilter} click': function() {
					$('.filterItem').removeClass( 'active' );
					self.doFilter('allposts');
					self.allPostsFilter().parent().addClass('active');

				},

				'{newPostsFilter} click': function() {
					$('.filterItem').removeClass( 'active' );
					self.doFilter('unread');
					self.newPostsFilter().parent().addClass('active');

					self.sortPopular().hide();
				},

				'{unResolvedFilter} click': function() {
					$('.filterItem').removeClass( 'active' );
					self.doFilter('unresolved');
					self.unResolvedFilter().parent().addClass('active');

				},

				'{unAnsweredFilter} click': function() {
					$('.filterItem').removeClass( 'active' );
					self.doFilter('unanswered');
					self.unAnsweredFilter().parent().addClass('active');

				}

			}; //end return
			} //end function(self)

		); //end EasyDiscuss.Controller

		EasyDiscuss.Controller(
			'Post.CheckNewReplyComment', {
				defaultOptions: {
					id: null,

					interval: 10,

					wrapper: {
						discuss: '#discuss-wrapper',
						notificationContainer: '.notificationContainer',
						notification: '.discussNotification',
						replyContainer: '.replyContainer',
						commentContainer: '.commentContainer',
						replyCount: '.replyCount',
						commentCount: '.commentCount',
						replyText: '.replyText',
						commentText: '.commentText'
					},

					discusswrapper: '#discuss-wrapper',

					notificationwrapper: '.discussNotification',

					view: {
						postNotification: 'post.notification'
					}
				}
			}, function(self) { return {
				init: function() {
					self.options.id = self.element.data('id');

					self.getCount().done(function(repliesCount, commentsCount) {
						EasyDiscuss.repliesCount = repliesCount;
						EasyDiscuss.commentsCount = commentsCount;

						self.autoCheck();
					});

					$(self.options.wrapper.discuss).append('<div class="notifications top-left notificationContainer"></div>');
				},

				autoCheck: function() {
					self.check = setTimeout(function() {
						self.checkCount().done(function() {
							self.autoCheck();
						});
					}, self.options.interval * 1000);
				},

				stopCheck: function() {
					clearTimeout(self.check);
				},

				getCount: function() {
					// get initial count first
					return EasyDiscuss.ajax('site.views.post.getUpdateCount', {
						id: self.options.id
					});
				},

				checkCount: function() {
					var checking = $.Deferred(),
						newReply = false,
						newComment = false;

					self.getCount().done(function(repliesCount, commentsCount) {
						newReply = repliesCount - EasyDiscuss.repliesCount;
						newComment = commentsCount - EasyDiscuss.commentsCount;

						self.newRepliesCount = repliesCount;
						self.newCommentsCount = commentsCount;

						(newReply > 0 || newComment > 0) && self.notify(newReply, newComment);

						checking.resolve();
					});

					return checking;
				},

				notify: function(newReply, newComment) {
					var notification = $(self.options.wrapper.notification);

					if (notification.length < 1) {
						var html = self.view.postNotification({
							newReply: newReply,
							newComment: newComment
						});

						$(self.options.wrapper.notificationContainer).notify({
							message: {
								html: html.toHTML()
							},
							fadeOut: {
								enabled: false
							},
							onClosed: function() {
								EasyDiscuss.repliesCount = self.newRepliesCount;
								EasyDiscuss.commentsCount = self.newCommentsCount;
							}
						}).show();
					} else {
						var replyContainer = notification.find(self.options.wrapper.replyContainer),
							commentContainer = notification.find(self.options.wrapper.commentContainer),
							replyCount = notification.find(self.options.wrapper.replyCount),
							commentCount = notification.find(self.options.wrapper.commentCount),
							replyText = notification.find(self.options.wrapper.replyText),
							commentText = notification.find(self.options.wrapper.commentText);

						newReply > 0 && replyContainer.length > 0 && replyCount.text(newReply) && replyContainer.is(':hidden') && replyContainer.show();
						newComment > 0 && commentContainer.length > 0 && commentCount.text(newComment) && commentContainer.is(':hidden') && commentContainer.show();

						newReply > 1 && replyText.text($.language('COM_EASYDISCUSS_NOTIFICATION_NEW_REPLIES'));
						newComment > 1 && commentText.text($.language('COM_EASYDISCUSS_NOTIFICATION_NEW_COMMENTS'));
					}
				}
			} }
		);

		module.resolve();
	});
});
