<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-post-item-badges">
	<?php foreach ($badges as $badge) { ?>
	<a href="<?php echo $badge->getPermalink();?>" data-ed-provide="tooltip" data-title="<?php echo $this->html('string.escape', JText::_($badge->title));?>">
		<img src="<?php echo $badge->getAvatar();?>" alt="<?php echo $this->html('string.escape', JText::_($badge->title));?>" width="20" height="20" />
	</a>
	<?php } ?>

	<?php if ($hasMoreBadges) { ?>
	<a href="javascript:void(0);" class="ed-post-item-badges__toggle" 
		data-ed-popbox data-ed-popbox-toggle="click" 
		data-ed-popbox-position="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'top-right' : 'top-left';?>" 
		data-ed-popbox-offset="4" 
		data-ed-popbox-type="ed-badges-list" 
		data-ed-popbox-component="ed" 
		data-ed-popbox-target="[data-ed-badges-list]">
		<i class="fa fa-ellipsis-h"></i>
	</a>

	<div class="t-hidden" data-ed-badges-list>			
		<div class="ed-popbox-badges">
			<?php foreach ($userBadges as $userBadge) { ?>
			<a href="<?php echo $userBadge->getPermalink();?>" data-ed-provide="tooltip" data-title="<?php echo $this->html('string.escape', JText::_($userBadge->title));?>">
				<img src="<?php echo $userBadge->getAvatar();?>" alt="<?php echo $this->html('string.escape', JText::_($userBadge->title));?>" width="20" height="20" />
			</a>
			<?php } ?>
		</div>
	</div>	
	<?php } ?>
</div>