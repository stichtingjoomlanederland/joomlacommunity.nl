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
                'selected' => $document->enabled,
                'true' => translate('Published'),
                'false' => translate('Unpublished')
            )); ?>
        </div>
        <? endif; ?>

        <div class="k-form-group">
            <label><?= translate('Date'); ?></label>
            <?= helper('behavior.calendar', array(
                'name' => 'created_on',
                'id' => 'created_on',
                'value' => $document->created_on,
                'format' => '%Y-%m-%d %H:%M:%S',
                'filter' => 'user_utc'
            ))?>
        </div>

        <? if(!isset($hide_publishing_field)) : ?>
        <div class="k-form-group">
            <label><?= translate('Start publishing on'); ?></label>

            <? $datetime = new DateTime(null, new DateTimeZone('UTC')) ?>
            <? $datetime->modify('-1 day'); ?>
            <?= helper('behavior.calendar', array(
                'name' => 'publish_on',
                'id' => 'publish_on',
                'value' => $document->publish_on,
                'format' => '%Y-%m-%d %H:%M:%S',
                'filter' => 'user_utc',
                'options' => array(
                    'clearBtn' => true,
                    'startDate' => $datetime->format('Y-m-d H:i:s'),
                    'todayBtn' => false
                )
            ))?>
        </div>

        <div class="k-form-group">
            <label><?= translate('Stop publishing on'); ?></label>
            <?= helper('behavior.calendar', array(
                'name' => 'unpublish_on',
                'id' => 'unpublish_on',
                'value' => $document->unpublish_on,
                'format' => '%Y-%m-%d %H:%M:%S',
                'filter' => 'user_utc',
                'options' => array(
                    'clearBtn' => true,
                    'startDate' => $datetime->format('Y-m-d H:i:s'),
                    'todayBtn' => false
                )
            ))?>
        </div>
        <? endif; ?>
    </div>


</fieldset>

<? if(empty($hide_tag_field)) : ?>
<fieldset class="k-form-block">

    <div class="k-form-block__header">
        <?= translate('Tags') ?>
    </div>

    <div class="k-form-block__content">
        <div class="k-form-group">
            <?= helper('listbox.tags', array(
                'entity' => $document,
                'autocreate' => $can_create_tag
            )) ?>
        </div>
    </div>
</fieldset>
<? endif ?>

<fieldset class="k-form-block">

    <div class="k-form-block__header">
        <?= translate('Permissions') ?>
    </div>

    <div class="k-form-block__content">

        <div class="k-form-group">
            <label><?= translate('Access'); ?></label>
            <?= helper('access.access_box', array(
                'entity' => $document
            )); ?>
        </div>

        <? if (!isset($hide_owner_field)): ?>
        <div class="k-form-group">
            <label><?= translate('Owner'); ?></label>
            <?= helper('listbox.users', array(
                'name' => 'created_by',
                'selected' => $document->created_by ? $document->created_by : object('user')->getId(),
                'deselect' => false
            )) ?>
        </div>
        <? endif; ?>

        <!-- ACL settings -->
        <!-- Uncomment to display "Access settings" in template override -->
        <?/*= import('com://admin/docman.document.default_field_acl.html'); */?>

    </div>

</fieldset>
