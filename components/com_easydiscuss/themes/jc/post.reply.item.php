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
require_once DISCUSS_HELPERS . '/jc.php';

// Have to place here due to ajax reply post does not pass by view.html.php
$replyBadges = $post->user->getBadges();
?>
<li>
	<a name="<?php echo JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $post->id;?>"></a>
	<div id="dc_reply_<?php echo $post->id;?>" class="reply discussReplyItem<?php echo $post->islock ? ' is-locked' : '';?><?php echo $post->minimize ? ' is-minimized' : '';?><?php echo $post->isPollLocked() ? ' is-poll-lock' : '';?>" data-id="<?php echo $post->id;?>">

		<div class="reply-maximized">
			
				<?php if ($system->config->get( 'layout_avatar' ) && $system->config->get( 'layout_avatar_in_post' )) { ?>
				
<?php
$layout	= new JLayoutFile('avatar');
echo $layout->render($post->getOwner()->id);
?>
				<?php } ?>
				<?php if( !empty( $post->user_id ) ) { ?>
					<?php echo $this->loadTemplate( 'ranks.php' , array( 'userId' => $post->getOwner()->id ) ); ?>
				<?php } ?>
		

			<div class="topic-info">
				<?php if( $system->config->get( 'main_allowvote' ) ){ ?>
					<?php echo $this->loadTemplate( 'post.vote.php' , array( 'access' => $post->access , 'post' => $post ) ); ?>
				<?php } ?>

				<header>
					<time datetime="<?php echo $this->formatDate( $system->config->get('layout_dateformat', '%A, %B %d %Y, %I:%M %p') , $post->created);?>">
						<?php echo DiscussJcHelper::time_delta('now', $post->created, 0); ?>
					</time>
					
					<?php echo JText::_( 'door' ); ?>
					<b>
						<?php if( !$post->user_id ){ ?>
							<?php echo $post->poster_name; ?>
						<?php } else { ?>
							<?php echo $post->getOwner()->name; ?>
						<?php } ?>
					</b>
					
			
					
	
				</header>

				<article>
					<?php echo $post->content;?>
				</article>
					<?php echo $this->loadTemplate( 'post.signature.php' , array( 'signature' => $post->getOwner()->signature ) ); ?>

					<?php echo $this->getFieldHTML( true , $post ); ?>
					<?php echo $this->loadTemplate( 'post.customfields.php', array( 'post' => $post ) ); ?>
					<?php echo $this->loadTemplate( 'post.location.php' , array( 'post' => $post ) ); ?>
				

				<footer>
					<?php echo $this->loadTemplate( 'post.comments.php' , array( 'reply' => $post, 'question' => $question  ) ); ?>
					<?php echo $this->loadTemplate( 'post.likes.php' , array( 'post' => $post ) ); ?>
					<?php echo $this->loadTemplate( 'post.qna.php' , array( 'reply' => $post, 'question' => $question ) ); ?>
					
					<?php echo $this->loadTemplate( 'post.actions.php' , array( 'access' => $post->access , 'post' => $post ) ); ?>
					
					<?php echo $this->loadTemplate( 'post.reply.comments.php' , array( 'post' => $post ) ); ?>
					<!-- UNFINISHED TASK! -->
					<?php echo $this->loadTemplate( 'post.report.php' , array( 'post' => $post ) ); ?>
				</footer>
			</div>
		</div>

		<div class="reply-minimized" id="reply_minimize_msg_5">
			<b><?php echo JText::_( 'COM_EASYDISCUSS_REPLY_CURRENTLY_MINIMIZED');?></b>
			<a href="javascript:void(0);" class="butt butt-default butt-s" onclick="discuss.reply.maximize('<?php echo $post->id;?>');"><?php echo JText::_( 'COM_EASYDISCUSS_SHOW' );?></a>
		</div>
	</div>
</li>
