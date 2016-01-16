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
<?php if( $system->config->get( 'main_favorite' ) && $system->my->id > 0 ) { ?>
<div class="discuss-post-favors discussFavourites" data-postid="<?php echo $post->id;?>">
	<?php if( !$post->isFavBy( $system->my->id ) ){ ?>
		<a class="butt butt-default btnFav<?php echo $post->isFavBy( $system->my->id ) ? ' isfav' : '';?>" href="javascript:void(0);" >
			<i class="i i-star muted"></i>
			&nbsp;
			<?php echo JText::_( 'COM_EASYDISCUSS_FAVOURITE_BUTTON_FAVOURITE' , true );?>
		</a>
	<?php }else{ ?>
		<a class="butt butt-default btnFav<?php echo $post->isFavBy( $system->my->id ) ? ' isfav' : '';?>" href="javascript:void(0);" >
			<i class="i i-star muted"></i>
			&nbsp;
			<?php echo JText::_( 'COM_EASYDISCUSS_FAVOURITE_BUTTON_UNFAVOURITE' , true );?>
		</a>
	<?php } ?>

	<span class="butt discuss-favor-counter">
		<span class="favCount muted">
			<?php echo $post->getMyFavCount(); ?>
		</span>
		<span class="favLoader discuss-loader" style="display: none;"></span>
	</span>
</div>
<?php } ?>

