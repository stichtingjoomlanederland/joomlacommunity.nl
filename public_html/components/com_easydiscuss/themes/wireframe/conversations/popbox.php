<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
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
                <div class="popbox-dropdown__title"><?php echo JText::_('COM_EASYDISCUSS_CONVERSATIONS'); ?></div>
            </div>
        </div>
    </div>

    <?php if ($conversations) { ?>
        <div class="popbox-dropdown__bd">
            <div class="popbox-dropdown-nav">
            <?php foreach ($conversations as $conversation) { ?>
                <div class="popbox-dropdown-nav__item <?php echo $conversation->isNew() ? 'is-unread' : ''; ?>">
                    <a href="<?php echo $conversation->getPermalink();?>" class="popbox-dropdown-nav__link">
                        <div class="o-flag">
                            <div class="o-flag__image o-flag--top">
                                <?php echo $this->html('user.avatar', $conversation->getLastReplier(), array(), false, true); ?>
                            </div>
                            <div class="o-flag__body">
                                <div class="popbox-dropdown-nav__post">
                                	<div class="popbox-dropdown-nav__post-user-name"><?php echo $conversation->getLastReplier()->getName(); ?></div>
                                	<div><?php echo JString::substr(strip_tags($conversation->getLastMessage($this->my->id, false)), 0, 150) . JText::_('COM_EASYDISCUSS_ELLIPSES'); ?></div>
                                </div>
                                <ol class="g-list-inline g-list-inline--delimited popbox-dropdown-nav__meta-lists">
                                    <li><?php echo ED::date()->toLapsed($conversation->lastreplied); ?></li>
                                </ol>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>
            </div>
        </div>

    <?php } else { ?>
        <div class="popbox-dropdown__bd">
            <?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_NO_CONVERSATIONS_YET' ); ?>
        </div>
    <?php } ?>

    <div class="popbox-dropdown__ft">
        <a href="<?php echo EDR::_('view=conversation'); ?>" class="popbox-dropdown__note pull-left">
            <?php echo JText::_('COM_EASYDISCUSS_VIEW_ALL_CONVERSATIONS'); ?>
        </a>
    </div>
</div>
