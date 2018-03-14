<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<? if ($params->track_downloads): ?>
    <?= helper('behavior.download_tracker'); ?>
<? endif; ?>

<? if (!empty($can_add)): ?>
    <?= helper('behavior.modal'); ?>
<? endif; ?>

<? foreach ($documents as $document): ?>
    <? // Document | Import child template from document view ?>
    <?= import('com://site/docman.document.document.html', array(
        'document' => $document,
        'params' => $params,
        'heading' => '4',
        'buttonstyle' => 'btn-default',
        'link' => 1,
        'description' => 'summary'
    )) ?>
<? endforeach ?>