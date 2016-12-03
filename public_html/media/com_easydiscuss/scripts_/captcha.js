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
