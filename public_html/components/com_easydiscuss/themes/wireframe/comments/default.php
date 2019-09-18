<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div data-ed-comments-wrapper data-id="<?php echo $post->id; ?>" class="ed-comments-wrapper <?php echo !$post->getComments() ? 'is-empty' : ''; ?>">
	
	<div class="commentNotification"></div>

	<div class="ed-comments__title"><?php echo JText::_('COM_EASYDISCUSS_COMMENT'); ?></div>

	<div class="ed-comment t-lg-mb--md" data-ed-comment-list>
		<?php if ($post->getComments()) { ?>
				<?php foreach ($post->getComments() as $comment) { ?>
					<?php echo $this->output('site/comments/default.item', array('comment' => $comment)); ?>
				<?php } ?>	
		<?php } ?>
	</div>

	<?php if ($this->config->get('main_comment_pagination') && isset($post->commentsCount) && $post->commentsCount > $this->config->get('main_comment_pagination_count')) { ?>
	<div class="text-center">
		<a href="javascript:void(0);" data-ed-comment-load-more class="commentLoadMore btn btn-default btn-sm" data-postid="<?php echo $post->id; ?>"><?php echo JText::_('COM_EASYDISCUSS_COMMENT_LOAD_MORE'); ?></a>
	</div>
	<?php } ?>

	<div class="ed-comments-wrapper__empty t-lg-mb--md">
		<?php echo JText::_('COM_EASYDISCUSS_NO_COMMENT_YET');?>
	</div>

	<?php if ($post->canComment()) { ?>
	<a href="javascript:void(0);" class="btn btn-default btn-sm" data-ed-toggle-comment><?php echo JText::_('COM_EASYDISCUSS_ADD_COMMENT')?></a>
	
	<div class="commentFormContainer t-hidden" data-ed-comment-form>
		<?php echo $this->output('site/comments/form', array('post' => $post)); ?>
	</div>
	<?php } ?>

</div>
