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
        <div class="ed-search-type"><?php echo $post->getItemTypeTitle(); ?></div>
        <div class="o-row">
            <div class="o-col">
                <h2 class="ed-post-item__title t-lg-mt--md t-lg-mb--md">
                    <a href="<?php echo $post->getPermalink(); ?>"><?php echo $post->getTitle(); ?></a>
                </h2>

                <div class="ed-post-content">
                    <?php echo $post->getContent(); ?>
                </div>

                <?php if (!$post->isCategory() && $this->config->get('main_qna')) { ?>
                <ol class="g-list-inline ed-post-item__post-meta">
                    <?php if ($post->isResolved()) { ?>
                        <li><span class="o-label o-label--success-o"><?php echo JText::_('COM_EASYDISCUSS_RESOLVED') ?></span></li>
                    <?php } else { ?>
                        <li><span class="o-label o-label--danger-o"><?php echo JText::_('COM_EASYDISCUSS_DISCUSSION_UNRESOLVED'); ?></span></li>
                    <?php } ?>
                </ol>
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
    <div class="ed-post-item__ft t-bdt-no">
        <ol class="g-list-inline g-list-inline--dashed">
            <li><span class=""><a href="<?php echo $post->getAuthorPermalink(); ?>"><?php echo $post->getAuthorName(); ?></a></span></li>
            <?php if (! $post->isCategory()) { ?>
            <li><span class=""><?php echo ED::date()->toLapsed($post->created); ?></span></li>
            <li><a class="" href="<?php echo $post->getCategory()->getPermalink(); ?>"><?php echo $post->getCategoryTitle(); ?></a></li>
            <?php } ?>
        </ol>
    </div>
</div>

