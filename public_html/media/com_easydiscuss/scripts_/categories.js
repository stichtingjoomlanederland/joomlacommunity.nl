EasyDiscuss.module('categories', function($) {
	var module = this;

	EasyDiscuss
	.require()
	.done(function($) {

		EasyDiscuss.Controller('Toggle.Categories' ,
		{
			defaultOptions:
			{
				postId: null,
				// Action buttons
				'{showChild}'	: '.showChild',
				'{hideChild}'	: '.hideChild',

				'{item}': '[data-item]'
			}
		},
		function(self) {
			return {

				init: function()
				{

				},

				'{showChild} click' : function( element )
				{
					var id = element.data('id');

					self.openChild( id );

					element.addClass( 'icon-sort-up hideChild' );
					element.removeClass( 'icon-sort-down' );
					element.removeClass( 'showChild' );
				},

				openChild: function( parentid )
				{
					var childs = self.item('[data-parent-id="' + parentid + '"]');

					childs.show();

					$.each(childs, function(i, child) {
						var childid = $(child).data('id');

						//self.openChild( childid );
					});
				},

				'{hideChild} click' : function( element )
				{
					var id = element.data('id');

					self.closeChild( id );

					element.addClass( 'icon-sort-down showChild' );
					element.removeClass( 'icon-sort-up' );
					element.removeClass( 'hideChild' );
				},

				closeChild: function( parentid )
				{
					var childs = self.item('[data-parent-id="' + parentid + '"]');

					childs.hide();

					$.each(childs, function(i, child) {
						var childid = $(child).data('id');

						self.closeChild( childid );
					});
				}
			};
		});
		module.resolve();
	});
});
