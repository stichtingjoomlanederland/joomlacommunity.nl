<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-post-item <?php echo !$post->isSeen($this->my->id) ? ' is-unread' : '';?>">
    <div class="ed-post-item__hd">
        <div class="o-flag">
            <div class="o-flag__image">
                <div class="o-avatar-status">
                    <div class="o-avatar-status__indicator"></div>
                    <a class="o-avatar" href="<?php echo $post->getOwner()->getLink();?>">
                        <img src="<?php echo $post->getOwner()->getAvatar();?>">
                    </a>
                </div>
            </div>
            <div class="flag__body">
                <a href="<?php echo $post->getOwner()->getLink();?>"><?php echo $post->getOwner()->getName();?></a>

                <?php if ($this->config->get('layout_profile_roles')) { ?>
                <div class="ed-user-role <?php echo $post->getOwner()->getRoleLabelClassname();?>"><?php echo $post->getOwner()->getRole();?></div>
                <?php } ?>
            </div>
        </div>

        <div class="ed-post-item__status">

            <?php if ($post->isFeatured()) { ?>
            <i class="fa fa-star"></i>
            <?php } ?>

            <?php if ($post->isLocked()) { ?>
            <i class="fa fa-lock"></i>
            <?php } ?>

            <?php if ($post->isProtected()) { ?>
            <i class="fa fa-lock"></i>
            <?php } ?>
        </div>

    </div>
    <div class="ed-post-item__bd">
        <div class="o-row">
            <div class="o-col">
                <h2 class="ed-post-item__title t-lg-mt--md t-lg-mb--md">
                    <a href="<?php echo $post->getPermalink();?>"><?php echo $post->getTitle();?></a>
                </h2>

                <p>
                    <?php echo $post->getIntro();?>
                </p>
                <?php if ($post->getTags()) { ?>
                <ol class="g-list-inline ed-post-item__post-meta">
                    <?php foreach ($post->getTags() as $tag) { ?>
                    <li>
                        <a href="#">#Home</a>
                    </li>
                    <?php } ?>
                </ol>
                <?php } ?>

            </div>

            <div class="o-col o-col--4">
                <div class="ed-statistic pull-right">
                    <div class="ed-statistic__item">
                        <a href="<?php echo $post->getPermalink();?>">
                            <span class="ed-statistic__item-count"><?php echo $post->getTotalReplies();?></span>
                            <span><?php echo JText::_('COM_EASYDISCUSS_REPLIES');?></span>
                        </a>
                    </div>
                    <div class="ed-statistic__item">
                        <a href="<?php echo $post->getPermalink();?>">
                            <span class="ed-statistic__item-count"><?php echo $post->getHits();?></span>
                            <span><?php echo JText::_('COM_EASYDISCUSS_VIEWS');?></span>
                        </a>
                    </div>
                    <div class="ed-statistic__item">
                        <a href="<?php echo $post->getPermalink();?>">
                            <span class="ed-statistic__item-count"><?php echo $post->getTotalVotes();?></span>
                            <span><?php echo JText::_('COM_EASYDISCUSS_VOTES');?></span>
                        </a>
                    </div>
                    <div class="ed-statistic__item">
                        <a href="<?php echo $post->getPermalink();?>">
                            <span class="ed-statistic__item-count"><?php echo $post->getTotalLikes();?></span>
                            <span><?php echo JText::_('COM_EASYDISCUSS_LIKES');?></span>
                        </a>
                    </div>
                </div>        
            </div>
        </div>
    </div>

    <div class="ed-post-item__ft">
        <ol class="g-list-inline g-list-inline--dashed">
            <li>
                <span class="muted"><?php echo $post->getDuration();?></span>
            </li>
            <li>
                <a href="<?php echo $post->getCategory()->getPermalink();?>" class=""><?php echo JText::_($post->getCategory()->title);?></a>
            </li>

            <?php if ($post->hasReplies()) { ?>
            <li class="current">
                <div class="">
                    <span><?php echo JText::_('COM_EASYDISCUSS_LAST_REPLIED_BY');?>: </span>
                    <a class="o-avatar o-avatar--sm" href="<?php echo $post->getLastReplier()->getLink();?>">
                        <img src="<?php echo $post->getLastReplier()->getAvatar();?>" />
                    </a>
                </div>
            </li>
            <?php } ?>
        </ol>
    </div>
</div>