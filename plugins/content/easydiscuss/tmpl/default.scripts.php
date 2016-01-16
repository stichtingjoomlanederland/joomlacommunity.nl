<?php
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
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">

EasyDiscuss.view_votes = <?php echo !$system->config->get( 'main_allowguestview_whovoted' ) && !$system->my->id ? 'false' : 'true'; ?>;

<?php if( $system->config->get( 'main_syntax_highlighter') ){ ?>
// Find any response that contains a code syntax.
EasyDiscuss.main_syntax_highlighter = true;
EasyDiscuss
	.require()
	.script('syntaxhighlighter' , 'likes' )
	.done(function($) {
		$('.discuss-content-item pre').each(function(i, e) {
		hljs.highlightBlock(e);
	});
});

<?php } ?>

EasyDiscuss
.require()
.script( 'likes' , 'favourites', 'attachments' , 'replies' , 'posts' )
.library( 'scrollTo' )
.done(function($){

	// Implement likes controller
	$( '.attachmentsItem' ).implement(
		EasyDiscuss.Controller.Attachments.Item,
		{
		}
	);

	// Implement reply item controller.
	$( '.discussionReplies' ).implement(
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
			editor 		= $( 'textarea[name=dc_reply_content]' );

		editor.val( editor.val() + '[quote]' + rawContent + '[/quote]' );

		// Scroll down to the response.
		$.scrollTo( '#respond' , 800 );

		editor.focus();
	});

});

</script>
