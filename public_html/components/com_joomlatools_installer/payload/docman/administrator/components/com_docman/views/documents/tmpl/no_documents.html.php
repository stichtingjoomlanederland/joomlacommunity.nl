<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<div class="k-empty-state">
    <? if(!$document_count) : ?>
        <p>
            <?= translate('It seems like you don\'t have any documents yet.'); ?>
        </p>
        <p>
            <a class="k-button k-button--success k-button--large" href="<?= route('option=com_docman&view=document') ?>">
                <?= translate('Add your first document')?>
            </a>
        </p>
    <? elseif(!count($documents)) : ?>
        <p>
            <?= translate('No documents found.'); ?><br>
            <small><?= translate('Maybe select another category or different filters?'); ?></small>
        </p>
    <? endif; ?>
</div>
