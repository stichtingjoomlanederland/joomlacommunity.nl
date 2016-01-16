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
<?php echo $adsense->header; ?>
<div class="discuss-entry">
	<div id="dc_main_notifications"></div>

	<?php if( $post->isPending() ) { ?>
	<div class="alert alert-error">
		<?php echo ( $post->user_id == $system->my->id ) ? JText::_( 'COM_EASYDISCUSS_NOTICE_POST_SUBMITTED_UNDER_MODERATION' ) : JText::_( 'COM_EASYDISCUSS_POST_UNDER_MODERATE' ) ; ?>
	</div>
	<?php } ?>

	

	<?php if( $access->canLabel() && false ) { ?>
	<!-- Post assignments -->
	<div class="discuss-post-label alert alert-info">
		<?php echo $this->loadTemplate( 'post.label.php' , array( 'post' => $post ) ); ?>
	</div>
	<?php } ?>

	<?php echo DiscussHelper::renderModule( 'easydiscuss-before-question' ); ?>
	<?php echo $this->loadTemplate( 'post.question.item.php' , array( 'post' => $post ) ); ?>
	<?php echo DiscussHelper::renderModule( 'easydiscuss-after-question' ); ?>

	<!-- Display the who's online block -->
	<?php echo DiscussHelper::getWhosOnline();?>

	<?php echo DiscussHelper::renderModule( 'easydiscuss-before-answer' ); ?>

	
	
	<?php if( $answer ){ ?>
	<h3><?php echo JText::_('COM_EASYDISCUSS_ENTRY_ACCEPTED_ANSWER'); ?></h3>
	<div class="well forum-answer">
		<a name="answer"></a>
		<ul class="forum-replies">
		<?php echo $this->loadTemplate( 'post.reply.item.php' , array( 'question' => $post, 'post' => $answer ) ); ?>
		</ul>
	</div>
	<?php } ?>

	<?php echo DiscussHelper::renderModule( 'easydiscuss-after-answer' ); ?>

	<?php echo $adsense->beforereplies; ?>
	

	
	<?php if( !$post->isProtected() || DiscussHelper::isModerator( $post->category_id ) ){ ?>
		<div class="forum-replies">
			<a name="replies"></a>
			<?php if( $category->canViewReplies() ){ ?>
				<div class="forum-filter">
					<h3 class="pull-left"><?php echo $totalReplies;?> <?php echo JText::_('COM_EASYDISCUSS_ENTRY_RESPONSES'); ?></h3>
					
					<a name="filter-sort"></a>
					<div class="btn-group pull-right">

						<?php if( $system->config->get( 'main_likes_replies') ){ ?>
						<a class="btn btn-default <?php echo ( $sort == 'likes') ? 'active' : '';?>" href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id . '&sort=likes'); ?>#filter-sort">
							<?php echo JText::_('COM_EASYDISCUSS_SORT_LIKED_MOST'); ?>
						</a>
						<?php } ?>

						<?php if( $system->config->get( 'main_allowvote') ){ ?>
						<a class="btn btn-default <?php echo ( $sort == 'voted') ? 'active' : '';?>" href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id . '&sort=voted'); ?>#filter-sort">
							<?php echo JText::_('COM_EASYDISCUSS_SORT_HIGHEST_VOTE'); ?>
						</a>
						<?php } ?>

						<a class="btn btn-default <?php echo ( $sort == 'latest') ? 'active' : '';?>" href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id . '&sort=latest'); ?>#filter-sort">
							<?php echo JText::_('COM_EASYDISCUSS_SORT_LATEST'); ?>
						</a>
						
						<a class="btn btn-default <?php echo ( $sort == 'oldest' || $sort == 'replylatest') ? 'active' : '';?>" href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id . '&sort=oldest'); ?>#filter-sort">
							<?php echo JText::_('COM_EASYDISCUSS_SORT_OLDEST'); ?>
						</a>
					</div>
				</div>

				<?php echo DiscussHelper::renderModule( 'easydiscuss-before-replies' ); ?>
				<div class="well">
				<ul class="forum-replies discussionReplies">
				<?php if( $replies ){ ?>
					<?php foreach( $replies as $reply ){ ?>
						<?php echo $this->loadTemplate( 'post.reply.item.php' , array( 'question' => $post, 'post' => $reply ) ); ?>
					<?php } ?>
				<?php } else { ?>
					<li class="empty">
						<div class="discuss-empty">
							<?php echo JText::_( 'COM_EASYDISCUSS_NO_REPLIES_YET' );?>
						</div>
					</li>
				<?php } ?>
				</ul>
				</div>
				<?php echo DiscussHelper::renderModule( 'easydiscuss-after-replies' ); ?>

				<?php if( $hasMoreReplies ) { ?>
				<div>
					<span>
						<a href="<?php echo $readMoreURI; ?>">
						<?php if( $system->config->get( 'layout_replies_pagination' ) ) { ?>
							<a class="replyLoadMore btn btn-block btn-primary" href="javascript:void(0);"><?php echo JText::_( 'COM_EASYDISCUSS_REPLY_LOAD_MORE' ); ?></a>
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
