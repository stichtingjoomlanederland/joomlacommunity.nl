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

// Have to place here due to ajax reply post does not pass by view.html.php
$replyBadges = $post->user->getBadges();
?>
<li id="dc_reply_<?php echo $post->id;?>" class="discuss-item discussReplyItem<?php echo $post->islock ? ' is-locked' : '';?><?php echo $post->minimize ? ' is-minimized' : '';?><?php echo $post->isPollLocked() ? ' is-poll-lock' : '';?>" data-id="<?php echo $post->id;?>">
	<a name="<?php echo JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $post->id;?>"></a>
	<div>

		<div class="discuss-reply">
			<div class="row-table">
				<div class="col-cell feed-head">
					<?php if ($system->config->get( 'layout_avatar' ) && $system->config->get( 'layout_avatar_in_post' )) { ?>
					<div class="discuss-avatar">
						<a href="<?php echo $post->getOwner()->link;?>">
							<img src="<?php echo $post->getOwner()->avatar;?>" alt="<?php echo $this->escape( $post->getOwner()->name );?>"<?php echo DiscussHelper::getHelper( 'EasySocial' )->getPopbox( $post->getOwner()->id );?> width="70" height="70"class="avatar" />
						</a>
					</div>
					<?php } ?>
					<?php if( !empty( $post->user_id ) ) { ?>
						<?php echo $this->loadTemplate( 'ranks.php' , array( 'userId' => $post->getOwner()->id ) ); ?>
					<?php } ?>
				</div>
				<div class="col-cell feed-body">
					<header>
						<div class="pull-right hide">
							<?php echo $this->loadTemplate( 'post.actions.php' , array( 'access' => $post->access , 'post' => $post ) ); ?>
						</div>
						<b>
							<?php if( !$post->user_id ){ ?>
								<?php echo $post->poster_name; ?>
							<?php } else { ?>
								<?php echo $post->getOwner()->name; ?>
							<?php } ?>
						</b>
						
						<?php echo $this->loadTemplate( 'online.php' , array( 'user' => $post->user ) ); ?>

						<?php if($system->config->get( 'layout_profile_roles' ) && $post->user->getRole() ) { ?>
						<span class="discuss-role<?php echo ' ' . $post->user->getRoleLabelClassname(); ?>"><?php echo $this->escape($post->user->getRole()); ?></span>
						<?php } ?>
						<div>
							<a href="<?php echo DiscussRouter::getPostRoute( $post->parent_id ) . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $post->id;?>" title="<?php echo JText::_('COM_EASYDISCUSS_REPLY_PERMALINK_TO'); ?>" class="muted" style="text-decoration: none;">
								<time datetime="<?php echo $this->formatDate( $system->config->get('layout_dateformat', '%A, %B %d %Y, %I:%M %p') , $post->created);?>">
									<?php echo $this->formatDate( $system->config->get('layout_dateformat', '%A, %B %d %Y, %I:%M %p') , $post->created);?>
								</time>
							</a>
						</div>
					</header>

					<article>
						<div class="discuss-editor">
						</div>

						<div class="discuss-content">
							<?php echo $post->content;?>
							<?php echo $this->loadTemplate( 'post.signature.php' , array( 'signature' => $post->getOwner()->signature ) ); ?>

							<?php echo $this->getFieldHTML( true , $post ); ?>
							<?php echo $this->loadTemplate( 'post.customfields.php', array( 'post' => $post ) ); ?>
							<?php echo $this->loadTemplate( 'post.location.php' , array( 'post' => $post ) ); ?>
						</div>

					</article>

					<footer>
						<?php echo $this->loadTemplate( 'post.qna.php' , array( 'reply' => $post, 'question' => $question ) ); ?>
						<?php echo $this->loadTemplate( 'post.comments.php' , array( 'reply' => $post, 'question' => $question  ) ); ?>
						<?php echo $this->loadTemplate( 'post.likes.php' , array( 'post' => $post ) ); ?>
						<?php echo $this->loadTemplate( 'post.reply.comments.php' , array( 'post' => $post ) ); ?>
						<!-- UNFINISHED TASK! -->
						<?php echo $this->loadTemplate( 'post.report.php' , array( 'post' => $post ) ); ?>
					</footer>
				</div>
				<?php if( $system->config->get( 'main_allowvote' ) ){ ?>
				<div class="col-cell feed-vote">
					<?php echo $this->loadTemplate( 'post.vote.php' , array( 'access' => $post->access , 'post' => $post ) ); ?>
				</div>
				<?php } ?>
			</div>
		</div>

		<div class="discuss-reply-minimized" id="reply_minimize_msg_5" >
			<b><?php echo JText::_( 'COM_EASYDISCUSS_REPLY_CURRENTLY_MINIMIZED');?></b>
			<a href="javascript:void(0);" class="butt butt-default butt-s" onclick="discuss.reply.maximize('<?php echo $post->id;?>');"><?php echo JText::_( 'COM_EASYDISCUSS_SHOW' );?></a>
		</div>
	</div>
</li>
