<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<? // Loading necessary Markup, CSS and JS ?>
<?= helper('ui.load', array('package' => 'docman')); ?>
<?= helper('behavior.tooltip') ?>


<? // Loading JavaScript ?>
<script data-inline src="media://com_docman/js/admin/files.default.js" type="text/javascript"></script>


<?= import('templates_modal.html'); ?>


<? // Setting up 'translations' to be used in JavaScript ?>
<?= helper('translator.script', array('strings' => array(
    'Create documents'
))); ?>


<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Title when sidebar is invisible -->
    <ktml:toolbar type="titlebar" title="COM_DOCMAN_SUBMENU_FILES" mobile>

    <!-- Overview -->
    <div class="k-content-wrapper">

        <!-- Sidebar -->
        <?= import('default_sidebar.html'); ?>

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Toolbar -->
            <ktml:toolbar type="actionbar">

            <!-- Content -->
            <ktml:content>

        </div><!-- k-content -->

    </div><!-- .k-content-wrapper -->

</div><!-- .k-wrapper -->
