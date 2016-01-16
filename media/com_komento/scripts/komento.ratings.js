Komento.module('komento.ratings', function($) {
var module = this;

Komento.require()
	.library('ui/stars')
	.done(function() {

		Komento.Controller(
			'Ratings.Item',
			{
				defaults: {
					"{input}"	: "[data-rating-value]"
				}
			},
			function(self)
			{
				return {

					init: function() {
						self.element.stars(
						{
							split			: 2,
							disabled		: true,
							showTitles		: true
						});
					},
				}
			}
		);

		Komento.Controller(
			'Ratings.Form',
			{
				defaults: {
					"{input}"	: "[data-rating-value]",
					'{cancel}'	: '.ui-stars-cancel'
				}
			},
			function(self)
			{
				return {

					init: function() {

						self.element.stars(
						{
							split			: 2,
							disabled		: false,
							callback: function(element, className, value) {
								self.input().val(value);
							}
						});
					},

					'{self} clear': function() {
						self.cancel().trigger('click.stars');
					}
				}
			}
		);

		module.resolve();
	});
});
