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
<script type="text/javascript">
//do not remove this line
ed.require(['edq'], function() {});
</script>
<div id="ed" class="ed-mod discuss-mod-categories discuss-categories<?php echo $params->get('moduleclass_sfx') ?>">
    <div class="ed-list--vertical">
    <?php if ($categories) { ?>
        <?php foreach ($categories as $category) { ?>
            <div class="ed-list__item">
                <div class="ed-list__item-group">
                    <div class="ed-list__item-group-hd">
                    <?php require(JModuleHelper::getLayoutPath('mod_easydiscuss_categories', 'tree_item')); ?>

                    <?php if (!$params->get('exclude_child_categories', false) && $category->totalSubcategories) { ?>
                        <a data-ed-toggle="collapse" href="#collapse-mod-cat-<?php echo $category->id; ?>" class="ed-list__toggle collapsed">
                            <i class="ed-list__toggle-icon"></i>
                        </a>
                    <?php } ?>

                    </div>

                    <?php if (!$params->get('exclude_child_categories', false) && $category->totalSubcategories) { ?>
                        <div class="ed-list__item-group-bd collapse" id="collapse-mod-cat-<?php echo $category->id; ?>">
                            <div class="ed-tree">
                                <?php echo modEasydiscussCategoriesHelper::printTree($category->childs, $category->id, $params); ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="no-item">
            <?php echo JText::_('MOD_DISCUSSIONSCATEGORIES_NO_ENTRIES'); ?>
        </div>
    <?php } ?>
    </div>
</div>
