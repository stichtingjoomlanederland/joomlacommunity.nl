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
<div class="discuss-categories">
    <div class="fd-cf">
        <h2 class="discuss-component-title"><?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_PAGE_HEADING'); ?></h2>
    </div>
    <hr />

    <?php if ($categories) { ?>
    <div class="discuss-timeline">
        <ul class="unstyled discuss-list clearfix toggleCategories">
        <?php foreach ($categories as $category) { ?>

            <?php if ($this->config->get('layout_category_toggle')) { ?>
            <li class="<?php echo !$category->depth ? 'parent' : 'child-' . $category->depth; ?>" 
                style="<?php echo !$category->depth ? '' : 'display: none;' ?>" 
                data-item 
                data-parent-id="<?php echo $category->parent_id; ?>" 
                data-id="<?php echo $category->id; ?>"
            >
            <?php } else { ?>
            <li class="<?php echo !$category->depth ? 'parent' : 'child-' . $category->depth; ?>" 
                data-item 
                data-parent-id="<?php echo $category->parent_id; ?>" 
                data-id="<?php echo $category->id; ?>"
            >
            <?php } ?>

                <div class="media">

                    <div class="media-object pull-left">
                        <?php if ($this->config->get('layout_category_toggle')) { ?>
                            <?php if ($category->getChildCount() != 0 && $this->config->get('layout_show_all_subcategories')) { ?>
                            <button type="button" class="showChild btn btn-mini btn-child-toggle" data-ed-category-toggle data-id="<?php echo $category->id; ?>">
                                <i class="icon-"></i>
                            </button>
                            <?php } ?>
                        <?php } ?>

                        <div class="discuss-avatar avatar-medium">
                            <img alt="<?php echo $this->escape($category->getTitle());?>" src="<?php echo $category->getAvatar();?>" />
                        </div>
                    </div>

                    <div class="media-body">
                        <div class="">
                            <h3>
                                <?php if( !$category->container ) { ?>
                                    <a href="<?php echo DiscussRouter::getCategoryRoute( $category->id );?>"><?php echo $category->getTitle();?></a>
                                <?php } else { ?>
                                    <?php echo $category->getTitle();?>
                                <?php } ?>
                            </h3>

                            <?php if( $category->getParam( 'show_description') && !$this->config->get( 'layout_category_description_hidden' ) ) { ?>
                                <?php echo $category->description;?>
                            <?php } ?>

                            <?php if (!$category->container) { ?>
                                <div class="fd-cf discuss-subscribe">
                                    <?php if( $this->config->get( 'main_rss' ) ){ ?>
                                    <a href="<?php echo $category->getRSSPermalink();?>"><i class="icon-ed-rss"></i> <?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_RSS_FEED');?></a>
                                    <?php } ?>

                                    <span class="ml-5"><?php echo $this->getNouns('COM_EASYDISCUSS_ENTRY_COUNT' , $category->getPostCount() , true );?></span>
                                </div>

                                <?php if ($this->config->get('layout_show_moderators')) { ?>
                                <div class="fd-cf discuss-moderator">
                                    <?php echo ED::moderator()->html($category->id); ?>
                                </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </li>
        <?php } ?>
        </ul>
    </div>
    <?php } else { ?>
    <div class="empty">
        <?php echo JText::_('COM_EASYDISCUSS_NO_RECORDS_FOUND'); ?>
    </div>
    <?php } ?>
</div>
