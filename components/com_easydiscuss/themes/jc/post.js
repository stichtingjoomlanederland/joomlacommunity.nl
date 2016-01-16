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

<?php if( JRequest::getInt( 'print' , 0 ) == 1 ){ ?>
window.print();
<?php } ?>

EasyDiscuss.view_votes = <?php echo !$system->config->get( 'main_allowguestview_whovoted' ) && !$system->my->id ? 'false' : 'true'; ?>;

EasyDiscuss
.require()
.script( 'legacy', 'likes' , 'favourites', 'attachments' , 'replies' , 'posts' )
.library( 'scrollTo' )
.done(function($){

	// Implement reply item controller.
	$( '.discussionReplies' ).implement(
		EasyDiscuss.Controller.Replies,
		{
			termsCondition : <?php echo $system->config->get( 'main_comment_tnc' ) ? 'true' : 'false'; ?>,
			sort: "<?php echo $sort; ?>"
		}
	);

	$('.discussAnswer').implement(
		EasyDiscuss.Controller.Replies,
		{
			termsCondition : <?php echo $system->config->get( 'main_comment_tnc' ) ? 'true' : 'false'; ?>,
			sort: "<?php echo $sort; ?>"
		}
	);

	// Implement loadmore reply controller if exist
	$('.replyLoadMore').length > 0 && $('.replyLoadMore').implement(
		EasyDiscuss.Controller.Replies.LoadMore,
		{
			controller: {
				list: $('.discussionReplies').controller()
			},
			id: <?php echo $post->id; ?>,
			sort: "<?php echo $sort; ?>"
		}
	);

	$( '.discussQuestion' ).implement(
		EasyDiscuss.Controller.Post.Question,
		{
			termsCondition : <?php echo $system->config->get( 'main_comment_tnc' ) ? 'true' : 'false'; ?>
		}
	);


	$( '.discuss-post-assign' ).implement( EasyDiscuss.Controller.Post.Moderator );


	$( '.discussQuestion' ).implement( EasyDiscuss.Controller.Post.CheckNewReplyComment,
		{
			interval: <?php echo $system->config->get( 'system_update_interval', 30 ); ?>
		}
	);

	$( '.discussFavourites' ).implement( EasyDiscuss.Controller.Post.Favourites );

	$(document).on('click.quote', '.quotePost', function(){

		var rawContent 	= $( this ).find( 'input' ).val(),
			rawAuthor	= $( this ).find( '.raw_author' ).val(),
			editor 		= $( 'textarea[name=dc_reply_content]' );

		editor.val( editor.val() + '[quote]' + '[b]' + rawAuthor + '<?php echo JText::_( 'COM_EASYDISCUSS_QUOTE_WROTE' )  ?>'  + ':[/b]' + '\n\r' + rawContent + '[/quote]' );

		// Scroll down to the response.
		$.scrollTo( '#respond' , 800 );

		editor.focus();
	});

});
