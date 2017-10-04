<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<fieldset class="k-form-block">

    <div class="k-form-block__header">
        <?= translate('Audit') ?>
    </div>

    <div class="k-form-block__content">
        <div class="k-form-group">
            <label><?= translate('Downloads'); ?></label>
            <div id="hits-container">
                <span><?= $document->hits; ?></span>

                <? if ($document->hits): ?>
                    <a href="#" class="k-button k-button--small k-button--default"><?= translate('Reset'); ?></a>
                <? endif; ?>
            </div>
        </div>

        <? if ($document->modified_by): ?>
            <div class="k-form-group">
                <label><?= translate('Modified by'); ?></label>
                <p class="k-form-info">
                    <?= object('user.provider')->load($document->modified_by)->getName(); ?>
                    <?= translate('on') ?>
                    <?= helper('date.format', array('date' => $document->modified_on)); ?>
                </p>
            </div>
        <? endif; ?>
    </div>

</fieldset>
