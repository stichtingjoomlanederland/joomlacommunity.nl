Komento.module('komento.famelist', function($) {
var module = this;

Komento.require()
	.library('ui/effect', 'dialog')
	.image(Komento.options.spinner)
	.script('sharelinks', 'markitup', 'komento.common', 'komento.commentitem', 'komento.language')
	.view(
		'dialogs/delete.single',
		'dialogs/delete.affectchild',
		'dialogs/unpublish.affectchild',
		'comment/item/edit.form',
		'dialogs/delete.attachment'
	)
	.done(function() {
		Komento.Controller(
			'FameList',
			{
				defaults: {
					'{commentList}': '.kmt-list',
					'{stickList}': '.stickList .kmt-list',
					'{loveList}': '.loveList .kmt-list',
					'{commentItem}': '.kmt-item',
					'{stickItem}': '.stickList .kmt-list .kmt-item',
					'{loveItem}': '.loveList .kmt-list .kmt-item',
					'{noComment}': '.kmt-empty-comment',
					'{loadMore}': '.loadMore',
					// Tabs
					'{navs}': '.navs',
					'{tabs}': '.kmt-tabs',
					// Comment Item
					'{commentText}'			: '.commentText',
					'{commentInfo}'			: '.commentInfo',
					'{commentForm}'			: '.commentForm',
					'{deleteButton}'		: '.deleteButton',
					'{editButton}'			: '.editButton',
					'{saveEditButton}'		: '.saveEditButton',
					'{editForm}'			: '.editForm',
					'{editInput}'			: '.editInput',
					'{replyButton}'			: '.replyButton',
					'{shareBox}'			: '.shareBox',
					'{reportButton}'		: '.reportButton',
					'{statusButton}'		: '.statusButton',
					'{statusOptions}'		: '.statusOptions',
					'{publishButton}'		: '.publishButton',
					'{unpublishButton}'		: '.unpublishButton',
					'{stickButton}'			: '.stickButton',
					'{likeButton}'			: '.likeButton',
					'{likesCounter}'		: '.likesCounter',
					'{parentLink}'			: '.parentLink',
					'{parentContainer}'		: '.parentContainer',
					'{socialButton}'		: '.socialButton',
					view: {
						editForm: 'comment/item/edit.form',
						deleteDialog: 'dialogs/delete.affectchild',
						publishDialog: 'dialogs/publish.affectchild',
						unpublishDialog: 'dialogs/unpublish.affectchild',
						deleteAttachment: 'dialogs/delete.attachment'
					}
				}
			},
			function(self)
			{ return {
				init: function() {
					if(self.navs().length > 0) {
						self.navs().eq(0).trigger('click');
					}
				},

				// initialise for ajax calls
				ajaxinit: function() {
					self.generateSharelinks();
				},

				generateSharelinks: function() {
					var callback = function() {
						self.socialButton().each(function(i, el) {
							$(el).sharelinks();
						});
					};

					self.generateShortLinks(callback);
				},

				generateShortLinks: function(callback) {
					var replaceUrl = function(url) {
						self.socialButton().each(function(index, element) {
							if( !$(element).attr('loaded') ) {
								var commentid = $(element).attr('commentid');
								var permalink = url + '#kmt-' + commentid;

								$(element).attr('url', permalink);
								$(element).parents('.kmt-share-balloon').find('.short-url').val(permalink);
							}
						});

						callback && callback();
					};

					if(!Komento.shortenLink) {
						$.shortenlink(Komento.contentLink, replaceUrl);
					} else {
						replaceUrl(Komento.shortenLink);
					}
				},

				/************** Tabs **************/
				'{navs} click': function(el) {
					var funcName = $(el).attr('func');
					var tabName = $(el).attr('tab');
					var tab = $('.' + tabName);

					if(!tab.attr('loaded'))
					{
						self[funcName]();

						if((tabName == 'stickList' && Komento.options.konfig.enforce_live_stickies != '1') || (tabName == 'loveList' && Komento.options.konfig.enforce_live_lovies != '1'))
						{
							tab.attr('loaded', 1);
						}
					}

					self.tabs().hide();
					tab.show();

					self.navs().removeClass('active');
					el.addClass('active');
				},

				loadMainList: function() {
					self.loadComments('main');
				},

				loadStickList: function() {
					if(Komento.options.acl.read_stickies == 1 && Komento.options.config.enable_stickies == 1 && Komento.options.konfig.enable_ajax_load_stickies == 1) {
						self.loadComments('stickies');
					}
				},

				loadLoveList: function() {
					if(Komento.options.acl.read_lovies == 1 && Komento.options.config.enable_lovies == 1 && Komento.options.konfig.enable_ajax_load_lovies == 1) {
						self.loadComments('lovies');
					}
				},

				loadComments: function(type) {
					var classname, sticked, threaded, sort;

					threaded = 0;

					switch(type) {
						case 'stickies':
							classname = '.stickList';
							sticked = 1;
							sort = 'default';
							limit = parseInt(Komento.options.config.max_stickies);
							break;
						case 'lovies':
							classname = '.loveList';
							sticked = 'all';
							sort = 'love';
							limit = parseInt(Komento.options.config.max_lovies);
							break;
						default:
							classname = '.mainList';
							sticked = 'all';
							sort = 'default';
							limit = parseInt(Komento.options.config.max_comments_per_page);
					}

					$(classname).html('<div class="loading"><img src="' + Komento.options.spinner + '" />' + $.language('COM_KOMENTO_COMMENTS_LOADING') + '</div>');

					Komento.ajax('site.views.komento.loadcomments', {
						type: type,
						component: Komento.component,
						cid: Komento.cid,
						sticked: sticked,
						threaded: threaded,
						sort: sort,
						limit: limit,
						contentLink: Komento.contentLink,
					},
					{
						success: function(html) {
							var newCommentList = $(html);

							$(classname).html(newCommentList);
							self.ajaxinit();
						},

						fail: function() {
							$(classname).text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				/************** List Actions **************/

				stickComment: function(item) {
					item = $(item);

					// remove depth indentation
					item.children('.kmt-wrap').attr('style', 'left-margin: 0px !important');

					var itemid = item.attr('id').split('-')[1];
					var inserted = 0;

					if(self.stickItem().length == 0) {
						self.loadStickList();
					} else {

						if(self.stickItem().length < Komento.options.config.max_stickies)
						{
							self.stickItem().each(function(i, el) {
								if($(el).attr('id').split('-')[1] > itemid) {
									$(el).before(item);
									inserted = 1;
									return;
								}
							});

							if(inserted == 0) {
								self.stickList().append($(item));
							}
						}
					}
				},

				unstickComment: function(commentid) {
					self.stickList().find('#' + commentid).remove();
				},

				/************** Comment Items **************/

				set: function(el) {
					self.item = el.itemset(self.options);
				},

				'{deleteButton} click': function(el) {
					self.set(el);
					self.showDeleteDialog();
				},

				'{editButton} click': function(el) {
					self.set(el);
					if(el.checkClick()) {
						if(el.checkSwitch()) {
							self.edit(el);
						} else {
							self.cancelEdit(el);
						}
					}
				},

				'{saveEditButton} click': function(el) {
					self.set(el);
					self.saveEdit(el);
				},

				'{replyButton} click': function(el) {
					self.set(el);
					if(Komento.options.konfig.enable_inline_reply == 1) {
						if(el.checkSwitch()) {
							self.reply(el);
						} else {
							self.cancelReply(el);
						}
					} else {
						self.kmt.form.staticReply(self.item.parentid);
					}
				},

				'{reportButton} click': function(el) {
					self.set(el);
					if(el.checkClick()) {
						if(el.checkSwitch()) {
							self.reportComment(el);
						} else {
							self.cancelreportComment(el);
						}
					}
				},

				'{unpublishButton} click': function() {
					self.set(el);
					if(self.childs > 0) {
						self.showUnpublishDialog(el);
					} else {
						self.unpublishComment(el);
					}
				},

				'{stickButton} click': function(el) {
					self.set(el);
					if(el.checkClick()) {
						if(el.checkSwitch()) {
							self.stick(el);
						} else {
							self.unstick(el);
						}
					}
				},

				'{likesCounter} click': function(el) {
					self.set(el);

					self.showLikesDialog();
				},

				'{likeButton} click': function(el) {
					self.set(el);
					if(el.checkClick()) {
						if(el.checkSwitch()) {
							self.like(el);
						} else {
							self.unlike(el);
						}
					}
				},

				'{parentLink} mouseover': function(el) {
					self.set(el);
					if(Komento.options.config.enable_threaded == 1) {
						$('#' + self.item.parentid).addClass('kmt-highlight');
					} else {
						self.item.element.mine.parentContainer.show();
						if(self.item.element.mine.parentContainer.attr('loaded') == 0) {
							self.item.element.mine.parentContainer.html('<img src="' + Komento.options.spinner + '" />');
							self.loadParent();
						}
					}
				},

				'{parentLink} mouseout': function(el) {
					self.set(el);
					if(Komento.options.config.enable_threaded == 1) {
						$('#' + self.item.parentid).removeClass('kmt-highlight');
					} else {
						self.item.element.mine.parentContainer.hide();
					}
				},

				'{parentLink} click': function(el) {
					self.set(el);
					var parent = $('.' + self.item.parentid);
					parent.highlight();
				},

				'{attachmentDelete} click': function(el) {
					self.set(el);

					self.showAttachmentDeleteDialog(el);
				},

				closeDialog: function() {
					if($('.foundryDialog').length > 0) {
						$('.foundryDialog').controller().close();
					}
				},

				edit: function() {
					var editWrap = self.item.mine.find('#' + self.item.commentid + '-edit');
					if(editWrap.length == 0) {
						Komento.ajax('site.views.komento.getcommentraw', {
							id: self.item.id
						},
						{
							success: function(comment) {
								self.item.element.mine.editButton
									.text($.language('COM_KOMENTO_COMMENT_EDIT_CANCEL'))
									.switchOff()
									.enable();

								var editForm = self.view.editForm({commentId: self.item.commentid, commentText: comment});
								self.item.element.mine.commentText.after(editForm);

								// set again for newly generated editform and editinput because previously set, dom for editform and editinput not yet created
								self.item.element.mine.editForm = $(editForm);
								self.item.element.mine.editInput = self.item.element.mine.editForm.find('.editInput');
								self.item.mine.data('item', self.item);

								if(Komento.options.config.enable_bbcode == 1) {
									self.item.element.mine.editInput.markItUp($.getBBcodeSettings());
								}
							},

							fail: function() {
								self.item.element.mine.editButton
									.text($.language('COM_KOMENTO_ERROR'));
							}
						});
					} else {
						editWrap.show();

						self.item.element.mine.editButton
							.text($.language('COM_KOMENTO_COMMENT_EDIT_CANCEL'))
							.switchOff()
							.enable();
					}

					// bugged
					// .after insert doesnt register at first
					// $('#' + commentId + '-edit').find('textarea').focus();
				},

				cancelEdit: function() {
					self.item.element.mine.editButton
						.text($.language('COM_KOMENTO_COMMENT_EDIT'))
						.switchOn()
						.enable();
					self.item.element.mine.editForm.hide();
				},

				saveEdit: function() {
					Komento.ajax('site.views.komento.editcomment', {
						id: self.item.id,
						edittedComment: self.item.element.mine.editInput.val()
					},
					{
						success: function(modified_html, modified_by, modified) {
							self.item.element.both.commentText.html(modified_html);
							self.item.element.both.commentInfo.text($.language('COM_KOMENTO_COMMENT_EDITTED_BY', modified_by, modified)).show();

							if(Komento.options.config.enable_syntax_highlighting == 1) {
								$.loadSHBrushes();
							}

							self.cancelEdit();
						},

						fail: function(data) {
							$.dialog({
								content: message
							});
						}
					});
				},

				reply: function() {
					var commentForm = $('.commentForm');

					$('.formAlert').hide().text('');;

					// only allow 1 reply at a time
					// revert all other cancelReplyButton upon clicking reply
					$(self.options['{replyButton}'])
						.switchOn()
						.find('span')
						.text($.language('COM_KOMENTO_COMMENT_REPLY'));

					self.item.element.mine.replyButton
						.switchOff()
						.find('span')
						.text($.language('COM_KOMENTO_COMMENT_REPLY_CANCEL'));

					self.kmt.form.reply(self.item);
				},

				cancelReply: function() {
					$(self.options['{replyButton}'])
						.switchOn()
						.find('span')
						.text($.language('COM_KOMENTO_COMMENT_REPLY'));

					self.kmt.form.cancelReply();
				},

				showDeleteDialog: function() {
					$.dialog({
						height: 150,
						content: self.view.deleteDialog(true, {childs: self.childs}),
						afterShow: function() {
							$('.foundryDialog').find('.delete-affectChild').click(function() {
								self.deleteComment(1);
							});

							$('.foundryDialog').find('.delete-moveChild').click(function() {
								self.deleteComment(0);
							});
						}
					});
				},

				deleteComment: function(affectChild) {
					$('.foundryDialog').find('.kmt-delete-status').show();

					Komento.ajax('site.views.komento.deletecomment', {
						id: self.item.id,
						affectChild: affectChild
					},
					{
						success: function() {
							self.closeDialog();

							if(affectChild) {
								self.deleteChild(self.item.id);
							} else {
								self.moveChildUp(self.item.id, self.item.parentid);
							}

							self.item.both.hide('fade', function() {
								self.item.both.remove();
							});
						},

						fail: function() {
							$('.foundryDialog').find('.kmt-delete-status').text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				deleteChild: function(id) {
					$('li[parentid="' + id + '"]').each(function() {

						// recursive function to delete other childs
						self.deleteChild($(this).attr('id'));
					}).hide('fade', function() {
						$(this).remove();
					});
				},

				moveChildUp: function(id, parentid) {
					$('li[parentid="' + id + '"]').attr('parentid', parentid).each(function() {

						// move child class up 1 level
						$(this).removeClass('kmt-child-' + $(this).attr('depth')).addClass('kmt-child-' + ($(this).attr('depth') - 1));

						// move depth up 1 level
						$(this).attr('depth', ($(this).attr('depth') - 1));

						// recursive function to move other childs
						self.moveChildUp($(this).attr('id'));
					});
				},

				reportComment: function() {
					Komento.ajax('site.views.komento.action', {
						id: self.item.id,
						type: 'report',
						action: 'add'
					},
					{
						success: function() {
							self.item.element.both.reportButton
								.text($.language('COM_KOMENTO_COMMENT_REPORTED'))
								.switchOff()
								.enable();
						},

						fail: function() {
							self.item.element.both.reportButton
								.text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				cancelreportComment: function() {
					Komento.ajax('site.views.komento.action', {
						id: self.item.id,
						type: 'report',
						action: 'remove'
					},
					{
						success: function() {
							self.item.element.both.reportButton
								.text($.language('COM_KOMENTO_COMMENT_REPORT'))
								.switchOn()
								.enable();
						},

						fail: function() {
							self.item.element.both.reportButton
								.text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				showUnpublishDialog: function() {
					$.dialog({
						height: 150,
						content: self.view.unpublishDialog(true, {childs: self.childs}),
						afterShow: function() {
							$('.foundryDialog').find('.unpublish-affectChild').click(function() {
								self.unpublishComment();
							});
						}
					});
				},

				unpublishComment: function() {
					Komento.ajax('site.views.komento.unpublish', {
						id: self.item.id
					},
					{
						success: function() {
							self.closeDialog();
							self.unpublishChild(self.item.id);
							self.item.both.hide('fade', function() {
								self.item.both.remove();
							});
						},

						fail: function() {

						}
					});
				},

				unpublishChild: function(id) {
					$('li[parentid="' + id + '"]').each(function() {
						self.unpublishChild($(this).attr('id'));
					}).hide('fade', function() {
						$(this).remove();
					});
				},

				stick: function(el) {
					Komento.ajax('site.views.komento.stick', {
						id: self.item.id
					},
					{
						success: function() {
							self.item.element.mine.stickButton
								.text($.language('COM_KOMENTO_COMMENT_UNSTICK'))
								.switchOff()
								.enable();

							self.item.mine.addClass('kmt-sticked');

							// append comment into stick list
							self.kmt.famelist.stickComment(self.item.mine.clone())
						},

						fail: function() {
							self.item.element.mine.stickButton
								.text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				unstick: function() {
					Komento.ajax('site.views.komento.unstick', {
						id: self.item.id
					},
					{
						success: function() {
							self.item.element.both.stickButton
								.text($.language('COM_KOMENTO_COMMENT_STICK'))
								.switchOn()
								.enable();

							self.item.both.removeClass('kmt-sticked');

							// remove comment from stick list
							self.unstickComment(self.item.commentid);
						},

						fail: function() {
							self.stickButton
								.text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				like: function() {
					Komento.ajax('site.views.komento.action', {
						id: self.item.id,
						type: 'likes',
						action: 'add'
					},
					{
						success: function() {
							self.item.element.both.likeButton
								.switchOff()
								.enable()
								.find('span')
								.text($.language('COM_KOMENTO_COMMENT_UNLIKE'));

							var likes = parseInt(self.item.element.mine.likesCounter.find('span').text()) + 1;
							self.item.element.both.likesCounter.find('span')
								.text(likes);
						},

						fail: function( message ) {
							self.item.element.both.likesCounter.find('span')
								.text( message );
								// .text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				unlike: function() {
					Komento.ajax('site.views.komento.action', {
						id: self.item.id,
						type: 'likes',
						action: 'remove'
					},
					{
						success: function() {
							self.item.element.both.likeButton
								.switchOn()
								.enable()
								.find('span')
								.text($.language('COM_KOMENTO_COMMENT_LIKE'));

							var likes = parseInt(self.item.element.mine.likesCounter.find('span').text()) - 1;
							self.item.element.both.likesCounter.find('span')
								.text(likes);
						},

						fail: function() {
							self.item.element.both.likesCounter.find('span')
								.text($.language('COM_KOMENTO_ERROR'))
						}
					});
				},

				showLikesDialog: function(el) {
					Komento.ajax('site.views.komento.getLikedUsers', {
						id: self.item.id
					},
					{
						success: function(html) {
							$.dialog({
								title: $.language('COM_KOMENTO_COMMENT_PEOPLE_WHO_LIKED_THIS'),
								content: html
							});
						}
					});
				},

				loadParent: function() {
					var parent = $('#' + self.item.parentid);
					if(parent.length != 0) {
						var avatar = parent.find('.kmt-avatar:not(.parentContainer > .kmt-avatar)').clone();
						var author = parent.find('.kmt-author:not(.parentContainer > .kmt-author)').clone();
						var time = parent.find('.kmt-time:not(.parentContainer > .kmt-time)').clone();
						var text = parent.find('.commentText:not(.parentContainer > .commentText)').clone();

						// todo: configurable
						self.item.element.both.parentContainer.html('')
							.append(avatar)
							.append(author)
							.append(time)
							.append(text);
					} else {
						var parentid = self.item.parentid.split('-')[1];

						Komento.ajax('site.views.komento.getcomment', {
							id: parentid
						},
						{
							success: function(html) {
								self.item.element.both.parentContainer.html(html);
							},

							fail: function() {

							}
						})
					}
					self.item.element.both.parentContainer.attr('loaded', 1)
				},

				showAttachmentDeleteDialog: function(el) {
					var attachmentid = $(el).parents('.attachmentFile').attr('attachmentid');
					var attachmentname = $(el).parents('.attachmentFile').attr('attachmentname');

					$.dialog({
						height: 150,
						content: self.view.deleteAttachment(true, {attachmentname: attachmentname}),
						afterShow: function() {
							$('.foundryDialog').find('.delete-attachment').click(function() {
								self.closeDialog();
								self.deleteFile(attachmentid);
							});

							$('.foundryDialog').find('.delete-attachment-cancel').click(function() {
								self.closeDialog();
							});
						}
					});
				},

				deleteFile: function(attachmentid) {
					var commentid = self.item.id;

					Komento.ajax('site.views.komento.deleteAttachment', {
						id: commentid,
						attachmentid: attachmentid
					},
					{
						success: function() {
							self.item.element.both.attachmentFile.filter('.file-' + attachmentid).remove();

							if(self.item.mine.find('.attachmentFile').length == 0) {
								self.item.element.both.attachmentWrap.remove();
							}
						},

						fail: function(error) {
							error = '<p class="error">' + error + '</p>';
							self.item.element.both.attachmentFile.filter('.file-' + attachmentid).append(error);
						}
					})
				}
			} }
		);
		module.resolve();
	});
});
