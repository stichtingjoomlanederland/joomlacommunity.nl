<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= helper('ui.load'); ?>
<?= helper('behavior.modal');?>


<? // RSS feed ?>
<link href="<?=route('format=rss');?>" rel="alternate" type="application/rss+xml" title="RSS 2.0" />


<div class="docman_list_layout docman_list_layout--default">

    <? // Page Heading ?>
    <? if ($params->get('show_page_heading')): ?>
    <h1 class="docman_page_heading">
        <?= escape($params->get('page_heading')); ?>
    </h1>
    <? endif; ?>

    <? // Toolbar ?>
    <ktml:toolbar type="actionbar">

    <? // Category ?>
    <? if (($params->show_category_title && $category->title)
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
                    <?= import('com://site/docman.document.icon.html', array('icon' => $category->icon, 'class' => 'k-icon--size-medium')) ?>
                </span>
            <? endif ?>

            <? // Header title ?>
            <? if ($params->show_category_title): ?>
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
            <? endif; ?>
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


    <? // Sub categories ?>
    <? if ($params->show_subcategories && count($subcategories)): ?>
        <? if ($category->id && $params->show_categories_header): ?>
            <div class="docman_block docman_block--top_margin">
                <? // Header ?>
                <h3 class="koowa_header koowa_header--bottom_margin">
                    <?= translate('Categories') ?>
                </h3>
            </div>
        <? endif; ?>

        <? // Categories list ?>
        <?=import('com://site/docman.list.categories.html', array(
            'categories' => $subcategories,
            'params' => $params,
            'config' => $config
        ))?>
    <? endif; ?>


    <? // Documents header & sorting ?>
    <? if (count($documents) || ($params->show_document_search)): ?>
        <div class="docman_block">
            <? if ($params->show_documents_header): ?>
            <h3 class="koowa_header">
                <?= translate('Documents')?>
            </h3>
            <? endif; ?>
        </div>

        <? // Documents & pagination  ?>
        <form action="" method="get" class="k-js-grid-controller">

            <? // Search ?>
            <?= import('com://site/docman.documents.search.html') ?>

            <? // Sorting ?>
            <? if ($params->show_document_sort_limit && count($documents)): ?>
                <div class="docman_sorting form-search">
                    <label for="sort-documents" class="control-label"><?= translate('Order by') ?></label>
                    <?= helper('paginator.sort_documents', array(
                        'sort'      => 'document_sort',
                        'direction' => 'document_direction',
                        'attribs'   => array('class' => 'input-medium', 'id' => 'sort-documents')
                    )); ?>
                </div>
            <? endif; ?>

            <? // Document list | Import child template from documents view ?>
            <?= import('com://site/docman.documents.list.html',array(
                'documents' => $documents,
                'params' => $params
            ))?>

            <? // Pagination  ?>
            <? if (parameters()->total) : ?>
                <?= helper('paginator.pagination', array(
                    'show_limit' => (bool) $params->show_document_sort_limit
                )) ?>
            <? endif; ?>

        </form>
    <? endif; ?>
</div>
