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
<li>
	<a name="<?php echo JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $reply->id;?>"></a>
	<div id="dc_reply_<?php echo $reply->id;?>" class="discuss-item discussReplyItem mt-10<?php echo $reply->toMinimizePost($reply->getTotalVotes()) ? ' is-minimized' : '';?>" data-id="<?php echo $reply->id;?>">

		<!-- Discussion left side bar -->
		<div class="discuss-item-left discuss-user discuss-user-role-<?php echo $reply->getOwner()->getRoleId(); ?>">

            <?php echo ED::themes()->html('user.avatar', $post->getOwner(), array('rank' => true, 'status' => true, 'size' => 'lg')); ?>

			<?php if( $system->my->id && $system->my->id != $reply->getOwner()->id && $reply->getOwner()->id != 0 ){ ?>
				<?php if( $system->config->get( 'integration_jomsocial_messaging' ) && JFile::exists( JPATH_ROOT . '/components/com_community/libraries/core.php') ){ ?>
					<?php
					require_once( JPATH_ROOT . '/components/com_community/libraries/core.php' );
					require_once( JPATH_ROOT . '/components/com_community/libraries/messaging.php' );
					CMessaging::load();
					?>
					<a href="javascript:void(0);" onclick="joms.messaging.loadComposeWindow('<?php echo $reply->getOwner()->id ;?>' );" class="btn btn-mini mt-10">
						<i class="icon-ed-pm"></i> <?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_WRITE' );?>
					</a>
				<?php } else { ?>
					<?php if( $system->config->get( 'main_conversations' ) ){ ?>
					<a href="javascript:void(0);" onclick="discuss.conversation.write('<?php echo $reply->getOwner()->id;?>' );" class="btn btn-mini mt-10" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SEND_MESSAGE_TO_USER' ); ?>" rel="ed-tooltip">
						<i class="icon-ed-pm"></i> <?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_WRITE' );?>
					</a>
					<?php } ?>
				<?php } ?>
			<?php } ?>

		</div>

		<!-- Discussion content area -->
		<div class="discuss-item-right">

			<div class="discuss-story">

				<div class="discuss-story-hd">

					<div class="discuss-action-options-1 fs-11">
						<div class="discuss-clock ml-10 pull-left">
							<i class="icon-ed-time"></i> <?php echo $this->formatDate('%A, %B %d %Y, %I:%M %p', $reply->created);?> -
							<a href="<?php echo EDR::getPostRoute( $reply->parent_id ) . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $reply->id;?>" title="<?php echo JText::_('COM_EASYDISCUSS_REPLY_PERMALINK_TO'); ?>">#<?php echo JText::_( 'COM_EASYDISCUSS_POST_PERMALINK' );?></a>
						</div>
					</div>

					<?php if( $reply->canEdit() || $reply->canFeature() || $reply->canDelete() || $reply->canResolve() || $reply->canLock() || $system->config->get( 'main_report' ) ){ ?>
					<div class="row-fluid discuss-admin-bar">

						<div class="pull-right mr-10">
							<?php if( $system->config->get( 'main_report' ) ){ ?>
								<?php if( ED::acl()->allowed( 'send_report' )){ ?>
									<a onclick="discuss.reports.add('<?php echo $reply->id;?>');" href="javascript:void(0);" class="btn btn-danger btn-mini" rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_REPORT_THIS' , true );?>">
										&nbsp;<i class="icon-warning-sign"></i>&nbsp;
									</a>
								<?php } ?>
							<?php } ?>
						</div>

						<?php if( $reply->canMove() || ($reply->canFeature() && $reply->isQuestion()) || ($reply->canLock() && $reply->isQuestion()) ){ ?>
						<div class="btn-group dropdown_ pull-right mr-5">

							<a class="btn btn-yellow btn-mini" data-foundry-toggle="dropdown">
								<i class="icon-cog"></i> <?php echo JText::_( 'COM_EASYDISCUSS_MODERATION_TOOLS' ); ?>
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<?php if( $reply->canMove() ){ ?>
								<li>
									<a href="javascript:void(0);" onclick="discuss.post.move('<?php echo $reply->id;?>')">
									<i class="icon-move"></i> <?php echo JText::_( 'COM_EASYDISCUSS_MOVE_POST' ); ?></a>
								</li>
								<li>
									<a href="javascript:void(0);" onclick="discuss.post.mergeForm('<?php echo $reply->id;?>')">
									<i class="icon-retweet"></i> <?php echo JText::_( 'COM_EASYDISCUSS_MERGE_WITH' ); ?></a>
								</li>
								<?php } ?>

								<?php if( $reply->canFeature() && $reply->isQuestion() ){ ?>
								<li>
									<a class="admin-featured" href="javascript:void(0);" onclick="discuss.post.feature('<?php echo $reply->id;?>' );" class="featurePost">
										<i class="icon-pushpin"></i> <?php echo JText::_('COM_EASYDISCUSS_ENTRY_FEATURE_THIS');?></a>
									<a class="admin-unfeatured" href="javascript:void(0);" onclick="discuss.post.unfeature('<?php echo $reply->id;?>' );" class="unfeaturePost">
										<i class="icon-pushpin"></i> <?php echo JText::_('COM_EASYDISCUSS_ENTRY_UNFEATURE_THIS');?></a>
								</li>
								<?php } ?>

								<?php if( $reply->canLock() && $reply->isQuestion() ){ ?>
								<li>
									<a class="admin-unlock" href="javascript:void(0);" class="unlockPost" onclick="discuss.post.unlock('<?php echo $reply->id; ?>');">
										<i class="icon-unlock"></i> <?php echo JText::_('COM_EASYDISCUSS_ENTRY_UNLOCK'); ?>
									</a>
									<a class="admin-lock" href="javascript:void(0);" class="lockPost" onclick="discuss.post.lock('<?php echo $reply->id; ?>');">
										<i class="icon-lock"></i> <?php echo JText::_('COM_EASYDISCUSS_ENTRY_LOCK'); ?>
									</a>
								</li>
								<?php } ?>
							</ul>
						</div>
						<?php } ?>

						<div class="pull-right mr-5">

							<?php if( $reply->canReply() ){ ?>
							<a href="javascript:void(0);" class="btn btn-mini quotePost" rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_QUOTE' , true );?>">
								&nbsp;<i class="icon-share-alt"></i>&nbsp;
								<input type="hidden" name="raw_message" value="<?php echo ED::string()->escape( $reply->content_raw );?>" />
							</a>
							<?php } ?>

							<?php if( $reply->isQuestion() ){ ?>
							<a href="<?php echo EDR::getPrintRoute( $reply->id );?>; ?>"
								onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;"
								class="btn btn-mini" rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_PRINT' , true );?>">
								&nbsp;<i class="icon-print"></i>&nbsp;
							</a>
							<?php } ?>

							<?php if( $reply->canEdit() ){ ?>
								<?php if( $reply->isQuestion() ){ ?>
									<a href="<?php echo EDR::getEditRoute( $reply->id );?>" class="btn btn-mini">
								<?php } else { ?>

									<?php if( $system->config->get( 'layout_editor' ) == 'bbcode' ){ ?>
										<a href="javascript:void(0);" class="editReplyButton btn btn-mini">
									<?php }else{ ?>
										<a href="<?php echo EDR::_( 'index.php?option=com_easydiscuss&view=post&layout=edit&id='. $reply->id ); ?>" class="btn btn-mini">
									<?php } ?>
								<?php } ?>
								<i class="icon-pencil"></i> <?php echo JText::_('COM_EASYDISCUSS_ENTRY_EDIT'); ?></a>
							<?php } ?>

							<?php if( $reply->canDelete() ){ ?>
								<?php if( $reply->isQuestion() ){ ?>
									<a href="javascript:void(0);" onclick="discuss.post.del('<?php echo $reply->id; ?>', 'post' , '<?php echo EDR::_( 'index.php?option=com_easydiscuss&view=index' );?>' );" class="btn btn-mini">
								<?php } else { ?>
									<a href="javascript:void(0);" onclick="discuss.post.del('<?php echo $reply->id; ?>', 'reply' , '<?php echo EDR::getPostRoute( $reply->id );?>' );" class="btn btn-mini">
								<?php }?>
								<i class="icon-remove"></i> <?php echo JText::_('COM_EASYDISCUSS_ENTRY_DELETE'); ?></a>
							<?php } ?>

							<?php if( $reply->canResolve() && $reply->isQuestion() ){ ?>
								<a class="admin-unresolve btn btn-mini" href="javascript:void(0);" onclick="discuss.post.unresolve('<?php echo $reply->id; ?>');">
									<i class="icon-remove-sign"></i> <?php echo JText::_('COM_EASYDISCUSS_ENTRY_MARK_UNRESOLVED'); ?></a>

								<a class="admin-resolve btn btn-mini" href="javascript:void(0);" onclick="discuss.post.resolve('<?php echo $reply->id; ?>');">
									<i class="icon-ok-sign"></i> <?php echo JText::_('COM_EASYDISCUSS_ENTRY_MARK_RESOLVED'); ?></a>
							<?php } ?>


						</div>


					</div>
					<?php } ?>


				</div>

				<div class="discuss-story-bd mb-10">

					<div class="ph-10">

						<div class="discuss-content">
							<div class="discuss-content-item">
								<?php echo DiscussHelper::bbcodeHtmlSwitcher( $reply , 'reply', false ); ?>
							</div>
						</div>

						<div class="discuss-users-action row-fluid mb-10">
            				<?php echo ED::likes()->button($reply); ?>
						</div>

						<div class="discuss-users-action row-fluid">
							<?php if( $reply->canComment() ) { ?>
							<div class="discuss-post-comment">
								<span id="comments-button-<?php echo $reply->id;?>" style="display:<?php echo $post->islock ? 'none' : '';?>">
									<a href="javascript:void(0);" class="addComment btn btn-mini"><?php echo JText::_('COM_EASYDISCUSS_COMMENT');?></a>
								</span>
							</div>
							<?php } ?>


							<?php if ($post->canAcceptAsAnswer()) { ?>
							<div class="btn-group">

								<?php if (!$post->isAnswer()) { ?>
								<a class="btn btn-default btn-xs" data-ed-post-qna data-task="confirmAccept">
									<?php echo JText::_('COM_EASYDISCUSS_REPLY_ACCEPT');?>
								</a>
								<?php } ?>

								<?php if ($post->isAnswer()) { ?>
								<a class="btn btn-default btn-xs" data-ed-post-qna data-task="confirmReject">
									<?php echo JText::_('COM_EASYDISCUSS_REPLY_REJECT');?>
								</a>
								<?php } ?>
							</div>
							<?php } ?>

							<div class="discuss-comments">
								<div class="commentNotification"></div>
								<div class="commentFormContainer" style="display:none;"></div>

								<ul class="unstyled discuss-list commentsList">
									<?php if( $reply->comments ){ ?>
										<?php foreach( $reply->comments as $comment ){ ?>

										<?php } ?>
									<?php } ?>
								</ul>

								<?php if( $system->config->get( 'main_comment_pagination' ) && isset( $reply->commentsCount ) && $reply->commentsCount > $system->config->get( 'main_comment_pagination_count' ) ) { ?>
									<a href="javascript:void(0);" class="commentLoadMore btn btn-small" data-postid="<?php echo $reply->id; ?>"><?php echo JText::_( 'COM_EASYDISCUSS_COMMENT_LOAD_MORE' ); ?></a>
								<?php } ?>

							</div>
						</div>


						<?php include dirname( __FILE__ ) . '/default.replies.item.location.php'; ?>
					</div>
				</div>

				<?php if( $system->config->get( 'main_signature_visibility' ) && !empty( $reply->getOwner()->signature ) ){ ?>
				<div class="discuss-action-options">
					<div class="discuss-signature fs-11"><?php echo DiscussHelper::bbcodeHtmlSwitcher( $reply->getOwner()->signature , 'signature', false ); ?></div>
				</div>
				<?php } ?>

				<div class="row-fluid">
					<a class="pull-right" href="#<?php echo JText::_( 'COM_EASYDISCUSS_TOP_ANCHOR' , true );?>" title="<?php echo JText::_( 'COM_EASYDISCUSS_BACK_TO_TOP' , true );?>"><i class="icon-circle-arrow-up"></i></a>
				</div>
			</div>

			<!-- @php when .discuss-story minimize show out -->
			<div id="reply_minimize_msg_5" class="discuss-reply-minimized">
				<b>The reply is currently minimized</b>
				<a href="javascript:void(0);" class="btn btn-small" onclick="discuss.reply.maximize('<?php echo $reply->id;?>');">Show</a>
			</div>

		</div>

	</div>
</li>
