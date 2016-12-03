EasyDiscuss.module('likes', function($) {
	var module = this;

	EasyDiscuss
	.require()
	.language(
		'COM_EASYDISCUSS_UNLIKE_THIS_POST',
		'COM_EASYDISCUSS_LIKE_THIS_POST',
		'COM_EASYDISCUSS_UNLIKE',
		'COM_EASYDISCUSS_LIKES'
	)
	.done(function($) {

		EasyDiscuss.Controller('Likes' ,
		{
			defaultOptions:
			{
				postId: null,
				registeredUser: null,

				// Action buttons
				'{likeButton}'	: '.btnLike',
				'{unlikeButton}'	: '.btnUnlike',
				'{likeText}'	: '.likeText',
				'{likeCount}'	: '.likeCount',
				'{likeStatus}'	: '.likeStatus'
			}
		},
		function(self) {
			return {

				init: function()
				{
					self.options.postId = self.element.data('postid');

					self.element.data('like', true);

					// Add a loading class.
					// self.likeText().addClass( 'loadingBar' );

					// Set the like data.
					// self.getLikesData();
				},

				getLikesData: function()
				{
					EasyDiscuss.ajax('site.views.likes.getData' ,
					{
						'id'	: self.options.postId
					})
					.done(function(result ) {
						self.likeText().html(result);
					});
				},

				likeItem: function()
				{
					if (!self.options.registeredUser)
					{
						return false;
					}

					EasyDiscuss.ajax('site.views.likes.like' ,
					{
						'postid' : self.options.postId
					})
					.done(function( text, count )
					{
						self.likeText().html( text );
						self.likeCount().html( count );
					});
				},

				'{likeButton} click' : function(element )
				{
					// If user is not logged in, do not allow them to click this.
					var btnLike = self.likeButton();

					btnLike.addClass('btnUnlike');
					btnLike.attr('data-original-title', $.language('COM_EASYDISCUSS_UNLIKE_THIS_POST'));
					btnLike.find('i')
						.removeClass('icon-ed-love')
						.addClass('icon-ed-remove');
					self.likeStatus().html($.language('COM_EASYDISCUSS_UNLIKE'));
					btnLike.removeClass('btnLike');
					self.likeItem(element);
				},

				'{unlikeButton} click' : function(element )
				{
					var btnUnlike = self.unlikeButton();

					btnUnlike.addClass('btnLike');
					btnUnlike.attr('data-original-title', $.language('COM_EASYDISCUSS_LIKE_THIS_POST'));
					btnUnlike.find('i')
						.removeClass('icon-ed-remove')
						.addClass('icon-ed-love');
					self.likeStatus().html($.language('COM_EASYDISCUSS_LIKES'));
					btnUnlike.removeClass('btnUnlike');
					self.likeItem();
				},

				'{unlikeButton} mouseover' : function(element )
				{
					// $(element).find('i')
					// 	.removeClass('icon-ed-love')
					// 	.addClass('icon-ed-remove');
				},
				'{unlikeButton} mouseout' : function(element )
				{
					// $(element).find('i')
					// 	.removeClass('icon-ed-remove')
					// 	.addClass('icon-ed-love');
				}
			};
		});
	});
	$(document).on('mouseover.discussLikes', '.discussLikes', function() {

		var e = $(this);

		if (e.data('like') == undefined) {
			var registeredUser = e.attr('data-registered-user') === 'true';

			e.implement(
				EasyDiscuss.Controller.Likes,
				{
					registeredUser: registeredUser
				}
			);
		}
	});

	module.resolve();
});
