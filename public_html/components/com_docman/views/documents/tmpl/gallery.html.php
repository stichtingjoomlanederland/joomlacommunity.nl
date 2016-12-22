<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= helper('ui.load'); ?>
<?= helper('behavior.jquery'); ?>
<?= helper('behavior.modal'); ?>

<? if ($params->document_title_link === 'download'): ?>
    <?= helper('behavior.photoswipe'); ?>
<? endif ?>

<? if ($params->track_downloads): ?>
    <?= helper('behavior.download_tracker'); ?>
<? endif; ?>

<? if (!empty($can_add)): ?>
    <?= helper('behavior.modal'); ?>
<? endif; ?>


<ktml:script src="media://com_docman/js/site/gallery.js" />
<ktml:script src="media://com_docman/js/site/items.js" />

<script>
    kQuery(function($) {

        var documentsGallery = $('.koowa_media_wrapper--documents'),
            categoriesGallery = $('.koowa_media_wrapper--categories'),
            itemWidth = parseInt($('.koowa_media_wrapper--documents .koowa_media__item').css('width'));

        if ( categoriesGallery ) {
            categoriesGallery.simpleGallery({
                item: {
                    'width': itemWidth
                }
            });
        }

        if ( documentsGallery ) {
            documentsGallery.simpleGallery({
                item: {
                    'width': itemWidth
                }
            });
        }
    });
</script>

<? // RSS feed ?>
<link href="<?=route('format=rss');?>" rel="alternate" type="application/rss+xml" title="RSS 2.0" />


<div itemprop="mainContentOfPage" itemscope itemtype="http://schema.org/ImageGallery">

    <? // Page Heading ?>
    <? if ($params->get('show_page_heading')): ?>
        <h1 class="docman_page_heading">
            <?= escape($params->get('page_heading')); ?>
        </h1>
    <? endif; ?>

    <? // Toolbar ?>
    <ktml:toolbar type="actionbar">


    <? // Category ?>
    <? if (isset($category) &&
        (($params->show_category_title && $category->title)
        || ($params->show_image && $category->image)
        || ($category->description_full && $params->show_description))
    ): ?>
    <div class="docman_category">

        <? // Header ?>
        <? if ($params->show_category_title && $category->title): ?>
        <h3 class="koowa_header">
            <? // Header image ?>
            <? if ($params->show_icon && $category->icon): ?>
            <span class="koowa_header__item koowa_header__item--image_container">
                <?= import('com://site/docman.document.icon.html', array('icon' => $category->icon == 'folder' ? 'image' : $category->icon, 'class' => 'k-icon--size-default')) ?>
            </span>
            <? endif ?>

            <? // Header title ?>
            <? if ($params->show_category_title): ?>
            <span class="koowa_header__item">
                <span class="koowa_wrapped_content">
                    <span class="whitespace_preserver">
                        <?= escape($category->title); ?>
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


    <? if( count($subcategories) || count($documents) || $params->show_document_search ): ?>

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

            <div class="koowa_media--gallery">
                <div class="koowa_media_wrapper koowa_media_wrapper--categories">
                    <div class="koowa_media_contents">
                        <?php // this comment below must stay ?>
                        <div class="koowa_media"><!--
                            <? foreach($subcategories as $category): ?>
                         --><div class="koowa_media__item">
                                <div class="koowa_media__item__content">
                                    <?= import('com://site/docman.category.gallery.html', array('category' => $category)) ?>
                                    <?= import('com://site/docman.category.manage.html', array('category' => $category, 'redirect' => 'self', 'parent' => 'div', 'parentClass' => 'koowa_media__item__options')) ?>
                                </div>
                            </div><!--
                            <? endforeach ?>
                     --></div>
                    </div>
                </div>
                <div class="koowa_media_wrapper koowa_media_wrapper--documents">
                    <div class="koowa_media_contents">
                        <?php // this comment below must stay ?>
                        <div class="koowa_media"><!--
                            <? $count = 0; ?>
                            <? foreach ($documents as $document): ?>
                         --><div class="koowa_media__item" itemscope itemtype="http://schema.org/ImageObject">
                                <div class="koowa_media__item__content document">
                                    <?= import('com://site/docman.document.gallery.html', array(
                                        'document' => $document,
                                        'params' => $params,
                                        'count' => $count
                                    )) ?>
                                </div>
                            </div><!--
                            <? $count++; ?>
                            <? endforeach ?>
                     --></div>
                    </div>
                </div>
            </div>

            <? // Pagination ?>
            <? if ($params->show_pagination !== '0' && parameters()->total): ?>
                <?= helper('paginator.pagination', array(
                    'show_limit' => (bool) $params->show_document_sort_limit
                )) ?>
            <? endif; ?>

        </form>

    <? endif; ?>
</div>
