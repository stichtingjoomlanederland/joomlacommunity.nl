<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die;
// str_replace helps convert the paths before the template filter transform media:// to full path
$options = str_replace('\/', '/', $config->options->toString());
?>

<ktml:script src="media://com_docman/js/document.scanner.js" />

<script>
kQuery(function($) {
    new Docman.Scanner({
        el: '.k-js-docman-scanner',
        store: $('.k-js-form-controller').data('controller').store,
        data: <?= $options ?>
    });
});
</script>


<div class="k-js-docman-scanner">

    <template v-if="isConnectEnabled">
        <p v-if="isRemote" class="k-form-info  k-color-error">
            <?= translate('Remote links are not searchable'); ?>
        </p>
        <p v-else-if="!entity.storage_path" class="k-form-info">
            <?= translate('Please select a file first'); ?>
        </p>
        <p v-else-if="!isIndexable" class="k-form-info">
            <?= translate('Document type is not searchable'); ?>
        </p>
        <p v-else-if="hasDocumentContents" class="k-form-info">
            <?= translate('Document contents are searchable'); ?>
        </p>
        <p v-else-if="hasPendingScan" class="k-form-info">
            <?= translate('Document is in the queue to be indexed'); ?>
        </p>
        <p v-else-if="entity.isNew || isIndexable" class="k-form-info">
            <?= translate('Document will be scanned after saving'); ?>
        </p>
    </template>
    <template v-else-if="isAdmin">
        <p class="k-form-info k-color-error">
            <?= translate('Document index requires connect', ['link' => 'https://www.joomlatools.com/connect/']); ?>
        </p>
    </template>
    <template v-else>
        <p class="k-form-info k-color-error">
            <?= translate('Document is not indexed'); ?>
        </p>
    </template>
</div>
