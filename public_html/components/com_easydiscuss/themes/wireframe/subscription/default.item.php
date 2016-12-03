<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-post-item">
    <div class="ed-post-item__hd">
        <div class="o-row">
            <div class="o-col">
                <h2 class="ed-post-item__title t-lg-mb--md"><a href="<?php echo $post->permalink; ?>"><?php echo $post->bname; ?></a></h2>
                <a href="<?php echo $post->unsubscribeLink; ?>" class="ed-unsubscribe-link"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE') ?></a>
            </div>
            <div class="o-col o-col--4">
                <div class="ed-statistic pull-right">
                    <div class="ed-statistic__item">
                        <a href="<?php echo $post->permalink; ?>">
                        <span class="ed-statistic__item-count"><?php echo $post->repliesCount; ?></span>
                        <span><?php echo JText::_('COM_EASYDISCUSS_STAT_TOTAL_REPLIES'); ?></span>
                        </a>
                    </div>

                    <div class="ed-statistic__item">
                        <a href="<?php echo $post->permalink; ?>">
                        <span class="ed-statistic__item-count"><?php echo $post->viewCount; ?></span>
                        <span><?php echo JText::_('COM_EASYDISCUSS_STAT_TOTAL_HITS'); ?></span>
                        </a>
                    </div>

                    <?php if ($this->config->get('main_allowquestionvote')) { ?>
                        <div class="ed-statistic__item">
                            <a href="<?php echo $post->permalink; ?>">
                            <span class="ed-statistic__item-count"><?php echo $post->voteCount; ?></span>
                            <span><?php echo JText::_('COM_EASYDISCUSS_STAT_TOTAL_VOTES'); ?></span>
                            </a>
                        </div>
                    <?php } ?>

                    <?php if ($this->config->get('main_likes_discussions')) { ?>
                        <div class="ed-statistic__item">
                            <a href="<?php echo $post->permalink; ?>">
                            <span class="ed-statistic__item-count"><?php echo $post->likeCount; ?></span>
                            <span><?php echo JText::_('COM_EASYDISCUSS_STAT_TOTAL_LIKES'); ?></span>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
