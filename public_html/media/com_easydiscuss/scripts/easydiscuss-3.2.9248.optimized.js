FD31.installer("EasyDiscuss", "definitions", function($){
$.module(["easydiscuss/attachments","easydiscuss/avatar","easydiscuss/bbcode","easydiscuss/captcha","easydiscuss/categories","easydiscuss/comments","easydiscuss/composer","easydiscuss/conversation","easydiscuss/customfields","easydiscuss/easydiscuss","easydiscuss/layout/responsive","easydiscuss/layout/lightbox","easydiscuss/legacy","easydiscuss/favourites","easydiscuss/filterbar","easydiscuss/likes","easydiscuss/location","easydiscuss/newpost","easydiscuss/polls","easydiscuss/posts","easydiscuss/prism","easydiscuss/profile","easydiscuss/ranks","easydiscuss/replies","easydiscuss/votes","easydiscuss/stylesheet","easydiscuss/tag","easydiscuss/toolbar"]);
$.require.template.loader(["easydiscuss/field.form.attachments.item","easydiscuss/field.form.attachments.fileinput","easydiscuss/conversation.read.item","easydiscuss/field.form.polls.answer","easydiscuss/comment.form","easydiscuss/post.notification"]);
$.require.language.loader(["COM_EASYDISCUSS_EXCEED_ATTACHMENT_LIMIT","COM_EASYDISCUSS_TERMS_PLEASE_ACCEPT","COM_EASYDISCUSS_COMMENT_SUCESSFULLY_ADDED","COM_EASYDISCUSS_COMMENT_LOAD_MORE","COM_EASYDISCUSS_COMMENT_LOADING_MORE_COMMENTS","COM_EASYDISCUSS_COMMENT_LOAD_ERROR","COM_EASYDISCUSS_CONVERSATION_EMPTY_CONTENT","COM_EASYDISCUSS_CUSTOMFIELDS_DISPLAY_ERROR","COM_EASYDISCUSS_BBCODE_BOLD","COM_EASYDISCUSS_BBCODE_ITALIC","COM_EASYDISCUSS_BBCODE_UNDERLINE","COM_EASYDISCUSS_BBCODE_URL","COM_EASYDISCUSS_BBCODE_TITLE","COM_EASYDISCUSS_BBCODE_PICTURE","COM_EASYDISCUSS_BBCODE_VIDEO","COM_EASYDISCUSS_BBCODE_BULLETED_LIST","COM_EASYDISCUSS_BBCODE_NUMERIC_LIST","COM_EASYDISCUSS_BBCODE_LIST_ITEM","COM_EASYDISCUSS_BBCODE_QUOTES","COM_EASYDISCUSS_BBCODE_CODE","COM_EASYDISCUSS_BBCODE_HAPPY","COM_EASYDISCUSS_BBCODE_SMILE","COM_EASYDISCUSS_BBCODE_SURPRISED","COM_EASYDISCUSS_BBCODE_TONGUE","COM_EASYDISCUSS_BBCODE_UNHAPPY","COM_EASYDISCUSS_BBCODE_WINK","COM_EASYDISCUSS_FAVOURITE_BUTTON_UNFAVOURITE","COM_EASYDISCUSS_FAVOURITE_BUTTON_FAVOURITE","COM_EASYDISCUSS_UNLIKE_THIS_POST","COM_EASYDISCUSS_LIKE_THIS_POST","COM_EASYDISCUSS_UNLIKE","COM_EASYDISCUSS_LIKES","COM_EASYDISCUSS_NOTIFICATION_NEW_REPLIES","COM_EASYDISCUSS_NOTIFICATION_NEW_COMMENTS","COM_EASYDISCUSS_PLEASE_SELECT_CATEGORY_DESC","COM_EASYDISCUSS_POST_TITLE_CANNOT_EMPTY","COM_EASYDISCUSS_POST_CONTENT_IS_EMPTY","COM_EASYDISCUSS_SUCCESS","COM_EASYDISCUSS_FAIL","COM_EASYDISCUSS_REPLY_LOADING_MORE_COMMENTS","COM_EASYDISCUSS_REPLY_LOAD_ERROR"]);
(function(){
var stylesheetNames = ["easydiscuss/fancybox/default"];
var state = ($.stylesheet({"content":""})) ? "resolve" : "reject";
$.each(stylesheetNames, function(i, stylesheet){ $.require.stylesheet.loader(stylesheet)[state](); });
})();
});
FD31.installer("EasyDiscuss", "scripts", function($){
EasyDiscuss.module("attachments", function($) {

	var module = this;

	EasyDiscuss.require()
	.view("field.form.attachments.item")
	.done(function($) {

		EasyDiscuss.Controller("Attachments", {
			defaultOptions: {

				view: {
					item: "field.form.attachments.item"
				},

				hasAttachmentLimit: false,
				attachmentLimit: 0,
				editable: false,

				"{itemGroup}": "[data-attachment-itemgroup]",
				"{item}" : "[data-attachment-item]"
			}
		},
		function(self) { return {

			init: function() {

				var options = self.options,
					attachmentLimit = self.element.attr("data-attachment-limit");

				// Data API
				if (attachmentLimit!==undefined) {
					options.hasAttachmentLimit = true;
					options.attachmentLimit = parseInt(attachmentLimit) || 0;
				}

				if (options.attachmentLimit===0) {
					options.hasAttachmentLimit = false;
				}

				options.editable = self.element.hasClass("editable");

				// Add attachment item controller
				// to existing attachment items.
				self.item()
					.addController("EasyDiscuss.Controller.Attachments.Item");
			},

			setLayout: function() {

				var options = self.options,
					count = self.item(":not(.new)").length,
					exceeded = options.hasAttachmentLimit && (count >= options.attachmentLimit);

				// Toggle limit-exceeded class
				// This will show the limit exceed hint.
				self.element
					.toggleClass("limit-exceeded", exceeded)
					.toggleClass("no-attachment", count < 1);

				if (options.editable) {

					// Remove any new attachment form
					self.item(".new").remove();

					// If attachment limit is not exceeded,
					// append a new attachment form at the buttom.
					if (!exceeded) {
						self.view.item()
							.appendTo(self.itemGroup())
							.addController("EasyDiscuss.Controller.Attachments.Item");
					}
				}
			},

			"{item} itemAdded": function() {
				self.setLayout();
			},

			"{item} itemRemoved": function() {

				setTimeout(function(){
					self.setLayout();
				}, 1);
			}

		}});

		EasyDiscuss.Controller("Attachments.Item", {
			defaultOptions: {
				"{removeButton}": "[data-attachment-remove-button]",
				"{file}": "[data-attachment-file]",
				"{title}": "[data-attachment-title]",
			}
		},
		function(self) { return {

			init: function() {

				self.file().prop("disabled", false);
			},

			add: function() {

				var filename = self.file().val()
					type = self.getType(filename),
					item = self.element;

				if (filename.match(/fakepath/)) {
					filename = filename.replace(/C:\\fakepath\\/i, '');
				};

				item
					.removeClass("new")
					.addClass("attachment-type-" + type);

				self.title().html(filename);

				self.trigger("itemAdded");
			},

			remove: function() {

				var id = self.element.attr("id");

				// If there is an id
				if (id) {

					var id = id.replace("attachment-", "");

					// Run ajax call to delete attachment.
					disjax.loadingDialog();
					disjax.load('attachments', 'confirmDelete', id.toString());

				} else {

					self.trigger("itemRemoved");
					self.element.remove();
				}
			},

			getType: function(filename) {

				var extension = filename.substr((filename.lastIndexOf('.') + 1)),
					type = "default";

				switch (extension) {
					case 'jpg':
					case 'png':
					case 'gif':
						type = 'image';
						break;
					case 'zip':
					case 'rar':
						type = 'archive';
						break;
					case 'pdf':
						type = 'pdf';
						break;
				}

				return type;
			},

			"{file} change": function() {

				self.add();
			},

			"{removeButton} click": function() {

				self.remove();
			}

		}});


		$(document).on("click.ed.attachment.remove", "[data-attachment-remove-button]", function(){

			var button = $(this),
				parent = button.parents(".discuss-attachments"),
				controller = "EasyDiscuss.Controller.Attachments";

			if (parent.hasController(controller)) return;

			parent.addController(controller);

			// Trigger trigger remove event
			button.click();
		});

		module.resolve();

	});

});

EasyDiscuss.module('avatar', function($) {

	var module = this;

  EasyDiscuss
.require()
.library('imgareaselect')
.done(function($) {

	EasyDiscuss.Controller('Avatar',
		{
			defaultOptions: {

				'{avatarContainer}': '.avatarContainer',
				'{avatar}' : '.avatarContainer img',

				'{avatarPreviewContainer}' : '.avatarPreviewContainer',
				'{avatarPreviewPlaceholder}': '.avatarPreviewPlaceholder',
				'{avatarPreview}' : '.avatarPreview',

				'{startCropButton}' : '.startCropButton',
				'{saveCropButton}' : '.saveCropButton',
				'{stopCropButton}' : '.stopCropButton',

				'{alertMessage}': '.alertMessage',

				selection: {
					disable: false,
					handles: true,
					show: true,
					minWidth: 160,
					minHeight: 160,
					x1: 0,
					y1: 0,
					x2: 160,
					y2: 160,
					previewWidth: 160,
					previewHeight: 160,
					aspectRatio: '1:1'
				}
			}
		},
		function(self) { return {

			init: function() {

				self.avatarPreviewContainer().css({
					width: self.options.selection.previewWidth,
					height: self.options.selection.previewHeight,
					position: 'relative',
					overflow: 'hidden'
				});

				// Some templates may have max-width: 100% on img tag.
				self.avatarPreview().css({
					maxWidth: 'none'
				});
			},

			start: function() {

				self.alertMessage().hide();

				self.element.addClass('cropping');

				var options = self.options.selection,
					avatar = self.avatar(),
					preview = self.avatarPreview(),
					imageWidth = avatar.width(),
					imageHeight = avatar.height(),
					x1, x2, y1, y2;

				self.selector = avatar.imgAreaSelect(
					$.extend({}, options, {

						parent: self.avatarContainer(),

						instance: true,

						x1: x1 = (imageWidth / 2) - (options.minWidth / 2),
						y1: y1 = (imageHeight / 2) - (options.minHeight / 2),
						x2: x1 + options.minWidth,
						y2: y1 + options.minHeight,

						onSelectChange: function(img, selection) {

							var scaleX = options.previewWidth / (selection.width || 1),
								scaleY = options.previewHeight / (selection.height || 1);

							preview.css({
								width: Math.round(scaleX * imageWidth) + 'px',
								height: Math.round(scaleY * imageHeight) + 'px',
								marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
								marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
							});
						}
					})
				);

				self.avatarPreviewPlaceholder().hide();

				self.avatarPreview().show();
			},

			stop: function() {

				self.alertMessage().remove();

				self.element.removeClass('cropping');

				self.avatarPreview().hide();

				self.avatarPreviewPlaceholder().show();

				// Disable selection
				self.avatar().imgAreaSelect({
					hide: true,
					disable: true
				});

				delete self.selector;
			},

			save: function() {

				if (self.selector == undefined) {
					self.stop();
					return;
				}

				var selection = self.selector.getSelection();

				self.saveCropButton()
					.addClass('btn-loading');

				EasyDiscuss.ajax('site.views.profile.cropphoto', selection)
					.done(function(imageUrl, message) {

						self.stop();

						self.avatarPreviewPlaceholder()
							.attr('src', imageUrl + '?' + $.uid());

						self.alert(message || 'Avatar successfully cropped!', 'success');
					})
					.fail(function(message) {

						self.alert(message || 'Unable to crop avatar.', 'error');
					})
					.always(function() {

						self.saveCropButton()
							.removeClass('btn-loading');
					});
			},

			alert: function(message, type) {

				if (type === undefined) type = 'info';

				self.alertMessage().remove();

				$('<div class="alert alertMessage"></div>')
					.addClass('alert-' + type)
					.html(message)
					.prependTo(self.element);
			},

			'{startCropButton} click': function() {

				self.start();
			},

			'{saveCropButton} click': function() {

				self.save();
			},

			'{stopCropButton} click': function() {

				self.stop();
			}
		}}
	);

	module.resolve();

      });

});

EasyDiscuss.module('bbcode', function($) {

	var module = this;

	window.insertVideoCode = function(videoURL , caretPosition , elementId )
	{
		if (videoURL.length == 0)
		{
			return false;
		}

		var textarea	= $('textarea[name=' + elementId + ']');
		var tag = '[video]' + videoURL + '[/video]';

		// If this is at the first position, we don't want to do anything here.
		if (caretPosition == 0)
		{
			$(textarea).val(tag);
			disjax.closedlg();
			return true;
		}

		var contents	= $(textarea).val();

		$(textarea).val(contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length));

		disjax.closedlg();
	};

	$.getEasyDiscussBBCodeSettings = {

		onTab:	{keepDefault: false, replaceWith: '    '},
		previewParserVar: 'data',
		markupSet: [
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_BOLD' ), key: 'B', openWith: '[b]', closeWith: '[/b]', className: 'markitup-bold'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_ITALIC' ), key: 'I', openWith: '[i]', closeWith: '[/i]', className: 'markitup-italic'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_UNDERLINE' ), key: 'U', openWith: '[u]', closeWith: '[/u]', className: 'markitup-underline'},
			{separator: '---------------' },
			{
				name: $.language( 'COM_EASYDISCUSS_BBCODE_URL' ),
				key: 'L',
				openWith: '[url=[![Link:]!]]',
				closeWith: '[/url]',
				placeHolder: $.language( 'COM_EASYDISCUSS_BBCODE_TITLE' ),
				beforeInsert: function(h ) {
				},
				className: 'markitup-url'
			},
			{
				name: $.language( 'COM_EASYDISCUSS_BBCODE_PICTURE' ),
				key: 'P',
				replaceWith: '[img][![Url]!][/img]',
				className: 'markitup-picture'
			},
			{
				name: $.language( 'COM_EASYDISCUSS_BBCODE_VIDEO' ),
				replaceWith: function(h) {

					disjax.loadingDialog();
					disjax.load('post' , 'showVideoDialog' , $(h.textarea).attr('name') , h.caretPosition.toString());

				},
				beforeInsert: function(h ) {
				},
				afterInsert: function(h ) {
				},
				className: 'markitup-video'
			},

			{separator: '---------------' },
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_BULLETED_LIST' ), openWith: '[list]\n', closeWith: '\n[/list]', className: 'markitup-bullet'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_NUMERIC_LIST' ) , openWith: '[list=[![Starting number]!]]\n', closeWith: '\n[/list]', className: 'markitup-numeric'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_LIST_ITEM' ) , openWith: '[*] ', className: 'markitup-list'},
			{separator: '---------------' },
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_QUOTES' ) , openWith: '[quote]', closeWith: '[/quote]', className: 'markitup-quote'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_CODE' ), openWith: '[code type="markup"]', closeWith: '[/code]', className: 'markitup-code'},
			{separator: '---------------' },
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_HAPPY' ) , openWith: ':D ', className: 'markitup-happy'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_SMILE' ) , openWith: ':) ', className: 'markitup-smile'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_SURPRISED' ) , openWith: ':o ', className: 'markitup-surprised'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_TONGUE' ) , openWith: ':p ', className: 'markitup-tongue'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_UNHAPPY' ) , openWith: ':( ', className: 'markitup-unhappy'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_WINK' ), openWith: ';) ', className: 'markitup-wink'}
		]
	};

	// $.markItUp.sets.bbcode_easydiscuss_dialog = {
	$.getEasyDiscussDialogBBCodeSettings = {

		onShiftEnter:	{keepDefault: false, replaceWith: '<br />\n'},
		onCtrlEnter:	{keepDefault: false, openWith: '\n<p>', closeWith: '</p>'},
		onTab:	{keepDefault: false, replaceWith: '    '},
		previewParserVar: 'data',
		markupSet: [
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_BOLD' ), key: 'B', openWith: '[b]', closeWith: '[/b]', className: 'markitup-bold'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_ITALIC' ), key: 'I', openWith: '[i]', closeWith: '[/i]', className: 'markitup-italic'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_UNDERLINE' ), key: 'U', openWith: '[u]', closeWith: '[/u]', className: 'markitup-underline'},
			{separator: '---------------' },
			{
				name: $.language( 'COM_EASYDISCUSS_BBCODE_URL' ),
				key: 'L',
				openWith: '[url=[![Link:!:http://]!]]',
				closeWith: '[/url]',
				placeHolder: $.language( 'COM_EASYDISCUSS_BBCODE_TITLE' ),
				className: 'markitup-url'
			},
			{
				name: $.language( 'COM_EASYDISCUSS_BBCODE_PICTURE' ),
				key: 'P',
				replaceWith: '[img][![Url]!][/img]',
				className: 'markitup-picture'
			},
			{separator: '---------------' },
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_BULLETED_LIST' ), openWith: '[list]\n', closeWith: '\n[/list]', className: 'markitup-bullet'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_NUMERIC_LIST' ), openWith: '[list=[![Starting number]!]]\n', closeWith: '\n[/list]', className: 'markitup-numeric'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_LIST_ITEM' ), openWith: '[*] ', className: 'markitup-list'},
			{separator: '---------------' },
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_QUOTES' ), openWith: '[quote]', closeWith: '[/quote]', className: 'markitup-quote'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_CODE' ), openWith: '[code type="xml"]', closeWith: '[/code]', className: 'markitup-code'},
			{separator: '---------------' },
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_HAPPY' ), openWith: ':D ', className: 'markitup-happy'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_SMILE' ), openWith: ':) ', className: 'markitup-smile'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_SURPRISED' ), openWith: ':o ', className: 'markitup-surprised'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_TONGUE' ), openWith: ':p ', className: 'markitup-tongue'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_UNHAPPY' ), openWith: ':( ', className: 'markitup-unhappy'},
			{name: $.language( 'COM_EASYDISCUSS_BBCODE_WINK' ), openWith: ';) ', className: 'markitup-wink'}
		]
	};

	module.resolve();
});

EasyDiscuss.module('captcha', function($) {
	var module = this;

	EasyDiscuss
	.require()
	.done(function($) {

		EasyDiscuss.Controller('Post.Captcha' ,
		{
			defaultOptions:
			{
				// Action buttons
				'{reloadImage}'	: '.reloadImage',
				'{captchaResponse}' : '#captcha-response',
				'{captchaId}' : '#captcha-id',
				'{captchaImage}' : '#captcha-image'
			}
		},
		function(self) {
			return {

				init: function()
				{
					console.log( 'Captcha init' );
				},

				'{reloadImage} click' : function(element )
				{
					EasyDiscuss.ajax('site.views.ask.reloadCaptcha' ,
					{
						'captchaId' : self.captchaId().val()
					},
					{
						beforeSend: function()
						{
							// $('.loader').show();
						}
					})
					.done(function( id, source )
					{
						self.captchaImage().attr( 'src' , source );
						self.captchaId().val( id );
						self.captchaResponse().val( '' );
					})
					.fail(function(message )
					{
						// show error message

					})
					.always(function() {
						//remove the loading here
						// $('.loader').hide();
					});
				},
			};
		});
		module.resolve();
	});
});

EasyDiscuss.module('categories', function($) {
	var module = this;

	EasyDiscuss
	.require()
	.done(function($) {

		EasyDiscuss.Controller('Toggle.Categories' ,
		{
			defaultOptions:
			{
				postId: null,
				// Action buttons
				'{showChild}'	: '.showChild',
				'{hideChild}'	: '.hideChild',

				'{item}': '[data-item]'
			}
		},
		function(self) {
			return {

				init: function()
				{

				},

				'{showChild} click' : function( element )
				{
					var id = element.data('id');

					self.openChild( id );

					element.addClass( 'icon-sort-up hideChild' );
					element.removeClass( 'icon-sort-down' );
					element.removeClass( 'showChild' );
				},

				openChild: function( parentid )
				{
					var childs = self.item('[data-parent-id="' + parentid + '"]');

					childs.show();

					$.each(childs, function(i, child) {
						var childid = $(child).data('id');

						//self.openChild( childid );
					});
				},

				'{hideChild} click' : function( element )
				{
					var id = element.data('id');

					self.closeChild( id );

					element.addClass( 'icon-sort-down showChild' );
					element.removeClass( 'icon-sort-up' );
					element.removeClass( 'hideChild' );
				},

				closeChild: function( parentid )
				{
					var childs = self.item('[data-parent-id="' + parentid + '"]');

					childs.hide();

					$.each(childs, function(i, child) {
						var childid = $(child).data('id');

						self.closeChild( childid );
					});
				}
			};
		});

		EasyDiscuss.Controller('Toggle.Module.Categories' ,
		{
			defaultOptions:
			{
				postId: null,
				// Action buttons
				'{showChild}'	: '.showChild',
				'{hideChild}'	: '.hideChild',

				'{item}': '[data-item]'
			}
		},
		function(self) {
			return {

				init: function()
				{

				},

				'{showChild} click' : function( element )
				{
					var id = element.data('id');

					self.openChild( id );

					element.addClass( 'icon-sort-up hideChild' );
					element.removeClass( 'icon-sort-down' );
					element.removeClass( 'showChild' );
				},

				openChild: function( parentid )
				{
					var childs = self.item('[data-parent-id="' + parentid + '"]');

					childs.show();

					$.each(childs, function(i, child) {
						var childid = $(child).data('id');

						//self.openChild( childid );
					});
				},

				'{hideChild} click' : function( element )
				{
					var id = element.data('id');

					self.closeChild( id );

					element.addClass( 'icon-sort-down showChild' );
					element.removeClass( 'icon-sort-up' );
					element.removeClass( 'hideChild' );
				},

				closeChild: function( parentid )
				{
					var childs = self.item('[data-parent-id="' + parentid + '"]');

					childs.hide();

					$.each(childs, function(i, child) {
						var childid = $(child).data('id');

						self.closeChild( childid );
					});
				}
			};
		});
		module.resolve();
	});
});

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

