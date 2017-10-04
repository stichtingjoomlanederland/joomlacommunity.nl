<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= helper('ui.load'); ?>

<div class="docman_list_layout docman_list_layout--tree">
    <div class="docman_list__sidebar">
        <div class="k-tree k-js-category-tree">
            <div class="k-sidebar-item__content k-sidebar-item__content--horizontal">
                <?= translate('Loading') ?>
            </div>
        </div>
    </div>
    <div class="docman_list__content">
        <ktml:content>
    </div>
</div>

<?= helper('behavior.category_tree_site', array(
    'element' => '.k-js-category-tree',
    'selected' => $selected,
    'state' => $state
)) ?>

<?= helper('behavior.sidebar', array(
    'sidebar'   => '#documents-sidebar',
    'target'    => '.k-js-category-tree',
    'affix'     => false,
    'minHeight' => 100
)) ?>

