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
                    <th width="1%" class="k-table-data--form">
                        <?= helper('grid.checkall')?>
                    </th>
                    <th width="1%" class="k-table-data--toggle" data-toggle="true"></th>
                    <th>
                        <?= helper('grid.sort', array('column' => 'title', 'title' => 'Title')); ?>
                    </th>
                    <th width="5%" data-hide="phone,tablet,desktop">
                       <?= helper('grid.sort', array('column' => 'count', 'title'  => '<span class="k-icon-documents" aria-hidden="true"></span><span class="k-visually-hidden">'.translate('Document count').'</span>', 'url' => route())); ?>
                    </th>
                    <th width="5%" data-hide="phone,tablet">
                        <?= helper('grid.sort', array('column' => 'created_on', 'title' => 'Date')); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
            <? foreach($tags as $tag): ?>
                <tr>
                    <td class="k-table-data--form">
                        <?= helper('grid.checkbox', array('entity' => $tag))?>
                    </td>
                    <td class="k-table-data--toggle"></td>
                    <td class="k-table-data--ellipsis">
                        <a data-k-tooltip='{"container":".k-ui-container","delay":{"show":500,"hide":50}}' data-original-title="<?= translate('Edit {title}', array('title' => escape($tag->title))); ?>" href="<?= route('view=tag&id='.$tag->id); ?>">
                            <?= escape($tag->title); ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= route('view=documents&tag[]='.$tag->slug)?>">
                            <?= escape($tag->count); ?>
                        </a>
                    </td>
                    <td class="k-table-data--nowrap">
                        <?= helper('date.format', array('date' => $tag->created_on, 'format' => 'd M Y')); ?>
                    </td>
                </tr>
            <? endforeach ?>
            </tbody>
        </table>

    </div><!-- .k-table -->

    <? if (count($tags)): ?>
        <div class="k-table-pagination">
            <?= helper('paginator.pagination') ?>
        </div><!-- .k-table-pagination -->
    <? endif; ?>

</div><!-- .k-table-container -->
