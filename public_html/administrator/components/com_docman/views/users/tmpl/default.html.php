<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<? // Loading necessary Markup, CSS and JS ?>
<?= helper('ui.load') ?>
<?= helper('behavior.modal') ?>
<?= helper('behavior.tooltip') ?>


<? // Loading JavaScript ?>
<ktml:script src="media://com_docman/js/toolbar.js" />
<ktml:script src="media://com_docman/js/admin/users.default.js"/>


<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Title when sidebar is invisible -->
    <ktml:toolbar type="titlebar" title="COM_DOCMAN_SUBMENU_USERS" mobile>

    <!-- Overview -->
    <div class="k-content-wrapper">

        <!-- Sidebar -->
        <?= import('default_sidebar.html'); ?>

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Toolbar -->
            <ktml:toolbar type="actionbar">

            <!-- Component wrapper -->
            <div class="k-component-wrapper">

                <!-- Component -->
                <form class="k-component k-js-component k-js-grid-controller " action="" method="get">

                    <!-- Scopebar -->
                    <?= import('default_scopebar.html'); ?>

                    <!-- Table -->
                    <?= import('default_table.html'); ?>

                </form><!-- .k-component -->

            </div><!-- .k-component-wrapper -->

        </div><!-- k-content -->

    </div><!-- .k-content-wrapper -->

</div><!-- .k-wrapper -->

<div class="k-dynamic-content-holder">
    <?= import('modal_assign.html') ?>
</div>
