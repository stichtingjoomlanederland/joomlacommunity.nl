<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */
defined('KOOWA') or die;

$can_upload = isset(parameters()->config['can_upload']) ? parameters()->config['can_upload'] : true;
?>

<?= import('com:files.files.scripts.html'); ?>

<ktml:script src="assets://files/js/files.compact.js" />


<? if ($can_upload): ?>
    <div id="koowa_dialog__file_dialog_upload">
        <?= import('com:files.files.uploader.html', array('multi_selection' => false)); ?>
    </div>
<? endif; ?>
