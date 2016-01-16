Komento.module('komento.commenttools', function($){
var module = this;

Komento.require()
	.script('komento.commentlist', 'komento.common', 'komento.language')
	.library('dialog')
	.view('notifications/new.comment')
	.done(function(){
		Komento.Controller(
			'CommentTools',
			{
				defaults: {
					'{commentCounter}': '.commentCounter',
					'{sortOldest}': '.sortOldest a',
					'{sortLatest}': '.sortLatest a',
					'{sortButton}': '.kmt-sorting a',
					'{adminButton}': '.adminMode a',
					'{subscribeEmail}': '.subscribeEmail a',
					'{addYours}': '.kmt-addyours',
					view: {
						newComment: 'notifications/new.comment'
					}
				}
			},
			function(self)
			{ return {
				init: function(){
					if(Komento.options.acl.read_comment == 1 && Komento.options.konfig.enable_ajax_load_list == 1){
						self.loadCommentList();
					}

					if(Komento.options.config.enable_live_notification == 1){
						self.hasNew = 0;
						self.newCount = 0;
						self.checkNewComment();
					}
				},

				loadCommentList: function(){
					self.reloadComments(Komento.sort);
				},

				'{addYours} click': function() {
					if(self.kmt.form) {
						self.kmt.form.loadForm();
					}
				},

				'{sortOldest} click': function(el){
					if(self.sortButton().checkClick())
					{
						self.sortButton().removeClass('selected');
						el.loading().addClass('selected');
						self.reloadComments('oldest');
					}
				},

				'{sortLatest} click': function(el){
					if(self.sortButton().checkClick()){
						self.sortButton().removeClass('selected');
						el.loading().addClass('selected');
						self.reloadComments('latest');
					}
				},

				'{sortLikes} click': function(el){
					if(self.sortButton().checkClick()){
						self.sortButton().removeClass('selected');
						el.loading().addClass('selected');
						self.reloadComments('likes');
					}
				},

				'{adminButton} click': function(el){
					self.reloadComments(Komento.sort, 'all');
				},

				'{subscribeEmail} click': function(el){
					$.dialog({
						content: self.getSubscribeContent(),
						afterShow: function(){
							var dialog = $('.foundryDialog'),
								submit = dialog.find('.subscribe'),
								unsubscribe = dialog.find('.unsubscribe'),
								name = dialog.find('.subscribeName'),
								email = dialog.find('.subscribeEmail'),
								user = dialog.find('.subscribeUser'),
								error = dialog.find('.subscribeError');

							submit.click(function(){
								if(user.length > 0) {
									Komento.ajax('site.views.komento.subscribeuser', {
										user: user.val(),
										component: Komento.component,
										cid: Komento.cid
									}, {
										success: function() {
											if(self.kmt.form) {
												self.kmt.form.subscribeForm().text($.language('COM_KOMENTO_FORM_NOTIFICATION_SUBSCRIBED')).addClass('subscribed');
											}

											$('.foundryDialog').controller().close();
										}
									})
								} else {
									if(name.val() == '' || email.val() == '') {
										error.show();
										return false;
									}

									Komento.ajax('site.views.komento.subscribeguest', {
										name: name.val(),
										email: email.val(),
										component: Komento.component,
										cid: Komento.cid
									}, {
										success: function() {
											$('.foundryDialog').controller().close();

											el.remove();
										}
									})
								}
							});

							unsubscribe.click(function() {
								Komento.ajax('site.views.komento.unsubscribeuser', {
									user: user.val(),
									component: Komento.component,
									cid: Komento.cid
								}, {
									success: function() {
										if(self.kmt.form) {
											self.kmt.form.subscribeForm().text($.language('COM_KOMENTO_FORM_NOTIFICATION_UNSUBSCRIBED')).removeClass('subscribed');
										}

										$('.foundryDialog').controller().close();
									}
								});
							});
						}
					});
				},

				getSubscribeContent: function(){
					var dfd = $.Deferred();

					Komento.ajax('site.views.komento.getsubscribecontent', {
						component: Komento.component,
						cid: Komento.cid
					}, {
						success: function(html){
							dfd.resolve(html);
						}
					});

					return dfd;
				},

				reloadComments: function(sort, published){
					// cancel all replies first
					// There are cases that self.kmt is not ready yet
					if(self.kmt){
						self.kmt.commentlist.cancelReply();
					}

					$('.commentList').html('<img src="' + Komento.options.spinner + '" />' + $.language('COM_KOMENTO_COMMENTS_LOADING'));
					Komento.ajax('site.views.komento.reloadcomments', {
						component: Komento.component,
						cid: Komento.cid,
						contentLink: Komento.contentLink,
						options: {
							sort: sort,
							published: published
						}
					},
					{
						success: function(html, loadedCount, totalCount)
						{
							Komento.sort = sort;
							Komento.loadedCount = loadedCount;
							Komento.totalCount = totalCount;

							self.setCommentCounter(loadedCount);

							var newCommentList = $(html);

							$('.commentList').html(newCommentList).controller().init();

							self.sortButton()
								.enable()
								.doneLoading();

							$('.kmt-notification').remove();
						},

						fail: function()
						{
							$('.commentList').text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				checkNewComment: function(){
					var timer = Komento.options.config.live_notification_interval ? parseInt(Komento.options.config.live_notification_interval) * 1000 : 30000;
					setTimeout(function(){
						Komento.ajax('site.views.komento.checknewcomment', {
							component: Komento.component,
							cid: Komento.cid,
							total: Komento.totalCount
						},
						{
							success: function(hasNew, newCount){
								if( hasNew == 1 && newCount > self.newCount ){
									$('.kmt-notification').remove();

									var notification = self.view.newComment({count: newCount});

									notification.find('.reloadComments').bind('click', function(){
										self.kmt.tools.reloadComments(Komento.sort);
									});

									$('body').append(notification);
								}

								self.newCount = newCount;
								self.hasNew = hasNew;
							},

							fail: function(){

							},

							complete: function(){
								self.kmt.tools.checkNewComment();
							}
						})
					}, timer);
				},

				addCommentCounter: function(){
					var current = self.commentCounter().text();
					self.commentCounter().text(parseInt(current) + 1);
				},

				deductCommentCounter: function(count){
					if( count === undefined ){
						count = 1;
					}
					var current = self.commentCounter().text();
					self.commentCounter().text(parseInt(current) - count);

					Komento.loadedCount -= count;
					Komento.totalCount -= count;
				},

				setCommentCounter: function(count){
					if( count === undefined ){
						count = 0;
					}

					self.commentCounter().text(count);
				}
			} }
		);

		module.resolve();
	});
});
