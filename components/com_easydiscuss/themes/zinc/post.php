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

	
	<div class="clearfix">
		<?php if( $access->canAssign() ) { ?>
		<!-- Post assignments -->
		<div class="discuss-post-assign float-l" data-id="<?php echo $post->id; ?>" data-category="<?php echo $post->category_id; ?>">
			<?php echo $this->loadTemplate( 'post.assignment.php' , array( 'post' => $post , 'moderators' => $moderators ) ); ?>
		</div>
		<?php } ?>

		<?php echo $this->loadTemplate( 'post.actions.php' , array( 'access' => $access , 'post' => $post ) ); ?>
	</div>

	<?php if( $access->canLabel() && false ) { ?>
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
			<?php if( $category->canViewReplies() ){ ?>
				<div class="discuss-filter clearfix">
					<b class="butt" style="padding-left: 0; padding-right: 0;"><?php echo JText::_('COM_EASYDISCUSS_ENTRY_RESPONSES'); ?> (<span class="replyCount"><?php echo $totalReplies;?></span>)</b>
					<a name="filter-sort"></a>
					<ul class="discuss-replies-filter reset-ul float-li float-r">

						<?php if( $system->config->get( 'main_likes_replies') ){ ?>
						<li class="<?php echo ( $sort == 'likes') ? 'active' : '';?> sortItem secondary-nav">
							<a class="butt butt-default" href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id . '&sort=likes'); ?>#filter-sort">
								<?php echo JText::_('COM_EASYDISCUSS_SORT_LIKED_MOST'); ?>
							</a>
						</li>
						<?php } ?>

						<?php if( $system->config->get( 'main_allowvote') ){ ?>
						<li class="<?php echo ( $sort == 'voted') ? 'active' : '';?> sortItem secondary-nav">
							<a class="butt butt-default" href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id . '&sort=voted'); ?>#filter-sort">
								<?php echo JText::_('COM_EASYDISCUSS_SORT_HIGHEST_VOTE'); ?>
							</a>
						</li>
						<?php } ?>

						<li class="<?php echo ( $sort == 'latest') ? 'active' : '';?> sortItem secondary-nav">
							<a class="butt butt-default" href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id . '&sort=latest'); ?>#filter-sort">
								<?php echo JText::_('COM_EASYDISCUSS_SORT_LATEST'); ?>
							</a>
						</li>

						<li class="<?php echo ( $sort == 'oldest' || $sort == 'replylatest') ? 'active' : '';?> sortItem secondary-nav">
							<a class="butt butt-default" href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id . '&sort=oldest'); ?>#filter-sort">
								<?php echo JText::_('COM_EASYDISCUSS_SORT_OLDEST'); ?>
							</a>
						</li>
					</ul>
				</div>

				<?php echo DiscussHelper::renderModule( 'easydiscuss-before-replies' ); ?>
				<ul class="discuss-list reset-ul discussionReplies">
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
