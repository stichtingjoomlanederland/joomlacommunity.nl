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
<div class="ed-post-replies">
    <div class="ed-reply-item">
        <div class="ed-reply-item__hd">
            <div class="o-row">
                <div class="o-col">
                    <div class="o-flag">
                        <div class="o-flag__image">
                            <div class="o-avatar-status is-online">
                                <div class="o-avatar-status__indicator"></div>
                                <a href="<?php echo $post->getOwner()->getLink();?>" class="o-avatar">
                                    <img src="<?php echo $post->getOwner()->getAvatar(); ?>">
                                </a>
                            </div>
                        </div>
                        <div class="o-flag__body">
                            <?php echo $this->html('user.username', $post->getOwner(), array('lgMarginBottom' => true)); ?>
                            
                            <div class="ed-user-rank t-lg-mb--sm o-label o-label--<?php echo $post->getOwner()->getRoleLabelClassname()?>"><?php echo $post->getOwner()->getRole(); ?></div>

                            <div class="ed-rank-bar">
                                <div style="width: <?php echo ED::ranks()->getScore($post->getOwner()->id, true); ?>%" class="ed-rank-bar__progress"></div>
                            </div>                            
                        </div>
                    </div>        
                </div>
            </div>
        </div>

        <div class="ed-reply-item__bd">
            <div class="o-row">
                <div class="o-col">
                    <div class="ed-reply-content">
                        <?php echo $post->getContent(); ?>
                    </div>

                    <?php echo $this->output('site/post/default.signature', array('post' => $post)); ?>

                    <div role="alert" class="o-alert o-alert--danger">
                        <?php echo JText::_('COM_EASYDISCUSS_REPLY_UNDER_MODERATE'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="ed-reply-item__ft">
        </div>
    </div>
</div>