/*!
 * jQuery RSRating! - A Star Rating Plugin
 *
 * The GPL License
 *
 * @author  : RSjoomla.com
 * @version : 1.0.0
 *
 */

(function($) {
	'use strict';

	$.fn.rsrating = function(options) {
		var initial_rating 	= options.initial ? options.initial : 0;
		var ratingElements 	= $(this).find('li a');
		var self			= $(this);
		
		ratingElements.each(function (index,el) {
			$(el).on('mouseenter', function() {
				for (var i = 0; i <= index; i++) {
					ratingElements[i].removeClass('fa-star-o').addClass('fa-star');
				}

				for (var j = index + 1; j < ratingElements.length; j++) {
					ratingElements[j].removeClass('fa-star').addClass('fa-star-o');
				}

			}).on('click', function() {
				var vote = index + 1;
				
				self.off('mouseleave');
				ratingElements.off('click').off('mouseenter');
				
				$('#rsepro_rating_loading').css('display','');
				$('#rsepro_rating_loading img').css('display','');
				
				$.ajax({
					url: options.root + 'index.php?option=com_rseventspro&task=rseventspro.rate',
					dataType: 'json',
					type: 'post',
					data: { feedback: vote, id: options.id }
				}).done(function( response ) {
					$('#rsepro_rating_loading img').css('display','none');
					if (typeof response.error != 'undefined') {
						$('#rsepro_rating_loading span').html(response.error);
						$('#rsepro_rating_loading span').animate({opacity: 0}, 3000, function() { $('#rsepro_rating_loading').css('display','none'); });
					} else {
						ratingElements.removeClass('fa-star-o').removeClass('fa-star').addClass('fa-star-o');
						
						for (var l = 0; l < response.rating; l++) {
							$(ratingElements[l]).removeClass('fa-star-o').addClass('fa-star');
						}
						
						ratingElements.off('mouseenter');
						$('#rsepro_rating_loading span').html(response.message);
						$('#rsepro_rating_loading span').animate({opacity: 0}, 3000, function() { $('#rsepro_rating_loading').css('display','none'); });
					}
				});
			});
		});

		$(this).on('mouseleave', function() {
			ratingElements.removeClass('fa-star-o').removeClass('fa-star').addClass('fa-star-o');
			
			for(var x = 0; x < initial_rating; x++) {
				ratingElements[x].removeClass('fa-star-o').addClass('fa-star');
			}
		});
	};
})(jQuery);