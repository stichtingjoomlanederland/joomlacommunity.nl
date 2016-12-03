EasyDiscuss.module('conversation', function($) {
	var module = this;

	EasyDiscuss
	.require()
	.language(
		'COM_EASYDISCUSS_CONVERSATION_EMPTY_CONTENT'
		)
	.library('markitup')
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
