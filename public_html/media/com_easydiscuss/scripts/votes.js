EasyDiscuss.module('votes', function($) {
	var module = this;

	EasyDiscuss
	.require()
	.done(function($) {

		EasyDiscuss.Controller('Votes' ,
		{
			defaultOptions:
			{
				postId: null,

				// Action buttons
				'{voteUp}'	: '.voteUp',
				'{voteDown}'	: '.voteDown',
				'{votePoints}'	: '.votePoints b',
				'{voteText}'	: '.voteText'
			}
		},
		function(self) {
			return {

				init: function()
				{
					self.options.postId = self.element.data('postid');
				},

				vote: function(type )
				{
					EasyDiscuss.ajax('site.views.votes.add' ,
					{
						'id'	: self.options.postId,
						'type'	: type
					})
					.done(function(totalVotes , voteText ) {

						// Update the vote count.
						self.votePoints().html(totalVotes);

					})
					.fail(function(message ) {
						console.log(message);
					});
				},

				/**
				 * Show voters that has voted in this post.
				 */
				'{votePoints} click' : function()
				{
					if (self.options.viewVotes)
					{
						disjax.loadingDialog();
						disjax.load('Votes' , 'showVoters', self.options.postId.toString());
					}
				},

				'{voteUp} click' : function()
				{
					self.vote('up');
				},

				'{voteDown} click' : function()
				{
					self.vote('down');
				}
			};
		});
		module.resolve();
	});
});
