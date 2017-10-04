<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<!-- Sidebar -->
<div class="k-sidebar-left k-js-sidebar-left">

    <!-- Navigation -->
    <div class="k-sidebar-item">
        <ktml:toolbar type="menubar">
    </div>

    <!-- Folder tree -->
    <div class="k-sidebar-item k-sidebar-item--flex">

        <div class="k-sidebar-item__header">
            <?= translate('Folders'); ?>
        </div>

        <div class="k-tree" id="files-tree">
            <div class="k-sidebar-item__content k-sidebar-item__content--horizontal">
                <?= translate('Loading') ?>
            </div>
        </div>

    </div>

</div><!-- k-sidebar-left -->
