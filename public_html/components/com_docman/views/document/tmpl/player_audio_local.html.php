<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= helper('behavior.koowa'); ?>

<?= helper('player.load', [
    'download' => $document->canPerform('download')
]); ?>

<div class="docman_player">
    <audio
        data-media-id="<?= $document->id ?>"
        data-title="<?= escape($document->title) ?>"
        data-category="docman"
        preload="none"
        controls>
        <source src="<?= $document->download_link ?>" type="audio/<?= $document->extension ?>" />
    </audio>
</div>