EasyDiscuss.module('composer', function($) {

	var module = this;

	EasyDiscuss.Controller(
		'Composer',
		{
			defaultOptions:
			{
				editorType: null,
				operation: null,

				'{editor}': '[name=dc_reply_content]',
				'{tabs}': '.formTabs [data-foundry-toggle=tab]',
				'{form}': 'form[name=dc_submit]',
				'{attachments}': 'input.fileInput',

				'{submitButton}': '.submit-reply',
				'{cancelButton}': '.cancel-reply',

				'{notification}': '.replyNotification',

				'{loadingIndicator}': '.reply-loading'
			}
		},
		function(self )
		{
			return {
				init: function()
				{
					// Composer ID
					self.id = self.element.data('id');

					// Composer operation
					self.options.operation = self.element.data('operation');

					// Composer editor
					self.options.editorType = self.element.data('editortype');

					if (self.options.editorType == 'bbcode')
					{
						EasyDiscuss.require()
							.library(
								'markitup',
								'expanding'
							)
							.script(
								'bbcode'
							)
							.done(function($) {
								self.editor()
									.markItUp($.getEasyDiscussBBCodeSettings)
									.expandingTextarea();
							});
					}

					// Automatically select the first tab
					self.tabs(':first').tab('show');

					// Resolve composer so plugin scripts can execute
					EasyDiscuss.module(self.id, function() {
						this.resolve(self);
					});
				},

				'{submitButton} click': function() {

					self.submit();
				},

				'{cancelButton} click': function() {

					self.trigger('cancel');
				},

				notify: function(type, message)
				{
					self.notification()
						.addClass('alert-' + type)
						.html(message)
						.show();
				},

				submit: function()
				{
					var params = self.form().serializeObject();

					// Ambiguity with normal reply form
					params.content = params.dc_reply_content;

					params.files = self.attachments();

					EasyDiscuss.ajax(
						'site.views.post.saveReply',
						params,
						{
							type: 'iframe',

							beforeSend: function()
							{
								self.submitButton().prop('disabled', true);
								self.loadingIndicator().show();
							},

							notify: self.notify,

							reloadCaptcha: function()
							{
								Recaptcha.reload();
							},

							complete: function()
							{
								if (self._destroyed) return;
								self.submitButton().removeAttr('disabled');
								self.loadingIndicator().hide();
							}
						})
						.done(function(content)
						{
							self.trigger('save', content);
						})
						.fail(self.notify);
				}
			};
		}
	);

	module.resolve();
});

EasyDiscuss.module('conversation', function($) {
	var module = this;

	EasyDiscuss
	.require()
	.language(
		'COM_EASYDISCUSS_CONVERSATION_EMPTY_CONTENT'
		)
	.library('markitup')
	.script('bbcode')
	.view('conversation.read.item')
	.done(function($) {

		EasyDiscuss.Controller('Conversation.Form',
		{
			defaultOptions:
			{
				'{textEditor}'	: '.replyMessage'
			}
		},
		function(self) {
			return{
				init: function()
				{
					// Initialize bbcode
					self.initBBCode();
				},

				initBBCode: function() {
					self.textEditor().markItUp({set: 'bbcode_easydiscuss'});
				}
			};
		});

		EasyDiscuss.Controller('Conversation.Read',
		{
			defaultOptions:
			{
				'{replyList}'	: '.replyList',

				'{deleteMessage}'	: '.deleteMessage',
				'{unreadMessage}'	: '.unreadMessage',

				'{replyForm}'	: '.replyForm',
				'{replyButton}'	: '.replyButton',
				'{replyMessage}'	: '.replyMessage',

				// Properties.
				messageId: null,

				view: {
					reply: 'conversation.read.item'
				}
			}
		},
		function(self) {
			return{
				init: function()
				{
					// Implement editor.
					self.replyForm().implement(EasyDiscuss.Controller.Conversation.Form,
						{
							'{textEditor}' : '.replyMessage'
						});

					// Obtain message id.
					self.options.messageId	= self.element.data('id');
				},

				'{deleteMessage} click' : function()
				{
					disjax.loadingDialog();
					disjax.load('conversation' , 'confirmDelete' , self.options.messageId + '');
				},

				'{replyButton} click' : function()
				{
					// Disable the reply button.
					self.replyButton().addClass('disabled');

					EasyDiscuss.ajax('site.views.conversation.reply' ,
					{
						'id'	: self.options.messageId,
						'message'	: self.replyMessage().val()
					}).done(function(post) {

						self.replyButton().removeClass('disabled');

						var html = self.view.reply({ 'post' : post });

						$(html).find('.discuss-message-content').html(post.message);

						// Reset the texteditor's content.
						self.replyMessage().val('');

						// Append output to the page.
						self.replyList().append(html);
					})
					.fail(function(message ) {
						$('.conversationError')
							.addClass(' alert alert-error')
							.html($.language('COM_EASYDISCUSS_CONVERSATION_EMPTY_CONTENT'));
					});
				}
			};
		});

		module.resolve();

	});

});

EasyDiscuss.module('customfields', function($) {
	var module = this;

	EasyDiscuss
	.require()
	.language(
		'COM_EASYDISCUSS_CUSTOMFIELDS_DISPLAY_ERROR'
	)
	.done(function($) {

		EasyDiscuss.Controller('Administrator.CustomFields' ,
		{
			defaultOptions:
			{
				'customId'	: null,
				'defaultType'	: null,
				'optionCount'	: '.optionCount',
				'{optionType}'	: '#type',
				'{advanceOptions}'	: '.customFieldAdvanceOption',
				'{addButton}'	: 'button[name="Add"]',
				'{removeButton}'	: 'button[name="Remove"]',
				'{fieldLoader}'	: '.fieldLoader',
				'{addContainer}'	: '.addContainer'
			}
		},
		function(self) {
			return {
				init: function()
				{
					// Load back the values when editing
					// self.advanceOptions().implement( EasyDiscuss.Controller.Administrator.CustomFields.AdvanceOptions );
					// self.getAdvanceOption( self.options.defaultType );
				},

				'{optionType} change' : function(element )
				{
					self.getAdvanceOption(element.val());
				},

				'{addButton} click': function(element )
				{
					self.addFieldOption(element.data('fieldtype'));
				},

				'{removeButton} click': function(element )
				{
					self.removeFieldOption(element, element.attr('id'), element.data('removetype'));
				},

				getAdvanceOption: function(type )
				{
					// GET NEW OR LOAD PREVIOUS CUSTOMFIELD VALUES
					EasyDiscuss.ajax('admin.views.customfields.getAdvanceOption' ,
					{
						'activeType' : type,
						'customId'	: self.options.customId
					},
					{
						beforeSend: function()
						{
							$('.fieldLoader').show();
						}
					})
					.done(function(addButton, html, count )
					{
						self.advanceOptions().html(html);
						$('.addContainer').html(addButton);
						$('.optionCount').attr('totalcount', count);
						$('.fieldLoader').hide();
					})
					.fail(function(message )
					{
						// show error message
						self.advanceOptions().append($.language('COM_EASYDISCUSS_CUSTOMFIELDS_DISPLAY_ERROR'));
					})
					.always(function() {
						//remove the loading here
						$('.fieldLoader').hide();
					});
				},

				addFieldOption: function(type )
				{
					// ADD BUTTON FOR BACKEND
					var totalCount = $('.optionCount').attr('totalcount');
					EasyDiscuss.ajax('admin.views.customfields.addFieldOption' ,
					{
						'activeType' : type,
						'customId'	: self.options.customId,
						'fieldCount'	: totalCount
					},
					{
						beforeSend: function()
						{
							$('.fieldLoader').show();
						}
					})
					.done(function(value, count )
					{
						self.advanceOptions().append(value);
						$('.optionCount').attr('totalcount', count);
						$('.fieldLoader').hide();
					})
					.fail(function(message )
					{
						// show error message
						self.advanceOptions().append($.language('COM_EASYDISCUSS_CUSTOMFIELDS_DISPLAY_ERROR'));
					})
					.always(function() {
						//remove the loading here
						$('.fieldLoader').hide();
					});
				},

				removeFieldOption: function(id, count, type )
				{
					$('#' + type + '_' + count).remove();
					$('.remove' + type + '_' + count).remove();
					id.remove();
				}
			};
		});
		module.resolve();
	});
});

EasyDiscuss.require()
	.library(
		'markitup',
		'expanding',
		'placeholder',
		'scrollTo'
	)
	.script(
		'layout/responsive',
		'layout/lightbox',
		'legacy'
	)
	.language(
		'COM_EASYDISCUSS_BBCODE_BOLD',
		'COM_EASYDISCUSS_BBCODE_ITALIC',
		'COM_EASYDISCUSS_BBCODE_UNDERLINE',
		'COM_EASYDISCUSS_BBCODE_URL',
		'COM_EASYDISCUSS_BBCODE_TITLE',
		'COM_EASYDISCUSS_BBCODE_PICTURE',
		'COM_EASYDISCUSS_BBCODE_VIDEO',
		'COM_EASYDISCUSS_BBCODE_BULLETED_LIST',
		'COM_EASYDISCUSS_BBCODE_NUMERIC_LIST',
		'COM_EASYDISCUSS_BBCODE_LIST_ITEM',
		'COM_EASYDISCUSS_BBCODE_QUOTES',
		'COM_EASYDISCUSS_BBCODE_CODE',
		'COM_EASYDISCUSS_BBCODE_HAPPY',
		'COM_EASYDISCUSS_BBCODE_SMILE',
		'COM_EASYDISCUSS_BBCODE_SURPRISED',
		'COM_EASYDISCUSS_BBCODE_TONGUE',
		'COM_EASYDISCUSS_BBCODE_UNHAPPY',
		'COM_EASYDISCUSS_BBCODE_WINK'
	)
	.done(function()
	{
		EasyDiscuss.require().script( 'bbcode' ).done();
	});
EasyDiscuss.module('layout/responsive', function($) {

	var module = this;
		$(function(){
			$('#discuss-wrapper')
				.responsive([
					{at: 818,  switchTo: 'w768'},
					{at: 600,  switchTo: 'w768 w600'},
					{at: 500,  switchTo: 'w768 w600 w320'}
				]);

			$('.discuss-searchbar').responsive({at: 600, switchTo: 'narrow'});

		});

	module.resolve();

});

EasyDiscuss.module('layout/lightbox', function($) {

	var module = this;

	EasyDiscuss.require()
		.library('fancybox')
		.stylesheet('fancybox/default')
		.script('legacy')
		.done(function(){

			discuss.attachments.initGallery({
				type: 'image',
				helpers: {
					overlay: null
				}
			});

			module.resolve();
		});
});
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php.
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

