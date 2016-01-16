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
<li class="commentItem" id="comment-<?php echo $comment->id;?>" data-id="<?php echo $comment->id;?>" data-post-id="<?php echo $postId;?>">
	<div class="media">
		<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
		<a href="<?php echo $comment->creator->getLink();?>" class="discuss-avatar float-l">
			<img alt="<?php echo $this->escape( $comment->creator->getName() );?>" src="<?php echo $comment->creator->getAvatar();?>" class="avatar" width="50" height="50" />
		</a>
		<?php } ?>

		<div class="media-body">
			<header>
				<a href="<?php echo $comment->creator->getLink();?>" class="comment-author"><?php echo $comment->creator->getName(); ?></a>
				<span class="muted"><?php echo $comment->duration; ?></span>
				<?php if( $comment->canDelete() ) { ?>
				<a class="float-r deleteComment" href="javascript:void(0);"><i class="i i-times muted"></i></a>
				<?php } ?>
			</header>
			<article>
				<?php echo nl2br($comment->comment); ?>
			</article>
			<?php if( $comment->canConvert() ){ ?>
			<footer>
				<a href="javascript:void(0);" data-comment-convert-link>
					<?php echo JText::_( 'COM_EASYDISCUSS_CONVERT_THIS_COMMENT_TO_REPLY' ); ?>
				</a>
			</footer>
			<?php } ?>
		</div>
	</div>
</li>
