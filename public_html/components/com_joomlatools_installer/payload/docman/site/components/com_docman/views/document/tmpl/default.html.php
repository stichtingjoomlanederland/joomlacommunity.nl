<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= helper('ui.load'); ?>
<?= helper('behavior.thumbnail_modal'); ?>

<? if ($params->track_downloads): ?>
    <?= helper('behavior.download_tracker'); ?>
<? endif; ?>

<div class="docman_document_layout">

    <? // Page Heading ?>
    <? if ($params->get('show_page_heading')): ?>
    <h1 class="docman_page_heading">
        <?= escape($params->get('page_heading')); ?>
    </h1>
    <? endif; ?>

    <? // Document | Import partial template from document view ?>
    <?= import('com://site/docman.document.document.html', array(
        'document' => $document,
        'params'   => $params,
        'heading'  => '1',
        'buttonstyle' => 'btn-primary',
        'link'     => 1
    )) ?>

</div>