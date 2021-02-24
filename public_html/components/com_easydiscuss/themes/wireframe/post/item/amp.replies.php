<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-reply-item <?php echo $reply->isAnswer() ? 'is-answer' : ''; ?>">
	<div class="ed-reply-item__body">
		<div class="ed-reply-item-content">
			<?php echo $reply->getContent(false, false, true, true, true); ?>
		</div>
	</div>
	<div class="ed-reply-item__body">
		<div class="ed-reply-item-meta">
			<div class="ed-reply-author">
				<a href="<?php echo $reply->getAuthor()->getPermalink(); ?>"><?php echo $reply->getAuthor()->getName();?></a>
			</div>
			<div class="">&middot;</div>
			<div class="ed-reply-date">
				<?php echo $reply->getReplyDate(); ?>
			</div>
			<div class="">&middot;</div>
			<div class="ed-reply-likes">
				<?php echo ED::formatNumbers($post->getTotalLikes());?> <?php echo JText::_('COM_ED_LIKES');?>
			</div>
			<div class="">&middot;</div>
			<div class="ed-reply-votes">
				<?php echo ED::formatNumbers($reply->getTotalVotes(false)); ?> <?php echo JText::_('COM_ED_AMP_VOTES'); ?>
			</div>
			<div class="">&middot;</div>
			<div class="ed-reply-comments">
				<?php echo ED::formatNumbers($reply->getTotalComments()); ?> <?php echo JText::_('COM_ED_AMP_COMMENTS'); ?>
			</div>
			<div class="">&middot;</div>
			<div>
				<a href="<?php echo $post->getPermalink(); ?>">#<?php echo $reply->id; ?></a>
			</div>
		</div>
	</div>
	<div class="ed-reply-item__body">
		<?php echo $this->output('site/post/item/amp.comments', ['post' => $reply]); ?>
	</div>
</div>