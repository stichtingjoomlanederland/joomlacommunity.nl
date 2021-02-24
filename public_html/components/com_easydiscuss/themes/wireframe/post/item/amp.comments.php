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
<div class="ed-comment-list">
<?php $comments = $post->getComments(); ?>
<?php if ($comments) { ?>
	<?php foreach ($comments as $comment) { ?>
		<div class="ed-comment-item">
			<div class="ed-comment-item-content">
				<?php echo $comment->getMessage();?>
			</div>
			<div class="ed-comment-item-meta">
				<div class="ed-comment-author">
					<a href="<?php echo $comment->getAuthor()->getPermalink(); ?>"><?php echo $comment->getAuthor()->getName();?></a>
				</div>
				<div class="ed-comment-date">
					<?php echo $comment->getDuration(); ?>
				</div>
				<div>
					<a href="<?php echo $comment->getPermalink(); ?>">#<?php echo $comment->id;?></a>
				</div>
			</div>
		</div>
	<?php } ?>
	<?php $total = $post->getTotalComments(); ?>
	<?php if ($this->config->get('main_comment_pagination') && $total > $this->config->get('main_comment_pagination_count')) { ?>
		<a href="<?php echo $post->getPermalink(); ?>" class="btn-ed ed-comment-view-more-btn"><?php echo JText::_('COM_ED_AMP_VIEW_MORE_COMMENTS'); ?></a>
	<?php } ?>
<?php } ?>
</div>