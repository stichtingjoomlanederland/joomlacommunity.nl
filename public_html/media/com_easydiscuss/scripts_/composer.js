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

					if (self.options.editorType == 'bbcode') {

						EasyDiscuss.require()
							.library(
								'markitup',
								'autogrow'
							)
							.done(function($) {
								self.editor()
									.markItUp({set: 'bbcode_easydiscuss'})
									.autogrow({lineBleed: 1});
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

				notify: function(type, message) {

					self.notification()
						.addClass('alert-' + type)
						.html(message)
						.show();
				},

				submit: function()
				{
					var params = self.form().serializeJSON();

					// Ambiguity with normal reply form
					params.content = params.dc_reply_content;

					params.files = self.attachments();

					EasyDiscuss.ajax(
							'site.views.post.saveReply',
							params,
							{
								type: 'iframe',

								beforeSend: function() {
									self.submitButton().attr('disabled', 'disabled');
									self.loadingIndicator().show();
								},

								notify: self.notify,

								reloadCaptcha: function() {
									Recaptcha.reload();
								},

								complete: function() {
									if (self._destroyed) return;
									self.submitButton().removeAttr('disabled');
									self.loadingIndicator().hide();
								}
							})
						.done(function(content) {

							self.trigger('save', content);
						})
						.fail(self.notify);
				}
			};
		}
	);

	module.resolve();
});
