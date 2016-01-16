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
