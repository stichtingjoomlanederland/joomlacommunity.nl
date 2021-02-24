<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<ktml:style src="media://com_docman/css/site.css" />

<? // Add download tracking code ?>
<? if (is_array($link->attributes) && strpos($link->attributes['class'], 'docman_track_download') !== false): ?>
    <?= helper('com://site/docman.behavior.download_tracker'); ?>
<? endif; ?>


<a <?= helper('behavior.buildAttributes', $link->attributes); ?>>


<? // Add icon ?>
<? if ($entity->icon && (!isset($show_icon) || $show_icon !== false)): ?>
    <?= import('com://site/docman.document.icon.html', array(
        'icon'  => $entity->icon,
        'class' => substr($entity->icon, 0, 5) === 'icon:' ? ' k-icon--size-large' : ' '
    )); ?>
<? endif; ?>


<?= $link->text; ?>


<? // Add document size ?>
<? if ($entity->size && (!isset($show_size) || $show_size !== false)): ?>
<span>(<!--
--><?= helper('com://admin/docman.string.humanize_filesize', array('size' => $entity->size)) ?><!--
-->)</span>
<? endif; ?>


</a>