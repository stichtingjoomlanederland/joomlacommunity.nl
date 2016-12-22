<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= helper('ui.load'); ?>
<?= helper('behavior.modal'); ?>

<? if ($can_delete): ?>
    <ktml:script src="media://com_docman/js/site/items.js" />
<? endif; ?>

<? // RSS feed ?>
<link href="<?=route('format=rss');?>" rel="alternate" type="application/rss+xml" title="RSS 2.0" />


<div class="docman_table_layout docman_table_layout--filtered_table">

    <? // Toolbar ?>
    <ktml:toolbar type="actionbar">

    <? if ($params->get('show_page_heading')): ?>
    <h1>
        <?= escape($params->get('page_heading')); ?>
    </h1>
    <? endif; ?>

    <? // Table | Import child template from documents view ?>
    <form action="" method="get" class="k-js-grid-controller">

        <? // Document list | Import child template from documents view ?>
        <?= import('com://site/docman.documents.table.html', array(
            'documents' => $documents,
            'params'    => $params,
            'state'     => parameters()
        ))?>

        <? // Pagination ?>
        <? if ($params->show_pagination !== '0' && parameters()->total): ?>
            <?= helper('paginator.pagination', array(
                'show_limit' => (bool) $params->show_document_sort_limit
            )) ?>
        <? endif; ?>

    </form>

</div>
