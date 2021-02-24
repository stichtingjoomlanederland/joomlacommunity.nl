<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<div class="k-sidebar-left k-js-sidebar-left">

    <!-- Navigation -->
    <div class="k-sidebar-item k-sidebar-item--flex">

        <div class="k-sidebar-item__header">
            <?= translate('Menu Items and categories')?>
        </div>

        <? // Only show if there are actually menu items available ?>
        <? if (count($pages)): ?>
            <div class="k-tree" id="documents-sidebar">
                <div class="k-sidebar-item__content k-sidebar-item__content--horizontal">
                    <?= translate('Loading') ?>
                </div>
            </div>
        <? else : ?>
            <div class="k-sidebar-item__content">
                <?= translate('No menu items found')?>
            </div>
        <? endif; ?>

    </div>

</div><!-- .k-sidebar-left -->
