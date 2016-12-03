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
