EasyDiscuss.module('favourites', function($) {
	var module = this;

	EasyDiscuss
	.require()
	.language(
		'COM_EASYDISCUSS_FAVOURITE_BUTTON_UNFAVOURITE',
		'COM_EASYDISCUSS_FAVOURITE_BUTTON_FAVOURITE'
	)
	.done(function($) {

		EasyDiscuss.Controller('Post.Favourites' ,
		{
			defaultOptions:
			{
				postId: null,
				// Action buttons
				'{favButton}'	: '.btnFav',
				'{removeButton}'	: '.btnRemove',
				'{favLoader}'	: '.favLoader',
				'{favCount}'	: '.favCount'
			}
		},
		function(self) {
			return {

				init: function()
				{
					self.options.postId = self.element.data('postid');
				},

				'{favButton} click' : function(element )
				{
					//element.addClass();

					EasyDiscuss.ajax('site.views.favourites.favourite' ,
					{
						'postid' : self.options.postId
					},
					{
						beforeSend: function()
						{
							$('.favLoader').show();
							$('.favCount').empty();
						}
					})
					.done(function(result, count )
					{
						// True if just added favourite
						if (result)
						{
							element
								.addClass('isfav');

							//$(element).attr('data-original-title', $.language('COM_EASYDISCUSS_FAVOURITE_BUTTON_UNFAVOURITE'));
							$(element).html($.language('COM_EASYDISCUSS_FAVOURITE_BUTTON_UNFAVOURITE'));
							$('.favCount').html(count);
						}
						else
						{
							element
								.removeClass('isfav btn-primary');

							//$(element).attr('data-original-title', $.language('COM_EASYDISCUSS_FAVOURITE_BUTTON_FAVOURITE'));
							$(element).html($.language('COM_EASYDISCUSS_FAVOURITE_BUTTON_FAVOURITE'));
							$('.favCount').html(count);
						}
					})
					.fail(function(message )
					{
						// show error message

					})
					.always(function() {
						//remove the loading here
						$('.favLoader').hide();
					});
				},

				'{removeButton} click' : function(element )
				{
					EasyDiscuss.ajax('site.views.favourites.remove' ,
					{
						'postid' : self.options.postId
					})
					.done(function()
					{
						element
							.removeClass('isfav');

						$('.discussItem' + self.options.postId).remove();

					})
					.fail(function(message )
					{
						// show error message
					});
				}
			};
		});
		module.resolve();
	});
});
