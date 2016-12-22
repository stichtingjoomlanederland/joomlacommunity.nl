<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= helper('ui.load'); ?>
<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.validator', array(
    'options' => array(
        'messages' => array(
            'storage_path_file' => array('required' => translate('This field is required')),
            'title'             => array('required' => translate('This field is required'))
        )
    )
)); ?>

<?= helper('translator.script', array('strings' => array(
    'Your link should either start with http:// or another protocol',
    'Invalid remote link. This link type is not supported by your server.'
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

                    <legend><?= translate('Details'); ?></legend>

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

                    <div class="control-group submit_document__document">
                        <ul class="nav nav-tabs">
                            <li>
                                <a href="#" class="upload-method" data-type="file">
                                    <?= translate('Upload a file')?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="upload-method" data-type="remote">
                                    <?= translate('Submit a link')?>
                                </a>
                            </li>
                        </ul>
                        <input type="hidden" name="storage_type" id="storage_type" />
                        <div class="upload-method-box" id="document-remote-path-row">
                            <input data-rule-streamwrapper="0"
                                   data-rule-storage="0"
                                   data-rule-scheme="0"
                                   class="validate-storage submitlink input input-block-level"
                                   data-type="remote"
                                   id="storage_path_remote"
                                   type="text"
                                   size="25"
                                   maxlength="512"
                                   placeholder="http://"
                                   data-streams="<?= htmlentities(json_encode($document->getSchemes())); ?>"
                                   name="storage_path_remote"
                                   value="<?= escape($document->storage_path); ?>"
                                />
                        </div>
                        <div class="form-group upload-method-box" id="document-file-path-row">

                            <div class="k-file-input-container">
                                <div class="k-file-input">
                                    <input class="k-js-file-input" id="file-input" data-multiple-caption="<?= translate('{count} files selected'); ?>" type="file" name="storage_path_file" required />
                                    <label for="file-input">
                                        <span class="k-file-input__button">
                                            <span class="k-icon-cloud-upload" aria-hidden="true"></span>
                                            <?= translate('Choose a file&hellip;'); ?>
                                        </span>
                                        <span class="k-file-input__files"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>

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
            </div>

            <input type="hidden" name="automatic_thumbnail" value="1" />
        </form>
    </div>

    <!-- Toolbar -->
    <ktml:toolbar type="actionbar">

</div>

<script type="text/javascript">
    /*
     Originally written by By Osvaldas Valutis, www.osvaldas.info
     Adapted by Robin Poort, www.robinpoort.com
     Available for use under the MIT License
     */

    kQuery(function($) {
        ( function ( document, window, index )
        {
            var inputs = document.querySelectorAll('.k-js-file-input');
            Array.prototype.forEach.call( inputs, function( input )
            {
                var label	 = input.nextElementSibling,
                    labelVal = label.innerHTML;

                input.addEventListener('change', function( e )
                {
                    var fileName = '';
                    if( this.files && this.files.length > 1 )
                        fileName = ( this.getAttribute('data-multiple-caption') || '' ).replace( '{count}', this.files.length );
                    else
                        fileName = e.target.value.split( '\\' ).pop();

                    if( fileName )
                        label.querySelector('.k-file-input__files').innerHTML = fileName;
                    else
                        label.innerHTML = labelVal;
                });

                // Add class for drop hover
                input.ondragover = function(ev) { this.classList.add('has-drop-focus'); };
                input.ondragleave = function(ev) { this.classList.remove('has-drop-focus'); };
                input.ondragend = function(ev) { this.classList.remove('has-drop-focus'); };
                input.ondrop = function(ev) { this.classList.remove('has-drop-focus'); };

                // Firefox bug fix
                input.addEventListener('focus', function(){ input.classList.add('has-focus'); });
                input.addEventListener('blur', function(){ input.classList.remove('has-focus'); });
            });
        }( document, window, 0 ));
    });
</script>
