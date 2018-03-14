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

<div class="docman_list_layout docman_list_layout--filtered_list">

    <? // Toolbar ?>
    <ktml:toolbar type="actionbar">

    <? // Page Heading ?>
    <? if ($params->get('show_page_heading')): ?>
    <h1 class="docman_page_heading">
        <?= escape($params->get('page_heading')); ?>
    </h1>
    <? endif; ?>

    <? // Documents & pagination  ?>
    <form action="" method="get" class="k-js-grid-controller">

        <? // Search ?>
        <?= import('com://site/docman.documents.search.html') ?>


        <? // Sorting ?>
        <? if ($params->show_document_sort_limit && count($documents)): ?>
        <div class="docman_block">
            <div class="docman_sorting form-search">
                <label for="sort-documents" class="control-label"><?= translate('Order by') ?></label>
                <?= helper('paginator.sort_documents', array(
                    'attribs'   => array(
                      'class' => 'input-medium',
                      'id' => 'sort-documents'
                    )
                )); ?>
            </div>
        </div>
        <? endif; ?>

        <? if (count($documents)): ?>

            <? // Document list | Import child template from documents view ?>
            <?= import('com://site/docman.documents.list.html', array(
                'documents' => $documents,
                'params' => $params
            ))?>

            <? // Pagination ?>
            <? if ($params->show_pagination !== '0' && parameters()->total): ?>
                <div class="k-table-pagination">
                <?= helper('paginator.pagination', array(
                    'show_limit' => (bool) $params->show_document_sort_limit
                )) ?>
                </div>
            <? endif; ?>

        <? endif; ?>


    </form>
</div>
