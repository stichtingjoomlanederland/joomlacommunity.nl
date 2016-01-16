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
<?php if( $config->get( 'main_likes_discussions' ) && $params->get( 'allow_likes' , true ) ){ ?>
<div class="row-fluid">
	<?php if( ( $system->config->get( 'main_likes_discussions' ) && !$post->parent_id ) || ( $system->config->get( 'main_likes_replies' ) && $post->parent_id ) ){ ?>
	<?php
		$isLiked 	= $post->isLikedBy( $system->my->id );

		if( $isLiked )
		{
			$message	= 'COM_EASYDISCUSS_UNLIKE_THIS_POST';
		}
		else
		{
			$message = 'COM_EASYDISCUSS_LIKE_THIS_POST';
		}
	?>
	<div class="discuss-likes discussLikes" data-postid="<?php echo $post->id;?>" data-registered-user="<?php echo $system->my->id ? "true" : "false"; ?>">
		<a href="javascript:void(0);" class="btn btn-likes<?php echo $isLiked ? ' btnUnlike' : ' btnLike';?>" href="javascript:void(0);" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( $message , true );?>">
			<i class="icon-ed-love"></i> <?php echo JText::_('COM_EASYDISCUSS_LIKES');?>
		</a>

		<span class="like-text likeText">
			<?php if( $post->likesAuthor ){ ?>
				<?php echo $post->likesAuthor; ?>
			<?php } else { ?>
				<?php echo JText::_( 'COM_EASYDISCUSS_BE_THE_FIRST_TO_LIKE' ); ?>
			<?php } ?>
		</span>
	</div>
	<?php } ?>
</div>
<?php } ?>