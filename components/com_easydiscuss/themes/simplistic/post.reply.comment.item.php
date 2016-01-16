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
	<div class="discuss-item">

		<div class="discuss-item-left discuss-user">
		<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
			<div class="discuss-avatar avatar-small avatar-circle">
				<a href="<?php echo $comment->creator->getLink();?>">
					<img alt="<?php echo $this->escape( $comment->creator->getName() );?>" src="<?php echo $comment->creator->getAvatar();?>" />
				</a>
			</div>
			<?php } ?>
		</div>

		<div class="discuss-item-right">
			<div class="discuss-story">
				<div class="discuss-story-hd ">
					<div class="pull-left">
						<a href="<?php echo $comment->creator->getLink();?>" class="discuss-user-name pull-left"><?php echo $comment->creator->getName(); ?></a>
						<div class="discuss-clock ml-5 pull-right "><?php echo $comment->duration; ?></div>

					</div>
					<div class="discuss-action-options-1 pull-right">

					<?php if( $comment->canDelete() ) { ?>
						<div class="pull-left">
							<a class="btn btn-small btn-link deleteComment" href="javascript:void(0);"><i class="icon-remove"></i></a>
						</div>
					<?php } ?>
					</div>
				</div>

				<div class="discuss-story-bd">
					<div class="discuss-comment-text">
						<?php echo nl2br($comment->comment); ?>
					</div>

					<?php if( $comment->canConvert() ){ ?>
					<div class="mt-10">
						<a href="javascript:void(0);" class="small" data-comment-convert-link><?php echo JText::_( 'COM_EASYDISCUSS_CONVERT_THIS_COMMENT_TO_REPLY' ); ?></a>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</li>
