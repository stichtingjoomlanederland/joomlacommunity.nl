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
<div class="o-col o-col--8">
    <div class="ed-post-reply-bar__sort-action pull-right">
        <ul class="o-tabs o-tabs--ed">
            <?php if ($this->config->get('main_likes_replies')) { ?>
                <li class="<?php echo ($sort == 'likes') ? 'active' : '';?> sortItem o-tabs__item ">
                    <a class="o-tabs__link" href="<?php echo EDR::_('view=post&id=' . $post->id . '&sort=likes'); ?>#filter-sort">
                        <?php echo JText::_('COM_EASYDISCUSS_SORT_LIKED_MOST'); ?>
                    </a>
                </li>
            <?php } ?>

            <?php if ($this->config->get('main_allowvote')) { ?>
                <li class="<?php echo ($sort == 'voted') ? 'active' : '';?> sortItem o-tabs__item ">
                    <a class="o-tabs__link" href="<?php echo EDR::_('view=post&id=' . $post->id . '&sort=voted'); ?>#filter-sort">
                        <?php echo JText::_('COM_EASYDISCUSS_SORT_HIGHEST_VOTE'); ?>
                    </a>
                </li>
            <?php } ?>

            <li class="<?php echo ($sort == 'latest') ? 'active' : '';?> sortItem o-tabs__item ">
                <a class="o-tabs__link" href="<?php echo EDR::_('view=post&id=' . $post->id . '&sort=latest'); ?>#filter-sort">
                    <?php echo JText::_('COM_EASYDISCUSS_SORT_LATEST'); ?>
                </a>
            </li>

            <li class="<?php echo (!$sort || $sort == 'oldest' || $sort == 'replylatest') ? 'active' : '';?> sortItem o-tabs__item ">
                <a class="o-tabs__link" href="<?php echo EDR::_('view=post&id=' . $post->id . '&sort=oldest'); ?>#filter-sort">
                    <?php echo JText::_('COM_EASYDISCUSS_SORT_OLDEST'); ?>
                </a>
            </li>
        </ul>
    </div>
</div> 
