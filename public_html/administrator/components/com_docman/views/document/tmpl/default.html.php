<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<?= helper('ui.load') ?>


<ktml:script src="media://com_docman/js/document.js" />


<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.validator'); ?>
<?= helper('behavior.icon_map'); ?>
<?= helper('behavior.vue', ['entity' => $document]); ?>


<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Overview -->
    <div class="k-content-wrapper">

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Toolbar -->
            <ktml:toolbar type="actionbar">

            <!-- Component wrapper -->
            <div class="k-component-wrapper">

                <!-- Component -->
                <form class="k-component k-js-component k-js-form-controller" action="" method="post">

                    <!-- Container -->
                    <div class="k-container">

                        <!-- Main information -->
                        <div class="k-container__main">

                            <fieldset>

                                <div class="k-form-group">
                                    <div class="k-input-group k-input-group--large">
                                        <?= helper('behavior.icon', array(
                                            'name'  => 'parameters[icon]',
                                            'id' => 'params_icon',
                                            'value' => $document->getParameters()->get('icon', 'default'),
                                            'link'  => route('option=com_docman&view=files&layout=select&container=docman-icons&types[]=image')
                                        ))?>
                                        <input required
                                               class="k-form-control"
                                               id="docman_form_title"
                                               type="text"
                                               name="title"
                                               maxlength="255"
                                               placeholder="<?= translate('Title') ?>"
                                               value="<?= escape($document->title); ?>" />
                                    </div>
                                </div>

                                <div class="k-form-group">
                                    <div class="k-input-group k-input-group--small">
                                        <label for="docman_form_alias" class="k-input-group__addon">
                                            <?= translate('Alias') ?>
                                        </label>
                                        <input id="docman_form_alias"
                                               type="text"
                                               class="k-form-control"
                                               name="slug"
                                               maxlength="255"
                                               value="<?= escape($document->slug) ?>"
                                               placeholder="<?= translate('Will be created automatically') ?>" />
                                    </div>
                                </div>

                                <?= import('default_field_file.html'); ?>

                                <div class="k-form-group">
                                    <label><?= translate('Category'); ?></label>
                                    <?= helper('listbox.categories', array(
                                        'check_access' => true,
                                        'deselect' => false,
                                        'required' => true,
                                        'name' => 'docman_category_id',
                                        'disable_if_empty' => true,
                                        'attribs' => array(
                                            'required' => true,
                                            'id'    => 'docman_category_id'
                                        ),
                                        'selected' => $document->docman_category_id
                                    ))?>
                                </div>

                                <div class="k-form-group">
                                    <?= helper('editor.display', array(
                                        'name' => 'description',
                                        'value' => $document->description,
                                        'id'   => 'description',
                                        'width' => '100%',
                                        'height' => '341',
                                        'cols' => '100',
                                        'rows' => '20',
                                        'buttons' => array('pagebreak')
                                    )); ?>
                                </div>

                            </fieldset>

                        </div><!-- .k-container__main -->

                        <!-- Other information -->
                        <div class="k-container__sub">

                            <fieldset class="k-form-block">

                                <div class="k-form-block__header">
                                    <?= translate('Publishing') ?>
                                </div>

                                <div class="k-form-block__content">
                                    <div class="k-form-group">
                                        <label><?= translate('Status'); ?></label>
                                        <?= helper('select.booleanlist', array(
                                            'name' => 'enabled',
                                            'selected' => $document->enabled,
                                            'true' => translate('Published'),
                                            'false' => translate('Unpublished')
                                        )); ?>
                                    </div>

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
                                                'todayBtn' => false
                                            )
                                        ))?>
                                    </div>
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

                                    <div class="k-form-group">
                                        <label><?= translate('Owner'); ?></label>
                                        <?= helper('listbox.users', array(
                                            'name' => 'created_by',
                                            'selected' => $document->created_by ? $document->created_by : object('user')->getId(),
                                            'deselect' => false,
                                        )) ?>
                                    </div>

                                    <!-- ACL settings -->
                                    <!-- Uncomment to display "Access settings" in template override -->
                                    <?/*= import('default_field_acl.html');*/ ?>

                                </div>

                            </fieldset>

                            <fieldset class="k-form-block">

                                <div class="k-form-block__header">
                                    <?= translate('Featured image') ?>
                                </div>

                                <div class="k-form-block__content">

                                    <div class="k-form-group">
                                        <?= helper('behavior.thumbnail', array(
                                            'entity' => $document
                                        )) ?>
                                    </div>

                                </div>

                            </fieldset>

                            <fieldset class="k-form-block">

                                <div class="k-form-block__header">
                                    <?= translate('Index') ?>
                                </div>

                                <div class="k-form-block__content">

                                    <div class="k-form-group">
                                        <?= helper('behavior.scanner', array(
                                            'entity' => $document
                                        )) ?>
                                    </div>

                                </div>

                            </fieldset>

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
                                                <small><a href="#"><?= translate('Reset'); ?></a></small>
                                            <? endif; ?>
                                        </div>
                                    </div>

                                    <? if ($document->modified_by): ?>
                                        <div class="k-form-group">
                                            <label><?= translate('Modified by'); ?></label>
                                            <p>
                                                <?= object('user.provider')->load($document->modified_by)->getName(); ?>
                                                <?= translate('on') ?>
                                                <?= helper('date.format', array('date' => $document->modified_on)); ?>
                                            </p>
                                        </div>
                                    <? endif; ?>

                                </div>

                            </fieldset>

                        </div><!-- .k-container__sub -->

                    </div><!-- .k-container -->

                </form><!-- .k-component -->

            </div><!-- .k-component-wrapper -->

        </div><!-- .k-content -->

    </div><!-- .k-content-wrapper -->

</div><!-- .k-wrapper -->
