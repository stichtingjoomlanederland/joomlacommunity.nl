<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<? if ($document->isDocument()): ?>

    <?= import('com://site/docman.document.document_document.html') ?>

<? elseif ($document->isArchive()): ?>

    <?= import('com://site/docman.document.document_archive.html') ?>

<? elseif ($document->isImage()): ?>

    <?= import('com://site/docman.document.document_image.html') ?>

<? elseif ($document->isVideo()): ?>

    <?= import('com://site/docman.document.document_video.html') ?>

<? elseif ($document->isAudio()): ?>

    <?= import('com://site/docman.document.document_audio.html') ?>

<? elseif ($document->isExecutable()): ?>

    <?= import('com://site/docman.document.document_executable.html') ?>

<? else: ?>

    <?= import('com://site/docman.document.document_default.html') ?>

<? endif; ?>
