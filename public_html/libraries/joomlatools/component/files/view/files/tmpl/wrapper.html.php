<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/*
 * Wraps around the default views adding toolbars, Bootstrap and CSS files to the page
 */
defined('KOOWA') or die; ?>

<?= helper('ui.load'); ?>

<ktml:module position="submenu">
    <ktml:toolbar type="menubar">
</ktml:module>

<ktml:module position="toolbar">
    <ktml:toolbar type="actionbar" title="Files">
</ktml:module>

<ktml:style src="assets://files/css/files.css" />

<ktml:content>