EasyDiscuss.module('legacy', function($) {

  var module = this;

  window.isSave = false;

  var discuss = window.discuss = {

	location: {
		remove: function(id )
		{
			disjax.loadingDialog();
			disjax.load('location' , 'removeLocation' , id.toString());
		},
		removeHTML: function(id )
		{
			$('#location-' + id).remove();
		}
	},

	reply: {
		clear: function(element) {

			// Empty contents
			$('textarea[name=dc_reply_content]').val('');

			// Clear out CKEditor's content
			if (window.CKEDITOR) {
				try {
					window.CKEDITOR.instances['dc_reply_content'].setData('');
				} catch (e) {}
			}

			// Clear off attachments
			discuss.attachments.clear();

			// Clear off references
			discuss.references.clear(form);

			// Maps
			$(form).find('.showCoords')
				.removeClass('showCoords');

			$(form).find('.locationMap')
				.remove();

			$(form).find('[name=latitude]').val('');
			$(form).find('[name=longitude]').val('');

			// Polls
			var pollController = $(form).find('.polls-tab').controller();

			if (pollController) {
				$(form).find('.polls-tab').controller().resetPollForm();
			}
		},
		submit: function(id) {
			var token	= $('.easydiscuss-token').val();
			action_url = discuss_site + '&view=post&layout=ajaxSubmitReply&format=ajax&tmpl=component&' + token + '=1';

			form = $('.' + id).find('[name=dc_submit]')[0];

			var replyNotification = $('.' + id).find('.replyNotification');

			var iframe = document.createElement('iframe');
				iframe.setAttribute('id', 'upload_iframe');
				iframe.setAttribute('name', 'upload_iframe');
				iframe.setAttribute('width', '0');
				iframe.setAttribute('height', '0');
				iframe.setAttribute('border', '0');
				iframe.setAttribute('style', 'width: 0; height: 0; border: none;');

			form.parentNode.appendChild(iframe);
			window.frames['upload_iframe'].name = 'upload_iframe';
			var iframeId = document.getElementById('upload_iframe');

			// Temporarily disable the submit button.
			$('.submit-reply').prop('disabled' , true);

			// Add event...
			var eventHandler = function()  {

				var content;

				if (iframeId.detachEvent)
				{
					iframeId.detachEvent('onload', eventHandler);
				}
				else
				{
					iframeId.removeEventListener('load', eventHandler, false);
				}

				// Message from server...
				if (iframeId.contentDocument)
				{
					content = iframeId.contentDocument;
				}
				else if (iframeId.contentWindow)
				{
					content = iframeId.contentWindow.document;
				}
				else if (iframeId.document)
				{
					content = iframeId.document;
				}

				content = $(content).find('script#ajaxResponse').html();

				var result = $.parseJSON(content);

				switch (result.type)
				{
					case 'success.captcha':
						Recaptcha.reload();
					case 'success':
						discuss.spinner.hide('reply_loading');

						replyNotification
							.html(result.message)
							.removeClass('alert-error')
							.addClass('alert-success');

						// Clear any empty replies
						$('.discussionReplies .empty').hide();

						// Update the counter.
						var count = $('.replyCount').html();
							count = parseInt(count) + 1;

						$('.replyCount').html(count);

						// Append the result to the list.

						// var item = $(result.html);

						var controller = $('.discussionReplies').controller();

						controller.addItem(result.html, true);

						// Reload the lightbox for new contents
						discuss.attachments.initGallery({
							type: 'image',
							helpers: {
								overlay: null
							}
						});

						if( EasyDiscuss.main_syntax_highlighter )
						{
							Prism.highlightAll();
						}
						

						// Reload the syntax highlighter.
						if (result.script != 'undefined')
						{
							eval(result.script);
						}

						form.reset();

						// Clear the form.
						discuss.reply.clear(form);
					break;
					case 'error':
						discuss.spinner.hide('reply_loading');
						$('.submit-reply').removeAttr('disabled');
						replyNotification
							.html(result.message)
							.removeClass('alert-success').addClass('alert-error');
					break;
					case 'error.captcha':
						Recaptcha.reload();
						discuss.spinner.hide('reply_loading');
						$('.submit-reply').removeAttr('disabled');

						replyNotification
							.html(result.message)
							.removeClass('alert-success')
							.addClass('alert-error');
					break;
				}

				replyNotification.show();

				$('.submit-reply').removeAttr('disabled');

				// Del the iframe...
				setTimeout(function()
				{
					$(iframeId).remove();
				}, 250);
			};

			$(iframeId).load(eventHandler);

			// Set properties of form...
			form.setAttribute('target', 'upload_iframe');
			form.setAttribute('action', action_url);
			form.setAttribute('method', 'post');
			form.setAttribute('enctype', 'multipart/form-data');
			form.setAttribute('encoding', 'multipart/form-data');

			// Submit the form...
			form.submit();

			// update reply count by 1
			EasyDiscuss.repliesCount = EasyDiscuss.repliesCount === undefined ? 1 : EasyDiscuss.repliesCount + 1;
		},
		verify: function() {

			$('#dc_notification .msg_in').html('');
			$('#dc_notification .msg_in').removeClass('dc_error dc_success dc_alert');
			// discuss.spinner.show('reply_loading');
			disjax.loadingDialog();
			disjax.load('post', 'checklogin');
		},
		post: function() {
			$('.submit-reply').prop('disabled' , true);
			$('#dc_notification .msg_in').html('');
			$('#dc_notification .msg_in').removeClass('dc_error dc_success dc_alert');
			discuss.spinner.show('reply_loading');
			if (discuss.post.validate(true, 'reply')) {
				disjax.load('post' , 'ajaxSubmitReply' , disjax.getFormVal('#dc_submit'));
			} else {
				discuss.spinner.hide('reply_loading');
				$('.submit-reply').removeAttr('disabled');
			}
		},
		minimize: function(id) {
			$('#dc_reply_' + id).hide();
			$('#reply_minimize_msg_' + id).show();
		},
		maximize: function(id) {
			$('#dc_reply_' + id).removeClass('is-minimized');
			$('#reply_minimize_msg_' + id).hide();
		},
		addURL: function(element ) {

			var data	= $(element).siblings('ul.attach-list').children(':first').clone();

			var remove = $('.remove-url').clone();
			remove.css('display' , 'block');

			// Clear up the value of the url.
			$(data).find('input').val('');

			// Show the remove link for new items.
			$(data).find('a').show();

			$(element).siblings('ul.attach-list').append(data);
		},
		removeURL: function(element ) {
			// Detect if this is the only url item left.
			var items = $('ul.attach-list').children();

			if (items.length == 1)
			{
				return false;
			}

			$(element).parents('li').remove();
		},
		accept: function(id ) {

			// Show dialog
			disjax.loadingDialog();
			disjax.load('post' , 'confirmAccept' , id);
			//disjax.load( 'post' , 'acceptReply' , id );
		},
		reject: function(id ) {
			disjax.loadingDialog();
			disjax.load('post' , 'confirmReject' , id);
			// disjax.load( 'post' , 'rejectReply' , id );
		}
	},

	composer: {

		init: function(element, options ) {
			
			EasyDiscuss.require()
				.script('composer')
				.done(function() {
					$(element).implement(EasyDiscuss.Controller.Composer, options);
				});
		}
	},
	/*
	 * Filter items
	 */
	filter: function(type , categoryId ) {
		EasyDiscuss.ajax('site.views.index.filter' , { args: [type, categoryId] } ,
		{
			beforeSend: function() {

				// Show loading
				discuss.spinner.show('index-loading');

				// Hide the main list item
				$('#dc_list').hide();

				// Hide all paginations during filter
				$('#dc_pagination').hide();

				// Remove all active classes from the child filters.
				$('#filter-links').children().removeClass('active');
			},
			success: function(showFeaturedList , content , sorting , type , nextLimit , paginationContent ) {

				// Show only if necessary
				if (showFeaturedList)
				{
					$('#dc_featured_list').show();
				}

				// Assign the new content
				$('#dc_list').html(content);

				// Update the sorting content
				$('#sort-wrapper').html();

				// Update the pagination type.
				$('#pagination-filter').val(type);

				// Update the pagination limit
				$('#pagination-start').val(nextLimit);

				// Update pagination content
				$('#dc_pagination').html(paginationContent);
			},
			complete: function() {

				// Hide loading once the process is complete
				discuss.spinner.hide('index-loading');

				// Since we hid it earlier, show the list content
				$('#dc_list').show();

				// Show the pagination since we hid it earlier.
				$('#dc_pagination').show();

				// Add active class for the child filter
				$('#filter-links').find('.' + type).addClass('active');
			}
		});
	},
	sort: function(type , filter , categoryId ) {
		discuss.spinner.show('index-loading');

		if (discuss_featured_style == '2' && filter != 'allposts')
		{
			$('#dc_featured_list').hide();
		}

		$('#dc_list').hide();

		// Hide all paginations during filter
		$('#dc_pagination').hide();

		// Remove all active classes from the child sorts.
		$('#sort-links').children().removeClass('active');

		// Add active class for the current sort type
		$('#sort-links').find('.' + type).addClass('active');

		disjax.load('index' , 'sort' , type, filter, categoryId);
	},
	references: {
		clear: function(element ) {

			var list = $(element).find('.field-references ul.attach-list');

			var data	= list.children(':first').clone();

			list.empty()
				.append(data);
		}
	},
	attachments: {
		initGallery: function(options ) {

			$('.attachment-image-link').fancybox(options);
		},
		clear: function() {
			$('.uploadQueue').empty();
		}
	},
	map: {
		render: function(title , latitude , longitude , elementId ) {

			var latLng = new google.maps.LatLng(latitude, longitude);

			var map	= new google.maps.Map(document.getElementById(elementId) ,
				{
					zoom: 12,
					center: latLng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			);

			var marker	= new google.maps.Marker(
				{
					position: latLng,
					center: latLng,
					title: title,
					map: map
				}
			);
		}
	},
	/**
	 * Widgets that can be toggled
	 */
	widget: {
		init: function() {
			$('.widget-toggle').click(function() {
				$(this).parents('.discuss-widget').find('.widget-body').toggle();
				$(this).parents('.discuss-widget').toggleClass('is-hidden');
			});
		}
	},
	/**
	 * Submit posts
	 */
	post: {
		move: function(postId )
		{
			disjax.loadingDialog();
			disjax.load('post' , 'movePost' , postId);
		},
		branch: function( postId )
		{
			disjax.loadingDialog();

			disjax.load( 'post' , 'branchForm' , postId );
		},
		mergeForm: function(postId )
		{
			disjax.loadingDialog();
			disjax.load('post' , 'mergeForm' , postId);
		},
		feature: function(postId )
		{
			disjax.loadingDialog();
			disjax.load('post' , 'confirmFeature' , postId);
		},
		unfeature: function(postId )
		{
			disjax.loadingDialog();
			disjax.load('post' , 'confirmUnfeature' , postId);
		},
		// deprecated since 3.0
		// submit new post
		submit: function() {
			if ($('#dc_submit #category_id').val() == '0')
			{
				disjax.loadingDialog();
				disjax.load('index', 'getTemplate', 'ajax.selectcategory.php');
				return false;
			}

			$('#createpost').prop('disabled', true);
			document.dc_submit.submit();
		},
		qqSubmit: function() {

			if ($('#category_id').val() == '0')
			{
				disjax.loadingDialog();
				disjax.load('index', 'getTemplate', 'ajax.selectcategory.php');
				return false;
			}

			$('#createpost').prop('disabled', true);
			document.mod_edqq.submit();
		},
		postTopicSubmit: function() {
			// Module post topic

			if ($('#mod_post_topic_category_id').val() == '0')
			{
				disjax.loadingDialog();
				disjax.load('index', 'getTemplate', 'ajax.selectcategory.php');
				return false;
			}

			$('#createpost').prop('disabled', true);
			document.mod_post_topic.submit();
		},
		// reply to post
		reply: function() {

			if (discuss.post.validate(true, 'reply')) {
				finalData	= disjax.getFormVal('#dc_submit');
				disjax.load('Post', 'ajaxSubmitReply', finalData);
			}
			return false;
		},
		// validate all required fields
		validate: function(notitle, submitType ) {

			if (!notitle) {
				// if the title is empty
				if ($('#ez-title').val() == '' || $('#ez-title').val() == langPostTitle)
				{
					// do something here
					if (submitType == 'reply')
					{
						$('#dc_notification .msg_in').html(langEmptyTitle);
						$('#dc_notification .msg_in').addClass('dc_error');
					}
					else
					{
						$('#dc_post_notification .msg_in').html(langEmptyTitle);
						$('#dc_post_notification .msg_in').addClass('dc_error');
					}
					return false;
				}
			}
			return true;
		},
		del: function(id, oType , url ) {
			disjax.loadingDialog();
			disjax.load('post' , 'deletePostForm' , id, oType, url);
		},

		vote: {

			add: function(post_id, value, vtype) {
				disjax.load('Post', 'ajaxAddVote', post_id, value);
			},

			check: function(post_id) {
				// this function is for debug purposes.
				disjax.load('Post', 'ajaxSumVote', post_id);
			},

			view: function(post_id) {
				disjax.load('Post', 'ajaxViewVote', post_id, '');
			}

		},

		lock: function(post_id) {
			disjax.load('Post', 'ajaxLockPost', post_id);
		},

		unlock: function(post_id) {
			disjax.load('Post', 'ajaxUnlockPost', post_id);
		},

		resolve: function(post_id) {
			disjax.load('Post', 'resolve', post_id);
		},

		unresolve: function(post_id) {
			disjax.load('Post', 'unresolve', post_id);
		},

		likes: function(contentId, status, likeId) {
			disjax.load('Post', 'ajaxLikes', contentId, status, likeId);
		},

		replyLikes: function(contentId, status, likeId) {
			disjax.load('Post', 'ajaxReplyLikes', contentId, status, likeId);
		},

		onhold: function(post_id) {
			disjax.load('Post', 'ajaxOnHoldPost', post_id);
		},

		accepted: function(post_id) {
			disjax.load('Post', 'ajaxAcceptedPost', post_id);
		},

		workingon: function(post_id) {
			disjax.load('Post', 'ajaxWorkingOnPost', post_id);
		},

		reject: function(post_id) {
			disjax.load('Post', 'ajaxRejectPost', post_id);
		},

		nostatus: function(post_id) {
			disjax.load('Post', 'ajaxNoStatusPost', post_id);
		},

		/**
		 * Deprecated since 3.0
		 */
		featured: function(contentId, status ) {
			disjax.load('Post', 'ajaxFeatured', contentId, status);
		},

		toggleTools: function(show, id, showDelete)
		{
			if (show)
			{
				$('.post_delete_link').show();
				$('.likes').show();
				$('.comments').show();
				$('.vote_up').show();
				$('.vote_down').show();
				$('#dc_main_reply_form').show();
			}
			else
			{
				//revert comment form if currently visible
				discuss.comment.cancel();

				if (showDelete == '1')
				{
					$('.post_delete_link').show();
				}
				else
				{
					$('.post_delete_link').hide();
				}
				$('.likes').hide();
				$('.comments').hide();
				$('.vote_up').hide();
				$('.vote_down').hide();
				$('#dc_main_reply_form').hide();
			}
		},

		attachment: {
			remove: function(attachment_id) {
				$('#button-delete-att-' + attachment_id).prop('disabled', true);
				disjax.load('post', 'deleteAttachment', attachment_id);
			}
		}

	},
	login: {
		verify: function() {
			// if the content is empty
			if (discuss.post.validate(false, 'reply')) {
				$('#submit').prop('disabled', true);
				disjax.loadingDialog();
				disjax.load('post', 'checklogin');
			} else {
				return false;
			}
		},
		token: '',
		showpane: function(usertype) {

			$('#usertype_pane_right').children().hide();
			$('#usertype_pane_left').children();

			$('#usertype_member').removeClass('active');
			$('#usertype_guest').removeClass('active');
			$('#discuss_register').removeClass('active');
			$('#usertype_twitter').removeClass('active');

			$('#usertype_status .msg_in').html('');
			$('#usertype_status .msg_in').removeClass('dc_error');


			switch (usertype)
			{
				case 'guest':
					$('#usertype_guest').addClass('active');
					$('#usertype_guest_pane_wrapper').show();
					break;
				case 'register':
					$('#discuss_register').addClass('active');
					$('#discuss_register_pane_wrapper').show();
					break;
				case 'twitter':
					$('#usertype_twitter').addClass('active');
					$('#usertype_twitter_pane_wrapper').show();
					break;
				case 'member':
				default:
					$('#usertype_member').addClass('active');
					$('#usertype_member_pane_wrapper').show();
			}
		},
		submit: {
			reply: function(usertype) {
				switch (usertype)
				{
					case 'guest':
						$('#edialog-guest-reply').prop('disabled', true);
						var email	= $('#discuss_usertype_guest_email').val();
						var name	= $('#discuss_usertype_guest_name').val();
						disjax.load('post', 'ajaxGuestReply', email, name);
						break;
					case 'member':
						$('#edialog-member-reply').prop('disabled', true);
						var username	= $('#discuss_usertype_member_username').val();
						var password	= $('#discuss_usertype_member_password').val();
						var token	= discuss.login.token;
						disjax.load('post', 'ajaxMemberReply', username, password, token);
						break;
					case 'twitter':
						$('#edialog-twitter-reply').prop('disabled', true);
						disjax.load('post', 'ajaxTwitterReply');
						break;
					default:
						break;
				}
			}
		},

		getGuestDefaultName: function() {
			var email = $('#discuss_usertype_guest_email').val();
			$('#discuss_usertype_guest_name').val(email.split('@', 1));
		},

		twitter: {
			signin: function(status, msg) {
				if (status) {
					disjax.load('post', 'ajaxRefreshTwitter');
				} else {
					alert('failed');
				}
			},

			signout: function() {
				disjax.load('post', 'ajaxSignOutTwitter');
			}
		}
	},
	files: {
		add: function() {
			jQuery('#file_contents div').before('<input type="file" name="filedata[]" id="filedata" size="50" />');
		}
	},
	pagination: {
		more: function(type ) {

			if (type == 'questions')
				disjax.load('index' , 'ajaxReadmore' , $('#pagination-start').val() , $('#pagination-sorting').val() , type, $('#discuss_parent').val(), $('#pagination-filter').val(), $('#pagination-category').val(), $('#pagination-query').val());
			else
				disjax.load('index' , 'ajaxReadmore' , $('#pagination-start').val() , $('#pagination-sorting').val() , type, $('#discuss_parent').val(), $('#pagination-filter').val(), $('#pagination-category').val());
		},
		addButton: function(type, label ) {
			html = '<a href="javascript:void(0);" onclick="discuss.pagination.more( \'' + type + '\' );"><span>' + label + '</span></a>';

			if ($('#dc_pagination a').length < 1)
				$('#dc_pagination').prepend(html);
		}
	},
	comment: {

		/**
		 * Deprecated since 3.0
		 */
		save: function(id) {
			discuss.spinner.show('discussSubmitWait');

			finalData	= disjax.getFormVal('#frmComment' + id);
			disjax.load('Post', 'ajaxSubmitComment', finalData);
		},
		/**
		 * Deprecated since 3.0
		 */
		add: function(id , commentBtn ) {

			// Add active state to the comment form.
			$(commentBtn).toggleClass('active');
			$('#comment-action-container-' + id).toggle();

			// Clear existing error messages.
			$('#err-msg .msg_in').html('');

			$('#comment-notification-' + id + ' .msg_in').html('');
			$('#comment-notification-' + id + ' .msg_in').removeClass('alert alert-error success');
		},

		clearForm: function(id )
		{
			$('#comment-err-msg .msg_in').html('');
			$('#comment-err-msg .msg_in').removeClass('alert alert-error success');

			$('#comment').val('');
			$('#comment-action-container-' + id).hide();
		},

		cancel: function(id) {

			$('#comment-err-msg .msg_in').html('');
			$('#comment-err-msg .msg_in').removeClass('dc_alert dc_error dc_success');

			$('#comment-notification-' + id + ' .msg_in').html('');
			$('#comment-notification-' + id + ' .msg_in').removeClass('alert alert-error success');

			$('#comment').val('');
			$('#comment-action-container-' + id).hide();

			//toggle toolbar button
			$('#comments-button-' + id).show();
		},

		remove: function(id) {
			var message = 'Are you sure?';
			// var title 	= langConfirmDeleteCommentTitle;

			if (window.confirm(message))
			{
				disjax.load('Post', 'ajaxCommentDelete', id);

				// Deduct a comment count
				EasyDiscuss.commentsCount = EasyDiscuss.commentsCount === undefined ? 0 : EasyDiscuss.commentsCount - 1;
			} else {
				return false;
			}
		},

		removeEntry: function(id) {
			$('#comment-' + id).remove();
		}
	},

	reports: {
		add: function(id) {
			disjax.loadingDialog();
			disjax.load('post' , 'reportForm' , id);
		},
		cancel: function() {
			disjax.closedlg();
		},
		submit: function() {
			disjax.load('post' , 'ajaxSubmitReport' , disjax.getFormVal('#frmReport'));
		},
		revertForm: function(id) {
			effect.highlight('#post_content_layout_' + id);

			setTimeout(function() {
				discuss.reports.cancel();
			}, 4000);
		}
	},

	/**
	 * Elements
	 */
	element: {

		focus: function(element) {
			ele	= '#' + element;
			$(ele).focus();
		}
	},


	/**
	 * Spinner
	 */
	spinner: {
		// toggle btw the spinner and save button
		show: function(id ) {
			var loading	= new Image;
			loading.src	= spinnerPath;
			loading.name	= 'discuss-loading';
			loading.id	= 'discuss-loading';

			$('#' + id).html(loading);
			$('#' + id).show();
		},
		// toggle btw the spinner and save button
		hide: function(id) {
			$('#' + id).hide();
		}
	},
	system: {
		redirect: function(url) {
			window.location = url;
		},

		refresh: function() {
			window.location.reload();
		}
	},
	subscribe: {
		post: function(post_id) {
			var type	= 'post';
			var email	= $('#subscribe_email').val();
			var name	= $('#subscribe_name').val();
			var interval	= 'instant';
			discuss.spinner.show('dialog_loading');
			disjax.load('post', 'ajaxAddSubscription', type, email, name, interval, post_id + '');
		},
		site: function() {
			var type	= 'site';
			var email	= $('#subscribe_email').val();
			var name	= $('#subscribe_name').val();
			var interval	= $('input:radio[name=subscription_interval]:checked').val();
			var post_id	= '0';

			discuss.spinner.show('dialog_loading');
			disjax.load('index', 'ajaxAddSubscription', type, email, name, interval, post_id + '');
		},

		tag: function(tag_id) {
			var type	= 'tag';
			var email	= $('#subscribe_email').val();
			var name	= $('#subscribe_name').val();
			var interval	= $('input:radio[name=subscription_interval]:checked').val();
			discuss.spinner.show('dialog_loading');
			disjax.load('index', 'ajaxAddSubscription', type, email, name, interval, tag_id + '');
		},

		category: function(cat_id) {
			var type	= 'category';
			var email	= $('#subscribe_email').val();
			var name	= $('#subscribe_name').val();
			var interval	= $('input:radio[name=subscription_interval]:checked').val();
			discuss.spinner.show('dialog_loading');

			disjax.load('index', 'ajaxAddSubscription', type, email, name, interval, cat_id + '');
		},

		user: function(user_id) {
			var type	= 'user';
			var email	= $('#subscribe_email').val();
			var name	= $('#subscribe_name').val();
			var interval	= $('input:radio[name=subscription_interval]:checked').val();
			discuss.spinner.show('dialog_loading');
			disjax.load('index', 'ajaxAddSubscription', type, email, name, interval, user_id + '');
		}
	},
	user: {
		tabs: {
			show: function(element , tabClass , ajax )
			{
				discuss.spinner.show('profile-loading');

				// Reset all tabs to non active.
				$('.user-tabs ul li').removeClass('active');

				// Set the current item as active
				$(element).parent().addClass('active');

				// Hide all tab contents first.
				$('#dc_profile .tab-item').hide();

				// Hide all paginations during filter
				$('#dc_pagination').hide();

				var pid	= $('#profile-id').val();

				if (ajax)
				{
					disjax.load('profile' , 'filter' , tabClass, pid);
				}
				else
				{
					$('#dc_profile .' + tabClass).show();
				}
			}
		},
		checkAlias: function() {
			var	alias	= $('#profile-alias').val();

			if (alias != '')
			{
				disjax.load('profile', 'ajaxCheckAlias', alias);
			}
		}
	},
	tooltips: {
		init: function() {},
		execute: function(id, type) {}
	},
	notifications: {
		interval: 3000,

		monitor: null,

		count: null,

		// Initializes the notification checks
		startMonitor: function()
		{
			var self = discuss.notifications;

			self.monitor = setTimeout(self.update, self.interval);
		},

		stopMonitor: function()
		{
			clearTimeout(discuss.notifications.monitor);
		},

		update: function()
		{
			var self = discuss.notifications;

			self.stopMonitor();

			var params	= {};

			params[$('.easydiscuss-token').val()]	= 1;

			EasyDiscuss.ajax('site.views.notifications.count', params,
			{
				type: 'jsonp',

				success: function(count)
				{
					if (count == 0)
					{
						$('#notification-count').hide();
						$('#mod-notification-count').hide();
					}

					if (count == 0 || !count) return;

					if (self.count != count)
					{
						$('#notification-count').html(count);
						$('#mod-notification-count').html(count);
					}

					if (count > 0)
					{
						$('#notification-count').show();
						$('#mod-notification-count').show();
					}
					// Update the count
					self.count = count;
				},

				complete: function()
				{
					self.startMonitor();
				}
			});
		}
	},
	conversation: {
		interval: 3000,

		monitor: null,

		count: null,

		// Initializes the notification checks
		startMonitor: function()
		{
			var self = discuss.conversation;

			self.monitor = setTimeout(self.update, self.interval);
		},

		stopMonitor: function()
		{
			clearTimeout(discuss.conversation.monitor);
		},

		update: function()
		{
			var self = discuss.conversation;

			self.stopMonitor();

			var params	= {};

			params[$('.easydiscuss-token').val()]	= 1;

			EasyDiscuss.ajax('site.views.conversation.count', params,
			{
				type: 'jsonp',

				success: function(count)
				{
					if (count == 0)
					{
						$('#conversation-count').hide();
					}

					if (count == 0 || !count) return;

					if (self.count != count)
					{
						$('#conversation-count').html(count);
					}

					if (count > 0)
					{
						$('#conversation-count').show();
					}


					// Update the count
					self.count = count;
				},

				complete: function()
				{
					self.startMonitor();
				}
			});
		},

		reply: function(recipientId ) {
			EasyDiscuss.ajax('site.views.conversation.reply' , {
				id: recipientId
			}).done(function() {

			});
		},

		write: function(userId ) {

			disjax.load('conversation' , 'write' , userId);
		},

		send: function()
		{
			var contents = $('#conversationMessage').val(),
				recipientId	= $('#recipientId').val();

			if (!contents)
			{
				$('#conversationEmptyMessage').show();
				return false;
			}

			EasyDiscuss.ajax('site.views.conversation.save' ,
			{
				'contents'	: contents,
				'recipient'	: recipientId
			})
			.done(function(options )
			{

				disjax.dialog(options);
			});
			// disjax.load('conversation' , 'save' , contents, recipientId);
		}
	},
	polls: {
		show: function() {
			$('#discuss-polls').toggle();
			$('#discuss-multiple-polls').toggle();
			$('#discuss-multiple-polls-title').toggle();

		},
		vote: function(element ) {

			var id	= $(element).val();
			disjax.load('polls' , 'vote' , id);
		},
		unvote: function(postId ) {
			disjax.load('polls' , 'unvote' , postId);
		},
		showVoters: function(pollId ) {
			disjax.load('polls' , 'getvoters' , pollId);
		},
		lock: function(postId) {
			//disjax.load('polls' , 'lockPolls' , postId);
			EasyDiscuss.ajax('site.views.polls.lockPolls' ,
			{
				'postId'	: postId
			})
			.done(function(msg, isQuestion, id, pollsId )
			{
				if (isQuestion)
				{
					$('.discussQuestion').addClass('is-poll-lock');
				}
				else
				{
					$('#dc_reply_' + id).addClass('is-poll-lock');
				}

				var length = pollsId.length,
				element = null;

				for (var i = 0; i < length; i++)
				{
					element = pollsId[i];

					$('#poll-count-' + element).attr('disabled', 'disabled');
					$('#poll-count-' + element).prop('disabled', 'disabled');
				}

				$('#poll_notice_' + id).html(msg);
				$('#poll_notice_' + id).show();
				$('#poll_notice_' + id).parent().addClass('alert');

			});
		},
		unlock: function(postId) {
			//disjax.load('polls' , 'unlockPolls' , postId);
			EasyDiscuss.ajax('site.views.polls.unlockPolls' ,
			{
				'postId'	: postId
			})
			.done(function(isQuestion, id, pollsId )
			{
				if (isQuestion)
				{
					$('.discussQuestion').removeClass('is-poll-lock');
				}
				else
				{
					$('#dc_reply_' + id).removeClass('is-poll-lock');
				}

				var length = pollsId.length,
				element = null;

				for (var i = 0; i < length; i++) {
					element = pollsId[i];
					$('#poll-count-' + element).removeAttr('disabled', 'disabled');
					$('#poll-count-' + element).removeProp('disabled', 'disabled');
				}

				$('#poll_notice_' + id).html('');
				$('#poll_notice_' + id).parent().removeClass('alert');

			});
		}

	},
	tabs: {
		show: function(element , className )
		{
			// Hide all tabs
			$('.form-tab-item').hide();

			// Remove active class
			$(element).parent().siblings().removeClass('active');

			$(element).parent().addClass('active');

			// Show the responsible tab
			$('.tab-' + className).show();


		}
	},
	toolbar: {
		login: function() {
			$('#dc_toolbar .to_login div.toolbar-note').toggle();
		}
	}
  };

  window.effect = effect = {
	highlight: function(element) {
		setTimeout(function() {
			$(element).animate({ backgroundColor: '#ffff66' }, 300).animate({ backgroundColor: 'transparent' }, 1500);
		}, 500);
	}
  };

  /* DISJAX */

  disjax = window.disjax = disjax = {
	http:	false, //HTTP Object
	format:	'text',
	callback:	function(data) {},
	error:	false,
	btnArray:	new Array(),
	getHTTPObject: function() {
		var http = false;

		//Use IE's ActiveX items to load the file.
		if (typeof ActiveXObject != 'undefined') {
			try {
				http = new ActiveXObject('Msxml2.XMLHTTP');
			}
			catch (e) {
				try {
					http = new ActiveXObject('Microsoft.XMLHTTP');
				}
				catch (E) {
					http = false;
				}
			}
		//If ActiveX is not available, use the XMLHttpRequest of Firefox/Mozilla etc. to load the document.
		}
		else if (XMLHttpRequest) {
			try {http = new XMLHttpRequest();}
			catch (e) {http = false;}
		}
		this.http	= http;
	},
	/**
	 * Ajax function
	 */
	load: function(view, method )
	{
		var callback = {};

		if (typeof view == 'object')
		{
			callback = view.callback;
			view = view.view;
		}

		// This will be the site we are trying to connect to.
		url	= discuss_site;
		url	+= '&tmpl=component';
		url += '&no_html=1';
		url += '&format=ajax';

		//Kill the Cache problem in IE.
		url	+= '&uid=' + new Date().getTime();

		var parameters	= '';
		parameters	= '&view=' + view + '&layout=' + method;

		// If there is more than 1 arguments, we want to accept it as parameters.
		if (arguments.length > 2)
		{

			// Make header requests
			for (var i = 2; i < arguments.length; i++)
			{
				var myArgument	= arguments[i];

				if ($.isArray(myArgument))
				{
					for (var j = 0; j < myArgument.length; j++)
					{
						var argument = myArgument[j];

						if (typeof(argument) == 'string')
						{
							// Regular expression to check if the argument have () or not
							var expr = /^\w+\(*\)$/;
							// check the argument
							var match = expr.exec(argument);

							var arg = argument;

							if (!match) {
								arg = escape(arg);
							}

							// Encode value to proper html entities.
							parameters	+= '&value' + (i - 2) + '[]=' + encodeURIComponent(arg);
						}
					}
				}
				else
				{
					var argument = myArgument;
					if (typeof(argument) == 'string')
					{
						// Regular expression to check if the argument have () or not
						var expr = /^\w+\(*\)$/;
						// check the argument
						var match = expr.exec(argument);

						var arg = argument;

						if (!match) {
							arg = escape(arg);
						}

						// Encode value to proper html entities.
						parameters	+= '&value' + (i - 2) + '=' + encodeURIComponent(arg);
					}
				}
			}
		}

		// Add in tokens
		var token	= $('.easydiscuss-token').val();
		parameters	+= '&' + token + '=1';

		this.getHTTPObject(); //The XMLHttpRequest object is recreated at every call - to defeat Cache problem in IE

		if (!this.http || !view || !method) return;


		var ths = this;//Closure

		this.http.open('POST' , url, true);

		// Required because we are doing a post
		this.http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		this.http.setRequestHeader('Content-length', parameters.length);
		this.http.setRequestHeader('Connection', 'close');

		this.http.onreadystatechange = function() {
			//Call a function when the state changes.
			if (!ths)
				return;

			var http = ths.http;

			if (http.readyState == 4)
			{
				//Ready State will be 4 when the document is loaded.
				if (http.status == 200)
				{
					var result = '';

					if (http.responseText)
					{
						result = http.responseText;
					}

					// Evaluate the result before processing the JSON text. New lines in JSON string,
					// when evaluated will create errors in IE.
					result	= result.replace(/[\n\r]/g, '');

					//alert(result);

					result	= eval(result);

					//Give the data to the callback function.
					ths.process(result, callback);
				}
				else
				{
					//An error occured
					if (ths.error) ths.error(http.status);
				}
			}
		};

		this.http.send(parameters);
	},

	/**
	 * Get form values
	 *
	 * @param	string	Form ID.
	 */
	getFormVal: function(element ) {

		var inputs = [];
		var val	= null;

		$(':input', $(element)).each(function() {
			val = this.value.replace(/"/g, '&quot;');
			val = encodeURIComponent(val);

			if ($(this).is(':checkbox') || $(this).is(':radio'))
			{
				if ($(this).prop('checked'))
					inputs.push(this.name + '=' + escape(val));
			}
			else
			{
				inputs.push(this.name + '=' + escape(val));
			}
		});

		return inputs;
	},

	process: function(result , callback ) {

		// If the callback is being applied we just push the data to the callback
		if (typeof(callback) == 'function')
		{
			return callback.apply(this, result);
		}

		// Process response according to the key
		for (var i = 0; i < result.length; i++)
		{
			var action	= result[i][0];

			switch (action)
			{
				case 'script':
					var data	= result[i][1];
					eval(data);
					break;

				case 'after':
					var id	= result[i][1];
					var value	= result[i][2];
					$('#' + id).after(value);
					break;

				case 'append':
					var id	= result[i][1];
					var value	= result[i][2];
					$('#' + id).append(value);
					break;

				case 'assign':
					var id	= result[i][1];
					var value	= result[i][2];

					$('#' + id).html(value);
					break;

				case 'value':
					var id	= result[i][1];
					var value	= result[i][2];

					$('#' + id).val(value);
					break;

				case 'prepend':
					var id	= result[i][1];
					var value	= result[i][2];
					$('#' + id).prepend(value);
					break;

				case 'destroy':
					var id	= result[i][1];
					$('#' + id).remove();
					break;

				case 'dialog':
					disjax.dialog(result[i][1]);
					break;

				case 'alert':
					disjax.alert(result[i][1], result[i][2], result[i][3]);
					break;

				case 'create':
					break;
			}
		}
		delete result;
	},

	loadingDialog: function() {

		disjax.dialog({title: $.language('COM_EASYDISCUSS_LOADING'), loading: true});
	},

	/**
	 * Dialog
	 */
	dialog: function(options ) {
		disjax._showPopup(options);
	},
	// Close dialog box
	closedlg: function() {
		var dialog = $('#discuss-dialog');
		var dialogOverlay = $('#discuss-overlay');
		dialogOverlay.hide();
		dialog
			.unbind('.dialog')
			.hide();

		$(document).off('click.ed.closedlg');

		$(document).unbind('keyup', disjax._attachPopupShortcuts);
	},

	/**
	 * Private function
	 *
	 * Generate dialog and popup dialog
	 */
	// _showPopup: function( type, content, callback, title, width, height ) {

	_showPopup: function(options) {

		var defaultOptions = {
			width: '500',
			height: 'auto',
			type: 'dialog',
			loading: false
		};

		var options = $.extend({}, defaultOptions, options);

		// var dialogOverlay = $('#discuss-overlay');

		// if (dialogOverlay.length < 1)
		// {
		// 	dialogOverlay = '<div id="discuss-overlay" class="si_pop_overlay"></div>';

		// 	dialogOverlay = $(dialogOverlay).appendTo('body');

		// 	dialogOverlay.click(function()
		// 	{
		// 		disjax.closedlg();
		// 	});
		// }

		// dialogOverlay
		// 	.css({
		// 		width: $(document).width(),
		// 		height: $(document).height()
		// 	})
		// 	.show();

		var dialog = $('#discuss-dialog');

		if (dialog.length < 1)
		{
			dialogTemplate = '<div id="discuss-dialog" class="modal"><div class="modal-header"><a href="javascript:void(0);" aria-hidden="true" onclick="disjax.closedlg();" class="close">x</a><h3 class="modal-title"></h3></div><div class="modal-body"></div><div class="modal-footer"></div>';

			dialog = $(dialogTemplate).appendTo('body');
		}

		dialog.fadeOut(0);

		if (options.loading) {
			dialog.addClass('modal-loading');
		} else {
			dialog.removeClass('modal-loading');
		}

		var dialogContent	= dialog.children('.modal-body');

		// Add title into the dialog
		if (typeof options.title === 'string')
		{
			dialog.find('h3.modal-title').html(options.title);
		} else {
			dialog.find('h3.modal-title').html('&nbsp;');
		}

		var modalFooter = dialog.find('.modal-footer');

		// Add submit button
		if ($.isArray(options.buttons))
		{
			// Reset all the buttons.
			modalFooter.html('').show();

			$.each(options.buttons, function(i, button) {

				var className = button.className,
					title = button.title,
					action = button.action,
					form = button.form;

				var html =
					$(document.createElement('a'))
						.attr('href', 'javascript: void(0);')
						.addClass('btn ' + className)
						.html(title);

				if (action) {
					html.attr('onclick' , action);
				}

				if (form) {
					html.bind('click' , function() {
						$(form).submit();
					});
				}

				modalFooter.append(html);
			});
		} else {

			modalFooter.hide();
		}

		dialogContent
			.html(options.content);

		dialog
			.css({
				width: (options.width == 'auto') ? 'auto' : parseInt(options.width),
				height: (options.height == 'auto') ? 'auto' : parseInt(options.height),
				margin: 0, /* counter bootstrap */
				zIndex: 99999
			})
			.show(0, function()
			{
				var positionDialog = function()
				{
					dialog
						.css({
							top: ($(window).height() - dialog.height()) / 2,
							left: ($(window).width() - dialog.width()) / 2
						});
				};

				var positionDelay;

				$(window).bind('resize.dialog scroll.dialog', function()
				{
					clearTimeout(positionDelay);
					positionDelay = setTimeout(positionDialog, 50);
				});

				positionDialog();
			});

		$(document).on('click.ed.closedlg', '#edialog-cancel, #edialog-submit', function(){
			disjax.closedlg();
		});

		$(document).bind('keyup', disjax._attachPopupShortcuts);
	},

	_attachPopupShortcuts: function(e)
	{
		if (e.keyCode == 27) { disjax.closedlg(); }
	}
  };

	// Apply bootstrap's tooltip
	$('[rel=ed-tooltip]').tooltip({
		animation: false,
		container: 'body',
		template: '<div class="tooltip tooltip-ed"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
	});


	$(document).on("mouseover", "[rel=ed-popover]", function(){
		$(this).popover({container: 'body', delay: { show: 100, hide: 100},animation: false, trigger: 'hover'});
	});

  module.resolve();

});

EasyDiscuss.module('favourites', function($) {
	var module = this;

	EasyDiscuss
	.require()
	.language(
		'COM_EASYDISCUSS_FAVOURITE_BUTTON_UNFAVOURITE',
		'COM_EASYDISCUSS_FAVOURITE_BUTTON_FAVOURITE'
	)
	.done(function($) {

		EasyDiscuss.Controller('Post.Favourites' ,
		{
			defaultOptions:
			{
				postId: null,
				// Action buttons
				'{favButton}'	: '.btnFav',
				'{removeButton}'	: '.btnRemove',
				'{favLoader}'	: '.favLoader',
				'{favCount}'	: '.favCount'
			}
		},
		function(self) {
			return {

				init: function()
				{
					self.options.postId = self.element.data('postid');
				},

				'{favButton} click' : function(element )
				{
					//element.addClass();

					EasyDiscuss.ajax('site.views.favourites.favourite' ,
					{
						'postid' : self.options.postId
					},
					{
						beforeSend: function()
						{
							$('.favLoader').show();
							$('.favCount').empty();
						}
					})
					.done(function(result, count )
					{
						// True if just added favourite
						if (result)
						{
							element
								.addClass('isfav');

							//$(element).attr('data-original-title', $.language('COM_EASYDISCUSS_FAVOURITE_BUTTON_UNFAVOURITE'));
							$(element).html($.language('COM_EASYDISCUSS_FAVOURITE_BUTTON_UNFAVOURITE'));
							$('.favCount').html(count);
						}
						else
						{
							element
								.removeClass('isfav btn-primary');

							//$(element).attr('data-original-title', $.language('COM_EASYDISCUSS_FAVOURITE_BUTTON_FAVOURITE'));
							$(element).html($.language('COM_EASYDISCUSS_FAVOURITE_BUTTON_FAVOURITE'));
							$('.favCount').html(count);
						}
					})
					.fail(function(message )
					{
						// show error message

					})
					.always(function() {
						//remove the loading here
						$('.favLoader').hide();
					});
				},

				'{removeButton} click' : function(element )
				{
					EasyDiscuss.ajax('site.views.favourites.remove' ,
					{
						'postid' : self.options.postId
					})
					.done(function()
					{
						element
							.removeClass('isfav');

						$('.discussItem' + self.options.postId).remove();

					})
					.fail(function(message )
					{
						// show error message
					});
				}
			};
		});
		module.resolve();
	});
});

EasyDiscuss.module('filterbar', function($) {
	var module = this;

	EasyDiscuss.Controller('DiscussFilterBar',
		{
			defaultOptions: {

				'catId': null,
				'{allpostsButton}': '.allpostsBtn',
				'{featuredButton}': '.featuredBtn',
				'{newButton}': '.newBtn',
				'{unansweredButton}': '.unansweredBtn'
			}
		},
		function(self) {
			return{
				init: function() {
				},
				'{allpostsButton} click': function() {
					self.filter('allposts');
				},

				'{featuredButton} click': function() {
					self.filter('featured');
				},

				'{newButton} click': function() {
					self.filter('new');
				},

				'{unansweredButton} click': function() {
					self.filter('unanswered');
				},

				filter: function(type ) {

					var id = ((self.options.catId == 0) ? null : self.options.catId);

					EasyDiscuss.ajax('site.views.index.filter' , { args: [type, id] } ,
					{
						beforeSend: function() {

							// Show loading
							discuss.spinner.show('index-loading');

							// Hide the main list item
							$('#dc_list').hide();

							// Hide all paginations during filter
							$('#dc_pagination').hide();

							// Remove all active classes from the child filters.
							$('#filter-links').children().removeClass('active');
						},
						success: function(showFeaturedList , content , sorting , type , nextLimit , paginationContent ) {

							// Show only if necessary
							if (showFeaturedList)
							{
								$('#dc_featured_list').show();
							}

							// Assign the new content
							$('#dc_list').html(content);

							// Update the sorting content
							$('#sort-wrapper').html();

							// Update the pagination type.
							$('#pagination-filter').val(type);

							// Update the pagination limit
							$('#pagination-start').val(nextLimit);

							// Update pagination content
							$('#dc_pagination').html(paginationContent);
						},
						complete: function() {

							// Hide loading once the process is complete
							discuss.spinner.hide('index-loading');

							// Since we hid it earlier, show the list content
							$('#dc_list').show();

							// Show the pagination since we hid it earlier.
							$('#dc_pagination').show();

							// Add active class for the child filter
							$('#filter-links').find('.' + type).addClass('active');
						}
					});
				}
			};
		});
	module.resolve();
});

EasyDiscuss.module('likes', function($) {
	var module = this;

	EasyDiscuss
	.require()
	.language(
		'COM_EASYDISCUSS_UNLIKE_THIS_POST',
		'COM_EASYDISCUSS_LIKE_THIS_POST',
		'COM_EASYDISCUSS_UNLIKE',
		'COM_EASYDISCUSS_LIKES'
	)
	.done(function($) {

		EasyDiscuss.Controller('Likes' ,
		{
			defaultOptions:
			{
				postId: null,
				registeredUser: null,

				// Action buttons
				'{likeButton}'	: '.btnLike',
				'{unlikeButton}'	: '.btnUnlike',
				'{likeText}'	: '.likeText',
				'{likeCount}'	: '.likeCount',
				'{likeStatus}'	: '.likeStatus'
			}
		},
		function(self) {
			return {

				init: function()
				{
					self.options.postId = self.element.data('postid');

					self.element.data('like', true);

					// Add a loading class.
					// self.likeText().addClass( 'loadingBar' );

					// Set the like data.
					// self.getLikesData();
				},

				getLikesData: function()
				{
					EasyDiscuss.ajax('site.views.likes.getData' ,
					{
						'id'	: self.options.postId
					})
					.done(function(result ) {
						self.likeText().html(result);
					});
				},

				likeItem: function()
				{
					if (!self.options.registeredUser)
					{
						return false;
					}

					EasyDiscuss.ajax('site.views.likes.like' ,
					{
						'postid' : self.options.postId
					})
					.done(function( text, count )
					{
						self.likeText().html( text );
						self.likeCount().html( count );
					});
				},

				'{likeButton} click' : function(element )
				{
					// If user is not logged in, do not allow them to click this.
					var btnLike = self.likeButton();

					btnLike.addClass('btnUnlike');
					btnLike.attr('data-original-title', $.language('COM_EASYDISCUSS_UNLIKE_THIS_POST'));
					btnLike.find('i')
						.removeClass('icon-ed-love')
						.addClass('icon-ed-remove');
					self.likeStatus().html($.language('COM_EASYDISCUSS_UNLIKE'));
					btnLike.removeClass('btnLike');
					self.likeItem(element);
				},

				'{unlikeButton} click' : function(element )
				{
					var btnUnlike = self.unlikeButton();

					btnUnlike.addClass('btnLike');
					btnUnlike.attr('data-original-title', $.language('COM_EASYDISCUSS_LIKE_THIS_POST'));
					btnUnlike.find('i')
						.removeClass('icon-ed-remove')
						.addClass('icon-ed-love');
					self.likeStatus().html($.language('COM_EASYDISCUSS_LIKES'));
					btnUnlike.removeClass('btnUnlike');
					self.likeItem();
				},

				'{unlikeButton} mouseover' : function(element )
				{
					// $(element).find('i')
					// 	.removeClass('icon-ed-love')
					// 	.addClass('icon-ed-remove');
				},
				'{unlikeButton} mouseout' : function(element )
				{
					// $(element).find('i')
					// 	.removeClass('icon-ed-remove')
					// 	.addClass('icon-ed-love');
				}
			};
		});
	});
	$(document).on('mouseover.discussLikes', '.discussLikes', function() {

		var e = $(this);

		if (e.data('like') == undefined) {
			var registeredUser = e.attr('data-registered-user') === 'true';

			e.implement(
				EasyDiscuss.Controller.Likes,
				{
					registeredUser: registeredUser
				}
			);
		}
	});

	module.resolve();
});

EasyDiscuss.module('location', function($) {

  var module = this;

  // require: start
  EasyDiscuss.require()
	.library(
		'ui/autocomplete'
	)
	.done(function() {

        // controller: start

	EasyDiscuss.Controller(

		'Location.Form',

		{
			defaultOptions: {

				language: 'en',

				initialLocation: null,

				mapType: 'ROADMAP',
				height: '250px',
				width: '100%',

				'{locationInput}': '.locationInput',
				'{locationLatitude}': '.locationLatitude',
				'{locationLongitude}': '.locationLongitude',
				'{latitudeDisplay}'	: '.latitudeDisplay',
				'{longitudeDisplay}' : '.longitudeDisplay',
				'{locationMap}': '.locationMap',
				'{autoDetectButton}': '.autoDetectButton',
				'{locationCoords}'	: '.locationCoords',
				'{removeLocationButton}'	: '.removeLocationButton'
			}
		},

		function(self) { return {


			init: function() {

				var mapReady = $.uid('ext');

				window[mapReady] = function() {
					$.___GoogleMaps.resolve();
				};

				if (!$.___GoogleMaps) {

					$.___GoogleMaps = $.Deferred();

					EasyDiscuss.require()
						.script(
							{prefetch: false},
							'https://maps.googleapis.com/maps/api/js?sensor=true&language=' + self.options.language + '&callback=' + mapReady
						);
				}

				// Defer instantiation of controller until Google Maps library is loaded.
				$.___GoogleMaps.done(function() {
					self._init();
				});
			},

			_init: function(el, event) {

				self.geocoder = new google.maps.Geocoder();

				self.hasGeolocation = navigator.geolocation !== undefined;

				if (!self.hasGeolocation) {
					self.autoDetectButton().remove();
				} else {
					self.autoDetectButton().show();
				}

				self.locationInput()
					.autocomplete({

						delay: 300,

						minLength: 0,

						source: self.retrieveSuggestions,

						select: function(event, ui) {

							self.locationInput()
								.autocomplete('close');

							self.setLocation(ui.item.location);
						}
					})
					.prop('disabled', false);

				self.autocomplete = self.locationInput().autocomplete('widget');

				self.autocomplete
					.addClass('location-suggestion');

				var initialLocation = $.trim(self.options.initialLocation);

				if (initialLocation) {

					self.getLocationByAddress(

						initialLocation,

						function(location) {

							self.setLocation(location[0]);
						}
					);

				}

				self.busy(false);
			},

			busy: function(isBusy) {
				self.locationInput().toggleClass('loading', isBusy);
			},

			getUserLocations: function(callback) {
				self.getLocationAutomatically(
					function(locations) {
						self.userLocations = self.buildDataset(locations);
						callback && callback(locations);
					}
				);
			},

			getLocationByAddress: function(address, callback) {

				self.geocoder.geocode(
					{
						address: address
					},
					callback);
			},

			getLocationByCoords: function(latitude, longitude, callback) {

				self.geocoder.geocode(
					{
						location: new google.maps.LatLng(latitude, longitude)
					},
					callback);
			},

			getLocationAutomatically: function(success, fail) {

				if (!navigator.geolocation) {
					return fail('ERRCODE', 'Browser does not support geolocation or do not have permission to retrieve location data.');
				}

				navigator.geolocation.getCurrentPosition(
					// Success
					function(position) {
						self.getLocationByCoords(position.coords.latitude, position.coords.longitude, success);
					},
					// Fail
					fail
				);
			},

			renderMap: function(location, tooltipContent) {

				self.busy(true);

				self.locationMap().css('width' , self.options.width).css('height' , self.options.height).show();

				var map	= new google.maps.Map(
					self.locationMap()[0],
					{
						zoom: 15,
						center: location.geometry.location,
						mapTypeId: google.maps.MapTypeId[self.options.mapType],
						disableDefaultUI: true
					}
				);

				var marker = new google.maps.Marker(
					{
						draggable: true,
						position: location.geometry.location,
						center: location.geometry.location,
						title: location.formatted_address,
						map: map
					}
				);

				var infoWindow = new google.maps.InfoWindow({ content: tooltipContent });

				google.maps.event.addListener(map, 'tilesloaded', function() {
					infoWindow.open(map, marker);
					self.busy(false);
				});

				// Add listener event when drag is end so we can update the latitude and longitude.
				google.maps.event.addListener(marker, 'dragend', function(event ) {

					self.getLocationByCoords(this.getPosition().lat() , this.getPosition().lng() , function(locations) {

						// now we get the user specified location. lets update the input.
						var address = locations[0].formatted_address;
						self.locationInput().val(address);

					});

					// Update the new latitude and longitude values.
					self.locationLatitude().val(this.getPosition().lat());
					self.locationLongitude().val(this.getPosition().lng());

					// Update the new latitude and longitude display values. This is not the input.
					self.latitudeDisplay().html(this.getPosition().lat());
					self.longitudeDisplay().html(this.getPosition().lng());
					self.locationCoords().addClass('showCoords');
				});

			},

			setLocation: function(location) {

				if (!location) return;

				self.locationResolved = true;

				self.lastResolvedLocation = location;

				self.locationInput()
					.val(location.formatted_address);

				self.locationLatitude()
					.val(location.geometry.location.lat());

				self.latitudeDisplay()
					.html(location.geometry.location.lat());

				self.longitudeDisplay()
					.html(location.geometry.location.lng());

				self.locationCoords().addClass('showCoords');

				self.locationLongitude()
					.val(location.geometry.location.lng());

				self.renderMap(location, location.formatted_address);
			},

			removeLocation: function() {

				self.locationResolved = false;

				self.locationInput().val('');

				self.locationLatitude().val('');

				// Remove the display values
				self.latitudeDisplay().html('');

				self.longitudeDisplay().html('');

				self.locationCoords().removeClass('showCoords');
				self.locationLongitude().val('');

				self.locationMap().hide();
			},

			buildDataset: function(locations) {

				var dataset = $.map(locations, function(location) {
					return {
						label: location.formatted_address,
						value: location.formatted_address,
						location: location
					};
				});

				return dataset;
			},

			retrieveSuggestions: function(request, response) {

				self.busy(true);

				var address = request.term,

					respondWith = function(locations) {
						response(locations);
						self.busy(false);
					};

				// User location
				if (address == '') {

					respondWith(self.userLocations || []);

				// Keyword search
				} else {

					self.getLocationByAddress(address, function(locations) {

						respondWith(self.buildDataset(locations));
					});
				}
			},

			suggestUserLocations: function() {

				if (self.hasGeolocation && self.userLocations) {

					self.removeLocation();

					self.locationInput()
						.autocomplete('search', '');
				}

				self.busy(false);
			},

			'{locationInput} blur': function() {

				// Give way to autocomplete
				setTimeout(function() {

					var address = $.trim(self.locationInput().val());

					// Location removal
					if (address == '') {

						self.removeLocation();

					// Unresolved location, reset to last resolved location
					} else if (self.locationResolved) {

						if (address != self.lastResolvedLocation.formatted_address) {

							self.setLocation(self.lastResolvedLocation);
						}
					} else {
						self.removeLocation();
					}

				}, 250);
			},

			'{autoDetectButton} click': function() {

				self.busy(true);

				if (self.hasGeolocation && !self.userLocations) {

					self.getUserLocations(self.suggestUserLocations);

				} else {

					self.suggestUserLocations();
				}
			},

			'{removeLocationButton} click' : function()
			{
				self.removeLocation();
			}

		}}
	);

	EasyDiscuss.Controller(

		'Location.Map',

		{
			defaultOptions: {
				animation: 'drop',
				language: 'en',
				useStaticMap: false,
				disableMapsUI: true,

				// fitBounds = true will disobey zoom
				// single location with fitBounds = true will set zoom to max (by default from Google)
				// locations.length == 1 will set fitBounds = false unless explicitly specified
				// locations.length > 1 will set fitBounds = true unless explicitly specified
				zoom: 5,
				fitBounds: null,

				minZoom: null,
				maxZoom: null,

				// location in center has to be included in locations array
				// center will default to first object in locations
				// latitude and longitude always have precedence over address
				// {
				// 	"latitude": latitude,
				// 	"longitude": longitude,
				// 	"address": address
				// }
				center: null,

				// address & title are optional
				// latitude and longitude always have precedence over address
				// title will default to geocoded address
				// first object will open info window
				// [
				// 	{
				// 		"latitude": latitude,
				// 		"longitude": longitude,
				// 		"address": address,
				// 		"title": title
				// 	}
				// ]
				locations: [],

				// Default map type to be road map. Can be overriden.
				mapType: 'ROADMAP',

				width: 500,
				height: 400,

				'{locationMap}': '.locationMap',
				'{removeLocation}' : '.removeLocation'
			}
		},

		function(self) { return {

			init: function()
			{
				self.mapLoaded = false;

				var mapReady = $.uid('ext');

				window[mapReady] = function() {
					$.___GoogleMaps.resolve();
				};

				if (self.options.useStaticMap == true) {
					var language = '&language=' + String(self.options.language);
					var dimension = '&size=' + String(self.options.width) + 'x' + String(self.options.height);
					var zoom = '&zoom=' + String(self.options.zoom);
					var center = '&center=' + String(parseFloat(self.options.locations[0].latitude).toFixed(6)) + ',' + String(parseFloat(self.options.locations[0].longitude).toFixed(6));
					var maptype = '&maptype=' + google.maps.MapTypeId[self.options.mapType];
					var markers = '&markers=';
					var url = 'https://maps.googleapis.com/maps/api/staticmap?sensor=false' + language + dimension;

					if (self.options.locations.length == 1) {
						markers += String(parseFloat(self.options.locations[0].latitude).toFixed(6)) + ',' + String(parseFloat(self.options.locations[0].longitude).toFixed(6));

						url += zoom + center + maptype + markers;
					} else {
						var temp = new Array();
						$.each(self.options.locations, function(i, location) {
							temp.push(String(parseFloat(location.latitude).toFixed(6)) + ',' + String(parseFloat(location.longitude).toFixed(6)));
						});
						markers += temp.join('|');

						url += markers + maptype;
					}

					self.locationMap().show().html('<img src="' + url + '" />');
					self.busy(false);
				} else {
					var mapReady = $.uid('ext');

					window[mapReady] = function() {
						$.___GoogleMaps.resolve();
					};

					if (!$.___GoogleMaps) {

						$.___GoogleMaps = $.Deferred();

						EasyDiscuss.require()
							.script(
								{prefetch: false},
								'https://maps.googleapis.com/maps/api/js?sensor=true&language=' + self.options.language + '&callback=' + mapReady
							);
					}

					// Defer instantiation of controller until Google Maps library is loaded.
					$.___GoogleMaps.done(function() {
						self._init();
					});
				}
			},

			_init: function() {

				// initialise fitBounds according to locations.length
				if (self.options.fitBounds === null) {
					if (self.options.locations.length == 1) {
						self.options.fitBounds = false;
					} else {
						self.options.fitBounds = true;
					}
				}

				// initialise disableMapsUI value to boolean
				self.options.disableMapsUI = Boolean(self.options.disableMapsUI);

				// initialise all location object
				self.locations = new Array();
				$.each(self.options.locations, function(i, location) {
				    if (location.latitude != 'null' && location.longitude != 'null') {
						self.locations.push(new google.maps.LatLng(location.latitude, location.longitude));
					}
				});

				if (self.locations.length > 0) {
					self.renderMap();
				}

				self.busy(false);
			},

			busy: function(isBusy) {
				self.locationMap().toggleClass('loading', isBusy);
			},

			renderMap: function() {
				self.busy(true);

				self.locationMap().show();

				var latlng;

				if (self.options.center) {
					latlng = new google.maps.LatLng(center.latitude, center.longitude);
				} else {
					latlng = self.locations[0];
				}

				self.map = new google.maps.Map(
					self.locationMap()[0],
					{
						zoom: parseInt(self.options.zoom),
						minZoom: parseInt(self.options.minZoom),
						maxZoom: parseInt(self.options.maxZoom),
						center: latlng,
						mapTypeId: google.maps.MapTypeId[self.options.mapType],
						disableDefaultUI: self.options.disableMapsUI
					}
				);

				google.maps.event.addListener(self.map, 'tilesloaded', function() {
					if (self.mapLoaded == false) {
						self.mapLoaded = true;
						self.loadLocations();
					}
				});
			},

			loadLocations: function() {
				self.bounds = new google.maps.LatLngBounds();
				self.infoWindow = new Array();

				var addLocations = function() {
					$.each(self.locations, function(i, location) {
						self.bounds.extend(location);
						var placeMarker = function() {
							self.addMarker(location, self.options.locations[i]);
						};

						setTimeout(placeMarker, 100 * (i + 1));
					});

					if (self.options.fitBounds) {
						self.map.fitBounds(self.bounds);
					}
				};

				setTimeout(addLocations, 500);
			},

			addMarker: function(location, info) {
				if (!location) return;

				var marker = new google.maps.Marker(
					{
						position: location,
						map: self.map
					}
				);

				marker.setAnimation(google.maps.Animation.DROP);
				self.addInfoWindow(marker, info);
			},

			addInfoWindow: function(marker, info) {
				var content = info.content;

				if (!content) {
					content = info.address;
				}

				var infoWindow = new google.maps.InfoWindow();
				infoWindow.setContent(content);
				self.infoWindow.push(infoWindow);

				if (self.options.locations.length > 1) {
					google.maps.event.addListener(marker, 'click', function() {
						$.each(self.infoWindow, function(i, item) {
							item.close();
						});
						infoWindow.open(self.map, marker);
					});
				} else {
					google.maps.event.addListener(marker, 'click', function() {
						infoWindow.open(self.map, marker);
					});

					infoWindow.open(self.map, marker);
				}

				// custom hack for postmap module
				if (info.ratingid) {
					google.maps.event.addListener(infoWindow, 'domready', function() {
						$.each(info.ratingid, function(i, rid) {
							eblog.ratings.setup('ebpostmap_' + rid + '-ratings' , true, 'entry');
							$('#ebpostmap_' + rid + '-ratings').removeClass('ui-state-disabled');
							$('#ebpostmap_' + rid + '-ratings-form').find('.blog-rating-text').hide();
							$('#ebpostmap_' + rid + '-ratings .ratings-value').hide();
						});
					});
				}
			},
			'{removeLocation} click' : function(element )
			{
				disjax.loadingDialog();
				disjax.load('location' , 'confirmRemoveLocation' , self.element.data('id').toString());
			}
		}}
	);

        module.resolve();

        // controller: end

	});
  // require: end
});

EasyDiscuss.module('newpost', function($)
    {
	var module = this;

	EasyDiscuss.Controller('DiscussNewPost',
		{
			defaultOptions:
			{
				'{pollsCheckbox}'	: '.pollsChkbox',
				'{radioButton}'	: '.radioBtn',
				'{chkboxButton}'	: '.chkboxBtn'
			}
		},
		function(self)
		{
			return {
				init: function()
				{
				},
				'{pollsCheckbox} click': function()
				{
					self.show();
				},
				'{chkboxButton} click': function(element )
				{
					var id = element.attr('id');
					self.vote(id);
				},
				show: function()
				{
					$('#discuss-polls').toggle();
					$('#discuss-multiple-polls').toggle();
					$('#discuss-multiple-polls-title').toggle();
				},
				vote: function(id )
				{
					EasyDiscuss.ajax('site.views.polls.vote' , {
						args: [id]
					}, {
						success: function() {
						}
					});
				}
			};
		});
	module.resolve();
    });

EasyDiscuss.module('polls', function($)
    {
	var module = this;

	EasyDiscuss.require()
	.view('field.form.polls.answer')
	.done(function($) {

		EasyDiscuss.Controller('Polls.Answers' ,
		{
			defaultOptions:
			{
				pollId: null,
				'{voteCount}' : '.voteCount',
				'{votersAvatar}': '.votersAvatar',
				'{votePoll}'	: '.votePoll',
				'{unvotePoll}'	: '.unvotePoll',
				'{pollGraph}'	: '.pollGraph'
			}
		},
		function(self)
		{
			return {
				init: function()
				{
					self.options.pollId	= self.element.data('id');
				},

				'{voteCount} click' : function()
				{
					disjax.load('polls' , 'getvoters' , self.options.pollId.toString());
				},

				'{votePoll} change' : function()
				{
					EasyDiscuss.ajax('site.views.polls.vote' ,
					{
						'id'	: self.options.pollId
					})
					.done(function(pollItems ) {


						$(pollItems).each(function(index , item ) {

							// Update the graph with the percentage.
							$('.pollAnswerItem-' + item.id).find('.pollPercentage').html(item.percentage);
							$('.pollAnswerItem-' + item.id).find('.pollGraph').css('width' , item.percentage + '%');

							// Update vote count.
							$('.pollAnswerItem-' + item.id).find('.voteCount').html(item.votes);

							// Update voters avatar
							$('.pollAnswerItem-' + item.id).find('.votersList').html(item.voters);
						});

					});
				}
			};
		});

		EasyDiscuss.Controller('Polls.Form',
			{
				defaultOptions:
				{
					// Poll answers
					'{pollAnswers}'	: '.pollAnswers',
					'{pollAnswersList}' : '.pollAnswersList',
					'{insertPollAnswer}'	: '.insertPollAnswer',

					// Enable poll checkbox
					'{pollCheckbox}'	: '.pollCheckbox',

					// Poll form wrapper
					'{pollForm}'	: '.pollForm',
					'{deletedPolls}'	: '#pollsremove',

					view:
					{
						answerItem: 'field.form.polls.answer'
					}
				}
			},
			function(self)
			{
				return {

					init: function()
					{

						// Implement subcontroller on each poll answer.
						self.pollAnswers().implement(EasyDiscuss.Controller.Polls.Form.Answer,
							{
								pollController: self
							});

						// If there's no answers on the page yet, append the default template on the page.
						if (self.pollAnswers().length == 0)
						{
							self.insertNewPollAnswer();
						}

					},

					resetPollForm: function(element)
					{
						self.pollAnswers(':not(:first)').remove();
						self.pollForm().hide();
						self.pollCheckbox().prop('checked', false);
					},

					insertNewPollAnswer: function(shiftFocus )
					{
						self.view.answerItem(
							{
								showRemove: self.pollAnswers().length > 0
							})
							.implement(EasyDiscuss.Controller.Polls.Form.Answer,
							{
								pollController: self,
								shiftFocus: shiftFocus
							})
							.appendTo(self.pollAnswersList());
					},

					updateDeletedPoll: function(id )
					{
						var current	= self.deletedPolls().val();

						if (current != '')
						{
							current += ',';
						}

						self.deletedPolls().val(current + id);
					},

					showPollForm: function(element )
					{
						self.pollForm().show();
					},

					'{insertPollAnswer} click' : function()
					{
						self.insertNewPollAnswer(true);
					},

					'{pollCheckbox} change' : function()
					{
						// Show multiple poll items.
						self.pollForm().toggle();
					}
				};
			});

		EasyDiscuss.Controller('Polls.Form.Answer',
		{
			defaultOptions:
			{
				'{answerText}'	: '.answerText',
				'{removeItem}'	: '.removeItem',

				pollController: null,
				shiftFocus: false
			}

		},
		function(self)
		{
			return {

				init: function()
				{
					if (self.options.shiftFocus)
					{
						self.answerText().focus();
					}
				},

				'{removeItem} click'	: function(element )
				{
					var id = $(element).data('pollid');

					if (id != null)
					{
						self.options.pollController.updateDeletedPoll(id);
					}

					self.element.remove();
				},

				'{answerText} keyup'	: function(element , event )
				{
					if (event.keyCode == $.ui.keyCode.ENTER)
					{
						self.options.pollController.insertNewPollAnswer(true);
					}
				}
			};
		});

		module.resolve();

	});

    });

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
						//$(this).prop('disabled' , true);

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

						$(this).prop('disabled' , true);

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

					'{allPostsFilter}'		: '.allPostsFilter',
					'{newPostsFilter}'		: '.newPostsFilter',
					'{unResolvedFilter}'	: '.unResolvedFilter',
					'{resolvedFilter}'		: '.resolvedFilter',
					'{unAnsweredFilter}'	: '.unAnsweredFilter',

					'{sortLatest}'			: '.sortLatest',
					'{sortPopular}'			: '.sortPopular',

					'{ulList}'				: 'ul.normal',
					'{loader}'				: '.loader',
					'{pagination}' 			: '.dc-pagination',

					'{filterTab}' 			: '[data-filter-tab]',
					'{sortTab}' 			: '[data-sort-tab]'
				}
			},
			function(self) { return {
				init: function() {
				},

				doSort: function(sortType ) {
					self.sortTab()
						.removeClass('active')
						.filterBy("sortType", sortType)
						.addClass("active");

					filterType = self.options.activefiltertype;

					console.log(filterType);
					self.doFilter(filterType, sortType);
				},

				doFilter: function(filterType, sortType) {

					self.filterTab()
						.removeClass('active')
						.filterBy("filterType", filterType)
						.addClass("active");


					self.options.activefiltertype = filterType;

					if (sortType === undefined) sortType = 'latest';

					// show loading-bar
					self.loader().show();

					self.ulList().children('li').remove();

					EasyDiscuss.ajax('site.views.index.filter' ,
					{
						'filter': filterType,
						'sort'  : sortType,
						'id'    : self.element.data('id'),
						'view'  : self.element.data('view')
					})
					.done(function( str, pagination ){
						// hide loading-bar
						self.loader().hide();
						self.ulList().append(str);
						self.pagination().html(pagination);
					})
					.fail(function(){
						// hide loading-bar
						self.loader().hide();
					})
					.always(function(){

					});
				},

				'{filterTab} click' : function(element)
				{
					var filterType = element.data('filterType');
					self.doFilter(filterType);
				},
				'{sortTab} click' : function(element)
				{
					//$('.filterItem.secondary-nav').removeClass( 'active' );
					//element.parent().addClass('active');
					var sortType = element.data('sortType');
					self.doSort( sortType );
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

EasyDiscuss.module( 'prism', function($) {
	var module = this;


/**
 * Prism: Lightweight, robust, elegant syntax highlighting
 * MIT license http://www.opensource.org/licenses/mit-license.php/
 * @author Lea Verou http://lea.verou.me
 */

(function(){

// Private helper vars
var lang = /\blang(?:uage)?-(?!\*)(\w+)\b/i;

var _ = self.Prism = {
	util: {
		type: function (o) { 
			return Object.prototype.toString.call(o).match(/\[object (\w+)\]/)[1];
		},
		
		// Deep clone a language definition (e.g. to extend it)
		clone: function (o) {
			var type = _.util.type(o);

			switch (type) {
				case 'Object':
					var clone = {};
					
					for (var key in o) {
						if (o.hasOwnProperty(key)) {
							clone[key] = _.util.clone(o[key]);
						}
					}
					
					return clone;
					
				case 'Array':
					return o.slice();
			}
			
			return o;
		}
	},
	
	languages: {
		extend: function (id, redef) {
			var lang = _.util.clone(_.languages[id]);
			
			for (var key in redef) {
				lang[key] = redef[key];
			}
			
			return lang;
		},
		
		// Insert a token before another token in a language literal
		insertBefore: function (inside, before, insert, root) {
			root = root || _.languages;
			var grammar = root[inside];
			var ret = {};
				
			for (var token in grammar) {
			
				if (grammar.hasOwnProperty(token)) {
					
					if (token == before) {
					
						for (var newToken in insert) {
						
							if (insert.hasOwnProperty(newToken)) {
								ret[newToken] = insert[newToken];
							}
						}
					}
					
					ret[token] = grammar[token];
				}
			}
			
			return root[inside] = ret;
		},
		
		// Traverse a language definition with Depth First Search
		DFS: function(o, callback) {
			for (var i in o) {
				callback.call(o, i, o[i]);
				
				if (_.util.type(o) === 'Object') {
					_.languages.DFS(o[i], callback);
				}
			}
		}
	},

	highlightAll: function(async, callback) {
		var elements = document.querySelectorAll('code[class*="language-"], [class*="language-"] code, code[class*="lang-"], [class*="lang-"] code');

		for (var i=0, element; element = elements[i++];) {
			_.highlightElement(element, async === true, callback);
		}
	},
		
	highlightElement: function(element, async, callback) {
		// Find language
		var language, grammar, parent = element;
		
		while (parent && !lang.test(parent.className)) {
			parent = parent.parentNode;
		}
		
		if (parent) {
			language = (parent.className.match(lang) || [,''])[1];
			grammar = _.languages[language];
		}

		if (!grammar) {
			return;
		}
		
		// Set language on the element, if not present
		element.className = element.className.replace(lang, '').replace(/\s+/g, ' ') + ' language-' + language;
		
		// Set language on the parent, for styling
		parent = element.parentNode;
		
		if (/pre/i.test(parent.nodeName)) {
			parent.className = parent.className.replace(lang, '').replace(/\s+/g, ' ') + ' language-' + language; 
		}

		var code = element.textContent;
		
		if(!code) {
			return;
		}
		
		code = code.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/\u00a0/g, ' ');
		
		var env = {
			element: element,
			language: language,
			grammar: grammar,
			code: code
		};
		
		_.hooks.run('before-highlight', env);
		
		if (async && self.Worker) {
			var worker = new Worker(_.filename);	
			
			worker.onmessage = function(evt) {
				env.highlightedCode = Token.stringify(JSON.parse(evt.data), language);

				_.hooks.run('before-insert', env);

				env.element.innerHTML = env.highlightedCode;
				
				callback && callback.call(env.element);
				_.hooks.run('after-highlight', env);
			};
			
			worker.postMessage(JSON.stringify({
				language: env.language,
				code: env.code
			}));
		}
		else {
			env.highlightedCode = _.highlight(env.code, env.grammar, env.language)

			_.hooks.run('before-insert', env);

			env.element.innerHTML = env.highlightedCode;
			
			callback && callback.call(element);
			
			_.hooks.run('after-highlight', env);
		}
	},
	
	highlight: function (text, grammar, language) {
		return Token.stringify(_.tokenize(text, grammar), language);
	},
	
	tokenize: function(text, grammar, language) {
		var Token = _.Token;
		
		var strarr = [text];
		
		var rest = grammar.rest;
		
		if (rest) {
			for (var token in rest) {
				grammar[token] = rest[token];
			}
			
			delete grammar.rest;
		}
								
		tokenloop: for (var token in grammar) {
			if(!grammar.hasOwnProperty(token) || !grammar[token]) {
				continue;
			}
			
			var pattern = grammar[token], 
				inside = pattern.inside,
				lookbehind = !!pattern.lookbehind,
				lookbehindLength = 0;
			
			pattern = pattern.pattern || pattern;
			
			for (var i=0; i<strarr.length; i++) { // Don’t cache length as it changes during the loop
				
				var str = strarr[i];
				
				if (strarr.length > text.length) {
					// Something went terribly wrong, ABORT, ABORT!
					break tokenloop;
				}
				
				if (str instanceof Token) {
					continue;
				}
				
				pattern.lastIndex = 0;
				
				var match = pattern.exec(str);
				
				if (match) {
					if(lookbehind) {
						lookbehindLength = match[1].length;
					}

					var from = match.index - 1 + lookbehindLength,
					    match = match[0].slice(lookbehindLength),
					    len = match.length,
					    to = from + len,
						before = str.slice(0, from + 1),
						after = str.slice(to + 1); 

					var args = [i, 1];
					
					if (before) {
						args.push(before);
					}
					
					var wrapped = new Token(token, inside? _.tokenize(match, inside) : match);
					
					args.push(wrapped);
					
					if (after) {
						args.push(after);
					}
					
					Array.prototype.splice.apply(strarr, args);
				}
			}
		}

		return strarr;
	},
	
	hooks: {
		all: {},
		
		add: function (name, callback) {
			var hooks = _.hooks.all;
			
			hooks[name] = hooks[name] || [];
			
			hooks[name].push(callback);
		},
		
		run: function (name, env) {
			var callbacks = _.hooks.all[name];
			
			if (!callbacks || !callbacks.length) {
				return;
			}
			
			for (var i=0, callback; callback = callbacks[i++];) {
				callback(env);
			}
		}
	}
};

var Token = _.Token = function(type, content) {
	this.type = type;
	this.content = content;
};

Token.stringify = function(o, language, parent) {
	if (typeof o == 'string') {
		return o;
	}

	if (Object.prototype.toString.call(o) == '[object Array]') {
		return o.map(function(element) {
			return Token.stringify(element, language, o);
		}).join('');
	}
	
	var env = {
		type: o.type,
		content: Token.stringify(o.content, language, parent),
		tag: 'span',
		classes: ['token', o.type],
		attributes: {},
		language: language,
		parent: parent
	};
	
	if (env.type == 'comment') {
		env.attributes['spellcheck'] = 'true';
	}
	
	_.hooks.run('wrap', env);
	
	var attributes = '';
	
	for (var name in env.attributes) {
		attributes += name + '="' + (env.attributes[name] || '') + '"';
	}
	
	return '<' + env.tag + ' class="' + env.classes.join(' ') + '" ' + attributes + '>' + env.content + '</' + env.tag + '>';
	
};

if (!self.document) {
	// In worker
	self.addEventListener('message', function(evt) {
		var message = JSON.parse(evt.data),
		    lang = message.language,
		    code = message.code;
		
		self.postMessage(JSON.stringify(_.tokenize(code, _.languages[lang])));
		self.close();
	}, false);
	
	return;
}

// Get current script and highlight
var script = document.getElementsByTagName('script');

script = script[script.length - 1];

if (script) {
	_.filename = script.src;
	
	if (document.addEventListener && !script.hasAttribute('data-manual')) {
		document.addEventListener('DOMContentLoaded', _.highlightAll);
	}
}

})();;
Prism.languages.markup = {
	'comment': /&lt;!--[\w\W]*?-->/g,
	'prolog': /&lt;\?.+?\?>/,
	'doctype': /&lt;!DOCTYPE.+?>/,
	'cdata': /&lt;!\[CDATA\[[\w\W]*?]]>/i,
	'tag': {
		pattern: /&lt;\/?[\w:-]+\s*(?:\s+[\w:-]+(?:=(?:("|')(\\?[\w\W])*?\1|\w+))?\s*)*\/?>/gi,
		inside: {
			'tag': {
				pattern: /^&lt;\/?[\w:-]+/i,
				inside: {
					'punctuation': /^&lt;\/?/,
					'namespace': /^[\w-]+?:/
				}
			},
			'attr-value': {
				pattern: /=(?:('|")[\w\W]*?(\1)|[^\s>]+)/gi,
				inside: {
					'punctuation': /=|>|"/g
				}
			},
			'punctuation': /\/?>/g,
			'attr-name': {
				pattern: /[\w:-]+/g,
				inside: {
					'namespace': /^[\w-]+?:/
				}
			}
			
		}
	},
	'entity': /&amp;#?[\da-z]{1,8};/gi
};

// Plugin to make entity title show the real entity, idea by Roman Komarov
Prism.hooks.add('wrap', function(env) {

	if (env.type === 'entity') {
		env.attributes['title'] = env.content.replace(/&amp;/, '&');
	}
});;
Prism.languages.css = {
	'comment': /\/\*[\w\W]*?\*\//g,
	'atrule': {
		pattern: /@[\w-]+?.*?(;|(?=\s*{))/gi,
		inside: {
			'punctuation': /[;:]/g
		}
	},
	'url': /url\((["']?).*?\1\)/gi,
	'selector': /[^\{\}\s][^\{\};]*(?=\s*\{)/g,
	'property': /(\b|\B)[\w-]+(?=\s*:)/ig,
	'string': /("|')(\\?.)*?\1/g,
	'important': /\B!important\b/gi,
	'ignore': /&(lt|gt|amp);/gi,
	'punctuation': /[\{\};:]/g
};

if (Prism.languages.markup) {
	Prism.languages.insertBefore('markup', 'tag', {
		'style': {
			pattern: /(&lt;|<)style[\w\W]*?(>|&gt;)[\w\W]*?(&lt;|<)\/style(>|&gt;)/ig,
			inside: {
				'tag': {
					pattern: /(&lt;|<)style[\w\W]*?(>|&gt;)|(&lt;|<)\/style(>|&gt;)/ig,
					inside: Prism.languages.markup.tag.inside
				},
				rest: Prism.languages.css
			}
		}
	});
};
Prism.languages.css.selector = {
	pattern: /[^\{\}\s][^\{\}]*(?=\s*\{)/g,
	inside: {
		'pseudo-element': /:(?:after|before|first-letter|first-line|selection)|::[-\w]+/g,
		'pseudo-class': /:[-\w]+(?:\(.*\))?/g,
		'class': /\.[-:\.\w]+/g,
		'id': /#[-:\.\w]+/g
	}
};

Prism.languages.insertBefore('css', 'ignore', {
	'hexcode': /#[\da-f]{3,6}/gi,
	'entity': /\\[\da-f]{1,8}/gi,
	'number': /[\d%\.]+/g,
	'function': /(attr|calc|cross-fade|cycle|element|hsla?|image|lang|linear-gradient|matrix3d|matrix|perspective|radial-gradient|repeating-linear-gradient|repeating-radial-gradient|rgba?|rotatex|rotatey|rotatez|rotate3d|rotate|scalex|scaley|scalez|scale3d|scale|skewx|skewy|skew|steps|translatex|translatey|translatez|translate3d|translate|url|var)/ig
});;
Prism.languages.clike = {
	'comment': {
		pattern: /(^|[^\\])(\/\*[\w\W]*?\*\/|(^|[^:])\/\/.*?(\r?\n|$))/g,
		lookbehind: true
	},
	'string': /("|')(\\?.)*?\1/g,
	'class-name': {
		pattern: /((?:(?:class|interface|extends|implements|trait|instanceof|new)\s+)|(?:catch\s+\())[a-z0-9_\.\\]+/ig,
		lookbehind: true,
		inside: {
			punctuation: /(\.|\\)/
		}
	},
	'keyword': /\b(if|else|while|do|for|return|in|instanceof|function|new|try|throw|catch|finally|null|break|continue)\b/g,
	'boolean': /\b(true|false)\b/g,
	'function': {
		pattern: /[a-z0-9_]+\(/ig,
		inside: {
			punctuation: /\(/
		}
	},
	'number': /\b-?(0x[\dA-Fa-f]+|\d*\.?\d+([Ee]-?\d+)?)\b/g,
	'operator': /[-+]{1,2}|!|&lt;=?|>=?|={1,3}|(&amp;){1,2}|\|?\||\?|\*|\/|\~|\^|\%/g,
	'ignore': /&(lt|gt|amp);/gi,
	'punctuation': /[{}[\];(),.:]/g
};
;
Prism.languages.javascript = Prism.languages.extend('clike', {
	'keyword': /\b(var|let|if|else|while|do|for|return|in|instanceof|function|new|with|typeof|try|throw|catch|finally|null|break|continue)\b/g,
	'number': /\b-?(0x[\dA-Fa-f]+|\d*\.?\d+([Ee]-?\d+)?|NaN|-?Infinity)\b/g
});

Prism.languages.insertBefore('javascript', 'keyword', {
	'regex': {
		pattern: /(^|[^/])\/(?!\/)(\[.+?]|\\.|[^/\r\n])+\/[gim]{0,3}(?=\s*($|[\r\n,.;})]))/g,
		lookbehind: true
	}
});

if (Prism.languages.markup) {
	Prism.languages.insertBefore('markup', 'tag', {
		'script': {
			pattern: /(&lt;|<)script[\w\W]*?(>|&gt;)[\w\W]*?(&lt;|<)\/script(>|&gt;)/ig,
			inside: {
				'tag': {
					pattern: /(&lt;|<)script[\w\W]*?(>|&gt;)|(&lt;|<)\/script(>|&gt;)/ig,
					inside: Prism.languages.markup.tag.inside
				},
				rest: Prism.languages.javascript
			}
		}
	});
}
;
Prism.languages.java = Prism.languages.extend('clike', {
	'keyword': /\b(abstract|continue|for|new|switch|assert|default|goto|package|synchronized|boolean|do|if|private|this|break|double|implements|protected|throw|byte|else|import|public|throws|case|enum|instanceof|return|transient|catch|extends|int|short|try|char|final|interface|static|void|class|finally|long|strictfp|volatile|const|float|native|super|while)\b/g,
	'number': /\b0b[01]+\b|\b0x[\da-f]*\.?[\da-fp\-]+\b|\b\d*\.?\d+[e]?[\d]*[df]\b|\W\d*\.?\d+\b/gi,
	'operator': {
		pattern: /([^\.]|^)([-+]{1,2}|!|=?&lt;|=?&gt;|={1,2}|(&amp;){1,2}|\|?\||\?|\*|\/|%|\^|(&lt;){2}|($gt;){2,3}|:|~)/g,
		lookbehind: true
	}
});;
/**
 * Original by Aaron Harun: http://aahacreative.com/2012/07/31/php-syntax-highlighting-prism/
 * Modified by Miles Johnson: http://milesj.me
 *
 * Supports the following:
 * 		- Extends clike syntax
 * 		- Support for PHP 5.3 and 5.4 (namespaces, traits, etc)
 * 		- Smarter constant and function matching
 *
 * Adds the following new token classes:
 * 		constant, delimiter, variable, function, package
 */

Prism.languages.php = Prism.languages.extend('clike', {
	'keyword': /\b(and|or|xor|array|as|break|case|cfunction|class|const|continue|declare|default|die|do|else|elseif|enddeclare|endfor|endforeach|endif|endswitch|endwhile|extends|for|foreach|function|include|include_once|global|if|new|return|static|switch|use|require|require_once|var|while|abstract|interface|public|implements|extends|private|protected|parent|static|throw|null|echo|print|trait|namespace|use|final|yield|goto|instanceof|finally|try|catch)\b/ig,
	'constant': /\b[A-Z0-9_]{2,}\b/g
});

Prism.languages.insertBefore('php', 'keyword', {
	'delimiter': /(\?>|&lt;\?php|&lt;\?)/ig,
	'variable': /(\$\w+)\b/ig,
	'package': {
		pattern: /(\\|namespace\s+|use\s+)[\w\\]+/g,
		lookbehind: true,
		inside: {
			punctuation: /\\/
		}
	}
});

// Must be defined after the function pattern
Prism.languages.insertBefore('php', 'operator', {
	'property': {
		pattern: /(->)[\w]+/g,
		lookbehind: true
	}
});

// Add HTML support of the markup language exists
if (Prism.languages.markup) {

	// Tokenize all inline PHP blocks that are wrapped in <?php ?>
	// This allows for easy PHP + markup highlighting
	Prism.hooks.add('before-highlight', function(env) {
		if (env.language !== 'php') {
			return;
		}

		env.tokenStack = [];

		env.code = env.code.replace(/(?:&lt;\?php|&lt;\?|<\?php|<\?)[\w\W]*?(?:\?&gt;|\?>)/ig, function(match) {
			env.tokenStack.push(match);

			return '{{{PHP' + env.tokenStack.length + '}}}';
		});
	});

	// Re-insert the tokens after highlighting
	Prism.hooks.add('after-highlight', function(env) {
		if (env.language !== 'php') {
			return;
		}

		for (var i = 0, t; t = env.tokenStack[i]; i++) {
			env.highlightedCode = env.highlightedCode.replace('{{{PHP' + (i + 1) + '}}}', Prism.highlight(t, env.grammar, 'php'));
		}

		env.element.innerHTML = env.highlightedCode;
	});

	// Wrap tokens in classes that are missing them
	Prism.hooks.add('wrap', function(env) {
		if (env.language === 'php' && env.type === 'markup') {
			env.content = env.content.replace(/(\{\{\{PHP[0-9]+\}\}\})/g, "<span class=\"token php\">$1</span>");
		}
	});

	// Add the rules before all others
	Prism.languages.insertBefore('php', 'comment', {
		'markup': {
			pattern: /(&lt;|<)[^?]\/?(.*?)(>|&gt;)/g,
			inside: Prism.languages.markup
		},
		'php': /\{\{\{PHP[0-9]+\}\}\}/g
	});
};
Prism.languages.insertBefore('php', 'variable', {
	'this': /\$this/g,
	'global': /\$_?(GLOBALS|SERVER|GET|POST|FILES|REQUEST|SESSION|ENV|COOKIE|HTTP_RAW_POST_DATA|argc|argv|php_errormsg|http_response_header)/g,
	'scope': {
		pattern: /\b[\w\\]+::/g,
		inside: {
			keyword: /(static|self|parent)/,
			punctuation: /(::|\\)/
		}
	}
});;
Prism.languages.coffeescript = Prism.languages.extend('javascript', {
	'block-comment': /([#]{3}\s*\r?\n(.*\s*\r*\n*)\s*?\r?\n[#]{3})/g,
	'comment': /(\s|^)([#]{1}[^#^\r^\n]{2,}?(\r?\n|$))/g,
	'keyword': /\b(this|window|delete|class|extends|namespace|extend|ar|let|if|else|while|do|for|each|of|return|in|instanceof|new|with|typeof|try|catch|finally|null|undefined|break|continue)\b/g
});

Prism.languages.insertBefore('coffeescript', 'keyword', {
	'function': {
		pattern: /[a-z|A-z]+\s*[:|=]\s*(\([.|a-z\s|,|:|{|}|\"|\'|=]*\))?\s*-&gt;/gi,
		inside: {
			'function-name': /[_?a-z-|A-Z-]+(\s*[:|=])| @[_?$?a-z-|A-Z-]+(\s*)| /g,
			'operator': /[-+]{1,2}|!|=?&lt;|=?&gt;|={1,2}|(&amp;){1,2}|\|?\||\?|\*|\//g
		}
	},
	'attr-name': /[_?a-z-|A-Z-]+(\s*:)| @[_?$?a-z-|A-Z-]+(\s*)| /g
});
;
Prism.languages.scss = Prism.languages.extend('css', {
	'comment': {
		pattern: /(^|[^\\])(\/\*[\w\W]*?\*\/|\/\/.*?(\r?\n|$))/g,
		lookbehind: true
	},
	// aturle is just the @***, not the entire rule (to highlight var & stuffs)
	// + add ability to highlight number & unit for media queries
	'atrule': /@[\w-]+(?=\s+(\(|\{|;))/gi,
	// url, compassified
	'url': /([-a-z]+-)*url(?=\()/gi,
	// CSS selector regex is not appropriate for Sass
	// since there can be lot more things (var, @ directive, nesting..)
	// a selector must start at the end of a property or after a brace (end of other rules or nesting)
	// it can contain some caracters that aren't used for defining rules or end of selector, & (parent selector), or interpolated variable
	// the end of a selector is found when there is no rules in it ( {} or {\s}) or if there is a property (because an interpolated var
	// can "pass" as a selector- e.g: proper#{$erty})
	// this one was ard to do, so please be careful if you edit this one :)
	'selector': /([^@;\{\}\(\)]?([^@;\{\}\(\)]|&amp;|\#\{\$[-_\w]+\})+)(?=\s*\{(\}|\s|[^\}]+(:|\{)[^\}]+))/gm
});

Prism.languages.insertBefore('scss', 'atrule', {
	'keyword': /@(if|else if|else|for|each|while|import|extend|debug|warn|mixin|include|function|return)|(?=@for\s+\$[-_\w]+\s)+from/i
});

Prism.languages.insertBefore('scss', 'property', {
	// var and interpolated vars
	'variable': /((\$[-_\w]+)|(#\{\$[-_\w]+\}))/i
});

Prism.languages.insertBefore('scss', 'ignore', {
	'placeholder': /%[-_\w]+/i,
	'statement': /\B!(default|optional)\b/gi,
	'boolean': /\b(true|false)\b/g,
	'null': /\b(null)\b/g,
	'operator': /\s+([-+]{1,2}|={1,2}|!=|\|?\||\?|\*|\/|\%)\s+/g
});
;
Prism.languages.bash = Prism.languages.extend('clike', {
	'comment': {
		pattern: /(^|[^"{\\])(#.*?(\r?\n|$))/g,
		lookbehind: true
	},
	'string': {
		//allow multiline string
		pattern: /("|')(\\?[\s\S])*?\1/g,
		inside: {
			//'property' class reused for bash variables
			'property': /\$([a-zA-Z0-9_#\?\-\*!@]+|\{[^\}]+\})/g
		}
	},
	'keyword': /\b(if|then|else|elif|fi|for|break|continue|while|in|case|function|select|do|done|until|echo|exit|return|set|declare)\b/g
});

Prism.languages.insertBefore('bash', 'keyword', {
	//'property' class reused for bash variables
	'property': /\$([a-zA-Z0-9_#\?\-\*!@]+|\{[^}]+\})/g
});
Prism.languages.insertBefore('bash', 'comment', {
	//shebang must be before comment, 'important' class from css reused
	'important': /(^#!\s*\/bin\/bash)|(^#!\s*\/bin\/sh)/g
});
;
Prism.languages.c = Prism.languages.extend('clike', {
	'keyword': /\b(asm|typeof|inline|auto|break|case|char|const|continue|default|do|double|else|enum|extern|float|for|goto|if|int|long|register|return|short|signed|sizeof|static|struct|switch|typedef|union|unsigned|void|volatile|while)\b/g,
	'operator': /[-+]{1,2}|!=?|&lt;{1,2}=?|&gt;{1,2}=?|\-&gt;|={1,2}|\^|~|%|(&amp;){1,2}|\|?\||\?|\*|\//g
});

Prism.languages.insertBefore('c', 'keyword', {
	//property class reused for macro statements
	'property': /#\s*[a-zA-Z]+/g
});
;
Prism.languages.cpp = Prism.languages.extend('c', {
	'keyword': /\b(alignas|alignof|asm|auto|bool|break|case|catch|char|char16_t|char32_t|class|compl|const|constexpr|const_cast|continue|decltype|default|delete|delete\[\]|do|double|dynamic_cast|else|enum|explicit|export|extern|float|for|friend|goto|if|inline|int|long|mutable|namespace|new|new\[\]|noexcept|nullptr|operator|private|protected|public|register|reinterpret_cast|return|short|signed|sizeof|static|static_assert|static_cast|struct|switch|template|this|thread_local|throw|try|typedef|typeid|typename|union|unsigned|using|virtual|void|volatile|wchar_t|while)\b/g,
	'operator': /[-+]{1,2}|!=?|&lt;{1,2}=?|&gt;{1,2}=?|\-&gt;|:{1,2}|={1,2}|\^|~|%|(&amp;){1,2}|\|?\||\?|\*|\/|\b(and|and_eq|bitand|bitor|not|not_eq|or|or_eq|xor|xor_eq)\b/g
});
;
Prism.languages.python= { 
	'comment': {
		pattern: /(^|[^\\])#.*?(\r?\n|$)/g,
		lookbehind: true
	},
	'string' : /("|')(\\?.)*?\1/g,
	'keyword' : /\b(as|assert|break|class|continue|def|del|elif|else|except|exec|finally|for|from|global|if|import|in|is|lambda|pass|print|raise|return|try|while|with|yield)\b/g,
	'boolean' : /\b(True|False)\b/g,
	'number' : /\b-?(0x)?\d*\.?[\da-f]+\b/g,
	'operator' : /[-+]{1,2}|=?&lt;|=?&gt;|!|={1,2}|(&){1,2}|(&amp;){1,2}|\|?\||\?|\*|\/|~|\^|%|\b(or|and|not)\b/g,
	'ignore' : /&(lt|gt|amp);/gi,
	'punctuation' : /[{}[\];(),.:]/g
};

;
Prism.languages.sql= { 
	'comment': {
		pattern: /(^|[^\\])(\/\*[\w\W]*?\*\/|((--)|(\/\/)).*?(\r?\n|$))/g,
		lookbehind: true
	},
	'string' : /("|')(\\?.)*?\1/g,
	'keyword' : /\b(ACTION|ADD|AFTER|ALGORITHM|ALTER|ANALYZE|APPLY|AS|ASC|AUTHORIZATION|BACKUP|BDB|BEGIN|BERKELEYDB|BIGINT|BINARY|BIT|BLOB|BOOL|BOOLEAN|BREAK|BROWSE|BTREE|BULK|BY|CALL|CASCADE|CASCADED|CASE|CHAIN|CHAR VARYING|CHARACTER VARYING|CHECK|CHECKPOINT|CLOSE|CLUSTERED|COALESCE|COLUMN|COLUMNS|COMMENT|COMMIT|COMMITTED|COMPUTE|CONNECT|CONSISTENT|CONSTRAINT|CONTAINS|CONTAINSTABLE|CONTINUE|CONVERT|CREATE|CROSS|CURRENT|CURRENT_DATE|CURRENT_TIME|CURRENT_TIMESTAMP|CURRENT_USER|CURSOR|DATA|DATABASE|DATABASES|DATETIME|DBCC|DEALLOCATE|DEC|DECIMAL|DECLARE|DEFAULT|DEFINER|DELAYED|DELETE|DENY|DESC|DESCRIBE|DETERMINISTIC|DISABLE|DISCARD|DISK|DISTINCT|DISTINCTROW|DISTRIBUTED|DO|DOUBLE|DOUBLE PRECISION|DROP|DUMMY|DUMP|DUMPFILE|DUPLICATE KEY|ELSE|ENABLE|ENCLOSED BY|END|ENGINE|ENUM|ERRLVL|ERRORS|ESCAPE|ESCAPED BY|EXCEPT|EXEC|EXECUTE|EXIT|EXPLAIN|EXTENDED|FETCH|FIELDS|FILE|FILLFACTOR|FIRST|FIXED|FLOAT|FOLLOWING|FOR|FOR EACH ROW|FORCE|FOREIGN|FREETEXT|FREETEXTTABLE|FROM|FULL|FUNCTION|GEOMETRY|GEOMETRYCOLLECTION|GLOBAL|GOTO|GRANT|GROUP|HANDLER|HASH|HAVING|HOLDLOCK|IDENTITY|IDENTITY_INSERT|IDENTITYCOL|IF|IGNORE|IMPORT|INDEX|INFILE|INNER|INNODB|INOUT|INSERT|INT|INTEGER|INTERSECT|INTO|INVOKER|ISOLATION LEVEL|JOIN|KEY|KEYS|KILL|LANGUAGE SQL|LAST|LEFT|LIMIT|LINENO|LINES|LINESTRING|LOAD|LOCAL|LOCK|LONGBLOB|LONGTEXT|MATCH|MATCHED|MEDIUMBLOB|MEDIUMINT|MEDIUMTEXT|MERGE|MIDDLEINT|MODIFIES SQL DATA|MODIFY|MULTILINESTRING|MULTIPOINT|MULTIPOLYGON|NATIONAL|NATIONAL CHAR VARYING|NATIONAL CHARACTER|NATIONAL CHARACTER VARYING|NATIONAL VARCHAR|NATURAL|NCHAR|NCHAR VARCHAR|NEXT|NO|NO SQL|NOCHECK|NOCYCLE|NONCLUSTERED|NULLIF|NUMERIC|OF|OFF|OFFSETS|ON|OPEN|OPENDATASOURCE|OPENQUERY|OPENROWSET|OPTIMIZE|OPTION|OPTIONALLY|ORDER|OUT|OUTER|OUTFILE|OVER|PARTIAL|PARTITION|PERCENT|PIVOT|PLAN|POINT|POLYGON|PRECEDING|PRECISION|PREV|PRIMARY|PRINT|PRIVILEGES|PROC|PROCEDURE|PUBLIC|PURGE|QUICK|RAISERROR|READ|READS SQL DATA|READTEXT|REAL|RECONFIGURE|REFERENCES|RELEASE|RENAME|REPEATABLE|REPLICATION|REQUIRE|RESTORE|RESTRICT|RETURN|RETURNS|REVOKE|RIGHT|ROLLBACK|ROUTINE|ROWCOUNT|ROWGUIDCOL|ROWS?|RTREE|RULE|SAVE|SAVEPOINT|SCHEMA|SELECT|SERIAL|SERIALIZABLE|SESSION|SESSION_USER|SET|SETUSER|SHARE MODE|SHOW|SHUTDOWN|SIMPLE|SMALLINT|SNAPSHOT|SOME|SONAME|START|STARTING BY|STATISTICS|STATUS|STRIPED|SYSTEM_USER|TABLE|TABLES|TABLESPACE|TEMPORARY|TEMPTABLE|TERMINATED BY|TEXT|TEXTSIZE|THEN|TIMESTAMP|TINYBLOB|TINYINT|TINYTEXT|TO|TOP|TRAN|TRANSACTION|TRANSACTIONS|TRIGGER|TRUNCATE|TSEQUAL|TYPE|TYPES|UNBOUNDED|UNCOMMITTED|UNDEFINED|UNION|UNPIVOT|UPDATE|UPDATETEXT|USAGE|USE|USER|USING|VALUE|VALUES|VARBINARY|VARCHAR|VARCHARACTER|VARYING|VIEW|WAITFOR|WARNINGS|WHEN|WHERE|WHILE|WITH|WITH ROLLUP|WITHIN|WORK|WRITE|WRITETEXT)\b/gi,
	'boolean' : /\b(TRUE|FALSE|NULL)\b/gi,
	'number' : /\b-?(0x)?\d*\.?[\da-f]+\b/g,
	'operator' : /\b(ALL|AND|ANY|BETWEEN|EXISTS|IN|LIKE|NOT|OR|IS|UNIQUE|CHARACTER SET|COLLATE|DIV|OFFSET|REGEXP|RLIKE|SOUNDS LIKE|XOR)\b|[-+]{1}|!|=?&lt;|=?&gt;|={1}|(&amp;){1,2}|\|?\||\?|\*|\//gi,
	'ignore' : /&(lt|gt|amp);/gi,
	'punctuation' : /[;[\]()`,.]/g
};
;
Prism.languages.groovy = Prism.languages.extend('clike', {
	'keyword': /\b(as|def|in|abstract|assert|boolean|break|byte|case|catch|char|class|const|continue|default|do|double|else|enum|extends|final|finally|float|for|goto|if|implements|import|instanceof|int|interface|long|native|new|package|private|protected|public|return|short|static|strictfp|super|switch|synchronized|this|throw|throws|transient|try|void|volatile|while)\b/g,
	'string': /("""|''')[\W\w]*?\1|("|'|\/)[\W\w]*?\2/g,
	'number': /\b0b[01_]+\b|\b0x[\da-f_]+(\.[\da-f_p\-]+)?\b|\b[\d_]+(\.[\d_]+[e]?[\d]*)?[glidf]\b|[\d_]+(\.[\d_]+)?\b/gi,
	'operator': /={0,2}~|\?\.|\*?\.@|\.&amp;|\.(?=\w)|\.{2}(&lt;)?(?=\w)|-&gt;|\?:|[-+]{1,2}|!|&lt;=&gt;|(&gt;){1,3}|(&lt;){1,2}|={1,2}|(&amp;){1,2}|\|{1,2}|\?|\*{1,2}|\/|\^|%/g,
	'punctuation': /\.+|[{}[\];(),:$]/g,
	'annotation': /@\w+/
});

Prism.languages.insertBefore('groovy', 'punctuation', {
	'spock-block': /\b(setup|given|when|then|and|cleanup|expect|where):/g
});

Prism.hooks.add('wrap', function(env) {
	if (env.language === 'groovy' && env.type === 'string') {
		var delimiter = env.content[0];

		if (delimiter != "'") {
			env.content = Prism.highlight(env.content, {
				'expression': {
					pattern: /([^\\])(\$(\{.*?\}|[\w\.]*))/,
					lookbehind: true,
					inside: Prism.languages.groovy
				}
			});

			env.classes.push(delimiter === '/' ? 'regex' : 'gstring');
		}
	}
});
;
Prism.languages.http = {
    'request-line': {
        pattern: /^(POST|GET|PUT|DELETE|OPTIONS)\b\shttps?:\/\/\S+\sHTTP\/[0-9.]+/g,
        inside: {
            // HTTP Verb
            property: /^\b(POST|GET|PUT|DELETE|OPTIONS)\b/g,
            // Path or query argument
            'attr-name': /:\w+/g
        }
    },
    'response-status': {
        pattern: /^HTTP\/1.[01] [0-9]+.*/g,
        inside: {
            // Status, e.g. 200 OK
            property: /[0-9]+[A-Z\s-]+$/g
        }
    },
    // HTTP header name
    keyword: /^[\w-]+:(?=.+)/gm
};

// Create a mapping of Content-Type headers to language definitions
var httpLanguages = {
    'application/json': Prism.languages.javascript,
    'application/xml': Prism.languages.markup,
    'text/xml': Prism.languages.markup,
    'text/html': Prism.languages.markup
};

// Insert each content type parser that has its associated language
// currently loaded.
for (var contentType in httpLanguages) {
    if (httpLanguages[contentType]) {
        var options = {};
        options[contentType] = {
            pattern: new RegExp('(content-type:\\s*' + contentType + '[\\w\\W]*?)\\n\\n[\\w\\W]*', 'gi'),
            lookbehind: true,
            inside: {
                rest: httpLanguages[contentType]
            }
        };
        Prism.languages.insertBefore('http', 'keyword', options);
    }
}
;
/**
 * Original by Samuel Flores
 *
 * Adds the following new token classes:
 * 		constant, builtin, variable, symbol, regex
 */
Prism.languages.ruby = Prism.languages.extend('clike', {
	'comment': /#[^\r\n]*(\r?\n|$)/g,
	'keyword': /\b(alias|and|BEGIN|begin|break|case|class|def|define_method|defined|do|each|else|elsif|END|end|ensure|false|for|if|in|module|new|next|nil|not|or|raise|redo|require|rescue|retry|return|self|super|then|throw|true|undef|unless|until|when|while|yield)\b/g,
	'builtin': /\b(Array|Bignum|Binding|Class|Continuation|Dir|Exception|FalseClass|File|Stat|File|Fixnum|Fload|Hash|Integer|IO|MatchData|Method|Module|NilClass|Numeric|Object|Proc|Range|Regexp|String|Struct|TMS|Symbol|ThreadGroup|Thread|Time|TrueClass)\b/,
	'constant': /\b[A-Z][a-zA-Z_0-9]*[?!]?\b/g
});

Prism.languages.insertBefore('ruby', 'keyword', {
	'regex': {
		pattern: /(^|[^/])\/(?!\/)(\[.+?]|\\.|[^/\r\n])+\/[gim]{0,3}(?=\s*($|[\r\n,.;})]))/g,
		lookbehind: true
	},
	'variable': /[@$&]+\b[a-zA-Z_][a-zA-Z_0-9]*[?!]?\b/g,
	'symbol': /:\b[a-zA-Z_][a-zA-Z_0-9]*[?!]?\b/g
});
;
// TODO:
// 		- Support for outline parameters
// 		- Support for tables

Prism.languages.gherkin = {
	'comment': {
		pattern: /(^|[^\\])(\/\*[\w\W]*?\*\/|((#)|(\/\/)).*?(\r?\n|$))/g,
		lookbehind: true
	},
	'string': /("|')(\\?.)*?\1/g,
	'atrule': /\b(And|Given|When|Then|In order to|As an|I want to|As a)\b/g,
	'keyword': /\b(Scenario Outline|Scenario|Feature|Background|Story)\b/g,
};
;
Prism.languages.csharp = Prism.languages.extend('clike', {
	'keyword': /\b(abstract|as|base|bool|break|byte|case|catch|char|checked|class|const|continue|decimal|default|delegate|do|double|else|enum|event|explicit|extern|false|finally|fixed|float|for|foreach|goto|if|implicit|in|int|interface|internal|is|lock|long|namespace|new|null|object|operator|out|override|params|private|protected|public|readonly|ref|return|sbyte|sealed|short|sizeof|stackalloc|static|string|struct|switch|this|throw|true|try|typeof|uint|ulong|unchecked|unsafe|ushort|using|virtual|void|volatile|while|add|alias|ascending|async|await|descending|dynamic|from|get|global|group|into|join|let|orderby|partial|remove|select|set|value|var|where|yield)\b/g,
	'string': /@?("|')(\\?.)*?\1/g,
	'preprocessor': /^\s*#.*/gm,
	'number': /\b-?(0x)?\d*\.?\d+\b/g
});
;
Prism.hooks.add('after-highlight', function (env) {
	// works only for <code> wrapped inside <pre data-line-numbers> (not inline)
	var pre = env.element.parentNode;
	if (!pre || !/pre/i.test(pre.nodeName) || pre.className.indexOf('line-numbers') === -1) {
		return;
	}

	var linesNum = (1 + env.code.split('\n').length);
	var lineNumbersWrapper;

	lines = new Array(linesNum);
	lines = lines.join('<span></span>');

	lineNumbersWrapper = document.createElement('span');
	lineNumbersWrapper.className = 'line-numbers-rows';
	lineNumbersWrapper.innerHTML = lines;

	if (pre.hasAttribute('data-start')) {
		pre.style.counterReset = 'linenumber ' + (parseInt(pre.getAttribute('data-start'), 10) - 1);
	}

	env.element.appendChild(lineNumbersWrapper);

});;






	module.resolve();
});

EasyDiscuss.module('profile', function($) {

	var module = this;

	EasyDiscuss.require()
	.done(function() {
		EasyDiscuss.Controller(
			'Profile',
			{
				defaultOptions:
				{
					'userid'	: null,
					'defaultTab'	: null,

					'{tabs}'	: '.profileTab',
					'{tabContents}'	: '.tabContents',
					'{loader}'	: '.loader'
				}
			},
			function(self)
			{
				return {

					init: function()
					{
						// Get the current user's id from the element.
						self.options.userid	= self.element.data('id');

						// Initialize tabs.
						self.initializeTabs();
					},

					initializeTabs: function()
					{
						// Find default tab.
						var defaultTab = self.options.defaultTab;

						// Check if there's an anchor already.
						var anchor	= $.uri(window.location).anchor();

						if (anchor)
						{
							defaultTab = anchor.charAt(0).toUpperCase() + anchor.slice(1);
						}


						// Set the default click
						self.tabs('.tab' + defaultTab).click();
					},

					loadTabContents: function(currentTab ) {


						EasyDiscuss.ajax('site.views.profile.tab' ,
							{
								'type'	: currentTab,
								'id' : self.options.userid
							} ,
							{
								beforeSend: function()
								{
									self.tabContents('#' + currentTab).addClass('tab-pane-loading');
								},
								success: function(contents , pagination )
								{
									var html = contents;

									if (pagination != null)
									{
										html += pagination;
									}

									self.tabContents('#' + currentTab).removeClass('tab-pane-loading');

									self.tabContents('#' + currentTab).html(html);
								},
								fail: function()
								{
								}
							});
					},

					'{tabs} click' : function(element )
					{
						var elementId = element.data('id'),
							tabContent = self.tabContents('#' + elementId);

						// Fix conflict with com_profile's tabpane.js
						tabContent
							.removeClass('dynamic-tab-pane-control')
							.find('.tab-row')
							.remove();

						var length = tabContent.children().length;

						if (length <= 0)
						{
							self.loadTabContents(element.data('id'));
						}
					}
				};
			}
		);

		module.resolve();
	});
});

EasyDiscuss.module('ranks', function($) {
	var module = this;

	EasyDiscuss
	.require()
	.language(
		'COM_EASYDISCUSS_SUCCESS',
		'COM_EASYDISCUSS_FAIL'
	)
	.done(function($) {

		EasyDiscuss.Controller('Administrator.Ranks' ,
		{
			defaultOptions:
			{
				userid : null,
				'{resetButton}'	: '.resetButton'
			}
		},
		function(self) {
			return {

				init: function()
				{
					// Init
				},

				'{resetButton} click' : function(element )
				{
					$(".resetMessage").addClass("discuss-loader");

					EasyDiscuss.ajax('admin.views.ranks.ajaxResetRank' ,
					{
						'userid' : self.options.userid
					})
					.done(function(result, count )
					{
						// Done
						$('.resetMessage').html($.language('COM_EASYDISCUSS_SUCCESS'));

					})
					.fail(function(message )
					{
						// show error message
						$('.resetMessage').html($.language('COM_EASYDISCUSS_FAIL'));
					})
					.always(function() {
						// Always goes here
						$(".resetMessage").removeClass("discuss-loader");
					});
				}
			};
		});
		module.resolve();
	});
});

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

					replaceItem: function(id, html)
					{
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

						// Apply syntax highlighter
						if( EasyDiscuss.main_syntax_highlighter )
						{
							Prism.highlightAll();
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

						if (replyItem.length > 0)
						{
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

EasyDiscuss.module('votes', function($) {
	var module = this;

	EasyDiscuss
	.require()
	.done(function($) {

		EasyDiscuss.Controller('Votes' ,
		{
			defaultOptions:
			{
				postId: null,

				// Action buttons
				'{voteUp}'	: '.voteUp',
				'{voteDown}'	: '.voteDown',
				'{votePoints}'	: '.votePoints b',
				'{voteText}'	: '.voteText'
			}
		},
		function(self) {
			return {

				init: function()
				{
					self.options.postId = self.element.data('postid');
				},

				vote: function(type )
				{
					EasyDiscuss.ajax('site.views.votes.add' ,
					{
						'id'	: self.options.postId,
						'type'	: type
					})
					.done(function(totalVotes , voteText ) {

						// Update the vote count.
						self.votePoints().html(totalVotes);

					})
					.fail(function(message ) {
						console.log(message);
					});
				},

				/**
				 * Show voters that has voted in this post.
				 */
				'{votePoints} click' : function()
				{
					if (self.options.viewVotes)
					{
						disjax.loadingDialog();
						disjax.load('Votes' , 'showVoters', self.options.postId.toString());
					}
				},

				'{voteUp} click' : function()
				{
					self.vote('up');
				},

				'{voteDown} click' : function()
				{
					self.vote('down');
				}
			};
		});
		module.resolve();
	});
});

EasyDiscuss.module('stylesheet', function($) {
	var module = this;

	EasyDiscuss
	.require()
	.language(
		'COM_EASYDISCUSS_SUCCESS',
		'COM_EASYDISCUSS_FAIL'
	)
	.done(function($) {

		EasyDiscuss.Controller('Post.Stylesheet' ,
		{
			defaultOptions:
			{
				// Action buttons
				type: null,
				'{compileButton}'	: '.compileButton',
				'{compileType}'	: '#compileType',
				'{compileResult}'	: '.compileResult'
			}
		},
		function(self) {
			return {

				init: function()
				{
				},
				'{compileButton} click' : function(element )
				{
					self.testCompile($('#compileType').val());
					$('.compileButton').addClass('btn-loading');
				},
				testCompile: function(type )
				{
					EasyDiscuss.ajax('site.views.compile.testCompile' ,
					{
						'type' : type
					})
					.done(function(result, type )
					{
						// Do not remove the console.log, it is for debugging purposes
						try {
							console.log(result);
						}
						catch (err) {

						}

						$('.compileResult').addClass('text-success');
						$('.compileResult').removeClass('text-error');
						$('.compileResult').html($.language('COM_EASYDISCUSS_SUCCESS'));
					})
					.fail(function(result, type )
					{
						// Do not remove the console.log, it is for debugging purposes
						try {
							console.log(result);
						}
						catch (err) {

						}
						$('.compileResult').addClass('text-error');
						$('.compileResult').removeClass('text-success');
						$('.compileResult').html($.language('COM_EASYDISCUSS_FAIL'));
					})
					.always(function() {
						//remove the loading here
						$('.compileButton').removeClass('btn-loading');
					});
				}
			};
		});
		module.resolve();
	});
});

EasyDiscuss.module('tag', function($) {

  var module = this;

  EasyDiscuss.Controller(

	'Tag.Form',
	{
		defaultOptions: {

			tags: [],

			tagSelections: [],

			tagSelectionLimit: 25,

			'{tagList}'	: '.discuss-tag-list.creation',
			'{tagItems}'	: '.discuss-tag-list.creation .tag-item',
			'{tagItemRemoveButton}'	: '.remove-tag',

			'{tagSelection}'	: '.discuss-tag-list.selection',
			'{tagSelectionItems}'	: '.discuss-tag-list.selection .tag-item',
			'{tagSelectionMoreButton}'	: '.discuss-tag-list.selection .more-tags',

			'{totalTags}'	: '.total-tags',

			'{tagCreate}'	: '.new-tag-item',
			'{tagInput}'	: '.tag-input',
			'{tagCreateButton}'	: '.tag-create',

			view: {
				// FIX ME: it can't find the ejs path.
				'tagItem': 'tags.item'
			}
		}
	},

	function(self) { return {

		init: function() {

			self.generateTagSelections(self.options.tagSelections);

			// Generate tags
			$.each(self.options.tags, function(i, tag) {
				self.tagMap[tag.title] = tag;
				self.createTag(tag.title);
			});
		},

		tagItem: {},

		tagMap: {},

		tagSelectionMap: {},

		sanitizeTitle: function(title) {

			return $.trim(title).replace(/[,\'\"\#\<\>]/gi, '');
		},

		getTagItem: function(title) {

			return self.tagItems(function() {
				return $(this).data('title') == title;
			});
		},

		createTag: function(title) {

			if (!self.checkTagLimit()) {
				return;
			}

			var title = self.sanitizeTitle(title);

			var tag;
			if (Object.prototype.hasOwnProperty.call(self.tagMap, title)) {
				tag = self.tagMap[title];
			}
			tag = tag || {title: title};

			var tagItem = $(self.getTagItem(title)[0] || self.view('tagItem', tag));

			tagItem
				.data({
					title: tag.title,
					title_filter: tag.title.toUpperCase()
				})
				.css({opacity: 0});

			if (self.tagItems().length > 0) {

				var lastTagItem = self.tagItems(':last');

				// Don't move tag if it's already the last one.
				if (lastTagItem[0] != tagItem[0]) {
					lastTagItem.after(tagItem);
				}

			} else {

				self.tagList()
					.prepend(tagItem);
			}

			tagItem.animate({opacity: 1});

			// Remove it from tag selection if exists
			self.useTagSelection(tag.title);

			self.checkTagLimit();

			return tagItem;
		},

		removeTag: function(title) {

			var tagItem = self.getTagItem(title);

			tagItem.remove();

			// If it is an existing tag that we're removing
			// (only applies to tag which are created previously before, e.g. when editing an article)
			if (self.tagMap[title]) {

				// then put it back to tag selection.
				self.createTagSelection(title);
			}

			self.discardTagSelection(title);

			self.checkTagLimit();
		},

		checkTagLimit: function() {
			var totalTags = self.tagItems().length;

			// update tag count
			self.totalTags().text(totalTags);

			if (self.options.tagLimit != 0) {
				if (totalTags > self.options.tagLimit) {
					return false;
				} else if (totalTags == self.options.tagLimit) {
					self.tagCreate().hide();
					return false;
				} else {
					self.tagCreate().show();
					return true;
				}
			}
		},

		generateTagSelections: function(tagSelections) {

			var tagSelectionLimit = self.options.tagSelectionLimit;

			if (tagSelections.length <= tagSelectionLimit) {
				self.tagSelectionMoreButton().remove();
			}

			if (tagSelections.length < 1) {
				self.element.addClass('no-selection');
				return;
			}

			self.options.tagSelections.reverse();

			var max = self.options.tagSelections.length;

			// Generate tag selections
			$.each(self.options.tagSelections, function(i, tag) {

				self.tagSelectionMap[tag.title] = tag;

				var tagSelectionItem = self.createTagSelection(tag.title);

				if (i == tagSelectionLimit + 1) {
					self.tagSelectionMoreButton().show();
				}

				if ((max - i) > tagSelectionLimit) {
					tagSelectionItem.addClass('extras');
				}
			});

		},

		'{tagSelectionMoreButton} click': function(tagSelectionMoreButton) {

			tagSelectionMoreButton.remove();

			self.tagSelectionItems('.extras')
				.css({opacity: 0})
				.removeClass('extras')
				.animate({opacity: 1});
		},

		getTagSelectionItem: function(title) {

			if (Object.prototype.hasOwnProperty.call(self.tagItem, title)) {
				return self.tagItem[title];
			}
		},

		createTagSelection: function(title) {

			var tagSelectionItem = self.getTagSelectionItem(title);

			if (tagSelectionItem) {
				tagSelectionItem.show();
				return tagSelectionItem;
			}

			var tagSelectionItem =
				$('<li>')
					.addClass('tag-item')
					.data({
						title: title,
						title_filter: title.toUpperCase()
					})
					.html(title);

			self.tagItem[title] = tagSelectionItem;

			self.tagSelection()
				.prepend(tagSelectionItem);

			return tagSelectionItem;
		},

		useTagSelection: function(title) {

			var tagSelection = self.getTagSelectionItem(title);

			if (tagSelection) {
				tagSelection
					.addClass('used')
					.hide();
			}
		},

		discardTagSelection: function(title) {

			var tagSelection = self.getTagSelectionItem(title);

			if (tagSelection) {

				self.getTagSelectionItem(title)
					.removeClass('used')
					.show();

			}
		},

		createTagFromInput: function() {

			var title = self.sanitizeTitle(self.tagInput().val());

			if (title != '') {
				self.createTag(title);
				self.tagInput().val('');
			}

			// Reset filtered tags to original state
			self.filterTagSelectionItems('');
		},

		filterTagSelectionItems: function(title) {

			title = self.sanitizeTitle(title).toUpperCase();

			if (title == '') {

				self.tagSelectionItems(':not(.used)').show();

				self.element.removeClass('no-selection');

				self.tagSelectionMoreButton().show();

			} else {

				self.tagSelectionMoreButton().hide();

				var tagSelectionItems = self.tagSelectionItems()
					.filter(function(i, tagSelectionItem) {

						var tagSelectionItem = $(this);

						if (tagSelectionItem.hasClass('.used')) {
							tagSelectionItem.hide();
							return false;
						}

						if (tagSelectionItem.data('title_filter').indexOf(title) < 0) {
							tagSelectionItem.hide();
							return false;
						}

						tagSelectionItem.show();
						return true;
					});

				if (tagSelectionItems.length < 1) {
					self.element.addClass('no-selection');
				}
			}
		},

		'{tagInput} keydown': function(tagInput, event) {

			event.stopPropagation();

			self.realEnterKey = (event.keyCode == 13);
		},

		'{tagInput} keypress': function(tagInput, event) {

			event.stopPropagation();

			// We need to verify whether or not the user is actually entering
			// an ENTER key or exiting from an IME context menu.
			self.realEnterKey = self.realEnterKey && (event.keyCode == 13);
		},

		'{tagInput} keyup': function(tagInput, event) {

			event.stopPropagation();

			switch (event.keyCode) {

				case 27: // escape
					tagInput.val('');
					break;

				case 13: // enter
					if (self.realEnterKey) {
						self.createTagFromInput();
					}
					break;
			}

			self.filterTagSelectionItems(tagInput.val());
		},

		'{tagCreateButton} click': function() {

			self.createTagFromInput();
		},

		'{tagItemRemoveButton} click': function(tagItemRemoveButton) {

			var title = tagItemRemoveButton.parents('.tag-item').data('title');

			self.removeTag(title);
		},

		'{tagSelectionItems} click': function(tagSelectionItem) {

			self.createTag(tagSelectionItem.data('title'));
		}
	}}
  );

  module.resolve();

});

EasyDiscuss.module('toolbar', function($) {

  (function() {
	var event = $.event,

		//helper that finds handlers by type and calls back a function, this is basically handle
		// events - the events object
		// types - an array of event types to look for
		// callback(type, handlerFunc, selector) - a callback
		// selector - an optional selector to filter with, if there, matches by selector
		//     if null, matches anything, otherwise, matches with no selector
		findHelper = function(events, types, callback, selector ) {
			var t, type, typeHandlers, all, h, handle,
				namespaces, namespace,
				match;
			for (t = 0; t < types.length; t++) {
				type = types[t];
				all = type.indexOf('.') < 0;
				if (!all) {
					namespaces = type.split('.');
					type = namespaces.shift();
					namespace = new RegExp('(^|\\.)' + namespaces.slice(0).sort().join('\\.(?:.*\\.)?') + '(\\.|$)');
				}
				typeHandlers = (events[type] || []).slice(0);

				for (h = 0; h < typeHandlers.length; h++) {
					handle = typeHandlers[h];

					match = (all || namespace.test(handle.namespace));

					if (match) {
						if (selector) {
							if (handle.selector === selector) {
								callback(type, handle.origHandler || handle.handler);
							}
						} else if (selector === null) {
							callback(type, handle.origHandler || handle.handler, handle.selector);
						}
						else if (!handle.selector) {
							callback(type, handle.origHandler || handle.handler);

						}
					}


				}
			}
		};

	/**
	 * Finds event handlers of a given type on an element.
	 * @param {HTMLElement} el
	 * @param {Array} types an array of event names.
	 * @param {String} [selector] optional selector.
	 * @return {Array} an array of event handlers.
	 */
	event.find = function(el, types, selector ) {
		var events = ($._data(el) || {}).events,
			handlers = [],
			t, liver, live;

		if (!events) {
			return handlers;
		}
		findHelper(events, types, function(type, handler ) {
			handlers.push(handler);
		}, selector);
		return handlers;
	};
	/**
	 * Finds all events.  Group by selector.
	 * @param {HTMLElement} el the element.
	 * @param {Array} types event types.
	 */
	event.findBySelector = function(el, types ) {
		var events = $._data(el).events,
			selectors = {},
			//adds a handler for a given selector and event
			add = function(selector, event, handler ) {
				var select = selectors[selector] || (selectors[selector] = {}),
					events = select[event] || (select[event] = []);
				events.push(handler);
			};

		if (!events) {
			return selectors;
		}
		//first check live:
		/*$.each(events.live || [], function( i, live ) {
			if ( $.inArray(live.origType, types) !== -1 ) {
				add(live.selector, live.origType, live.origHandler || live.handler);
			}
		});*/
		//then check straight binds
		findHelper(events, types, function(type, handler, selector ) {
			add(selector || '', type, handler);
		}, null);

		return selectors;
	};
	event.supportTouch = 'ontouchend' in document;

	$.fn.respondsTo = function(events ) {
		if (!this.length) {
			return false;
		} else {
			//add default ?
			return event.find(this[0], $.isArray(events) ? events : [events]).length > 0;
		}
	};
	$.fn.triggerHandled = function(event, data ) {
		event = (typeof event == 'string' ? $.Event(event) : event);
		this.trigger(event, data);
		return event.handled;
	};
	/**
	 * Only attaches one event handler for all types ...
	 * @param {Array} types llist of types that will delegate here.
	 * @param {Object} startingEvent the first event to start listening to.
	 * @param {Object} onFirst a function to call.
	 */
	event.setupHelper = function(types, startingEvent, onFirst ) {

		if (!onFirst) {
			onFirst = startingEvent;
			startingEvent = null;
		}
		var add = function(handleObj ) {

			var bySelector, selector = handleObj.selector || '';
			if (selector) {
				bySelector = event.find(this, types, selector);
				if (!bySelector.length) {
					$(this).delegate(selector, startingEvent, onFirst);
				}
			}
			else {
				//var bySelector = event.find(this, types, selector);
				if (!event.find(this, types, selector).length) {
					event.add(this, startingEvent, onFirst, {
						selector: selector,
						delegate: this
					});
				}

			}

		};

		var remove = function(handleObj) {
			var bySelector, selector = handleObj.selector || '';
			if (selector) {
				bySelector = event.find(this, types, selector);
				if (!bySelector.length) {
					$(this).undelegate(selector, startingEvent, onFirst);
				}
			}
			else {
				if (!event.find(this, types, selector).length) {
					event.remove(this, startingEvent, onFirst, {
						selector: selector,
						delegate: this
					});
				}
			}
		};

		$.each(types, function() {
			event.special[this] = {
				add: add,
				remove: remove,
				setup: function() {},
				teardown: function() {}
			};
		});
	};

	var supportTouch = 'ontouchend' in document,
		scrollEvent = 'touchmove scroll',
		touchStartEvent = supportTouch ? 'touchstart' : 'mousedown',
		touchStopEvent = supportTouch ? 'touchend' : 'mouseup',
		touchMoveEvent = supportTouch ? 'touchmove' : 'mousemove',
		data = function(event) {
			var d = event.originalEvent.touches ?
				event.originalEvent.touches[0] || event.originalEvent.changedTouches[0] :
				event;
			return {
				time: (new Date).getTime(),
				coords: [d.pageX, d.pageY],
				origin: $(event.target)
			};
		};

	/**
	 * @add jQuery.event.special
	 */
	$.event.setupHelper(['tap'], touchStartEvent, function(ev) {

		//listen to mouseup
		var start = data(ev),
			stop,
			delegate = ev.delegateTarget || ev.currentTarget,
			selector = ev.handleObj.selector,
			entered = this,
			moved = false,
			touching = true,
			timer;


		function upHandler(event) {
			stop = data(event);
			if ((Math.abs(start.coords[0] - stop.coords[0]) < 10) ||
			    (Math.abs(start.coords[1] - stop.coords[1]) < 10)) {
				$.each($.event.find(delegate, ['tap'], selector), function() {
					this.call(entered, ev, {start: start, end: stop});
				});
			}
		};

		timer = setTimeout(function() {
			$(delegate).unbind(touchStopEvent, upHandler);
		}, 500);

		$(delegate).one(touchStopEvent, upHandler);

	});

  })();


	var module = this;

	EasyDiscuss
	.require()
	.done(function($) {

		EasyDiscuss.Controller(

			'Toolbar',
			{
				defaultOptions:
				{

					'{items}'	: '.toolbarItem',
					'{dropdowns}'	: '.dropdown-menu',

					// Notifications
					'{notificationLink}' : '.notificationLink',
					'{notificationDropDown}'	: '.notificationDropDown',
					'{notificationResult}'	: '.notificationResult',
					'{notificationItems}'	: '.notificationItem',
					'{notificationLoader}'	: '.notificationLoader',

					// Messaging
					'{messageLink}' : '.messageLink',
					'{messageDropDown}'	: '.messageDropDown',
					'{messageResult}'	: '.messageResult',
					'{messageLoader}'	: '.messageLoader',
					'{messageItems}'	: '.messageItem',

					// Logout
					'{logoutForm}'	: '#logoutForm',
					'{logoutButton}'	: '.logoutButton',

					// Login
					'{loginLink}'	: '.loginLink',
					'{loginDropDown}'	: '.loginDropDown',

					// Profile
					'{profileLink}'	: '.profileLink',
					'{profileDropDown}'	: '.profileDropDown'

				}
			},

			function(self) { return {

				init: function()
				{
					// Apply responsive layout on the toolbar.
					$.responsive(self.element, {

						elementWidth: function()
						{
							return self.element.outerWidth(true) - 15;
						},
						conditions:
						{
							at: (function() {

								var listWidth = 0;

								self.items().each(function(i , element ) {
									listWidth += $(element).outerWidth(true);
								});

								return listWidth;
							})(),

							alsoSwitch:
							{
								'.dc_toolbar-wrapper'	: 'narrow',
								'.dc-button'	: 'show',
								'#dc_toolbar'	: 'hidden-height'
							}
						}
					});

				},

				'{logoutButton} click' : function()
				{
					self.logoutForm().submit();
				},

				'{loginLink} click' : function()
				{
					self.messageDropDown().hide();
					self.notificationDropDown().hide();

					self.loginDropDown().toggle();
				},

				'{profileLink} tap' : function()
				{
					self.messageDropDown().hide();
					self.notificationDropDown().hide();

					self.profileDropDown().toggle();
				},

				'{messageLink} tap' : function()
				{
					// Hide other drop downs.
					self.profileDropDown().hide();
					self.notificationDropDown().hide();

					// If the current drop down is not active, we need to get the data.
					if (self.messageDropDown().css('display') == 'none')
					{
						var params	= {};

						params[$('.easydiscuss-token').val()]	= 1;

						EasyDiscuss.ajax('site.views.conversation.load', params,
						{
							beforeSend: function()
							{
								// Ensure that the loader is shown all the time.
								self.messageLoader().show();

								// Clear off all notification items first.
								self.messageItems().remove();
							},
							success: function(html)
							{
								// Remove loading indicator.
								self.messageLoader().hide();

								self.messageResult().append(html);
							}
						});
					}

					// Toggle the notification drop down
					self.messageDropDown().toggle();
				},

				'{notificationLink} tap' : function()
				{
					self.messageDropDown().hide();
					self.profileDropDown().hide();

					// If the current drop down is not active, we need to get the data.
					if (self.notificationDropDown().css('display') == 'none')
					{
						var params	= {};

						params[$('.easydiscuss-token').val()]	= 1;

						EasyDiscuss.ajax('site.views.notifications.load', params,
						{
							beforeSend: function()
							{
								// Ensure that the loader is shown all the time.
								self.notificationLoader().show();

								// Clear off all notification items first.
								self.notificationItems().remove();
							},
							success: function(html)
							{
								// Remove loading indicator.
								self.notificationLoader().hide();

								self.notificationResult().append(html);
							}
						});
					}

					// Toggle the notification drop down
					self.notificationDropDown().toggle();

				}

			} }
		);

		EasyDiscuss.Controller(

			'mod_notifications',
			{
				defaultOptions:
				{

					'{items}'	: '.toolbarItem',
					'{dropdowns}'	: '.dropdown-menu',

					// Notifications
					'{notificationLink}' : '.notificationLink',
					'{notificationDropDown}'	: '.notificationDropDown',
					'{notificationResult}'	: '.notificationResult',
					'{notificationItems}'	: '.notificationItem',
					'{notificationLoader}'	: '.notificationLoader',

					// Messaging
					'{messageLink}' : '.messageLink',
					'{messageDropDown}'	: '.messageDropDown',
					'{messageResult}'	: '.messageResult',
					'{messageLoader}'	: '.messageLoader',
					'{messageItems}'	: '.messageItem',

					// Logout
					'{logoutForm}'	: '#logoutForm',
					'{logoutButton}'	: '.logoutButton',

					// Login
					'{loginLink}'	: '.loginLink',
					'{loginDropDown}'	: '.loginDropDown',

					// Profile
					'{profileLink}'	: '.profileLink',
					'{profileDropDown}'	: '.profileDropDown'

				}
			},

			function(self) { return {

				init: function()
				{
					// Apply responsive layout on the toolbar.
					$.responsive(self.element, {

						elementWidth: function()
						{
							return self.element.outerWidth(true) - 15;
						},
						conditions:
						{
							at: (function() {

								var listWidth = 0;

								self.items().each(function(i , element ) {
									listWidth += $(element).outerWidth(true);
								});

								return listWidth;
							})(),

							alsoSwitch:
							{
								'.dc_toolbar-wrapper'	: 'narrow',
								'.dc-button'	: 'show',
								'#dc_toolbar'	: 'hidden-height'
							}
						}
					});

				},

				'{logoutButton} click' : function()
				{
					self.logoutForm().submit();
				},

				'{loginLink} click' : function()
				{
					self.messageDropDown().hide();
					self.notificationDropDown().hide();

					self.loginDropDown().toggle();
				},

				'{profileLink} click' : function()
				{
					self.messageDropDown().hide();
					self.notificationDropDown().hide();

					self.profileDropDown().toggle();
				},

				'{messageLink} click' : function()
				{
					// Hide other drop downs.
					self.profileDropDown().hide();
					self.notificationDropDown().hide();

					// If the current drop down is not active, we need to get the data.
					if (self.messageDropDown().css('display') == 'none')
					{
						var params	= {};

						params[$('.easydiscuss-token').val()]	= 1;

						EasyDiscuss.ajax('site.views.conversation.load', params,
						{
							beforeSend: function()
							{
								// Ensure that the loader is shown all the time.
								self.messageLoader().show();

								// Clear off all notification items first.
								self.messageItems().remove();
							},
							success: function(html)
							{
								// Remove loading indicator.
								self.messageLoader().hide();

								self.messageResult().append(html);
							}
						});
					}

					// Toggle the notification drop down
					self.messageDropDown().toggle();
				},

				'{notificationLink} click' : function()
				{
					self.messageDropDown().hide();
					self.profileDropDown().hide();

					// If the current drop down is not active, we need to get the data.
					if (self.notificationDropDown().css('display') == 'none')
					{
						var params	= {};

						params[$('.easydiscuss-token').val()]	= 1;

						EasyDiscuss.ajax('site.views.notifications.load', params,
						{
							beforeSend: function()
							{
								// Ensure that the loader is shown all the time.
								self.notificationLoader().show();

								// Clear off all notification items first.
								self.notificationItems().remove();
							},
							success: function(html)
							{
								// Remove loading indicator.
								self.notificationLoader().hide();

								self.notificationResult().append(html);
							}
						});
					}

					// Toggle the notification drop down
					self.notificationDropDown().toggle();

				}

			} }
		);


		module.resolve();
	});


});
});
