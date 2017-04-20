<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<?= helper('translator.script', array('strings' => array(
    'Please wait for the upload to finish before saving the document',
    'Your link should either start with http:// or another protocol',
    'Invalid remote link. This link type is not supported by your server.',
    'Update',
    'Upload'
))); ?>


<?= helper('behavior.modal') ?>


<div class="k-form-group">
    <label><?= translate('File settings'); ?></label>
    <div class="js-document-path" data-current="<?= $document->storage_type ?: 'file' ?>">
        <div class="js-document-type-file" style="display: none;">
            <input type="hidden" id="storage_path_file" name="storage_path_file"
                   data-type="file" data-rule-storage="0" data-rule-upload="0"
                   value="<?= $document->storage_type === 'file' ? $document->storage_path : ''?>" />
            <?
            $folder   = $document->storage ? $document->storage->folder : '';
            $file     = $document->storage ? $document->storage->name : '';
            $size     = $document->storage ? $document->size : 0;
            $location = 'folder='.rawurlencode($folder).'&file='.rawurlencode($file);
            ?>
            <div class="k-upload__buttons k-upload__buttons--right js-more-button" style="display: none" <?= JFactory::getApplication()->isAdmin() || $document->canPerform('manage') ? 'data-enabled="1"' : '' ?>>
                <a href="<?= route('option=com_docman&view=files&layout=select&tmpl=koowa&callback=Docman.onSelectFile&'.$location); ?>"
                   class="mfp-iframe k-upload__text-button"
                   data-k-modal="<?= htmlentities(json_encode(array('mainClass' => 'koowa_dialog_modal'))) ?>"
                ><?= translate('Select existing file') ?></a>
            </div>

            <?= helper('com:files.uploader.container', array(
                'container' => 'docman-files',
                'element' => '.docman-uploader',
                'options'   => array(
                    'prevent_duplicates' => false,
                    'url' => route('view=file&plupload=1&routed=1&format=json', false, false)
                )
            )); ?>

            <script>
                kQuery(function($) {
                    var folder, name, size;

                    folder = <?= json_encode($folder); ?>;
                    name   = <?= json_encode($file); ?>;
                    size   = <?= json_encode($size); ?>;


                    // pseudo-select for docman form
                    $('.docman-uploader').on('uploader:ready', function() {
                        if (folder) {
                            Docman.setSelectedFolder(folder);
                        }

                        if (name && name !== '') {
                            Docman.setSelectedFile(name, size);
                        }
                    })
                });

            </script>
            <p><a href="#" data-switch="remote"><?= translate('Enter a URL instead') ?></a></p>
        </div>

        <div class="js-document-type-remote" style="display: none;">
            <input data-rule-streamwrapper="0"
                   data-rule-storage="0"
                   data-rule-scheme="0"
                   class="title input-block-level input-group-form-control"
                   data-type="remote"
                   id="storage_path_remote"
                   type="text"
                   maxlength="512"
                   placeholder="http://"
                   data-streams="<?= htmlentities(json_encode($document->getSchemes())); ?>"
                   name="storage_path_remote"
                   value="<?= escape($document->storage_type === 'remote' ? $document->storage_path : ''); ?>"
                />
            <p><?= translate('Enter the remote URL in the field above') ?></p>
            <p><a href="#" data-switch="file"><?= translate('Upload a file instead') ?></a></p>
        </div>
    </div>
</div>
