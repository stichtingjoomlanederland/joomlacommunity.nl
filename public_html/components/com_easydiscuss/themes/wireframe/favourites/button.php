<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-favourites <?php echo $fav ? 'is-active' : ''; ?>" data-ed-favourites-wrapper data-id="<?php echo $post->id;?>">
	<a href="javascript:void(0);"
		data-ed-favourites
		data-task="<?php echo $fav ? 'unfavourite' : 'favourite';?>"
		data-ed-provide="tooltip"
		data-original-title="<?php echo $fav ? JText::_('COM_ED_UNFAVOURITE_TOOLTIP_TITLE') : JText::_('COM_ED_FAVOURITE_TOOLTIP_TITLE');?>"
	>
		<i class="ed-favourites__icon fas fa-heart"></i>
	</a>

	<a href="javascript:void(0);" class="t-mx--xs"
		data-ed-popbox="ajax://site/views/popbox/favourite"
		data-ed-popbox-position="top-center"
		data-ed-popbox-toggle="click"
		data-ed-popbox-offset="5"
		data-ed-popbox-type="ed-favourites"
		data-ed-popbox-component="o-popbox--avatar-list"
		data-ed-popbox-cache="1"
		data-args-id="<?php echo $post->id; ?>"
	>
		<span class="ed-favourites__counter" data-counter><?php echo $total; ?></span>
	</a>
</div>