EasyDiscuss.module('layout/responsive', function($) {

	var module = this;
		$(function(){
			$('#discuss-wrapper')
				.responsive([
					{at: 818,  switchTo: 'w768'},
					{at: 600,  switchTo: 'w768 w600'},
					{at: 500,  switchTo: 'w768 w600 w320'}
				]);

			$('.discuss-searchbar').responsive({at: 600, switchTo: 'narrow'});

		});

	module.resolve();

});
