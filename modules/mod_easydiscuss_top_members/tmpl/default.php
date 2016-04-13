<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div id="ed" class="ed-mod m-top-members <?php echo $params->get('moduleclass_sfx');?>">
    <div class="ed-list--vertical has-dividers--bottom-space">
    <?php foreach($users as $user) { ?>
        <div class="ed-list__item">
            <div class="o-flag">
            <?php if ($params->get('showavatar')) { ?>
                <div class="o-flag__img t-lg-mr--md">
                    <a class="o-avatar" href="<?php echo $user->getLink(); ?>">
                        <img src="<?php echo $user->getAvatar(); ?>">
                    </a>
                </div>
            <?php } ?>
                <div class="o-flag__body">
                    <a href="<?php echo $user->getLink(); ?>" class="m-post-title t-lg-mb--sm">
                        <?php echo $user->getName(); ?>
                    </a>
                    <div class="m-list--inline m-list--has-divider t-lg-mb-sm">
                        <div class="m-list__item">
                            <?php if ($params->get('showpost')) { ?>
                                <div class="m-post-meta t-fs--sm"><?php echo $user->getNumTopicPosted(); ?> <?php echo JText::_('MOD_EASYDISCUSS_TOP_MEMBERS_POSTS'); ?></div>
                            <?php } ?>
                        </div>
                        <div class="m-list__item">
                            <?php if ($params->get('showanswered')) { ?>
                                <div class="m-post-meta t-fs--sm"><?php echo $user->getNumTopicAnswered(); ?> <?php echo JText::_('MOD_EASYDISCUSS_TOP_MEMBERS_REPLIES'); ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if ($params->get('showlastonline')) { ?>
                        <div class="m-post-meta t-fs--sm"><i class="fa fa-clock-o t-lg-mr--sm"></i><?php echo $user->getLastOnline(true); ?></div>
                    <?php } ?>        
                </div>
            </div>
        </div>
    <?php } ?>
    </div>
</div>