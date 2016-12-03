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

				'{attachmentItem}': "[data-attachment-item]",
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

					params.files = self.attachmentItem(':not(.new)').find('input[type=file]');
					// params.files = self.attachments();

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
								typeof Recaptcha !== 'undefined' && Recaptcha.reload();
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
