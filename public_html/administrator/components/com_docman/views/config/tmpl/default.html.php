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


<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.validator'); ?>
<?= helper('behavior.modal'); ?>


<? // Setting up 'translations' to be used in JavaScript ?>
<?= helper('translator.script', array('strings' => array(
    'Folder names can only contain letters, numbers, dash, underscore or colons',
    'Audio files',
    'Archive files',
    'Documents',
    'Images',
    'Video files',
    'Add another extension...'
))); ?>


<? // Loading JavaScript ?>
<ktml:script src="media://com_docman/js/jquery.tagsinput.js" />
<ktml:script src="media://com_docman/js/admin/config.default.js" />


<? if (!$thumbnails_available): ?>
    <script>
        kQuery(function($) {
            $('input[name="thumbnails"]').attr('disabled', 'disabled');
            $('input[name="thumbnails"][value="0"]').attr('checked', 'checked');
        });
    </script>
<? endif ?>


<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Overview -->
    <div class="k-content-wrapper">

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Toolbar -->
            <ktml:toolbar type="actionbar">

            <!-- Component wrapper -->
            <div class="k-component-wrapper">

                <!-- Component -->
                <form class="k-component k-js-component k-js-form-controller" action="" method="post">

                    <!-- Container -->
                    <div class="k-container">

                        <!-- Main information -->
                        <div class="k-container__main">

                            <fieldset>
                                <div class="k-form-group">
                                   <label for="document_path"><?= translate('Store files in') ?></label>
                                    <div class="k-input-group">
                                        <input disabled required data-rule-storagepath="0" class="k-form-control" type="text"
                                               value="<?= escape($config->document_path) ?>" id="document_path" name="document_path" />
                                        <div class="k-input-group__button">
                                            <button class="k-button k-button--default edit_document_path" type="button">
                                                <?= translate('Edit'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="k-form-group">
                                    <label for="maximum_size"><?= translate('File size limit');?></label>
                                    <div class="k-input-group">
                                        <label class="k-input-group__addon file_size_checkbox">
                                            <input type="checkbox" <?= $config->maximum_size == 0 ? 'checked' : '' ?> />
                                            <?= translate('Unlimited') ?>
                                        </label>
                                        <input class="k-form-control" type="text" id="maximum_size"
                                               value="<?= floor($config->maximum_size/1048576); ?>"
                                               data-maximum="<?= $upload_max_filesize; ?>" />
                                        <span class="k-input-group__addon">
                                            <?= translate('MB'); ?>
                                        </span>
                                    </div>
                                    <p class="k-form-info">
                                        <?= translate('File size limit message', array(
                                            'link' => 'http://www.joomlatools.com/support/forums/topic/3369-does-docman-have-any-filesize-limitations',
                                            'size' => floor($upload_max_filesize/1048576)-1)); ?>
                                    </p>
                                </div>

                                <div class="k-form-group">
                                    <label><?= translate('Create thumbnails from uploaded images');?></label>
                                    <?= helper('select.booleanlist', array('name' => 'thumbnails', 'selected' => $config->thumbnails)); ?>
                                    <? if (!$thumbnails_available): ?>
                                        <p class="k-form-info"><?=translate('DOCman requires GD to be installed on your server for generating thumbnails')?></p>
                                    <? endif ?>
                                </div>

                            </fieldset>

                            <fieldset>

                                <legend><?= translate('Automatic monitoring'); ?> <span class="label label-warning">BETA</span></legend>
                                <div class="k-well">
                                    <?= translate('Automatic monitoring explanation'); ?>
                                </div>
                                <div class="k-form-group">
                                    <label><?= translate('Automatically create a category for new folders') ?></label>
                                    <div><?= helper('select.booleanlist', array('name' => 'automatic_category_creation', 'selected' => $config->automatic_category_creation)); ?></div>
                                    <p class="k-form-info"><?=translate('The new category will inherit settings such as permissions and owner from the parent folder category')?></p>
                                </div>

                                <div class="k-form-group">
                                    <label><?= translate('Automatically create a document for uploaded files') ?></label>
                                    <div><?= helper('select.booleanlist', array('name' => 'automatic_document_creation', 'selected' => $config->automatic_document_creation)); ?></div>
                                    <p class="k-form-info"><?=translate('The new document will inherit settings such as permissions and owner from the folder category')?></p>
                                </div>

                                <div class="k-form-group">
                                    <label><?= translate('Use human readable titles for created categories and documents'); ?></label>
                                    <div><?= helper('select.booleanlist', array('name' => 'automatic_humanized_titles', 'selected' => $config->automatic_humanized_titles)); ?></div>
                                    <p class="k-form-info">(document-2013-07-08.pdf &raquo; Document 2013 07 08)</p>
                                </div>

                                <div class="k-form-group">
                                    <label><?= translate('Default owner for root folders'); ?></label>
                                    <?= helper('listbox.users', array(
                                        'name' => 'default_owner',
                                        'selected' => $config->default_owner,
                                        'deselect' => false,
                                    )) ?>
                                    <p class="k-form-info"><?=translate('A default owner needs to be selected for root folders since they do not have a parent to inherit from')?></p>
                                </div>

                            </fieldset>

                            <fieldset>

                                <legend><?= translate('Allowed file extensions'); ?></legend>

                                <div style="display: none"  class="k-inline-form-group k-js-extension-preset">
                                    <p class="k-static-form-label k-js-extension-preset-label"></p>
                                    <div class="k-button-group">
                                        <button type="button" class="k-js-add k-button k-button--default k-button--tiny">
                                            <span class="k-icon-plus" aria-hidden="true"></span>
                                            <span class="k-visually-hidden"><?= translate('Plus icon') ?></span>
                                        </button>
                                        <button type="button" class="k-js-remove k-button k-button--default k-button--tiny">
                                            <span class="k-icon-minus" aria-hidden="true"></span>
                                            <span class="k-visually-hidden"><?= translate('Minus icon') ?></span>
                                        </button>
                                    </div>
                                </div><!-- .k-inline-form-group -->

                                <div class="k-form-group">
                                    <label for="allowed_extensions_tag"><?= translate('Select from presets'); ?></label>
                                    <div id="extension_groups" class="k-js-extension-groups extension-groups"></div>
                                </div>

                                <div class="k-form-group">
                                    <input type="text" class="k-form-control" name="allowed_extensions" id="allowed_extensions"
                                           value="<?= implode(',', KObjectConfig::unbox($config->allowed_extensions)); ?>"
                                           data-filetypes="<?= htmlentities(json_encode($filetypes)); ?>" />
                                </div>

                            </fieldset>

                        </div><!-- .k-container__main -->

                        <!-- Other information -->
                        <div class="k-container__sub">

                            <fieldset class="k-form-block">

                                <div class="k-form-block__header">
                                    <?= translate('Global permissions') ?>
                                </div>

                                <div class="k-form-block__content">

                                    <div class="k-form-group">
                                        <label><?= translate('Users can edit their own documents and categories');?></label>
                                        <?= helper('select.booleanlist', array('name' => 'can_edit_own', 'selected' => $config->can_edit_own)); ?>
                                    </div>

                                    <div class="k-form-group">
                                        <label><?= translate('Users can delete their own documents and categories');?></label>
                                        <?= helper('select.booleanlist', array('name' => 'can_delete_own', 'selected' => $config->can_delete_own)); ?>
                                    </div>

                                    <div class="control-group">
                                        <label><?= translate('Users can add new tags in document form');?></label>
                                        <?= helper('select.booleanlist', array('name' => 'can_create_tag', 'selected' => $config->can_create_tag)); ?>
                                    </div>

                                </div>

                            </fieldset>

                            <fieldset class="k-form-block">

                                <div class="k-form-block__header">
                                    Joomlatools Connect
                                </div>

                                <div class="k-form-block__content">
                                    <? if (!$connect_support): ?>
                                        <p class="k-alert k-alert--info">
                                            <?= translate('Document scan requires connect', ['link' => 'https://www.joomlatools.com/connect/']); ?>
                                        </p>
                                    <? endif ?>

                                    <div class="k-form-group">
                                        <p>
                                            <a class="k-button k-button--default <?= !$connect_support ? 'k-is-disabled' : '' ?>"
                                               <?= $connect_support ? 'href="'.route('view=script&script=scan').'"' : '' ?>
                                            >
                                                <?= translate('Scan all documents')?>
                                            </a>
                                        </p>
                                        <p class="k-form-info">
                                            <?= translate('Scan all documents info'); ?>
                                        </p>
                                    </div>
                                </div>

                            </fieldset>

                            <fieldset class="k-form-block">

                                <div class="k-form-block__header">
                                    <?= translate('Migrate');?>
                                </div>

                                <div class="k-form-block__content">

                                    <div class="k-form-group">
                                        <p>
                                            <a class="k-button k-button--default" href="<?= route('view=export') ?>">
                                                <?= translate('Export DOCman data')?>
                                            </a>
                                        </p>
                                        <p class="k-form-info">
                                            <?= translate('Export DOCman data for backup or site migration.'); ?>
                                        </p>

                                        <p>
                                            <a class="k-button k-button--default" href="<?= route('view=import') ?>">
                                                <?= translate('Import from ZIP file')?>
                                            </a>
                                        </p>
                                        <p class="k-form-info">
                                            <?= translate('Import a DOCman export ZIP file'); ?>
                                        </p>
                                    </div>
                                </div>

                            </fieldset>

                            <fieldset class="k-form-block">

                                <div class="k-form-block__header">
                                    <?= translate('Action');?>
                                </div>

                                <div class="k-form-block__content">

                                    <div class="k-form-group">
                                        <p>
                                            <a class="k-button k-button--default" id="advanced-permissions-toggle" href="#advanced-permissions">
                                                <?= translate('Change action permissions')?>
                                            </a>
                                        </p>
                                        <p class="k-form-info k-color-error">
                                            <?= translate('For advanced use only'); ?>
                                        </p>
                                        <p class="k-form-info">
                                            <?= translate('If you would like to restrict actions like downloading a document, editing a category based on the user groups, you can use the Advanced Permissions screen.'); ?>
                                        </p>
                                    </div>
                                </div>

                                <?= import('modal_permissions.html'); ?>

                            </fieldset>

                        </div><!-- .k-container__sub -->

                    </div><!-- .k-container -->

                </form><!-- .k-component -->

            </div><!-- .k-component-wrapper -->

        </div><!-- .k-content -->

    </div><!-- .k-content-wrapper -->

</div><!-- .k-wrapper -->
