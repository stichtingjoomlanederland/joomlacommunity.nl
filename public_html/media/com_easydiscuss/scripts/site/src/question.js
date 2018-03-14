ed.define('site/src/question', ['jquery', 'site/src/ed'], function($, EasyDiscuss){

	var id = null;
	var addComment = $('.addComment');
	var container = $('.commentFormContainer');
	var comments = $('.commentsList');
	var loadmore = $('.commentLoadMore');
	var location = $('.postLocation');
	var locationData = $('.locationData');

	var post = {

		init: function() {
			// // Implement comments list.
			// self.commentsList().implement(EasyDiscuss.Controller.Comment.List);

			// // Implement comment pagination.
			// self.commentLoadMore().length > 0 && self.commentLoadMore().implement(EasyDiscuss.Controller.Comment.LoadMore, {
			// 	controller: {
			// 		list: self.commentsList().controller()
			// 	}
			// });

			// // Initialize post id.
			// self.options.id	= self.element.data('id');

			// if(self.locationData().length > 0) {
			// 	var mapOptions = $.parseJSON(self.locationData().val());
			// 	self.postLocation().implement("EasyDiscuss.Controller.Location.Map", mapOptions);
			// }

			addComment.on('click', function() {

				// // Retrieve the comment form and implement it.
				// var commentForm = self.view.commentForm({
				// 	'id'	: self.options.id
				// });

				// $(commentForm).implement(
				// 	EasyDiscuss.Controller.Comment.Form,
				// 	{
				// 		container: self.commentFormContainer(),
				// 		notification: self.commentNotification(),
				// 		commentsList: self.commentsList(),
				// 		loadMore: self.commentLoadMore(),
				// 		termsCondition: self.options.termsCondition
				// 	}
				// );

				// self.commentFormContainer().html(commentForm).toggle();
			});
		}
	}

	return post;
});
