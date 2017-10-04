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
        <?= translate('Publishing') ?>
    </div>

    <div class="k-form-block__content">

        <? if(!isset($hide_publishing_field)) : ?>
            <div class="k-form-group">
                <label><?= translate('Status'); ?></label>

                <?= helper('select.booleanlist', array(
                    'name' => 'enabled',
                    'selected' => $category->enabled,
                    'true' => 'Published',
                    'false' => 'Unpublished'
                )); ?>
            </div>
        <? endif; ?>

        <div class="k-form-group">
            <label><?= translate('Date'); ?></label>
            <?= helper('behavior.calendar', array(
                'name' => 'created_on',
                'id' => 'created_on',
                'value' => $category->created_on,
                'format' => '%Y-%m-%d %H:%M:%S',
                'filter' => 'user_utc'
            ))?>
        </div>

    </div>

</fieldset>

<fieldset class="k-form-block">

    <div class="k-form-block__header">
        <?= translate('Folder') ?>
    </div>

    <div class="k-form-block__content">
        <?= import('com://admin/docman.category.default_field_folder.html') ?>
    </div>

</fieldset>

<fieldset class="k-form-block">

    <div class="k-form-block__header">
        <?= translate('Featured image') ?>
    </div>

    <div class="k-form-block__content">

        <div class="k-form-group">
            <?= helper('behavior.thumbnail', array(
                'entity' => $category
            )) ?>
        </div>

    </div>

</fieldset>

<fieldset class="k-form-block">

    <div class="k-form-block__header">
        <?= translate('Permissions') ?>
    </div>

    <div class="k-form-block__content">

        <div class="k-form-group">
            <label><?= translate('Access'); ?></label>
            <?= helper('access.access_box', array(
                'entity' => $category
            )); ?>
        </div>

        <? if (!isset($hide_owner_field)): ?>
            <div class="k-form-group">
                <label><?= translate('Owner'); ?></label>
                <?= helper('listbox.users', array(
                    'name' => 'created_by',
                    'selected' => $category->created_by ? $category->created_by : object('user')->getId(),
                    'deselect' => false
                )) ?>
            </div>
        <? endif; ?>

    </div>

</fieldset>