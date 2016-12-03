ed.require(['edq'], function($) {

	$( '.layoutList' ).bind( 'click' , function(){
		$( '.discuss-list' ).removeClass("discuss-list-grid")
	});

	$( '.layoutColumn' ).bind( 'click' , function(){
		$( '.discuss-list' ).addClass("discuss-list-grid")
	});

	// $( '.viewFavourites' ).implement( EasyDiscuss.Controller.Post.Favourites );
});
