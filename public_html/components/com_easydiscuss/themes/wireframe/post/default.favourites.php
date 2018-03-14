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

if (!$post->canFav()) {
	return;
}
?>
<div data-ed-favourite class="ed-btn-counter-group t-lg-mb--lg <?php echo $post->getMyFavCount() ? 'has-counter' : ''; ?> <?php echo $post->isFavBy($this->my->id) ? 'is-active' : ''; ?>">
	<div class="ed-fav is-loading has-counter is-loading" style="display:none;">
  		<a href="javascript:void(0)" class="btn btn-ed-favor btn-xs">
	  		<i class="ed-btn-counter-group__icon fa fa-heart"></i>
	  		<?php echo JText::_('COM_EASYDISCUSS_LOADING');?>
	  	</a>
	</div>
	<a href="javascript:void(0);" 
		class="btn btn-ed-favor ed-unfav btn-xs" 
		style="<?php echo $post->isFavBy($this->my->id) ? 'display:none;' : ''; ?>"
		data-ed-post-favourite
		data-task="favourite"
		data-id="<?php echo $post->id;?>"
	>
		<i class="ed-btn-counter-group__icon fa fa-heart"></i> 
		<?php echo JText::_('COM_EASYDISCUSS_FAVOURITE_BUTTON_FAVOURITE'); ?>
	</a>

	<a href="javascript:void(0);" 
		class="btn btn-ed-favor ed-fav btn-xs" 
		style="<?php echo $post->isFavBy($this->my->id) ? '' : 'display:none;'; ?>"
		data-ed-post-favourite
		data-task="unfavourite"
		data-id="<?php echo $post->id;?>"
	>
		<i class="ed-btn-counter-group__icon fa fa-heart"></i> 
		<?php echo JText::_('COM_EASYDISCUSS_FAVOURITE_BUTTON_UNFAVOURITE'); ?>
	</a>

	<span class="ed-btn-counter-group__counter" data-ed-favourite-count>
		<?php echo $post->getMyFavCount(); ?>
	</span>
</div>