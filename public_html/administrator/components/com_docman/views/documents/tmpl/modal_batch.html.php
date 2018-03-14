<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<div id="document-batch-modal" class="k-ui-namespace k-small-inline-modal-holder mfp-hide">
    <div class="k-inline-modal">
        <form class="k-js-batch-form">
            <h3 class="k-inline-modal__title">
                <?= translate('Batch process the selected documents') ?>
            </h3>

            <div class="k-form-group">
                <label><?= translate('Tags'); ?></label>
                <?= helper('listbox.tags', array(
                    'autocreate' => $can_create_tag,
                    'deselect' => true,
                    'prompt'   => translate('- Keep original tags -')
                )) ?>
                <input type="hidden" name="tags_operation" value="append" />
            </div>

            <div class="k-form-group">
                <label><?= translate('Owner');?>:</label>
                <script>
                    function docmanSetOwnerDropdownParent(options) {
                        options.dropdownParent = kQuery('#document-batch-modal');

                        return options;
                    }
                </script>
                <?= helper('listbox.users', array(
                    'name' => 'created_by',
                    'prompt'   => translate('- Keep original owner -'),
                    'attribs'  => array(
                        'id' => 'js-owner-selector'
                    ),
                    'options_callback' => 'docmanSetOwnerDropdownParent'
                ))?>
            </div>

            <div class="k-form-group">
                <button class="k-button k-button--primary" ><?= translate('Save'); ?></button>
            </div>
        </form>
    </div>
</div>
