<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<? // Loading tree behavior ?>
<?= helper('behavior.category_tree', array(
    'element'  => '.k-js-category-tree',
    'selected' => parameters()->parent_id,
    'options'  => array(
        'state' => 'parent_id'
    ),
    'state'     => array(
        'sort'  => parameters()->sort
    )
))
?>


<div class="k-sidebar-left k-js-sidebar-left">

    <!-- Navigation -->
    <div class="k-sidebar-item">
        <ktml:toolbar type="menubar">
    </div>

    <? // Do not display if no category has been created yet ?>
    <? if ($category_count > 0): ?>
        <!-- Category tree -->
        <div class="k-sidebar-item k-sidebar-item--flex">
            <div class="k-sidebar-item__header">
                <?= translate('Categories'); ?>
            </div>
            <div class="k-tree k-js-category-tree">
                <div class="k-sidebar-item__content k-sidebar-item__content--horizontal">
                    <?= translate('Loading') ?>
                </div>
            </div><!-- k-tree -->
        </div>

        <!-- Filters -->
        <div class="k-sidebar-item k-js-sidebar-toggle-item">
            <div class="k-sidebar-item__header">
                <?= translate('Quick filters'); ?>
            </div>
            <ul class="k-list">
                <li class="<?= parameters()->created_by == object('user')->getId() ? 'k-is-active' : ''; ?>">
                    <a href="<?= route('parent_id=&created_by='.(parameters()->created_by == 0 ? object('user')->getId() : '')) ?>">
                        <span class="k-icon-person" aria-hidden="true"></span>
                        <?= translate('My Categories') ?>
                    </a>
                </li>
            </ul>
        </div>
    <? endif; ?>

</div><!-- .k-sidebar-left -->
