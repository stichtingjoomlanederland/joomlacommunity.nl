<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<?= helper('translator.script', array('strings' => array(
    'Please wait for the upload to finish before saving the document',
    'Please select a file first',
    'Your link should either start with http:// or another protocol',
    'Invalid remote link. This link type is not supported by your server.',
    'Update',
    'Upload'
))); ?>


<?= helper('behavior.modal') ?>

<ktml:script src="media://com_docman/js/document.file.js" />

<script>
    kQuery(function($) {
        new Docman.File({
            el: '.k-js-document-file',
            store: $('.k-js-form-controller').data('controller').store,
            data: {
                remote_streams: <?= json_encode($document->getSchemes()) ?>
            }
        });
    });
</script>

<? /* Load uploader scripts out of Vue.js container */ ?>
<?= helper('com:files.uploader.scripts'); ?>


<div class="k-upload__buttons k-upload__buttons--right js-more-button" style="display: none" <?= JFactory::getApplication()->isAdmin() || $document->canPerform('manage') ? 'data-enabled="1"' : '' ?>>
    <a href="<?= route('option=com_docman&view=files&layout=select&folder=&file=&callback=Docman.onSelectFile'); ?>"
       class="mfp-iframe k-upload__text-button"
       data-k-modal="<?= htmlentities(json_encode(array('mainClass' => 'koowa_dialog_modal'))) ?>"
    ><?= translate('Select existing file') ?></a>
</div>


<div class="k-form-group k-js-document-file">
    <label><?= translate('File settings'); ?></label>
    <div>
        <p v-show="error_message" class="k-form-info  k-color-error">{{error_message}}</p>

        <div v-show="entity.storage_type !== 'remote'">

            <?= helper('com:files.uploader.container', array(
                'container' => 'docman-files',
                'element' => '.docman-uploader',
                'options'   => array(
                    'prevent_duplicates' => false,
                    'multipart_params' => array( // Special case - tmp is always reported empty
                        'overwrite' => $document->isNew() ? 1 : 0
                    ),
                    'duplicate_mode' => $document->isNew() ? 'overwrite' : 'confirm',
                    'url' => route('view=file&plupload=1&routed=1&format=json', false, false)
                )
            )); ?>

            <p><a href="#" @click.prevent="switchToRemote"><?= translate('Enter a URL instead') ?></a></p>
        </div>

        <div v-show="entity.storage_type === 'remote'">
            <input :value="last_remote" @input="updateRemotePath"
                   class="title k-js-remote input-block-level input-group-form-control k-form-control"
                   type="text"
                   maxlength="512"
                   placeholder="http://"
                />
            <p><?= translate('Enter the remote URL in the field above') ?></p>
            <p><a href="#" @click.prevent="switchToFile"><?= translate('Upload a file instead') ?></a></p>
        </div>
    </div>
</div>
