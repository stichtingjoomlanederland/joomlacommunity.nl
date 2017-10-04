<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= helper('ui.load'); ?>
<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.tooltip') ?>
<?= helper('behavior.validator', array(
    'options' => array(
        'ignore' => '',
        'messages' => array(
            'storage_path_file' => array('required' => translate('This field is required')),
            'title'             => array('required' => translate('This field is required'))
        )
    )
)); ?>

<?= helper('translator.script', array('strings' => array(
    'Please wait for the upload to finish before saving the document',
    'Your link should either start with http:// or another protocol',
    'Invalid remote link. This link type is not supported by your server.',
    'Update',
    'Upload'
))); ?>

<ktml:script src="media://com_docman/js/site/submit.default.js" />


<div class="docman_submit_layout">

    <? // Header ?>
    <? if ($params->get('show_page_heading')): ?>
        <h1>
            <?= escape($params->get('page_heading')); ?>
        </h1>
    <? endif; ?>

    <? // Form ?>
    <div class="koowa_form">
        <form action="" method="post" class="k-js-form-controller" enctype="multipart/form-data">
            <div class="k-ui-namespace boxed">
                <fieldset class="form-horizontal">

                    <div class="control-group">
                        <label><?= translate('File') ?></label>
                        <input type="hidden" id="storage_path_file" required value="" />

                        <?= helper('com:files.uploader.container', array(
                            'container' => 'docman-files',
                            'element' => '.docman-uploader',
                            'attributes' => array(
                                'style' => 'margin-bottom: 0'
                            ),
                            'options'   => array(
                                'check_duplicates' => false,
                                'multi_selection' => false,
                                'autostart' => false,
                                'url' => route('view=file&plupload=1&routed=1&format=json', false, false)
                            )
                        )); ?>
                    </div>


                    <div class="control-group submit_document__title_field">
                        <label for="title_field"><?= translate('Title'); ?></label>
                        <input required
                               class="input input-block-level"
                               id="title_field"
                               type="text"
                               name="title"
                               maxlength="255"
                               placeholder="<?= translate('Title') ?>"
                               value="<?= escape($document->title); ?>" />
                    </div>

                    <? if ($show_categories): ?>
                    <div class="control-group submit_document__category_field">
                        <label><?= translate('Category') ?></label>
                        <?=
                        helper('listbox.categories', array(
                            'name'   => 'docman_category_id',
                            'deselect' => false,
                            'filter' => array(
                                'parent_id'    => $categories,
                                'include_self' => true,
                                'level'        => $level,
                                'access'       => object('user')->getRoles(),
                                'current_user' => object('user')->getId(),
                                'enabled'      => true
                            ))) ?>
                    </div>
                    <? endif ?>
                </fieldset>

                <? if ($params->get('show_description', 0)): ?>
                <fieldset>
                    <legend><?= translate('Description'); ?></legend>
                    <?= helper('editor.display', array(
                        'name'    => 'description',
                        'value' => $document->description,
                        'width'   => '100%', 'height' => '200',
                        'rows'    => '20',
                        'buttons' => null
                    )); ?>
                </fieldset>
                <? endif ?>
            </div>

            <input type="hidden" name="automatic_thumbnail" value="1" />
        </form>
    </div>

    <!-- Toolbar -->
    <ktml:toolbar type="actionbar">

</div>
