<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<div id="document-duplicate-modal" class="k-ui-namespace k-small-inline-modal-holder mfp-hide">
    <div class="k-inline-modal">
        <form class="k-js-duplicate-form">

            <h3 class="k-inline-modal__title">
                <?= translate('Duplicate into') ?>
            </h3>

            <div class="k-form-group">
                <?= helper('listbox.categories', array(
                    'deselect' => true,
                    'check_access' => true,
                    'attribs' => array('id' => 'document_duplicate_target'),
                    'selected' => null
                )) ?>
            </div>

            <div class="k-form-group">
                <button class="k-button k-button--primary" disabled ><?= translate('Duplicate'); ?></button>
            </div>

        </form>
    </div>
</div>
