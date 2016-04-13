<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<div class="ed-cat-item">
    <div class="o-flag">
        <?php if ($params->get('showcavatar', true)) { ?>
        <div class="o-flag__image o-flag--top">
            <a class="o-avatar o-avatar--md" href="<?php echo $category->getPermalink();?>">
                <img src="<?php echo $category->getAvatar();?>" alt="<?php echo ED::themes()->html('string.escape', $category->getTitle());?>" />
            </a>
        </div>
        <?php } ?>
        <div class="o-flag__body">
            <a class="ed-cat-name" href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
            <ol class="g-list-inline g-list-inline--delimited ed-cat-item-meta">
                <?php if ($params->get('showpostcnt', true)) { ?>
                <li><?php echo ED::themes()->getNouns('MOD_DISCUSSIONSCATEGORIES_ENTRY_COUNT', $category->getTotalPosts(), true);?></li>
                <?php } ?>
                <li data-breadcrumb="·"><?php echo ED::themes()->getNouns('MOD_DISCUSSIONSCATEGORIES_SUBCATEGORIES_COUNT', $category->totalSubcategories, true);?></li>
            </ol>
        </div>
    </div>
</div>
