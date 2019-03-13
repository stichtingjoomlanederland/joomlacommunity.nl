<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-comments">
	<?php if ($post->hasComments()) { ?>
	<div class="ed-comments__title"><?php echo JText::_('COM_EASYDISCUSS_COMMENT_TITLE')?></div>
		<div class="ed-comment t-lg-mb--lg">
				<?php $postId = $post->parent_id ? $post->parent_id : $post->id; ?>
				<?php foreach ($post->comments as $comment) { ?>
					<?php echo $this->output('site/post/default.comments.item', array('comment' => $comment, 'postId' => $postId)); ?>
				<?php } ?>
			
			<?php if ($this->config->get('main_comment_pagination') && isset($post->commentsCount) && $post->commentsCount > $this->config->get( 'main_comment_pagination_count')) { ?>
				<a href="javascript:void(0);" class="commentLoadMore btn btn-small" data-postid="<?php echo $post->id; ?>"><?php echo JText::_('COM_EASYDISCUSS_COMMENT_LOAD_MORE'); ?></a>
			<?php } ?>
		</div>
	<?php } ?>
	<form autocomplete="off" action="<?php echo JRoute::_('index.php');?>" method="post" enctype="multipart/form-data" data-ed-comment-form>
		<div class="ed-comment-action">
			<a href="javascript:void(0);" class="btn btn-default btn-sm" data-ed-add-comment data-id="<?php echo $post->id; ?>"><?php echo JText::_('COM_EASYDISCUSS_ADD_COMMENT')?></a>
			<div data-ed-comment-composer class="hide" >
				<?php echo $composer->renderEditor(); ?>
				<input class="btn btn-primary pull-right" type="button" value="Comment" data-ed-comment-submit="" name="submit-comment">
			</div>
		</div>
		<?php echo $this->html('form.hidden', 'comments', 'post', 'save'); ?>
	</form>
</div>