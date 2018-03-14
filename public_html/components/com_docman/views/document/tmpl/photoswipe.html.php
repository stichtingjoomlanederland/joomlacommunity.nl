<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('_JEXEC') or die; ?>


<ktml:script src="media://com_docman/js/photoswipe/photoswipe-photoswipe-ui-default.min.js" />
<ktml:script src="media://com_docman/js/photoswipe/gallery.js" />

<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="pswp__bg">
        <div class="pswp__custom_preloader"><?= @translate('Loading...'); ?></div>
    </div>
    <div class="pswp__scroll-wrap">
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>
        <div class="pswp__ui pswp__ui--hidden">
            <div class="pswp__top-bar">
                <div class="pswp__counter"></div>
                <button class="pswp__button pswp__button--close" title="<?= @translate('Close (Esc)'); ?>"></button>
                <button class="pswp__button pswp__button--share" title="<?= @translate('Share'); ?>"></button>
                <button class="pswp__button pswp__button--fs" title="<?= @translate('Toggle fullscreen'); ?>"></button>
                <button class="pswp__button pswp__button--zoom" title="<?= @translate('Zoom in/out'); ?>"></button>
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                        <div class="pswp__preloader__cut">
                            <div class="pswp__preloader__donut"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div>
            </div>
            <button class="pswp__button pswp__button--arrow--left" title="<?= @translate('Previous (arrow left)'); ?>"></button>
            <button class="pswp__button pswp__button--arrow--right" title="<?= @translate('Next (arrow right)'); ?>'"></button>
            <div class="pswp__caption">
                <div class="pswp__caption__center"></div>
            </div>
        </div>
    </div>
</div>