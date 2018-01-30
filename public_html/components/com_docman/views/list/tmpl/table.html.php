<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<? if ($can_delete): ?>
<ktml:script src="media://com_docman/js/site/items.js" />
<? endif; ?>

<? if (!empty($can_add)): ?>
    <?= helper('behavior.modal'); ?>
<? endif; ?>

<?= helper('ui.load'); ?>



<? // RSS feed ?>
<link href="<?=route('format=rss');?>" rel="alternate" type="application/rss+xml" title="RSS 2.0" />


<? if ($params->track_downloads): ?>
    <?= helper('behavior.download_tracker'); ?>
<? endif; ?>

<div class="docman_table_layout docman_table_layout--default">

    <? // Page heading ?>
    <? if ($params->get('show_page_heading')): ?>
        <h1 class="docman_page_heading">
            <?= escape($params->get('page_heading')); ?>
        </h1>
    <? endif; ?>

    <? // Toolbar ?>
    <ktml:toolbar type="actionbar">

    <? // Category ?>
    <? if (($params->show_icon && $category->icon)
    || ($params->show_category_title)
    || ($params->show_image && $category->image)
    || ($category->description_full && $params->show_description)
    ): ?>
    <div class="docman_category">

        <? // Header ?>
        <? if ($params->show_category_title && $category->title): ?>
        <h3 class="koowa_header">
            <? // Header image ?>
            <? if ($params->show_icon && $category->icon): ?>
                <span class="koowa_header__item koowa_header__item--image_container">
                    <?= import('com://site/docman.document.icon.html', array('icon' => $category->icon, 'class' => ' k-icon--size-medium')) ?>
                </span>
            <? endif ?>

            <? // Header title ?>
            <span class="koowa_header__item">
                <span class="koowa_wrapped_content">
                    <span class="whitespace_preserver">
                        <?= escape($category->title); ?>

                        <? // Label locked ?>
                        <? if ($category->canPerform('edit') && $category->isLockable() && $category->isLocked()): ?>
                            <span class="label label-warning"><?= translate('Locked'); ?></span>
                        <? endif; ?>

                        <? // Label status ?>
                        <? if (!$category->enabled): ?>
                            <span class="label label-draft"><?= translate('Draft'); ?></span>
                        <? endif; ?>

                        <? // Label owner ?>
                        <? if ($params->get('show_category_owner_label', 1) && !$category->isNew() && object('user')->getId() == $category->created_by): ?>
                            <span class="label label-success"><?= translate('Owner'); ?></span>
                        <? endif; ?>
                    </span>
                </span>
            </span>
        </h3>
        <? endif; ?>

        <? // Edit area | Import partial template from category view ?>
        <?= import('com://site/docman.category.manage.html', array('category' => $category)) ?>

        <? // Category image ?>
        <? if ($params->show_image && $category->image): ?>
            <?= helper('behavior.thumbnail_modal'); ?>
            <a class="docman_thumbnail thumbnail" href="<?= $category->image_path ?>">
                <img src="<?= $category->image_path ?>" alt="<?= escape($category->title); ?>" />
            </a>
        <? endif ?>

        <? // Category description full ?>
        <? if ($category->description_full && $params->show_description): ?>
            <div class="docman_description">
                <?= prepareText($category->description_full); ?>
            </div>
        <? endif; ?>
    </div>
    <? endif; ?>

    <? // Tables ?>
    <form action="" method="get" class="k-js-grid-controller koowa_table_list">

        <? // Category table ?>
        <? if ($params->show_subcategories && count($subcategories)): ?>

            <? // Category header ?>
            <? if ($category->id && $params->show_categories_header): ?>
                <h3 class="koowa_header koowa_header--bottom_margin">
                    <?= translate('Categories') ?>
                </h3>
            <? endif; ?>

            <? // Table ?>
            <table class="table table-striped koowa_table koowa_table--categories">
                <tbody>
                    <? foreach ($subcategories as $subcategory): ?>
                    <tr>
                        <td>
                            <span class="koowa_header">
                                <? if ($params->show_icon && $subcategory->icon): ?>
                                <span class="koowa_header__item koowa_header__item--image_container">
                                    <a class="iconImage" href="<?= helper('route.category', array('entity' => $subcategory)) ?>">
                                        <?= import('com://site/docman.document.icon.html', array('icon' => $subcategory->icon, 'class' => 'k-icon--size-default')) ?>
                                    </a>
                                </span>
                                <? endif ?>

                                <span class="koowa_header__item">
                                    <span class="koowa_wrapped_content">
                                        <span class="whitespace_preserver">
                                            <a href="<?= helper('route.category', array('entity' => $subcategory)) ?>">
                                                <?= escape($subcategory->title) ?>
                                            </a>

                                            <? // Label locked ?>
                                            <? if ($subcategory->canPerform('edit') && $subcategory->isLockable() && $subcategory->isLocked()): ?>
                                                <span class="label label-warning"><?= translate('Locked'); ?></span>
                                            <? endif; ?>

                                            <? // Label status ?>
                                            <? if (!$subcategory->enabled): ?>
                                                <span class="label label-draft"><?= translate('Draft'); ?></span>
                                            <? endif; ?>

                                            <? // Label owner ?>
                                            <? if ($params->get('show_category_owner_label', 1) && object('user')->getId() == $subcategory->created_by): ?>
                                                <span class="label label-success"><?= translate('Owner'); ?></span>
                                            <? endif; ?>
                                        </span>
                                    </span>
                                </span>
                            </span>
                        </td>

                        <? // Edit area | Import partial template from category view ?>
                        <?= import('com://site/docman.category.manage.html', array('category' => $subcategory, 'redirect' => 'self', 'parent' => 'td', 'parentClass' => 'k-no-wrap')) ?>
                    </tr>
                    <? endforeach; ?>
                </tbody>
            </table>
        <? endif; ?>

        <? // Documents table | Import child template from documents view ?>
        <? if (count($documents) || ($params->show_document_search)): ?>
            <?= import('com://site/docman.documents.table.html') ?>
        <? endif; ?>

        <? // Pagination ?>
        <? if (parameters()->total): ?>
            <?= helper('paginator.pagination', array(
                'show_limit' => (bool) $params->show_document_sort_limit
            )) ?>
        <? endif; ?>

    </form>
</div>
