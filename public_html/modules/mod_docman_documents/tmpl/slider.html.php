<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2017 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= helper('ui.load', array(
    'package' => 'docman',
    'wrapper' => false
)); ?>

<?= helper('com:docman.slider.load', [
    'selector' => '.mod_docman--slider',
    'options'  => $params->slider_options,
]) ?>

<? if (parameters()->total == 0): ?>
    <p class="alert alert-info">
        <?= translate('You do not have any image documents yet.'); ?>
    </p>
<? else: ?>

<div class="koowa_media--slider mod_docman--slider">
	<div class="splide__track">
		<div class="splide__list">
            <? foreach ($documents as $document): ?>
                <? if ($document->isPreviewableImage()): echo ($document->title.$document->image_download_path); ?>
                    <div class="splide__slide">
                        <img
                            <? if ($params->slider_options->lazyLoad == 'nearby' || $params->slider_options->lazyLoad == 'sequential'): ?>
                            data-splide-lazy="<?= $document->image_path ?: $document->image_download_path ?>"
                            <? else: ?>
                            src="<?= $document->image_path ?: $document->image_download_path ?>"
                            <? endif ?>
                            alt="<?= $document->title ?>"
                        />
                    </div>
                <? endif ?>
            <? endforeach ?>
		</div>
	</div>
</div>

<? endif; ?>