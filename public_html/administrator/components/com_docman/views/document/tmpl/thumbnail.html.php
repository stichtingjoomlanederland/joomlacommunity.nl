<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die;
// str_replace helps convert the paths before the template filter transform media:// to full path
$options = str_replace('\/', '/', $config->options->toString());
?>


<?= helper('behavior.modal'); ?>


<ktml:script src="media://com_docman/js/modal.js" />
<script>
kQuery(function(){
    new Docman.Modal.Thumbnail(<?= $options ?>);
});
</script>


<div class="thumbnail-picker">
    <div class="thumbnail-controls k-form-group">
        <div class="k-optionlist k-js-thumbnailpicker">
            <div class="k-optionlist__content">
                <input type="radio" name="thumbnailpicker" id="thumbnailpicker0" value="0" class="k-js-thumbnail-none" />
                <label for="thumbnailpicker0"><?= translate('None'); ?></label>

                <input type="radio" name="thumbnailpicker" id="thumbnailpicker1" value="1" class="k-js-thumbnail-custom"
                       data-href="<?= route('option=com_docman&view=files&layout=select&tmpl=koowa&container=docman-images&types[]=image&callback=Docman.Modal.request_map.select_image'); ?>"
                />
                <label for="thumbnailpicker1"><?= translate('Custom'); ?></label>

                <? if($config->allow_automatic): ?>
                    <input type="radio" name="thumbnailpicker" id="thumbnailpicker2" value="2" class="k-js-thumbnail-automatic" />
                    <label for="thumbnailpicker2"><?= translate('Generate automatically'); ?></label>
                <? endif ?>

                <div class="k-faux-focus"></div>
            </div>
        </div>
        <div class="k-js-input-container" style="display: none">
            <input data-type="custom" name="image" id="image" value="<?= escape($config->value); ?>" type="hidden" disabled="disabled">
        </div>
    </div>

    <div class="thumbnail-info">
        <p class="k-is-hidden k-js-alert k-alert k-alert--info automatic-unsupported-format">
            <?= translate('Automatically generated thumbnails are not supported on this file type.'); ?>
        </p>
        <p class="k-is-hidden k-js-alert k-alert k-alert--info automatic-unsupported-location">
            <?= translate('Automatically generated thumbnails are only supported on local files.'); ?>
        </p>
    </div>

    <div class="thumbnail-preview">
        <div class="k-card k-card--rounded" style="width: 132px;">
            <div class="k-card__body">
                <div class="k-card__section k-card__section--small-spacing">
                    <div class="k-ratio-block k-ratio-block--1-to-1">
                        <div class="k-ratio-block__body">
                            <div class="k-ratio-block__centered">
                                <div class="k-js-no-thumbnail">128x128</div>
                                <img class="k-js-yes-thumbnail k-is-hidden" src="" alt="" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="k-card__footer">
                    <button class="k-js-thumbnail-change k-button k-button--block k-button--default k-button--small" type="button"><?= translate('Change'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
