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
<div class="popbox-dropdown">
    
    <div class="popbox-dropdown__hd">
        <div class="o-flag o-flag--rev">
            <div class="o-flag__body">
                <div class="popbox-dropdown__title"><?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS'); ?></div>
            </div>
        </div>
    </div>

    <?php if ($notifications) { ?>
        <div class="popbox-dropdown__bd">
            <div class="popbox-dropdown-nav">
            <?php foreach ($notifications as $notification) { ?>
                <div class="popbox-dropdown-nav__item">
                    <a href="<?php echo $notification->permalink; ?>" class="popbox-dropdown-nav__link">
                        <div class="o-flag">
                            <div class="o-flag__image o-flag--top">
                                <i class="popbox-dropdown-nav__icon fa fa-comment"></i> 
                            </div>
                            <div class="o-flag__body">
                                <div class="popbox-dropdown-nav__post">
                                	<?php echo $notification->postTitle;?>
                                </div>
                                <ol class="g-list-inline g-list-inline--delimited popbox-dropdown-nav__meta-lists">
                                    <li><?php echo ED::date()->toLapsed($notification->created); ?></li>
                                </ol>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>
            </div>
        </div>

        <div class="popbox-dropdown__ft">
            <a href="<?php echo EDR::_('view=notifications'); ?>" class="popbox-dropdown__note pull-left">
                <?php echo JText::_('COM_EASYDISCUSS_VIEW_ALL_NOTIFICATIONS'); ?>
            </a>
            <a href="javascript:void(0)" data-ed-notifications-read-all class="popbox-dropdown__note pull-right">
                <?php echo Jtext::_('COM_EASYDISCUSS_MARK_ALL_AS_READ'); ?>
            </a>
        </div>
    <?php } else { ?>
        <div class="popbox-dropdown__bd">
            <?php echo JText::_('COM_EASYDISCUSS_NO_NEW_NOTIFICATIONS_YET'); ?>
        </div>
        <div class="popbox-dropdown__ft">
            <a href="<?php echo EDR::_('view=notifications'); ?>" class="popbox-dropdown__note pull-left">
                <?php echo JText::_('COM_EASYDISCUSS_VIEW_ALL_NOTIFICATIONS'); ?>
            </a>
        </div>
    <?php } ?>
</div>