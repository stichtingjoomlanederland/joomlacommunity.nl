<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= helper('behavior.koowa'); ?>
<?= helper('behavior.vue'); ?>
<?= helper('behavior.select2'); ?>
<?= helper('translator.script', array('strings' => array(
    'Calculating',
))); ?>


<ktml:script src="media://com_docman/js/access.js" />


<script>
var Docman = Docman || {};
Docman.viewlevels = <?= json_encode($viewlevels); ?>;

kQuery(function($) {
    new Docman.AccessBox({
        el: '.k-js-access-box',
        store: $('.k-js-form-controller').data('controller').store,
        data: {
            current_value: <?= json_encode($entity->access_raw) ?>,
            default_preset: <?= json_encode($default_access->id) ?>
        }
    });
});
</script>


<div class="k-js-access-box">
    <div class="k-form-group">
        <div class="k-optionlist">
            <div class="k-optionlist__content">
                <input type="radio" v-model="active" value="inherit" id="accesspicker0"/>
                <label for="accesspicker0">
                    <template v-if="selected_category">
                        <template v-if="entity._name === 'document'">
                            <?= translate('Inherit from category'); ?>
                        </template>
                        <template v-else-if="entity._name === 'category'">
                            <?= translate('Inherit from parent category'); ?>
                        </template>
                    </template>
                    <template v-else>
                        <?= translate('Use default'); ?>
                    </template>
                </label>
                <input type="radio" v-model="active" value="groups" id="accesspicker1" />
                <label for="accesspicker1"><?= translate('Groups'); ?></label>
                <input type="radio" v-model="active" value="presets" id="accesspicker2" />
                <label for="accesspicker2"><?= translate('Presets'); ?></label>
                <div class="k-faux-focus"></div>
            </div>
        </div>
    </div>

    <div class="k-form-group" v-show="active === 'groups'">
        <label class="control-label">
            <?= translate('This {item_type} can be viewed by:', array('item_type' => $type)); ?>
        </label>
        <?= helper('listbox.groups', array(
            'name' => '',
            'selected' => array_keys($entity->getGroups()),
            'attribs'  => array(
                'multiple' => 'true',
                'class'    => 'k-js-group-selector'
            )
        )); ?>
    </div>

    <div class="k-form-group" v-show="active === 'presets'">
        <?= helper('listbox.access', array(
            'name'     => '',
            'deselect' => false,
            'selected' => $entity->access_raw >= 0 ? $entity->access_raw : null,
            'attribs' => array(
                'class' => 'k-js-access-selector input-block-level'
            )
        )); ?>
    </div>

    <div class="who-can-see-container" v-show="active !== 'groups'">
        <label>
            <?= translate('This {item_type} can be viewed by:', array('item_type' => $type)); ?>
        </label>
        <ul class="who-can-see">
            <li v-html="group" v-for="group in allowed_groups"></li>
        </ul>
    </div>
    <div class="k-dynamic-content-holder">
        <input type="checkbox" name="inherit" value="1" checked
               v-if="active == 'inherit'" />

        <input type="hidden" name="groups"
               v-if="active === 'presets'"
               v-bind:value="selected_access"
        />

        <input type="hidden" name="groups[]"
               v-if="active === 'groups'"
               v-for="group in selected_groups"
               v-bind:value="group"
        />
    </div>
</div>
