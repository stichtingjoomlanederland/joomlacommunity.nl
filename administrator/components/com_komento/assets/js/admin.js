Komento.ready(function($){
	$(document).ready( function() {

		// insert span tag into submenu item for more flexibility
		$('#submenu li a').each( function() {
			$(this).wrapInner('<span></span>');
		});


		// move system message
		if ( $('#system-message').length > 0 )
		{
			var message = $('#system-message').html();

			$('#system-message').remove();

			$( '<dl id="system-message">' + message + '</dl>' ).insertAfter('#toolbar-box');
		}

		$('body #adminForm .admintable tr:odd').addClass('tr-odd');

		$('.admintable tr').hover( function(){
			$(this).addClass('tr-hover');
		},
		function() {
			$(this).removeClass('tr-hover');
		});

		// move version notice to header
		$('#newpost').appendTo('.icon-48-home').show();
		$('.icon-48-home').css({ position: 'relative' });


		$('.icon-item').click( function(event) {
			window.location.href = $('a', this).attr('href');
		});

		$('.icon-item').hover( function(event) {
			$(this).addClass('hover');
		}, function() {
			$(this).removeClass('hover');
		});

		admin.checkbox.init();
	});


	var admin = window.admin = {
		checkbox: {
			init: function(){
				// Transform checkboxes.
				$( '.option-enable' ).click( function(){
					var parent = $(this).parent();
					$( '.option-disable' , parent ).removeClass( 'selected' );
					$( this ).addClass( 'selected' );
					$( '.radiobox' , parent ).attr( 'value' , 1 );
				});

				$( '.option-disable' ).click( function(){
					var parent = $(this).parent();
					$( '.option-enable' , parent ).removeClass( 'selected' );
					$( this ).addClass( 'selected' );
					$( '.radiobox' , parent ).attr( 'value' , 0 );
				});
			}
		}
	}

});
