Komento.module('komento.commentform', function($) {
var module = this;

var req = Komento.require();
var scriptsToLoad = new Array();

scriptsToLoad.push('komento.common');
scriptsToLoad.push('komento.upload');

if(Komento.options.config.enable_bbcode == 1) {
	// scriptsToLoad.push('markitup');
	scriptsToLoad.push('komento.bbcode');
}

if(Komento.options.config.show_location == 1) {
	scriptsToLoad.push('location');
}

// dynamic scripts loading
req.script.apply(req, scriptsToLoad).image(Komento.options.spinner).library('dialog', 'markitup').done(function() {
Komento.Controller(
	'CommentForm',
	{
		defaults: {
			'{addCommentButton}'		: '.addCommentButton',
			'{formArea}'				: '.formArea',
			'{commentInput}'			: '#commentInput',
			'{markItUpButtons}'			: '.markItUpButton a',
			'{submitButton}'			: '.submitButton',
			'{parentId}'				: 'input[name="parent"]',
			'{formAuthor}'				: '.formAuthor',
			'{usernameInput}'			: '#register-username',
			'{nameInput}'				: '#register-name',
			'{emailInput}'				: '#register-email',
			'{websiteInput}'			: '#register-website',
			'{registerCheckbox}'		: '#register-checkbox',
			'{tncCheckbox}'				: '.tncCheckbox',
			'{ratings}'					: '[data-rating-value]',
			'{tncRead}'					: '.tncRead',
			'{subscribeForm}'			: '.subscribeForm',
			'{subscribeCheckbox}'		: '.subscribeCheckbox',
			'{unsubscribeButton}'		: '.unsubscribeButton',
			'{locationForm}'			: '.locationForm',
			'{formAlert}'				: '.formAlert',
			'{recaptchaResponse}'		: '#g-recaptcha-response',
			'{captchaImage}'			: '#captcha-image',
			'{captchaResponse}'			: '#captcha-response',
			'{captchaId}'				: '#captcha-id',
			'{captchaReload}'			: '.kmt-captcha-reload',
			'{locationInput}'			: '.locationInput',
			'{locationLatitude}'		: '.locationLatitude',
			'{locationLongitude}'		: '.locationLongitude',
			'{parentLink}'				: '.parentLink',
			'{parentContainer}'			: '.parentContainer',
			'{cancelStaticReplyButton}'	: '.cancelStaticReply',
			'{commentLength}'			: '.commentLength',
			'{commentLengthCount}'		: '.commentLengthCount',
			'{uploaderWrap}'			: '.uploaderWrap',
			'{pageItemId}'				: '.pageItemId',
		}
	},
	function(self)
	{ return {
		init: function() {
			// initialise parent id = 0
			//self.parentId().val(0);
			self.parentid = 0;

			// initialise comment input to empty
			self.commentInput().val('');

			// initialise bbcode
			if(Komento.options.config.enable_bbcode == 1) {
				self.commentInput().markItUp($.getBBcodeSettings());

				if($.isArray(Komento.options.config.smileycode)) {
					$.each(Komento.options.config.smileycode, function(index, code) {
						var selector = '.kmt-markitup-custom-' + index + ' a',
							path = Komento.options.config.smileypath[index];

						$.cssRule(selector, {
							"background-image": "url('" + path + "') !important"
						});
					});
				}
			}

			// initialise location map
			if(Komento.options.config.show_location == 1 && self.locationForm().exists()) {
				self.locationForm().implement('Komento.Controller.Location.Form.Simple');
			}

			// initialise plupload
			if(Komento.options.config.upload_enable == 1 && Komento.options.acl.upload_attachment == 1 && self.uploaderWrap().exists()) {
				Komento.options.element.commentupload = $('.uploaderWrap').addController('Komento.Controller.UploadForm');
				Komento.options.element.commentupload.kmt = Komento.options.element;
			}
		},

		"{commentInput} textChange" :function(el) {
			self.commentLengthCheck();
			self.experimentalValidateComment();
		},

		"{commentInput} keyup" :function(el) {
			self.commentLengthCheck();
			self.experimentalValidateComment();
		},

		"{nameInput} keyup": function() {
			self.experimentalValidateComment();
		},

		"{emailInput} keyup": function() {
			self.experimentalValidateComment();
		},

		"{websiteInput} keyup": function() {
			self.experimentalValidateComment();
		},

		"{subscribeCheckbox} click": function() {
			self.experimentalValidateComment();
		},

		"{tncCheckbox} click": function() {
			self.experimentalValidateComment();
		},

		"{captchaResponse} keyup": function() {
			self.experimentalValidateComment();
		},

		"{recaptchaResponse} keyup": function() {
			self.experimentalValidateComment();
		},

		"{tncRead} click": function() {
			var content = '<p>' + Komento.options.config.tnc_text.replace(/\n/g, "<br />") + '</p>';

			$.dialog({
				title: $.language( 'COM_KOMENTO_FORM_TNC' ),
				customClass: 'kmt-dialog',
				width: 470,
				showOverlay: false,
				content: content
			});
		},

		"{submitButton} click": function(el) {
			if(el.checkClick()) {
				el.html('<img src="' + Komento.options.spinner + '" />');
			}

			self.validateComment();
		},

		"{unsubscribeButton} click": function(el) {
			if(el.checkClick()) {
				el.html('<img src="' + Komento.options.spinner + '" />');

				self.unsubscribe();
			}
		},

		"{captchaReload} click": function() {
			self.reloadCaptcha();
		},

		"{parentLink} mouseover": function() {
			self.parentContainer().show();
		},

		"{parentLink} mouseout": function() {
			self.parentContainer().hide();
		},

		"{cancelStaticReplyButton} click": function() {
			self.cancelStaticReply();
		},

		"{addCommentButton} click": function() {
			self.loadForm();
		},

		loadForm: function()
		{
			if( Komento.options.config.form_toggle_button == 1 )
			{
				self.addCommentButton().hide();
				self.formArea().show();

				// Only refresh when upload is enabled.
				if( Komento.options.config.enable_upload == 1 && self.kmt.commentupload )
				{
					// manual refresh because element was not rendered before, hence causing upload button to not work on IE
					self.kmt.commentupload.plupload.refresh();
				}
			}
		},

		hideForm: function()
		{
			if( Komento.options.config.form_toggle_button == 1 )
			{
				self.addCommentButton().show();
				self.formArea().hide();
			}
		},

		/*wysiwygSave: function(strip, trim) {
			if(strip === undefined) strip = true;
			if(trim === undefined) trim = true;

			if(Komento.options.config.form_editor != 'bbcode' && Komento.options.config.form_editor != 'none') {
				window.saveContent();
				var text = window.getContent();

				if(strip) {
					text = $('<div>').html(text).text();
				}

				if( trim ) {
					text = $.trim(text);
				}

				self.commentInput().val(text);
			}
		},*/

		commentLengthCheck: function() {

			// self.wysiwygSave();

			if(Komento.options.config.antispam_max_length_enable == 1 && self.commentInput().val().length > Komento.options.config.antispam_max_length) {
				self.commentInput().val(self.commentInput().val().slice(0, Komento.options.config.antispam_max_length));
			}

			self.commentLengthCount().text(self.commentInput().val().length);
		},

		validateComment: function() {
			self.clearNotification();

			if( Komento.options.konfig.enable_js_form_validation == 0 ) {
				if(self.kmt.commentupload) {
					self.kmt.commentupload.startUpload();
				} else {
					self.postComment();
				}
				return;
			}

			// perform a save if editor is wysiwyg
			// self.wysiwygSave();

			// set and trim all fields
			var name = $.trim(self.nameInput().val());
			var email = $.trim(self.emailInput().val());
			var website = $.trim(self.websiteInput().val());
			var comment = $.trim(self.commentInput().val());
			var captcha = $.trim(self.captchaResponse().val());
			var recaptcha = $.trim(self.recaptchaResponse().val());

			var validation = [];

			// validate comment input
			if(comment.length == 0) {
				self.errorNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_COMMENT_REQUIRED'));
				validation.push( '1' );
			}
			else {
				if(Komento.options.config.antispam_min_length_enable == 1 && Komento.options.config.antispam_min_length > 0 && comment.length < Komento.options.config.antispam_min_length)
				{
					self.errorNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_COMMENT_TOO_SHORT'));
					validation.push( '1' );
				}
			}

			// validate captcha input
			if((self.captchaResponse().exists() && captcha.length == 0) || (self.recaptchaResponse().exists() && recaptcha.length == 0)) {
				self.errorNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_CAPTCHA_REQUIRED'));
				validation.push( '1' );
			}

			// validate name field
			if(name.length == 0 && ((Komento.options.config.show_name == 2 && Komento.options.config.require_name == 2) || (Komento.options.guest == 1 && Komento.options.config.show_name > 0 && Komento.options.config.require_name == 1))) {
				if(self.nameInput().siblings('span').length == 0) {
					self.nameInput().after('<span class="help-inline">' + $.language('COM_KOMENTO_FORM_NOTIFICATION_NAME_REQUIRED') + '</span>');
				} else {
					self.nameInput().siblings('span').show();
				}

				self.nameInput().parents('li').addClass('error');
				validation.push( '1' );
			} else {
				self.nameInput().siblings('span').hide();
				self.nameInput().parents('li').removeClass('error');
			}

			// validate email field + subscription checkbox
			self.emailInput().siblings('span').remove();
			if(email.length == 0 && ((Komento.options.config.show_email == 2 && (Komento.options.config.require_email == 2 || self.subscribeCheckbox().prop('checked'))) || (Komento.options.guest == 1 && Komento.options.config.show_email > 0 && (Komento.options.config.require_email == 1 || self.subscribeCheckbox().prop('checked'))))) {
				self.emailInput().after('<span class="help-inline">' + $.language('COM_KOMENTO_FORM_NOTIFICATION_EMAIL_REQUIRED') + '</span>');
				self.emailInput().parents('li').addClass('error');
				validation.push( '1' );
			} else {

				// regex test email
				if(email.length > 0 && !self.validateEmail(email)) {
					self.emailInput().after('<span class="help-inline">' + $.language('COM_KOMENTO_FORM_NOTIFICATION_EMAIL_INVALID') + '</span>');
					self.emailInput().parents('li').addClass('error');
					validation.push( '1' );
				} else {
					self.emailInput().parents('li').removeClass('error');
				}
			}

			// validate website field
			self.websiteInput().siblings('span').remove();
			if(website.length == 0 && ((Komento.options.config.show_website == 2 && Komento.options.config.require_website == 2) || (Komento.options.guest == 1 && Komento.options.config.show_website > 0 && Komento.options.config.require_website == 1))) {
				self.websiteInput().after('<span class="help-inline">' + $.language('COM_KOMENTO_FORM_NOTIFICATION_WEBSITE_REQUIRED') + '</span>');
				self.websiteInput().parents('li').addClass('error');
				validation.push( '1' );
			} else {
				// regex test website
				if(website.length > 0 && !self.validateWebsite(website)) {
					self.websiteInput().after('<span class="help-inline">' + $.language('COM_KOMENTO_FORM_NOTIFICATION_WEBSITE_INVALID') + '</span>');
					self.websiteInput().parents('li').addClass('error');
					validation.push( '1' );
				} else {
					self.websiteInput().parents('li').removeClass('error');
				}
			}

			// validate tnc checkbox
			if(Komento.options.config.show_tnc == 1 && self.tncCheckbox().prop('checked') == 0) {
				self.errorNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_TNC_REQUIRED'));
				validation.push( '1' );
			}

			// validate location field
			if( self.locationLongitude().val() == '' || self.locationLatitude().val() == '' || self.locationInput().val() == '' ) {
				self.locationInput().val('');
			}

			if(validation.length == 0) {
				if(self.kmt.commentupload) {
					self.kmt.commentupload.startUpload();
				} else {
					self.postComment();
				}
			} else {
				if(self.parentid == 0) {
					self.submitButton().text($.language('COM_KOMENTO_FORM_SUBMIT'));
				} else {
					self.submitButton().text($.language('COM_KOMENTO_FORM_REPLY'));
				}
			}
		},

		experimentalValidateComment: function() {
			if( Komento.options.konfig.enable_live_form_validation == 0 ) {
				self.submitButton().enable();
				return true;
			}

			// perform a save if editor is wysiwyg
			// self.wysiwygSave();

			var name = $.trim(self.nameInput().val());
			var email = $.trim(self.emailInput().val());
			var website = $.trim(self.websiteInput().val());
			var comment = $.trim(self.commentInput().val());
			var captcha = $.trim(self.captchaResponse().val());
			var recaptcha = $.trim(self.recaptchaResponse().val());

			var validation = [];

			// validate comment input
			if(self.commentInput().val().length == 0) {
				validation.push( '1' );
			}
			else {
				if(Komento.options.config.antispam_min_length_enable == 1 && Komento.options.config.antispam_min_length > 0 && comment.length < Komento.options.config.antispam_min_comment_length)
				{
					validation.push( '1' );
				}
			}

			// validate captcha field
			if((self.captchaResponse().exists() && captcha.length == 0) || (self.recaptchaResponse().exists() && recaptcha.length == 0)) {
				validation.push( '1' );
			}

			// validate name field
			if(name.length == 0 && ((Komento.options.config.show_name == 2 && Komento.options.config.require_name == 2) || (Komento.options.guest == 1 && Komento.options.config.show_name > 0 && Komento.options.config.require_name > 0))) {
				validation.push( '1' );
			}

			// validate email field + subscription checkbox
			if(email.length == 0 && ((Komento.options.config.show_email == 2 && (Komento.options.config.require_email == 2 || self.subscribeCheckbox().prop('checked'))) || (Komento.options.guest == 1 && Komento.options.config.show_email > 0 && (Komento.options.config.require_email > 0 || self.subscribeCheckbox().prop('checked'))))) {
				validation.push( '1' );
			}

			// validate website field
			if(website.length == 0 && ((Komento.options.config.show_website == 2 && Komento.options.config.require_website == 2) || (Komento.options.guest == 1 && Komento.options.config.show_website > 0 && Komento.options.config.require_website > 0))) {
				validation.push( '1' );
			}

			// validate tnc checkbox
			if(Komento.options.config.show_tnc == 1 && self.tncCheckbox().prop('checked') == 0) {
				validation.push( '1' );
			}

			if(validation.length == 0) {
				self.submitButton().enable();
			}
			else {
				self.submitButton().disable();
			}
		},

		validateEmail: function(email) {
			if( Komento.options.config.enable_email_regex == 1 ) {
				var syntax = Komento.options.config.email_regex;

				if($.isArray(Komento.options.config.email_regex)) {
					syntax = decodeURIComponent(Komento.options.config.email_regex[0]);
				}

				var regex = new RegExp( syntax );
				return regex.test(email);
			} else {
				return true;
			}

		},

		validateWebsite: function(website) {
			if( Komento.options.config.enable_website_regex == 1 ) {
				var syntax = Komento.options.config.website_regex;

				if($.isArray(Komento.options.config.website_regex)) {
					syntax = decodeURIComponent(Komento.options.config.website_regex[0]);
				}

				var regex = new RegExp( syntax );
				return regex.test(website);
			} else {
				return true;
			}

		},

		postComment: function() {
			self.submitButton().disable();

			var attachments = [];

			if(self.kmt.commentupload) {
				attachments = self.kmt.commentupload.options.uploadedId;
			}

			Komento.ajax('site.views.komento.addcomment', {
				component: Komento.component,
				cid: Komento.cid,
				comment: self.commentInput().val(),
				parent_id: self.parentid,
				depth: self.depth,
				username: self.usernameInput().val(),
				name: self.nameInput().val(),
				email: self.emailInput().val(),
				website: self.websiteInput().val(),
				subscribe: self.subscribeCheckbox().prop('checked'),
				register: self.registerCheckbox().prop('checked'),
				tnc: self.tncCheckbox().prop('checked'),
				recaptchaResponse: self.recaptchaResponse().val(),
				captchaResponse: self.captchaResponse().val(),
				captchaId: self.captchaId().val(),
				latitude: self.locationLatitude().val(),
				longitude: self.locationLongitude().val(),
				address: self.locationInput().val(),
				contentLink: Komento.contentLink,
				attachments: attachments,
				pageItemId: self.pageItemId().val(),
				ratings: self.ratings().val()
			},
			{
				success: function(nodeId, html, publishStatus) {
					var commentHtml = $.buildHTML(html);

					if(publishStatus == 1) {
						if(Komento.options.acl.read_comment == 1) {
							self.kmt.commentlist.addComment(nodeId, commentHtml);
						}
						self.successNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_SUBMITTED'));
					}
					else {
						self.successNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_PENDING'));
					}

					// Reset everything

					// Reset recaptcha
					if(self.captchaId().length != 0 || $('#recaptcha_table').length != 0) {
						//self.reloadCaptcha();
					}

					// Reset reply
					if(self.parentid != 0) {
						self.kmt.commentlist.cancelReply();
					}

					// Reset comment text
					self.commentInput().val('');

					// Reset comment length count
					self.commentLengthCount().text('0');

					// Reset location form
					if( self.locationForm().length > 0 ) {
						self.locationForm().controller().removeLocation();
					}

					// Reset submit button text
					if(self.parentid == 0) {
						self.submitButton().text($.language('COM_KOMENTO_FORM_SUBMIT'));
					} else {
						self.submitButton().text($.language('COM_KOMENTO_FORM_REPLY'));
					}

					// Reset attachments
					if(self.kmt.commentupload) {
						self.kmt.commentupload.options.uploadedId = [];
					}

					self.ratings().trigger('clear');
				},

				fail: function(data) {
					self.errorNotification(data);
					self.submitButton().text($.language('COM_KOMENTO_ERROR'));
				},

				captcha: function(data) {
					if(Komento.options.config.antispam_captcha_type == 1) {
						Recaptcha.reload();
					} else {
						self.captchaImage().attr('src', data.image);
						self.captchaId().val(data.id);
						self.captchaResponse().val('');
					}

					if(self.parentid == 0) {
						self.submitButton().text($.language('COM_KOMENTO_FORM_SUBMIT'));
					} else {
						self.submitButton().text($.language('COM_KOMENTO_FORM_REPLY'));
					}
				},

				subscribe: function() {
					self.subscribeForm().text($.language('COM_KOMENTO_FORM_NOTIFICATION_SUBSCRIBED')).addClass('subscribed');
				},

				confirmSubscribe: function() {
					self.subscribeForm().text($.language('COM_KOMENTO_FORM_NOTIFICATION_SUBSCRIBE_CONFIRMATION_REQUIRED')).addClass('subscribed');
				},

				subscribeError: function() {
					self.subscribeForm().text($.language('COM_KOMENTO_FORM_NOTIFICATION_SUBSCRIBE_ERROR'));
				},

				notification: function(message) {
					self.notification(message);
				},

				error: function(xhr) {
					self.errorNotification(xhr.statusText);

					$.bugReport(xhr);

					self.submitButton().text($.language('COM_KOMENTO_ERROR'));
				}
			});
		},

		reloadCaptcha: function() {
			if(Komento.options.config.antispam_captcha_type == 1) {
				if(Recaptcha !== undefined) {
					Recaptcha.reload();
				}
			} else {
				Komento.ajax('site.views.komento.reloadCaptcha',
				{
					component: Komento.component
				},
				{
					success: function(data)
					{
						self.captchaImage().attr('src', data.image);
						self.captchaId().val(data.id);
						self.captchaResponse().val('');
					}
				});
			}
		},

		staticReply: function(item) {
			var id = item.parentid.split('-')[1];
			self.parentid = id;
			self.depth = parseInt(item.depth) + 1;

			var parent = $('#' + parentid);

			var avatar = parent.find('.kmt-avatar:not(.parentContainer > .kmt-avatar)').clone();
			var author = parent.find('.kmt-author:not(.parentContainer > .kmt-author)').clone();
			var time = parent.find('.kmt-time:not(.parentContainer > .kmt-time)').clone();
			var text = parent.find('.commentText:not(.parentContainer > .commentText)').clone();
			var title = '<a href="javascript:void(0);" class="cancelStaticReply">x</a>' + $.language('COM_KOMENTO_FORM_IN_REPLY_TO') + '<a href="' + Komento.contentLink + '#' + parentid + '" class="parentLink kmt-parent-link">' + '#' + id + '</a>';

			var parentContainer = $('<span class="parentContainer hidden"></span>');
			parentContainer.html('').append(avatar).append(author).append(time).append(text);

			self.element.find('h3.kmt-title').html('').append(title).append(parentContainer);
			self.submitButton().text($.language('COM_KOMENTO_FORM_REPLY'));

			self.element.scroll();
		},

		cancelStaticReply: function() {
			self.parentid = 0;
			self.depth = 0;
			self.element.find('h3.kmt-title').text($.language('COM_KOMENTO_FORM_LEAVE_YOUR_COMMENTS'));
		},

		reply: function(item) {
			self.loadForm();

			self.parentid = item.id;

			self.depth = parseInt( item.depth ) + 1;

			self.element.find('h3.kmt-title').text($.language('COM_KOMENTO_FORM_REPLY'));
			self.element.find('.submitButton').text($.language('COM_KOMENTO_FORM_REPLY'));

			item.mine.append(self.element);
			self.element.scroll();
		},

		cancelReply: function() {
			self.hideForm();

			self.element.find('h3.kmt-title').text($.language('COM_KOMENTO_FORM_LEAVE_YOUR_COMMENTS'));
			self.parentid = 0;
			self.depth = 0;
			self.element.find('.submitButton').text($.language('COM_KOMENTO_FORM_SUBMIT'));

			if(Komento.options.config.form_position == 0) {
				if(Komento.options.config.tabbed_comments == 0 ) {
					$('.commentTools').before(self.element);
				} else {
					$('.fameList').before(self.element);
				}
			} else {
				if(Komento.options.config.tabbed_comments == 0 ) {
					$('.commentList').after(self.element);
				} else {
					$('.fameList').after(self.element);
				}
			}
		},

		unsubscribe: function() {
			Komento.ajax('site.views.komento.unsubscribe', {
				component: Komento.component,
				cid: Komento.cid
			},
			{
				success: function() {
					self.subscribeForm().text($.language('COM_KOMENTO_FORM_NOTIFICATION_UNSUBSCRIBED')).removeClass('subscribed');
				},

				fail: function() {
					self.subscribeForm().text($.language('COM_KOMENTO_ERROR'));
				}
			});
		},

		errorNotification: function(message) {
			self.formAlert().removeClass('success').addClass('error');
			self.notification(message);
		},

		successNotification: function(message) {
			self.formAlert().removeClass('error').addClass('success');

			var autohide = parseInt(Komento.options.config.autohide_form_notification);
			self.notification(message, autohide);
		},

		notification: function(message, autohide) {
			// todo: add js effects
			self.formAlert().show();
			self.formAlert().append('<li>' + message + '</li>');

			if(autohide == 1) {
				setTimeout(function() {
					self.closeNotification();
				}, 5000);
			}
		},

		closeNotification: function() {
			self.formAlert().hide();
		},

		clearNotification: function() {
			self.formAlert().html('').removeClass('error').hide();
		}

	} }
);

module.resolve();
});
});
