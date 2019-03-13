<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<div class="k-table-container">
    <div class="k-table">

        <table class="k-js-responsive-table">
            <thead>
            <tr>
                <th width="1%" class="k-table-data--icon" data-ignore="true" data-hide="phone, tablet">
                    <?= helper('grid.sort', array('column' => 'ordering', 'title' => '<span class="k-icon-move"></span>', 'direction' => 'desc')) ?>
                </th>
                <th width="1%" class="k-table-data--form">
                    <?= helper('grid.checkall')?>
                </th>
                <th width="1%" class="k-table-data--toggle" data-toggle="true"></th>
                <th width="1%" class="k-table-data--icon"></th>
                <th>
                    <?= helper('grid.sort', array('column' => 'title', 'title' => 'Title')); ?>
                </th>
                <th width="5%" data-hide="phone,tablet">
                    <?= helper('grid.sort', array('column' => 'status', 'title' => 'Status')); ?>
                </th>
                <th width="5%" data-hide="phone,tablet,desktop">
                    <?= helper('grid.sort', array('column' => 'access', 'title' => 'Access')); ?>
                </th>
                <th width="5%" data-hide="phone,tablet,desktop">
                    <?= helper('grid.sort', array('column' => 'created_by', 'title' => 'Owner')); ?>
                </th>
                <th width="5%" data-hide="phone,tablet,desktop">
                    <?= helper('grid.sort', array('column' => 'created_on', 'title' => 'Date')); ?>
                </th>
                <? if(!parameters()->category) : ?>
                    <th width="5%" data-hide="phone,tablet">
                        <?= helper('grid.sort', array('column' => 'docman_category_id', 'title' => 'Category')); ?>
                    </th>
                <? endif ?>
                <th width="1%" class="k-table-data--icon" data-hide="phone,tablet,desktop">
                    <?= helper('grid.sort', array('column' => 'hits', 'title' => '<span class="k-icon-cloud-download" aria-hidden="true"></span><span class="k-visually-hidden">'.translate('Download').'</span>')); ?>
                </th>
                <th width="1%" class="k-table-data--icon"></th>
            </tr>
            </thead>
            <tbody <?= parameters()->sort == 'ordering' ? 'data-behavior="orderable"' : '' ?>>
            <? $i = 1;
            foreach ($documents as $document):
                $document->isPermissible();
                $location = false;
                ?>
                <tr
                data-item="<?= $document->id ?>"
                data-ordering="<?= $document->ordering ?>"
                >
                    <td class="k-table-data--icon">
                        <div>
                            <? if(parameters()->sort == 'ordering') : ?>
                                <a class="js-sort-handle">
                                    <span class="k-positioner k-is-active"></span>
                                </a>
                            <? else: ?>
                                <span class="k-positioner"
                                      data-k-tooltip='{"container":".k-ui-container"}'
                                      data-original-title="<?= translate('Please order by this column first by clicking the column title') ?>"></span>
                          <? endif; ?>
                        </div>
                    </td>
                    <td class="k-table-data--form">
                        <?= helper('grid.checkbox', array('entity' => $document, 'attribs' => array(
                            'data-permissions' => htmlentities(json_encode($document->getPermissions()))
                        )))?>
                    </td>
                    <td class="k-table-data--toggle"></td>
                    <td class="k-table-data--icon">
                        <? if (substr($document->icon, 0, 5) === 'icon:'): ?>
                            <span class="koowa_header__image_container">
                                <img src="icon://<?= substr($document->icon, 5) ?>" class="koowa_header__image" />
                            </span>
                        <? else: ?>
                        <span class="k-icon-document-<?= $document->icon; ?>" aria-hidden="true"></span>
                        <? endif ?>
                    </td>
                    <td class="k-table-data--ellipsis">
                        <a data-k-tooltip='{"container":".k-ui-container","delay":{"show":500,"hide":50}}' data-original-title="<?= translate('Edit {title}', array('title' => escape($document->title))); ?>" href="<?= route('view=document&id='.$document->id); ?>">
                            <?= escape($document->title); ?>
                        </a>
                        <? if ($document->storage_type == 'remote') : ?>
                            <? $location = $document->storage_path; ?>
                        <? elseif ($document->storage_type == 'file') : ?>
                            <? $location = $document->storage_path; ?>
                            <? if ($document->size): ?>
                                <span>
                                    <? $location .= ' - '.helper('string.humanize_filesize', array('size' => $document->size)); ?>
                                </span>
                            <? endif; ?>
                        <? endif ?>
                        <? if($location) : ?>
                            <small title="<?= escape($location) ?>">
                                <?= $location ?>
                            </small>
                        <? endif ?>
                    </td>
                    <td>
                        <?= helper('grid.state', array('entity' => $document, 'clickable' => $document->canPerform('edit'))) ?>
                    </td>
                    <td>
                        <?= escape($document->access_title) ?>
                        <? if ($document->access_raw == 0): ?>
                            <br /><small><?= translate('Inherited') ?></small>
                        <? endif ?>
                    </td>
                    <td>
                        <div class="k-ellipsis" style="max-width: 150px;">
                            <?= escape($document->getAuthor()->getName()); ?>
                        </div>
                    </td>
                    <td class="k-table-data--nowrap">
                        <?= helper('date.format', array('date' => $document->created_on, 'format' => 'd M Y')); ?>
                    </td>
                    <? if(!parameters()->category) : ?>
                    <td>
                        <div class="k-ellipsis" style="max-width: 200px;">
                            <?= helper('grid.document_category', array('entity' => $document)) ?>
                        </div>
                    </td>
                    <? endif ?>
                    <td>
                        <?= $document->hits; ?>
                    </td>
                    <td class="k-table-data--icon">
                        <? if ($document->storage_type == 'remote'): ?>
                            <? $location = $document->storage_path; ?>
                        <? else: ?>
                            <? $location = route('view=file&routed=1&container=docman-files&folder='.($document->storage->folder === '.' ? '' : rawurlencode($document->storage->folder)).'&name='.rawurlencode($document->storage->name)); ?>
                        <? endif ?>
                        <a data-k-tooltip='{"container":".k-ui-container","delay":{"show":500,"hide":50}}' data-original-title="Download document" href="<?= $location; ?>" target="_blank">
                            <span class="k-icon-data-transfer-download" aria-hidden="true"></span>
                            <span class="k-visually-hidden"><?= translate('Download'); ?></span>
                        </a>
                    </td>
                </tr>
            <? endforeach; ?>
            </tbody>
        </table>
    </div><!-- .k-table -->

    <? if (count($documents)): ?>
        <div class="k-table-pagination">
            <?= helper('paginator.pagination') ?>
        </div><!-- .k-table-pagination -->
    <? endif; ?>

</div><!-- .k-table-container -->
