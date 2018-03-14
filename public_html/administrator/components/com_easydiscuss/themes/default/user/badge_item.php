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
<?php foreach ($badges as $badge) { ?>
<li data-original-title="<?php echo $badge->get('title'); ?>" style="margin-bottom:15px;" >
    <div class="row">
        <div class="col-md-2">
            <img src="<?php echo $badge->getAvatar(); ?>" width="48px" />
        </div>
        <div class="col-md-10">
            <b><?php echo $badge->get('title'); ?></b>
            <a href="javascript:void(0);" class="btn" data-ed-removeBadge data-id="<?php echo $badge->id;?>">
            <i class="fa fa-close" 
                data-ed-provide="popover"
                data-content="Remove badge"
                data-placement="top"
                >
            </i>
            </a>
        
            <div class="input-group t-mb--lg">
                <input id="customMessage" class="form-control" placeholder="<?php echo $badge->description; ?>" type="text" value="<?php echo $badge->custom ? $badge->custom : ''; ?>" />
                <span class="input-group-btn">
                    <a href="javascript:void(0);" class="btn btn-primary" type="button"
                        data-ed-saveMessage
                        data-ed-provide="popover"
                        data-id="<?php echo $badge->id;?>"
                        data-title="<?php echo JText::_('COM_EASYDISCUSS_BADGE_CUSTOM_MESSAGE_BUTTON');?>"
                        data-content="<?php echo JText::_('COM_EASYDISCUSS_BADGE_CUSTOM_MESSAGE_BUTTON_DESC');?>"
                        data-placement="top"
                        ><?php echo JText::_('COM_EASYDISCUSS_BADGE_CUSTOM_MESSAGE_BUTTON');?>
                    </a>
                </span>
            </div>
            <div class="hidden" data-ed-message></div>
        </div>
    </div>
</li>
<?php } ?>