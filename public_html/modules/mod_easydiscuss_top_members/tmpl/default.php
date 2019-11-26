<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="ed" class="ed-mod m-top-members <?php echo $params->get('moduleclass_sfx');?>">
    <div class="ed-list--vertical has-dividers--bottom-space">
    <?php foreach($users as $user) { ?>
        <div class="ed-list__item">
            <div class="o-flag">
            <?php if ($params->get('showavatar')) { ?>
                <div class="o-flag__img t-lg-mr--md">
                    <?php echo ED::themes()->html('user.avatar', $user, array('rank' => true, 'status' => true, 'size' => 'md')); ?>
                </div>
            <?php } ?>
                <div class="o-flag__body">
                    <a href="<?php echo $user->getLink(); ?>" class="m-post-title t-lg-mb--sm">
                        <?php echo $user->getName(); ?>
                    </a>
                    <?php if ($params->get('showpost') || $params->get('showanswered')) { ?>
                    <div class="m-list--inline m-list--has-divider t-lg-mb-sm">
                        <?php if ($params->get('showpost')) { ?>
                        <div class="m-list__item">
                            <div class="m-post-meta t-fs--sm"><?php echo JText::sprintf('MOD_EASYDISCUSS_TOP_MEMBERS_POSTS', $user->getNumTopicPosted()); ?></div>
                        </div>
                        <?php } ?>
                        <?php if ($params->get('showanswered')) { ?>
                        <div class="m-list__item">
                            <div class="m-post-meta t-fs--sm"><?php echo JText::sprintf('MOD_EASYDISCUSS_TOP_MEMBERS_REPLIES', $user->getTotalReplies()); ?></div>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                    <?php if ($params->get('showlastonline')) { ?>
                        <div class="m-post-meta t-fs--sm"><i class="fa fa-clock-o t-lg-mr--sm"></i><?php echo $user->getLastOnline(true); ?></div>
                    <?php } ?>        
                </div>
            </div>
        </div>
    <?php } ?>
    </div>
</div>