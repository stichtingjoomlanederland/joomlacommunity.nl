<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= helper('behavior.downloadlabel', array('params' => $params)); ?>

<div class="docman_document" itemscope itemtype="http://schema.org/CreativeWork">

    <? // Header ?>
    <? if ($params->show_document_title
          || ($params->show_document_recent && isRecent($document))
          || ($document->canPerform('edit') && $document->isLockable() && $document->isLocked())
          || (!$document->enabled)
          || (object('user')->getId() == $document->created_by)
          || ($params->show_document_popular && ($document->hits >= $params->hits_for_popular))
    ): ?>
    <h<?= $heading; ?> class="koowa_header">
        <? // Header image ?>
        <? if ($document->icon && $params->show_document_icon): ?>
        <span class="koowa_header__item koowa_header__item--image_container">
            <? if ($params->document_title_link && $link == 1): ?>
            <a class="koowa_header__image_link <?= $params->document_title_link === 'download' ? 'docman_track_download' : ''; ?>"
               href="<?= ($document->title_link) ?>"
               data-title="<?= escape($document->title); ?>"
               data-id="<?= $document->id; ?>"
                <?= $params->download_in_blank_page && $params->document_title_link === 'download' ? 'target="_blank"' : ''; ?>><!--
                -->
                <?= import('com://site/docman.document.icon.html', array(
                    'icon'  => $document->icon,
                    'class' => ' k-icon--size-medium'.(strlen($document->extension) ? ' k-icon-type-'.$document->extension : '')
                )) ?>
            </a>
            <? else: ?>
                <?= import('com://site/docman.document.icon.html', array(
                    'icon'  => $document->icon,
                    'class' => ' k-icon--size-medium'.(strlen($document->extension) ? ' k-icon-type-'.$document->extension : '')
                )) ?>
            <? endif; ?>
        </span>
        <? endif ?>

        <? // Header title ?>
        <span class="koowa_header__item">
            <span class="koowa_wrapped_content">
                <span class="whitespace_preserver">
                    <? if ($params->show_document_title): ?>
                        <? if ($params->document_title_link && $link): ?>
                        <a class="koowa_header__title_link <?= $params->document_title_link === 'download' ? 'docman_track_download' : ''; ?>"
                           href="<?= ($document->title_link) ?>"
                           data-title="<?= escape($document->title); ?>"
                           data-id="<?= $document->id; ?>"
                           <?= $params->download_in_blank_page && $params->document_title_link === 'download' ? 'target="_blank"' : ''; ?>><!--
                            --><span itemprop="name"><?= escape($document->title); ?></span></a>
                        <? else: ?>
                            <span itemprop="name"><?= escape($document->title); ?></span>
                        <? endif; ?>
                    <? endif; ?>

                    <? // Show labels ?>

                    <? // Label new ?>
                    <? if ($params->show_document_recent && isRecent($document)): ?>
                        <span class="label label-success"><?= translate('New'); ?></span>
                    <? endif; ?>

                    <? // Label locked ?>
                    <? if ($document->canPerform('edit') && $document->isLockable() && $document->isLocked()): ?>
                        <span class="label label-warning"><?= helper('grid.lock_message', array('entity' => $document)); ?></span>
                    <? endif; ?>

                    <? // Label status ?>
                    <? if (!$document->enabled || $document->status !== 'published'): ?>
                        <? $status = $document->enabled ? translate($document->status) : translate('Draft'); ?>
                        &nbsp;<span class="label label-<?= $document->enabled ? $document->status : 'draft' ?>"><?= ucfirst($status); ?></span>
                    <? endif; ?>

                    <? // Label owner ?>
                    <? if ($params->get('show_document_owner_label', 1) && object('user')->getId() == $document->created_by): ?>
                        <span class="label label-success"><?= translate('Owner'); ?></span>
                    <? endif; ?>

                    <? // Label popular ?>
                    <? if ($params->show_document_popular && ($document->hits >= $params->hits_for_popular)): ?>
                        <span class="label label-danger label-important"><?= translate('Popular') ?></span>
                    <? endif ?>
                </span>
            </span>
        </span>
    </h<?= $heading; ?>>
    <? endif; ?>

    <? // After title - content plugin event ?>
    <?= helper('event.trigger', array(
        'name'       => 'onContentAfterTitle',
        'attributes' => array($event_context, &$document, &$params, 0)
    )); ?>


    <? // Dates&Owner ?>
    <? if (($params->show_document_created)
        || ($document->modified_by && $params->show_document_modified)
        || ($params->show_document_created_by)
        || ($params->show_document_category)
        || ($params->show_document_tags)
        || ($params->show_document_hits && $document->hits)
    ): ?>
    <p class="docman_document_details">

        <? // Created ?>
        <? if ($params->show_document_created): ?>
        <span class="created-on-label">
            <time itemprop="datePublished" datetime="<?= $document->publish_date ?>">
                <?= translate('Published on'); ?> <?= helper('date.format', array('date' => $document->publish_date)); ?>
            </time>
        </span>
        <? endif; ?>

        <? // Modified ?>
        <? if ($params->show_document_modified && $document->modified_by): ?>
        <span class="modified-on-label">
            <time itemprop="dateModified" datetime="<?= $document->modified_on ?>">
                <?= translate('Modified on'); ?> <?= helper('date.format', array('date' => $document->modified_on)); ?>
            </time>
        </span>
        <? endif; ?>

        <? // Owner ?>
        <? if ($params->show_document_created_by && $document->created_by):
            $owner = '<span itemprop="author">'.$document->getAuthor()->getName().'</span>'; ?>
            <span class="owner-label">
                <?= translate('By {owner}', array('owner' => $owner)); ?>
            </span>
        <? endif; ?>

        <? // Category ?>
        <? if ($params->show_document_category):
            $category = '<span itemprop="genre">'.$document->category_title.'</span>'; ?>
            <span class="category-label">
                <?= translate('In {category}', array('category' => $category)); ?>
            </span>
        <? endif; ?>

        <? // Tags ?>
        <? if ($params->show_document_tags && $document->tag_list): ?>
            <span class="tag-label">
                <?= translate('Tagged in {tags}', array('tags' => helper('tags.link', [
                    'entity' => $document, 'menu' => $menu
                ]))); ?>
            </span>
        <? endif; ?>

        <? // Downloads ?>
        <? if ($params->show_document_hits && $document->hits): ?>
            <meta itemprop="interactionCount" content="UserDownloads:<?= $document->hits ?>">
            <span class="hits-label">
                <?= object('translator')->choose(array('{number} download', '{number} downloads'), $document->hits, array('number' => $document->hits)) ?>
            </span>
        <? endif ?>
    </p>
    <? endif; ?>

    
    <? // Render audio/video player ?>
    <? if(!$params->force_download && $params->show_player): ?>
    <p>
        <?= $player = helper('player.render', array('document' => $document)) ?>
    </p>
    <? endif; ?>


    <? // Download area ?>
    <? if (empty($player) && (!object('user')->isAuthentic() || $document->canPerform('download'))): ?>
    <div class="docman_download<?php if ($document->description != '') echo " docman_download--right"; ?>">
        <a class="btn btn-large <?= $buttonstyle; ?> btn-block docman_download__button docman_track_download"
           href="<?= $document->download_link; ?>"
           data-title="<?= escape($document->title); ?>"
           data-id="<?= $document->id; ?>"
           <? if(!$params->force_download): ?>
           data-mimetype="<?= $document->mimetype ?>"
           data-extension="<?= $document->extension ?>"
           <? endif; ?>
           <?= ($params->download_in_blank_page) ? 'target="_blank"' : ''; ?>>

            <span class="docman_download_label">
              <?= translate('Download'); ?>
            </span>

            <? // Filetype and Filesize  ?>
            <? if (($params->show_document_size && $document->size) || ($document->storage_type == 'file' && $params->show_document_extension)): ?>
                <span class="docman_download__info">(<!--
                --><? if ($document->storage_type == 'file' && $params->show_document_extension): ?><!--
                    --><?= escape($document->extension . ($params->show_document_size && $document->size ? ', ':'')) ?><!--
                --><? endif ?><!--
                --><? if ($params->show_document_size && $document->size): ?><!--
                    --><?= helper('string.humanize_filesize', array('size' => $document->size)) ?><!--
                --><? endif ?><!--
                -->)</span>
            <? endif; ?>
        </a>

        <? // Filename ?>
        <? if ($document->storage->name && $params->show_document_filename): ?>
            <p class="docman_download__filename" title="<?= escape($document->storage->name); ?>"><?= escape($document->storage->name); ?></p>
        <? endif; ?>
    </div>
    <? endif ?>


    <? // Before display - content plugin event ?>
    <?= helper('event.trigger', array(
        'name'       => 'onContentBeforeDisplay',
        'attributes' => array($event_context, &$document, &$params, 0)
    )); ?>


    <? // Document description ?>
    <? if ($params->show_document_description || $params->show_document_image): ?>
    <div class="docman_description">
        <? if ($params->show_document_image && $document->image): ?>
            <?= helper('behavior.thumbnail_modal'); ?>
            <a class="docman_thumbnail thumbnail" href="<?= $document->image_download_path ?>">
                <img itemprop="thumbnailUrl" src="<?= $document->image_path ?>" alt="<?= escape($document->title); ?>" />
            </a>
        <? endif ?>

        <? if ($params->show_document_description):
            $field = 'description_'.(isset($description) ? $description : 'full');
        ?>
            <div itemprop="description">
            <?= prepareText($document->$field); ?>
            </div>
        <? endif; ?>
    </div>
    <? endif ?>


    <? // After display - content plugin event ?>
    <?= helper('event.trigger', array(
        'name'       => 'onContentAfterDisplay',
        'attributes' => array($event_context, &$document, &$params, 0)
    )); ?>


    <? // Edit area | Import partial template from document view ?>
    <?= import('com://site/docman.document.manage.html', array('document' => $document)) ?>

</div>
