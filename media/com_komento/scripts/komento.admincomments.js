Komento.module('komento.admincomments', function($) {
	var module = this;

	Komento.require()
		.library(
			'dialog'
		)
		.done(function() {
			Komento.Controller('AdminComments',
			{
				defaults: {

				}
			},
			function(self)
			{ return {
				init: function()
				{

				}
			} });

			module.resolve();
		});
});
