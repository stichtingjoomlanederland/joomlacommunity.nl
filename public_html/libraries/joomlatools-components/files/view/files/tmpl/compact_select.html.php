<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */
defined('KOOWA') or die;
?>

<?= import('com:files.files.templates_compact.html');?>

<div class="k-table-container" id="files-grid"></div>
<div class="k-loader-container">
    <span class="k-loader k-loader--large"><?= translate('Loading') ?></span>
</div>