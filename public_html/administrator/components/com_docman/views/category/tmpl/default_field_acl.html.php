<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<? // Status field ?>
<? if ($category->isPermissible() && $category->canPerform('admin')): ?>
<div class="mfp-hide" id="advanced-permissions">
    <button title="<?= translate('Close (Esc)'); ?>" type="button" class="mfp-close">×</button>
    <div class="k-inline-modal">
        <?= helper('access.rules', array(
            'section' => 'category',
            'asset' => $category->getAssetName(),
            'asset_id' => $category->asset_id
        )); ?>
    </div>
    <script>
        kQuery(function($){
            $('#advanced-permissions-toggle').on('click', function(e){
                e.preventDefault();

                $.magnificPopup.open({
                    items: {
                        src: $('#advanced-permissions'),
                        type: 'inline'
                    }
                });
            });
        });
    </script>
</div>
<? endif; ?>
