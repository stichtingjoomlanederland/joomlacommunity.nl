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


<?= helper('behavior.local_dates'); ?>
<?= helper('behavior.tooltip') ?>
<?= helper('behavior.doclink', array('list' => $pages, 'editor' => $editor)) ?>


<? // Loading JavaScript ?>
<ktml:script src="media://com_docman/js/admin/documents.default.js"/>

<script>
    kQuery(function($) {
        var container = $('.mce-in.mce-panel', window.parent.document);
        if (container.length) {
            container.addClass('k-joomla-modal-override');
        }
    });
</script>


<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Titlebar -->
    <div class="k-title-bar k-title-bar--mobile k-js-title-bar">
        <div class="k-title-bar__heading"><?= translate('Select document'); ?></div>
    </div><!-- .k-titlebar -->

    <!-- Overview -->
    <div class="k-content-wrapper">

        <!-- Sidebar -->
        <?= import('default_sidebar.html'); ?>

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Component wrapper -->
            <div class="k-component-wrapper">

                <!-- Component -->
                <div class="k-component k-js-component">

                    <? // Only show if there are actually menu items available ?>
                    <? if (count($pages)): ?>

                        <!-- Scopebar -->
                        <?= import('default_scopebar.html'); ?>

                        <!-- Table -->
                        <?= import('default_table.html'); ?>

                    <? else: ?>

                        <!-- No pages -->
                        <?= import('no_pages.html'); ?>

                    <? endif; ?>

                </div><!-- .k-component -->

                <? // Only show if there are actually menu items available ?>
                <? if (count($pages)): ?>
                    <!-- Sidebar -->
                    <?= import('default_sidebar_right.html'); ?>
                <? endif; ?>

            </div><!-- .k-component-wrapper -->

        </div><!-- k-content -->

    </div><!-- .k-content-wrapper -->

</div><!-- .k-wrapper -->
