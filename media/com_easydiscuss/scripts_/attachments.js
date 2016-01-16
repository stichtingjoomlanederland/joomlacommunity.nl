EasyDiscuss.module('attachments', function($)
    {
	var module = this;

	EasyDiscuss.require()
	.view('field.form.attachments.item' , 'field.form.attachments.fileinput')
	.done(function($) {

		EasyDiscuss.Controller('Attachments' ,
		{
			defaultOptions:
			{
				'{attachmentItem}' : '.attachmentItem',
				'{fileInput}'	: '.fileInput',
				'{itemQueue}'	: '.uploadQueue',

				view:
				{
					attachmentItem: 'field.form.attachments.item',
					inputFile: 'field.form.attachments.fileinput'
				}
			}
		},
		function(self )
		{
			return {

				init: function()
				{
					self.attachmentItem().implement(EasyDiscuss.Controller.Attachments.Item);
				},

				getExtension: function(name )
				{
					var extension = name.substr((name.lastIndexOf('.') + 1));

					switch (extension)
					{
						case 'jpg':
						case 'png':
						case 'gif':
							extension = 'image';
						break;
						case 'zip':
						case 'rar':
							extension = 'archive';
						break;
						case 'pdf':
							extension = 'pdf';
						break;
						default:
							extension = 'default';
					}

					return extension;
				},

				'{fileInput} change' : function(element )
				{
					// Add a new file input
					element.after(self.view.inputFile({}));

					var attachmentItem = self.view.attachmentItem({
							'attachment' :
							{
								'type'	: self.getExtension($(element).val()),
								'title'	: $(element).val()
							}
						});

					// Implement controller
					attachmentItem.implement(EasyDiscuss.Controller.Attachments.Item);

					// Add the file input back to the
					attachmentItem.append($(element).hide());

					// Add the attachment item to the queue.
					self.itemQueue().append(attachmentItem);

				}
			};
		});

		EasyDiscuss.Controller('Attachments.Item' ,
		{
			defaultOptions:
			{
				'{removeItem}'	: '.removeItem'
			}
		},
		function(self)
		{
			return {

				init: function()
				{
				},

				'{removeItem} click' : function(element )
				{
					var id = $(element).data('id');

					if (id != null)
					{
						// Run ajax call to delete attachment.
						disjax.loadingDialog();
						disjax.load('attachments' , 'confirmDelete' , id.toString());
					}
					else
					{
						self.element.remove();
					}
				}
			};
		});


		module.resolve();

	});

    });
