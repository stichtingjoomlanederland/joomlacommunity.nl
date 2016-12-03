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
