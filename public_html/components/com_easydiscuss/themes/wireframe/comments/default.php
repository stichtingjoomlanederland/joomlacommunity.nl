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
<div 
	data-ed-comments-wrapper 
	data-id="<?php echo $post->id; ?>" 
	data-ordering="<?php echo $this->config->get('main_comment_ordering');?>"
>
	<div class="o-card__body">
		<div class="o-meta">
			<ol class="g-list-inline g-list-inline--dashed t-font-size--01">
				<li>
					<a href="javascript:void(0);">
						<i class="far fa-comments"></i>&nbsp; <?php echo $post->getTotalComments();?>
					</a>
				</li>
				<?php if ($post->canComment()) { ?>
				<li>
					<a href="javascript:void(0);" class="t-outline--none" data-ed-toggle-comment>
						<?php echo JText::_('COM_EASYDISCUSS_ADD_COMMENT')?>
					</a>
				</li>
				<?php } ?>
			</ol>
		</div>
	</div>
	<div class="o-card__footer t-px--no t-pt--no <?php echo !$post->getComments() ? 't-d--none' : '';?>" data-ed-comment-container>
		<div class="ed-comments l-stack">
			<div class="commentNotification"></div>

			<div class="ed-comments-list-wrapper <?php echo !$post->getComments() ? 'is-empty' : ''; ?>" data-ed-comment-list-wrapper>
				
				<div class="ed-comments-list" data-ed-comment-list>
					<?php if ($post->comments) { ?>
						<?php foreach ($post->comments as $comment) { ?>
							<?php echo $this->output('site/comments/item/default', [
								'comment' => $comment, 
								'isNew' => false
							]); ?>
						<?php } ?>
					<?php } ?>
				</div>

				<?php if ($this->config->get('main_comment_pagination') && isset($post->commentsCount) && $post->commentsCount > $this->config->get('main_comment_pagination_count')) { ?>
				<div class="t-text--center">
					<a href="javascript:void(0);" data-ed-comment-load-more class="commentLoadMore o-btn o-btn--default-o o-btn--sm" data-postid="<?php echo $post->id; ?>"><?php echo JText::_('COM_EASYDISCUSS_COMMENT_LOAD_MORE'); ?></a>
				</div>
				<?php } ?>

				<?php echo $this->html('card.emptyCard', '', 'COM_EASYDISCUSS_NO_COMMENT_YET', false); ?>
			</div>

			<?php if ($post->canComment()) { ?>
				<?php echo $this->output('site/comments/form/default', [
					'post' => $post, 
					'isEdit' => false
				]); ?>
			<?php } ?>
		</div>
	</div>
</div>