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

<div class="ed-btn-counter-group t-mb--lg <?php echo $button->total ? 'has-counter' : ''; ?> <?php echo $fav ? 'is-active' : ''; ?>" data-ed-post-favourite>

	<span class="ed-favourite-loader t-hidden" data-ed-fav-loader>
  		<a href="javascript:void(0)" class="btn btn-ed-favor btn-xs"><i class="fa fa-spinner fa-spin ed-favourite__loading"></i> <?php echo JText::_('COM_EASYDISCUSS_LOADING');?></a>
	</span>

	<a class="btn btn-ed-favor ed-fav btn-xs"
		style="<?php echo $fav ? 'display:none;' : ''; ?>"
		href="javascript:void(0);"
		data-ed-fav-button
		data-id="<?php echo $post->id;?>"
		data-task="favourite"
		rel="ed-tooltip"
		data-placement="top"
	>
		<i data-ed-fav-icon class="ed-btn-counter-group__icon fa fa-heart"></i>

		<span class="favStatus" data-ed-fav-status><?php echo JText::_('COM_EASYDISCUSS_FAVOURITE_BUTTON_FAVOURITE');?></span>
	</a>

	<a class="btn btn-ed-favor ed-unfav btn-xs"
	   style="<?php echo !$fav ? 'display:none;' : ''; ?>"
	   data-ed-fav-button
	   data-id="<?php echo $post->id;?>"
	   data-task="unfavourite"
	   href="javascript:void(0);"
	   rel="ed-tooltip"
	   data-placement="top"
	>
		<i data-ed-fav-icon class="ed-btn-counter-group__icon fa fa-heart"></i>

		<span class="favStatus" data-ed-fav-status><?php echo JText::_('COM_EASYDISCUSS_FAVOURITE_BUTTON_UNFAVOURITE');?></span>
	</a>

	<a class="ed-btn-counter-group__counter"
		data-ed-counter-fav
		data-id="<?php echo $post->id; ?>"
		data-task="popbox"
		href="javascript:void(0);"
		rel="ed-tooltip"
		data-placement="top"
	>
	    <span class="ed-btn-counter-group__" data-ed-fav-count><?php echo $button->total; ?></span>
	</a>
</div>