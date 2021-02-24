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
                <? if ($document->isPreviewableImage()): ?>
                    <div class="splide__slide">
                        <? $path = $document->image_path ?: $document->image_download_path; ?>
                        <img
                            <? if ($params->slider_options->lazyLoad == 'nearby' || $params->slider_options->lazyLoad == 'sequential'): ?>
                            data-splide-lazy="<?= $path ?>"
                            <? else: ?>
                            src="<?= $path ?>"
                            <? endif ?>
                            alt="<?= $document->title ?>"
                        />
                    </div>
                <? elseif ($document->isVideo()): ?>
                    <div class="splide__slide" data-splide-html-video="<?= $document->download_link ?>"></div>
                <? elseif ($document->isYoutube()): ?>
                    <div class="splide__slide" data-splide-youtube="<?= $document->storage->path ?>"></div>
                <? elseif ($document->isVimeo()): ?>
                    <div class="splide__slide" data-splide-vimeo="<?= $document->storage->path ?>"></div>
                <? endif ?>
            <? endforeach ?>
		</div>
	</div>
</div>

<? endif; ?>