<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<ktml:script src="media://com_docman/js/site/gallery.js" />
<ktml:script src="media://com_docman/js/site/items.js" />
<ktml:style src="media://com_docman/css/site.css" />

<script>
    kQuery(function($) {

        var documentsGallery = $('.koowa_media_wrapper--documents'),
            categoriesGallery = $('.koowa_media_wrapper--categories'),
            itemWidth = parseInt($('.koowa_media_wrapper--documents .koowa_media__item').css('width'));

        if ( categoriesGallery ) {
            categoriesGallery.simpleGallery({
                item: {
                    'width': itemWidth
                }
            });
        }

        if ( documentsGallery ) {
            documentsGallery.simpleGallery({
                item: {
                    'width': itemWidth
                }
            });
        }
    });
</script>

<? if ($params->track_downloads): ?>
    <?= helper('com://admin/docman.behavior.download_tracker'); ?>
<? endif; ?>