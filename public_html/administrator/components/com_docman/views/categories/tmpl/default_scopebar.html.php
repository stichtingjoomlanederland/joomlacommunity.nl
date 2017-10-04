<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<div class="k-js-filters k-dynamic-content-holder">
    <div data-filter data-title="<?= translate('Status'); ?>"
         data-count="<?= (!empty(parameters()->enabled) || parameters()->enabled === 0) ? 1 : 0 ?>"
    >
        <?= helper('listbox.published', array(
            'select2' => true
        )); ?>
    </div>
    <div data-filter data-title="<?= translate('Owner'); ?>"
         data-count="<?= !empty(parameters()->created_by) ? count(parameters()->created_by) : 0 ?>"
    >
        <?= helper('listbox.users', array(
            'name' => 'created_by'
        )) ?>
    </div>
    <div data-filter data-title="<?= translate('Group'); ?>"
         data-count="<?= !empty(parameters()->group) ? count(parameters()->group) : 0 ?>"
    >
        <?= helper('listbox.groups', array(
            'attribs' => array(
                'class' => 'group_selector input-block-level',
                'multiple' => 'true'
            ),
            'name' => 'group',
            'select2' => true
        )); ?>
    </div>
    <div data-filter data-title="<?= translate('Access'); ?>"
        <?= parameters()->access ? 'data-label="1"' : '' ?>
    >
        <?= helper('listbox.access',  array(
            'select2' => true
        )); ?>
    </div>
</div>


<!-- Scopebar -->
<div class="k-scopebar k-js-scopebar">

    <!-- Scopebar filters -->
    <div class="k-scopebar__item k-scopebar__item--filters">

        <!-- Filters wrapper -->
        <div class="k-scopebar__filters-content">

            <!-- Filters -->
            <div class="k-scopebar__filters k-js-filter-container">

                <!-- Filter -->
                <div style="display: none;" class="k-scopebar__item--filter k-scopebar-dropdown k-js-filter-prototype k-js-dropdown">
                    <button type="button" class="k-scopebar-dropdown__button k-js-dropdown-button">
                        <span class="k-scopebar__item--filter__title k-js-dropdown-title"></span>
                        <span class="k-scopebar__item--filter__icon k-icon-chevron-bottom" aria-hidden="true"></span>
                        <span class="k-scopebar__item-label k-js-dropdown-label"></span>
                    </button>
                    <div class="k-scopebar-dropdown__body k-js-dropdown-body">
                        <div class="k-scopebar-dropdown__body__buttons">
                            <button type="button" class="k-button k-button--default k-js-clear-filter"><?= translate('Clear') ?></button>
                            <button type="button" class="k-button k-button--primary k-js-apply-filter"><?= translate('Apply filter') ?></button>
                        </div>
                    </div>
                </div>

            </div><!-- .k-scopebar__filters -->

        </div><!-- .k-scopebar__filters-content -->

    </div><!-- .k-scopebar__item--filters -->

    <!-- Search -->
    <div class="k-scopebar__item k-scopebar__item--search">
        <?= helper('grid.search', array('submit_on_clear' => true)) ?>
    </div><!-- .k-scopebar__item--search -->

</div><!-- .k-scopebar -->
