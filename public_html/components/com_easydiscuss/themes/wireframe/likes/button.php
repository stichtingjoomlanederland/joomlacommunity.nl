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
<div class="ed-btn-counter-group t-lg-mb--lg <?php echo $button->total ? 'has-counter' : ''; ?> <?php echo $liked ? 'is-active' : ''; ?>" data-ed-post-likes>
	
	<span class="ed-likes-loader t-hidden" data-ed-like-loader>
  		<a href="javascript:void(0)" class="btn btn-ed-likes btn-xs"><i class="fa fa-spinner fa-spin ed-likes__loading"></i> <?php echo JText::_('COM_EASYDISCUSS_LOADING');?></a>
	</span>

	<a class="btn btn-ed-likes ed-like btn-xs"
		style="<?php echo $liked ? 'display:none;' : ''; ?>"
		href="javascript:void(0);"
		data-ed-likes-button
		data-id="<?php echo $post->id;?>"
		data-task="like"
		rel="ed-tooltip"
		data-placement="top"
	>
		<i data-ed-like-icon class="ed-btn-counter-group__icon fa fa-thumbs-up"></i>

		<span class="likeStatus" data-ed-like-status><?php echo JText::_('COM_EASYDISCUSS_LIKES');?></span>
	</a>

	<a class="btn btn-ed-likes ed-unlike btn-xs"
	   style="<?php echo !$liked ? 'display:none;' : ''; ?>"
	   data-ed-likes-button
	   data-id="<?php echo $post->id;?>"
	   data-task="unlike"
	   href="javascript:void(0);"
	   rel="ed-tooltip"
	   data-placement="top"
	>
		<i data-ed-like-icon class="ed-btn-counter-group__icon fa fa-thumbs-up"></i>

		<span class="likeStatus" data-ed-like-status><?php echo JText::_('COM_EASYDISCUSS_UNLIKE');?></span>
	</a>

  	<a class=""
		data-ed-counter-like
		data-id="<?php echo $post->id; ?>"
		data-task="popbox"
		href="javascript:void(0);"
		rel="ed-tooltip"
		data-placement="top"
	>
	    <span class="ed-btn-counter-group__counter" data-ed-like-count><?php echo $button->total; ?></span>
	</a>
</div>