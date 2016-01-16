
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

EasyDiscuss
.require()
.library( 'markitup' )
.script( 'posts' , 'legacy' , 'bbcode' )
.done(function($){

	$('.discuss-form').implement( EasyDiscuss.Controller.Post.Ask );

	discuss.composer.init("<?php echo $composer->selector; ?>");

	discuss.getContent = function(){
		<?php if( $system->config->get( 'layout_editor' ) == 'bbcode' ) { ?>
			return $( '#dc_reply_content' ).val();
		<?php } else { ?>
		<?php echo 'return ' . JFactory::getEditor( $system->config->get( 'layout_editor' ) )->getContent( 'dc_reply_content' ); ?>
		<?php } ?>
	};

	<?php if( $system->config->get( 'main_similartopic' ) ) { ?>

	var textField = $('input#ez-title');
	var queryJob = null;
	var menuLock = false;
	textField.keydown(function( e )
	{
		var keynum; // set the variable that will hold the number of the key that has been pressed.

		//now, set keynum = the keystroke that we determined just happened...
		if(window.event)// (IE)
		{
			keynum = e.keyCode;
		}
		else if(e.which) // (other browsers)
		{
			keynum = e.which;
		}
		else
		{ // something funky is happening and no keycode can be determined...
			keynum = 0;
		}

		if( keynum == 9 || keynum == 27)
		{
			$('#dc_similar-questions').hide();
			return;
		}

		clearTimeout(queryJob);

		// Start this job after 1 second
		queryJob = setTimeout(function()
		{

			if( textField.val().length <= 3 )
				return;

			//show loading icon
			$('#dc-search-loader').show();

			var params	= { query: textField.val() };

			params[ $( '.easydiscuss-token' ).val() ]	= 1;

			EasyDiscuss.ajax('site.views.post.similarQuestion', params ,
			function(data){
				//hide loading icon
				$('#dc-search-loader').hide();
				if( data != '' )
				{
					// Do whatever you like with the data returned from server.
					$('#dc_similar-questions').html(data);
					$('#dc_similar-questions').show();

					$('#similar-question-close').click( function()
					{
						$('#dc_similar-questions').hide();
						return;
					});
				}
			});
		}, 1500);
	});

	$('#dc_similar-questions').bind('mousemove click', function(){
		textField.focus();
		menuLock = true;
	})
	.mouseout(function(){
		menuLock = false;
	});

	textField.blur( function()
	{
		if (menuLock) return;

		$('#dc_similar-questions').hide();
		return;
	});

	<?php } ?>
});
