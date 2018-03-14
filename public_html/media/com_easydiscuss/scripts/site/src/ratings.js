ed.define('site/src/ratings', ['edq', 'jquery.raty'], function($) {

    var ratings = $('[data-ed-ratings-stars]');
    var postId = ratings.data('id');

    ratings.raty({
        score: ratings.data('score'),
        readOnly: ratings.data('locked'),
        click: function(score, event) {

        	var self = $(this);
        	var form = self.parents('[data-ed-ratings]');
        	var message = form.find('[data-ed-ratings-message]');
        	var count = form.find('[data-ed-ratings-total]');

            // Disable the rating button once user click on it.
			ratings.raty({
				score: score,
				readOnly: true
			});        	
 
            EasyDiscuss.ajax('site/views/ratings/submit', {
                "score": score,
                "postId": postId
            }).done(function(value, total, messageHtml) {
 				ratings.raty({
 					score: value,
 					readOnly: true
 				});

 				message.html(messageHtml);

 				count.text(total.toString());

                form.addClass('is-voted');
            });
        }
    });
});