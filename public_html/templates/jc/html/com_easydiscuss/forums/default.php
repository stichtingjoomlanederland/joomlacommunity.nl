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
<div class="ed-forums">
    <?php if ($activeCategory) { ?>
        <?php echo $this->output('site/forums/active', array('activeCategory' => $activeCategory, 'listing' => $listing)); ?>
    <?php } ?>
    <div class="ed-list">
        <?php if (!empty($threads)) { ?>
            <?php foreach ($threads as $thread) { ?>
                <div class="ed-forum">
                    <div class="ed-forum__hd">
                        <div class="o-row">
                            <div class="o-col-sm o-col-sm--8">
                                <div class="ed-forum__hd-title">
                                    <h2 class="ed-forum-item__title">
                                        <a href="<?php echo EDR::getForumsRoute($thread->category->id); ?>">
                                           <?php echo JString::strtoupper($thread->category->getTitle()); ?>
                                        </a>
                                    </h2>
                                </div>
                            </div>

                            <div class="o-col-sm"></div>

                            <div class="o-col-sm ed-forum-item__col-avatar t-text--center">
                                <div>
                                    <?php echo JText::_('COM_EASYDISCUSS_FORUMS_POSTED_BY'); ?>
                                </div>
                            </div>
                            <div class="o-col-sm ed-forum-item__col-avatar t-text--center">
                                <div>
                                    <?php echo JText::_('COM_EASYDISCUSS_FORUMS_LAST_REPLY'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ed-forum__bd">
                        <?php echo $this->output('site/forums/item', array('thread' => $thread->posts)); ?>
                    </div>
                    <div class="ed-forum__ft">
                        <?php if (!$thread->category->container) { ?>
                            <a href="<?php echo EDR::getCategoryRoute($thread->category->id); ?>">
                                <?php echo JText::_('COM_EASYDISCUSS_FORUMS_VIEW_ALL_POST'); ?> &rarr;
                            </a>
                        <?php } ?>
                        <span class="pull-right"><?php echo JText::sprintf('COM_EASYDISCUSS_FORUMS_COUNT_POST', count($thread->posts), $thread->category->getTotalPosts()); ?></span>
                    </div>
                </div>
            <?php } ?>

        <?php } else { ?>
            <div class="ed-forum">
                <div class="ed-forum__empty t-mt--xl is-empty">
                    <div class="o-empty">
                        <div class="o-empty__content">
                            <i class="o-empty__icon fa fa-book"></i>
                            <div class="o-empty__text"><?php echo JText::_('COM_EASYDISCUSS_FORUMS_EMPTY_THREAD');?></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php if (isset($pagination)) { ?>
    <div class="ed-pagination">
        <?php echo $pagination->getPagesLinks();?>
    </div>
<?php } ?>

<?php if ($this->config->get('layout_board_stats') && $this->acl->allowed('board_statistics')) { ?>
    <?php echo $this->html('forums.stats'); ?>
<?php } ?>
