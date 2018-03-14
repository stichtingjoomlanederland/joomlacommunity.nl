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

<?= helper('behavior.jquery'); ?>
<?= helper('behavior.modal'); ?>
<?= helper('com://site/docman.behavior.photoswipe'); ?>
<?= helper('com://site/docman.gallery.load', array('params' => $params)) ?>

<? // No documents message if the "Show only user's documents" parameter is enabled ?>
<? if (parameters()->total == 0): if ($params->own): ?>
    <p class="alert alert-info">
        <?= translate('You do not have any documents yet.'); ?>
    </p>
<? endif; else: ?>

<div class="k-ui-namespace">
    <div class="mod_docman mod_docman--documents mod_docman--gallery">
        <div class="koowa_media--gallery">
            <div class="koowa_media_wrapper koowa_media_wrapper--documents">
                <div class="koowa_media_contents">
                    <?php // these comments below must stay ?>
                    <div class="koowa_media"><!--
                        <? $count = 0; ?>
                        <? foreach ($documents as $document): ?>
                         --><div class="koowa_media__item" itemscope itemtype="http://schema.org/ImageObject">
                                <div class="koowa_media__item__content document">
                                        <?= import('com://site/docman.document.gallery.html', array(
                                            'document' => $document,
                                            'params' => $params,
                                            'count' => $count,
                                            'manage' => false
                                        )) ?>
                                    </div>
                                </div><!--
                            <? $count++; ?>
                        <? endforeach ?>
                 --></div>
                </div>
            </div>
        </div>
    </div>
</div>

<? endif; ?>