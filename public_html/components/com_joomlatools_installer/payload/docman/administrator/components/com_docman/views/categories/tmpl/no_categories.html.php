<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<div class="k-empty-state">
    <? if(!$category_count) : ?>
        <p>
            <?= translate('It seems like you don\'t have any categories yet.'); ?>
        </p>
        <p>
            <a class="k-button k-button--large k-button--success" href="<?= route('option=com_docman&view=category') ?>">
                <?= translate('Add your first category')?>
            </a>
        </p>
    <? else : ?>
        <p>
            <?= translate('No categories found.'); ?><br>
            <small><?= translate('Maybe select another category or different filters?'); ?></small>
        </p>
    <? endif; ?>
</div>