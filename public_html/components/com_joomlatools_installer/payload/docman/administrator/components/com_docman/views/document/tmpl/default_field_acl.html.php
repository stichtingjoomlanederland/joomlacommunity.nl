<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<? // Status field ?>
<? if ($document->isPermissible() && $document->canPerform('admin')): ?>
<div class="mfp-hide" id="advanced-permissions">
    <button title="Close (Esc)" type="button" class="mfp-close">×</button>
    <div class="k-inline-modal">
        <?= helper('access.rules', array(
            'section' => 'document',
            'asset' => $document->getAssetName(),
            'asset_id' => $document->asset_id
        )); ?>
    </div>
</div>

<div class="k-form-group">
    <label><?= translate('Action');?></label>
    <p>
        <a class="k-button k-button--default" id="advanced-permissions-toggle" href="#advanced-permissions">
            <?= translate('Change action permissions')?>
        </a>
    </p>
    <p class="k-form-info k-color-error">
        <?= translate('For advanced use only'); ?>
    </p>
</div>
<? endif; ?>
