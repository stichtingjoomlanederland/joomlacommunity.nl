<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<div class="k-flex-wrapper">

    <?= import('com://admin/docman.doclink.no_selection.html'); ?>
    <?= import('com://admin/docman.doclink.no_documents.html'); ?>

    <div class="k-table-container k-js-doclink-table-documents k-js-doclink-table-state" style="display: none">
        <div class="k-table">
            <table id="document_list">
                <thead>
                <tr>
                    <th data-name="title" class="footable-sortable">
                        <a href="#"><?= translate('Title'); ?> <span class="footable-sort-indicator k-icon-sort-ascending"></span></a>
                    </th>
                    <th data-name="access_title" class="footable-sortable" width="1%">
                        <a href="#"><?= translate('Access'); ?> <span class="footable-sort-indicator k-icon-sort-ascending"></span></a>
                    </th>
                    <th data-name="created_on" class="footable-sortable" data-sort-initial="descending" width="1%">
                        <a href="#"><?= translate('Date'); ?> <span class="footable-sort-indicator k-icon-sort-ascending"></span></a>
                    </th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="k-js-doclink-spinner k-loader-container k-is-hidden">
        <span class="k-loader k-loader--large"><?= translate('Loading') ?></span>
    </div>

</div>
