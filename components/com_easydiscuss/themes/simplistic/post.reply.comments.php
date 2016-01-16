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
<div class="discuss-comments">
	<div class="commentNotification"></div>
	<div class="commentFormContainer" style="display:none;"></div>

	<ul class="unstyled discuss-list commentsList">
		<?php if( $post->comments ){ ?>
			<?php $postId = $post->parent_id ? $post->parent_id : $post->id; ?>
			<?php foreach( $post->comments as $comment ){ ?>
				<?php echo $this->loadTemplate( 'post.reply.comment.item.php' , array( 'comment' => $comment , 'postId' => $postId ) ); ?>
			<?php } ?>
		<?php } ?>
	</ul>

	<?php if( $system->config->get( 'main_comment_pagination' ) && isset( $post->commentsCount ) && $post->commentsCount > $system->config->get( 'main_comment_pagination_count' ) ) { ?>
		<a href="javascript:void(0);" class="commentLoadMore btn btn-small" data-postid="<?php echo $post->id; ?>"><?php echo JText::_( 'COM_EASYDISCUSS_COMMENT_LOAD_MORE' ); ?></a>
	<?php } ?>

</div>
