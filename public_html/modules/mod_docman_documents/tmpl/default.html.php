<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<?= helper('ui.load', array(
    'package' => 'docman',
    'wrapper' => false
)); ?>


<? // No documents message if the "Show only user's documents" parameter is enabled ?>
<? if (parameters()->total == 0): if ($params->own): ?>
    <p class="alert alert-info">
        <?= translate('You do not have any documents yet.'); ?>
    </p>
<? endif; else: ?>

<? if ($params->track_downloads): ?>
    <?= helper('com://admin/docman.behavior.download_tracker'); ?>
<? endif; ?>

<div class="k-ui-namespace">
    <div class="mod_docman mod_docman--documents">
        <ul<?= $params->show_icon ? ' class="mod_docman_icons"' :'' ?>>
        <? foreach ($documents as $document): ?>
            <li class="module_document">

                <div class="koowa_header">
                    <? // Header icon/image ?>
                    <? if ($document->icon && $params->show_icon): ?>
                    <span class="koowa_header__item koowa_header__item--image_container">
                        <a href="<?= $document->title_link; ?>"
                           class="koowa_header__image_link <?= $params->link_to_download ? 'docman_track_download' : ''; ?>"
                           data-title="<?= escape($document->title); ?>"
                           data-id="<?= $document->id; ?>"
                            <?= $params->download_in_blank_page ? 'target="_blank"' : ''; ?>
                            >
                            <? // Icon ?>
                            <?= import('com://site/docman.document.icon.html', array('icon' => $document->icon, 'class' => 'k-icon--size-default')) ?>
                        </a>
                    </span>
                    <? endif ?>

                    <? // Header title ?>
                    <span class="koowa_header__item">
                        <span class="koowa_wrapped_content">
                            <span class="whitespace_preserver">
                                <a href="<?= $document->title_link; ?>"
                                   class="koowa_header__title_link <?= $params->link_to_download ? 'docman_track_download' : ''; ?>"
                                   data-title="<?= escape($document->title); ?>"
                                   data-id="<?= $document->id; ?>"
                                    <?= $params->download_in_blank_page ? 'target="_blank"' : ''; ?>
                                    >
                                    <?= escape($document->title);?></a>

                                <? // Label new ?>
                                <? if ($params->show_recent && isRecent($document)): ?>
                                    <span class="label label-success"><?= translate('New'); ?></span>
                                <? endif; ?>

                                <? // Label popular ?>
                                <? if ($params->show_popular && ($document->hits >= $params->get('hits_for_popular', 100))): ?>
                                    <span class="label label-danger label-important"><?= translate('Popular') ?></span>
                                <? endif ?>
                            </span>
                        </span>
                    </span>
                </div>


                <div class="module_document__info">
                    <? // Category ?>
                    <? if ($document->category_link): ?>
                    <div class="module_document__category">
                        <span class="koowa_wrapped_content">
                            <span class="whitespace_preserver">
                                <?= translate('In {category}', array('category' => '<a href="'.$document->category_link.'">'.escape($document->category_title).'</a>')); ?>
                            </span>
                        </span>
                    </div>
                    <? endif; ?>

                    <? // Created ?>
                    <? if ($params->show_created): ?>
                    <div class="module_document__date">
                        <?= helper('date.format', array('date' => $document->publish_date)); ?>
                    </div>
                    <? endif; ?>

                    <? // Size ?>
                    <? if ($params->show_size && $document->size): ?>
                    <div class="module_document__size">
                        <?= helper('com://admin/docman.string.humanize_filesize', array('size' => $document->size)); ?>
                    </div>
                    <? endif; ?>

                    <? // Downloads ?>
                    <? if ($params->show_hits && $document->hits): ?>
                    <div class="module_document__downloads">
                        <?= object('translator')->choose(array('{number} download', '{number} downloads'), $document->hits, array('number' => $document->hits)) ?>
                    </div>
                    <? endif; ?>
                </div>
            </li>
        <? endforeach; ?>
        </ul>
    </div>
</div>

<? endif; ?>
