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
                    <?= helper('grid.sort', array('column' => 'custom', 'title' => '<span class="k-icon-move"></span>', 'direction' => 'desc')) ?>
                </th>
                <th width="1%" class="k-table-data--form">
                    <?= helper('grid.checkall')?>
                </th>
                <th width="1%" class="k-table-data--toggle" data-toggle="true"></th>
                <th>
                    <?= helper('grid.sort', array('column' => 'title', 'title' => 'Title', 'direction' => 'asc')) ?>
                </th>
                <th width="5%" data-hide="phone,tablet,desktop">
                    <span class="k-icon-documents" aria-hidden="true"></span>
                    <span class="k-visually-hidden"><?= translate('Document count')?></span>
                </th>
                <th width="5%" data-hide="phone,tablet">
                    <?= translate('Status') ?>
                </th>
                <th width="5%" data-hide="phone,tablet,desktop">
                    <?= translate('Access')?>
                </th>
                <th width="5%" data-hide="phone,tablet,desktop">
                    <?= translate('Owner')?>
                </th>
                <th width="5%" data-hide="phone,tablet,desktop">
                    <?= helper('grid.sort', array('column' => 'created_on', 'title' => 'Date')); ?>
                </th>
            </tr>
            </thead>
            <tbody data-behavior="orderable" data-params='{"nested":true}'>
            <? foreach($categories as $category):
                $category->isPermissible();
                ?>
                <tr
                    data-level="<?= $category->level ?>"
                    data-item="<?= $category->id ?>"
                    data-parent="<?= $category->getParentId() ?>"
                    data-parents="<?= implode($category->getParentIds(), ' ') ?>"
                    data-ordering="<?= $category->ordering ?>"
                >
                    <td class="k-table-data--icon">
                        <? if(parameters()->sort == 'custom') : ?>
                            <a class="js-sort-handle">
                                <span class="k-positioner k-is-active"></span>
                            </a>
                        <? else: ?>
                            <span data-k-tooltip='{"container":".k-ui-container"}'
                                  data-original-title="<?= translate('Please order by this column first by clicking the column title') ?>">
                            <span class="k-positioner"></span>
                        </span>
                        <? endif; ?>
                    </td>
                    <td class="k-table-data--form">
                        <?= helper('grid.checkbox', array('entity'=> $category, 'attribs' => array(
                            'data-document-count' => $category->document_count,
                            'data-permissions' => htmlentities(json_encode($category->getPermissions()))
                        ))); ?>
                    </td>
                    <td class="k-table-data--toggle"></td>
                    <td width="90%" class="k-table-data--ellipsis k-table__item-level k-table__item-level<?= $category->level;?>">
                        <a class="k-table__item-level__icon-item" data-k-tooltip='{"container":".k-ui-container","delay":{"show":500,"hide":50}}' data-original-title="<?= translate('Edit {title}', array('title' => escape($category->title))); ?>" href="<?= route('view=category&id='.$category->id)?>">
                            <? if (substr($category->icon, 0, 5) === 'icon:'): ?>
                                <img class="k-image-16" src="icon://<?= substr($category->icon, 5) ?>"/>
                            <? else: ?>
                                <span class="k-icon-document-<?= $category->icon; ?>" aria-hidden="true"></span>
                            <? endif ?>
                            <?= escape($category->title) ?>
                        </a>
                    </td>
                    <td>
                        <a data-k-tooltip='{"container":".k-ui-container","delay":{"show":500,"hide":50}}' data-original-title="<?= translate('View documents in this category') ?>" href="<?= route('view=documents&category='.$category->id)?>">
                            <?= $category->document_count; ?>
                        </a>
                    </td>
                    <td>
                        <?= helper('grid.state', array('entity' => $category, 'clickable' => $category->canPerform('edit'))) ?>
                    </td>
                    <td>
                        <?= escape($category->access_title) ?>
                        <? if ($category->access_raw == 0): ?>
                            <small><?= $category->level > 1 ? translate('Inherited') : translate('Default') ?></small>
                        <? endif ?>
                    </td>
                    <td>
                        <div class="k-ellipsis" style="max-width: 150px;">
                            <?= escape($category->getAuthor()->getName()); ?>
                        </div>
                    </td>
                    <td class="k-table-data--nowrap">
                        <?= helper('date.format', array('date' => $category->created_on, 'format' => 'd M Y')); ?>
                    </td>
                </tr>
            <? endforeach ?>
            </tbody>
        </table>
    </div><!-- .k-table -->

    <? if (count($categories)): ?>
        <div class="k-table-pagination">
            <?= helper('paginator.pagination') ?>
        </div><!-- .k-table-pagination -->
    <? endif; ?>

</div><!-- .k-table-container -->
