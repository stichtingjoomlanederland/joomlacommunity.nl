<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<script>
    kQuery(function($) {
        var addHiddenFields = function(button) {
            var field, i = 0, fields = ['.k-js-tag-search', '.k-js-category-search'];

            for (; i < fields.length; i++) {
                field = $(fields[i]);

                if (field.length && !field.val()) {
                    var input = $('<input type="hidden" />')
                        .attr('name', field.attr('name').replace('[]', ''))
                        .val('');

                    $(button[0].form).append(input);
                }
            }
        };

        $('.k-js-search-submit').click(function() {
            addHiddenFields($(this));
        });

        $('.k-js-search-reset').click(function(event) {
            event.preventDefault();

            var button = $(this),
                form   = button[0].form;

            $('.k-filters')
                .find('input:not(:checkbox), textarea').val('').end()
                .find('select').val(null).trigger('change');

            addHiddenFields(button);

            $(form).append($('<input type="hidden" />').val('1')
                .attr('name', '<?= !empty($filter_group) ? $filter_group.'[reset]' : 'reset' ?>'));

            form.submit();
        });
    });
</script>

<? if ($params->show_document_search): ?>
    <div class="well well-small k-filters k-filters--toggleable">
        <input class="k-checkbox-dropdown-toggle" id="k-checkbox-dropdown-toggle" type="checkbox"
            <?= !empty($filter_toggled) ? 'checked' : '' ?>
        >
        <label class="k-checkbox-dropdown-label" for="k-checkbox-dropdown-toggle"><?= translate('Search for documents'); ?></label>
        <div class="k-checkbox-dropdown-content">
            <div class="form-group">
                <label for="search">
                    <?= translate('Find by title or description…') ?>
                </label>
                <input
                    class="form-control input-block-level"
                    type="search"
                    name="<?= !empty($filter_group) ? $filter_group.'[search]' : 'search' ?>"
                    value="<?= $filter->search ?>" />
            </div>

            <? if ($params->show_content_filter): ?>
                <div class="k-form-group">
                    <label class="control-label"><?= translate('Search in') ?></label>
                    <?= helper('listbox.optionlist', array(
                        'name' => !empty($filter_group) ? $filter_group.'[search_contents]' : 'search_contents',
                        'select2'   => true,
                        'selected' => $filter->search_contents,
                        'options' => [
                            ['value' => 0, 'label' => translate('Document title and description')],
                            ['value' => 1, 'label' => translate('Document contents, title, and description')],
                        ]
                    )); ?>
                </div>
            <? endif ?>

            <? if ($params->get('show_category_filter', 1)): ?>
                <div class="form-group">
                    <label class="control-label"><?= translate('Category') ?></label>
                    <?= helper('listbox.categories', array(
                        'deselect' => true,
                        'check_access' => false,
                        'name' => !empty($filter_group) ? $filter_group.'[category]' : 'category',
                        'filter'  => $category_filter,
                        'attribs' => array(
                            'id' => 'category', 'multiple' => true,
                            'class' => 'form-control input-block-level k-js-category-search'
                        ),
                        'selected' => $filter->category
                    )) ?>
                </div>
            <? endif; ?>

            <? if ($params->get('show_tag_filter', 1)): ?>
                <div class="form-group">
                    <label class="control-label"><?= translate('Tags') ?></label>
                    <?= helper('listbox.tags', array(
                        'model' => $tag_model,
                        'autocreate' => false,
                        'name' => !empty($filter_group) ? $filter_group.'[tag]' : 'tag',
                        'value' => 'slug',
                        'selected' => $filter->tag,
                        'filter' => $tag_filter,
                        'attribs'  => array(
                            'data-placeholder' => translate('All Tags'),
                            'class' => 'form-control input-block-level k-js-tag-search'
                        )
                    )); ?>
                </div>
            <? endif ?>

            <? if ($params->get('show_owner_filter', 1)): ?>
                <div class="form-group">
                    <label class="control-label"><?= translate('Owner') ?></label>
                    <?= helper('listbox.users', array(
                        'name' => !empty($filter_group) ? $filter_group.'[created_by]' : 'created_by',
                        'selected' => $filter->created_by,
                        'attribs'  => array(
                            'multiple' => true,
                            'class' => 'form-control input-block-level'
                        ),
                        'deselect' => true
                    )) ?>
                </div>
            <? endif ?>

            <? if ($params->get('show_date_filter', 1)): ?>
            <div class="form-group docman-search-date">
                <label class="control-label"><?= translate('Date start') ?></label>
                <?= helper('behavior.calendar', array(
                    'name' => !empty($filter_group) ? $filter_group.'[created_on_from]' : 'created_on_from',
                    'id'   => 'created_on_from',
                    'format' => '%Y-%m-%d',
                    'value' => $filter->created_on_from,
                    'attribs' => array('placeholder' => date('Y-m-d'))
                )) ?>

                <label class="control-label"><?= translate('Date end') ?></label>
                <?= helper('behavior.calendar', array(
                    'name' => !empty($filter_group) ? $filter_group.'[created_on_to]' : 'created_on_to',
                    'id'   => 'created_on_to',
                    'format' => '%Y-%m-%d',
                    'value' => $filter->created_on_to,
                    'attribs' => array('placeholder' => date('Y-m-d'))
                )) ?>
            </div>
            <? endif ?>

            <? // Temp JS until new UI arrived. Basically we're adding old Bootstrap 2.3.2 classes to the datepickers; ?>
            <script>
                kQuery(document).ready(function() {
                    kQuery('.docman-search-date').find('.k-input-group').addClass('input-append').find('input').addClass('input-block-level');
                });
            </script>

            <button class="btn btn-lg k-js-search-submit" type="submit"><?= translate('Search') ?></button>

            <button class="btn btn-link k-js-search-reset"><?= translate('Reset') ?></button>

        </div>
    </div>

    <? if ($filter->search && isset($documents) && !count($documents)): ?>

        <? // No documents found message ?>
        <div class="alert alert-warning"><?= import('com://site/docman.documents.no_results.html') ?></div>

    <? endif ?>
<? elseif ($filter->tag): ?>
    <div class="docman_block">
        <? // Header ?>
        <h4 class="koowa_header">
            <?= translate('Tagged in: {tag}', ['tag' => helper('tags.title', ['tag' => $filter->tag])]) ?>
        </h4>
    </div>
    <p>
        <?= translate('Showing documents tagged with {tag}.', ['tag' => helper('tags.title', ['tag' => $filter->tag])]); ?>
        <a href="<?= route(!empty($filter_group) ? $filter_group.'[tag]=' : 'tag=') ?>"> <?= translate('Show all') ?></a>
    </p>
<? endif; ?>

