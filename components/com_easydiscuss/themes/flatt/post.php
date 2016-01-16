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

		var rawContent 	= $( this ).find( '.raw_message' ).val(),
			rawAuthor	= $( this ).find( '.raw_author' ).val(),
			editor 		= $( 'textarea[name=dc_reply_content]' );

		editor.val( editor.val() + '[quote]' + '[b]' + rawAuthor + '<?php echo JText::_( 'COM_EASYDISCUSS_QUOTE_WROTE' )  ?>'  + ':[/b]' + '\n\r' + rawContent + '[/quote]' );

		// Scroll down to the response.
		$.scrollTo( '#respond' , 800 );

		editor.focus();
	});

});

</script>

<?php echo $adsense->header; ?>
<div class="discuss-entry">

	<div id="dc_main_notifications"></div>

	<?php if( $post->isPending() ) { ?>
	<div class="alert alert-error">
		<?php echo ( $post->user_id == $system->my->id ) ? JText::_( 'COM_EASYDISCUSS_NOTICE_POST_SUBMITTED_UNDER_MODERATION' ) : JText::_( 'COM_EASYDISCUSS_POST_UNDER_MODERATE' ) ; ?>
	</div>
	<?php } ?>

	<div class="row-fluid">
		<div class="pull-right mb-15">
			<a href="#respond" class="btn btn-mini btn-success"><i class="icon-plus-sign"></i> <?php echo JText::_( 'COM_EASYDISCUSS_ADD_A_REPLY' );?></a>
			<a href="#replies" class="btn btn-mini btn-info"><?php echo JText::_( 'COM_EASYDISCUSS_VIEW_REPLIES' );?> (<?php echo count($replies);?>) &rarr;</a>
		</div>
	</div>

	<div class="discuss-action-bar <?php echo $post->featured? 'is-featured' : '' ?> <?php echo $post->isresolve ? ' is-resolved' : '';?> <?php echo $post->isPollLocked() ? ' is-poll-lock' : '';?> <?php echo $post->islock ? ' is-locked' : '';?>">
		<div class="row-fluid">
			<div class="span5">
				<?php if( $access->canAssign() ) { ?>
					<!-- Post assignments -->
					<div class="discuss-post-assign" data-id="<?php echo $post->id; ?>" data-category="<?php echo $post->category_id; ?>">
						<?php echo $this->loadTemplate( 'post.assignment.php' , array( 'post' => $post, $moderators ) ); ?>
					</div>
				<?php } ?>

			</div>
			<div class="span7">
				<?php echo $this->loadTemplate( 'post.actions.php' , array( 'access' => $access , 'post' => $post ) ); ?>
			</div>
		</div>
	</div>

	<?php if( $access->canLabel() && false ) { ?>
		<!-- Post assignments -->
		<div class="discuss-post-label alert alert-info">
			<?php echo $this->loadTemplate( 'post.label.php' , array( 'post' => $post ) ); ?>
		</div>
	<?php } ?>

	<?php echo DiscussHelper::renderModule( 'easydiscuss-before-question' ); ?>
	<?php echo $this->loadTemplate( 'post.question.item.php' , array( 'post' => $post ) ); ?>
	<?php echo DiscussHelper::renderModule( 'easydiscuss-after-question' ); ?>

	<?php echo DiscussHelper::renderModule( 'easydiscuss-before-answer' ); ?>
	<?php if( $answer ){ ?>
	<div class="discuss-answer discussAnswer">
		<a name="answer"></a>
		<?php echo $this->loadTemplate( 'post.reply.item.php' , array( 'question' => $post, 'post' => $answer ) ); ?>
	</div>
	<?php } ?>
	<?php echo DiscussHelper::renderModule( 'easydiscuss-after-answer' ); ?>

	<?php echo $adsense->beforereplies; ?>
	<?php if( !$post->isProtected() || DiscussHelper::isModerator( $post->category_id ) ){ ?>
		<div class="discuss-replies">

			<a name="replies"></a>
			<div class="discuss-component-title"><?php echo JText::_('COM_EASYDISCUSS_ENTRY_RESPONSES'); ?> (<span class="replyCount"><?php echo $totalReplies;?></span>)</div>

			<?php if( $category->canViewReplies() ){ ?>
				<!-- @php post filter nav -->
				<div class="discuss-filter">
					<a name="filter-sort"></a>
					<ul class="nav nav-tabs">

						<?php if( $system->config->get( 'main_likes_replies') ){ ?>
						<li class="<?php echo ( $sort == 'likes') ? 'active' : '';?> sortItem secondary-nav">
							<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id . '&sort=likes'); ?>#filter-sort">
								<?php echo JText::_('COM_EASYDISCUSS_SORT_LIKED_MOST'); ?>
							</a>
						</li>
						<?php } ?>

						<?php if( $system->config->get( 'main_allowvote') ){ ?>
						<li class="<?php echo ( $sort == 'voted') ? 'active' : '';?> sortItem secondary-nav">
							<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id . '&sort=voted'); ?>#filter-sort">
								<?php echo JText::_('COM_EASYDISCUSS_SORT_HIGHEST_VOTE'); ?>
							</a>
						</li>
						<?php } ?>

						<li class="<?php echo ( $sort == 'latest') ? 'active' : '';?> sortItem secondary-nav">
							<a class="btn-small" href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id . '&sort=latest'); ?>#filter-sort">
								<?php echo JText::_('COM_EASYDISCUSS_SORT_LATEST'); ?>
							</a>
						</li>

						<li class="<?php echo ( $sort == 'oldest' || $sort == 'replylatest') ? 'active' : '';?> sortItem secondary-nav">
							<a class="btn-small" href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id . '&sort=oldest'); ?>#filter-sort">
								<?php echo JText::_('COM_EASYDISCUSS_SORT_OLDEST'); ?>
							</a>
						</li>
					</ul>
				</div>

				<?php echo DiscussHelper::renderModule( 'easydiscuss-before-replies' ); ?>
				<ul class="unstyled discuss-list clearfix discussionReplies">
				<?php if( $replies ){ ?>
					<?php foreach( $replies as $reply ){ ?>
					<li>
						<?php echo $this->loadTemplate( 'post.reply.item.php' , array( 'question' => $post, 'post' => $reply ) ); ?>
					</li>
					<?php } ?>
				<?php } else { ?>
					<li class="empty">
						<?php echo JText::_( 'COM_EASYDISCUSS_NO_REPLIES_YET' );?>
					</li>
				<?php } ?>
				</ul>
				<?php echo DiscussHelper::renderModule( 'easydiscuss-after-replies' ); ?>

				<?php if( $hasMoreReplies ) { ?>
				<div>
					<span>
						<a href="<?php echo $readMoreURI; ?>">
						<?php if( $system->config->get( 'layout_replies_pagination' ) ) { ?>
							<a class="replyLoadMore btn btn-small" href="javascript:void(0);"><?php echo JText::_( 'COM_EASYDISCUSS_REPLY_LOAD_MORE' ); ?></a>
						<?php } else { ?>
							<a href="<?php echo $readMoreURI; ?>"><?php echo JText::sprintf('COM_EASYDISCUSS_READ_ALL_REPLIES', $totalReplies); ?></a>
						<?php } ?>
						</a>
					</span>
				</div>
				<?php } ?>

			<?php } else { ?>
				<div class="alert alert-notice mb-10">
					<i class="icon-lock"></i> <?php echo JText::_( 'COM_EASYDISCUSS_UNABLE_TO_VIEW_REPLIES' ); ?>
				</div>
			<?php } ?>
		</div>

		<?php echo DiscussHelper::renderModule( 'easydiscuss-before-replyform' ); ?>
		<?php echo $this->loadTemplate( 'post.reply.form.php' ); ?>
		<?php echo DiscussHelper::renderModule( 'easydiscuss-after-replyform' ); ?>

	<?php } ?>


</div>
<?php echo $adsense->footer; ?>
