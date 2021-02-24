<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row" data-badge-item>
	<div class="col-md-2">
		<img src="<?php echo $badge->getAvatar();?>" width="48px" />
	</div>
	<div class="col-md-10">
		<div class="row">
			<div class="col-md-10 l-stack l-spaces--sm">
				<div class="o-title">
					<?php echo $badge->get('title'); ?>
				</div>
				<div class="">
					<input type="text" id="customMessage" name="customMessage" class="o-form-control" value="<?php echo $badge->custom ? $badge->custom : ''; ?>" placeholder="<?php echo $badge->description; ?>">
				</div>
				<div>
					<a href="javascript:void(0);" class="btn btn-primary" type="button"
						data-ed-saveMessage
						data-ed-provide="popover"
						data-id="<?php echo $badge->id;?>"
						data-title="<?php echo JText::_('COM_EASYDISCUSS_BADGE_CUSTOM_MESSAGE_BUTTON');?>"
						data-content="<?php echo JText::_('COM_EASYDISCUSS_BADGE_CUSTOM_MESSAGE_BUTTON_DESC');?>"
						data-placement="top"
						>
						<?php echo JText::_('COM_EASYDISCUSS_BADGE_CUSTOM_MESSAGE_BUTTON');?>
					</a>
				</div>
				<div class="t-hidden o-alert o-alert--info" data-ed-message></div>
			</div>
			<div class="col-md-2">
				<a href="javascript:void(0);" class="o-btn o-btn--danger-o o-btn--sm" 
				data-ed-removeBadge 
				data-id="<?php echo $badge->id;?>"
				data-ed-provide="popover"
				data-content="<?php echo JText::_('COM_EASYDISCUSS_REMOVE_BADGE');?>"
				data-placement="top"
				><i class="fa fa-times"></i>
				</a>
			</div>
		</div>
	</div>
</div>