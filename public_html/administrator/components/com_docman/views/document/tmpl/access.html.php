<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die;

$type = KStringInflector::singularize($entity->getIdentifier()->name);
?>


<?= helper('behavior.koowa'); ?>
<?= helper('behavior.select2'); ?>
<?= helper('translator.script', array('strings' => array(
    'Calculating',
    'Use default',
    'Inherit from category',
    'Inherit from parent category'
))); ?>


<ktml:script src="media://com_docman/js/access.js" />


<script>
var DOCman = DOCman || {};
DOCman.viewlevels = <?= json_encode($viewlevels); ?>;

kQuery(function() {
    new DOCman.Usergroups('.access-box', {
        category: "<?= $type === 'document' ? '#docman_category_id' : '#category' ?>",
        entity: "<?= $type ?>"
    });
});
</script>


<div class="access-box"
     data-selected="<?= $entity->access_raw ?>"
     data-default-id="<?= $default_access->id ?>"
     data-default-title="<?= $default_access->title ?>"
    >

    <div class="access_container">

        <div class="k-form-group">
            <div class="k-optionlist">
                <div class="k-optionlist__content">
                    <input type="radio" name="accesspicker" id="accesspicker0" value="0" class="k-js-access-button k-js-access-inherit" />
                    <label for="accesspicker0"><?= translate('Inherit'); ?></label>
                    <input type="radio" name="accesspicker" id="accesspicker1" value="1" class="k-js-access-button k-js-access-groups"
                           data-toggle data-pane="groups" />
                    <label for="accesspicker1"><?= translate('Groups'); ?></label>
                    <input type="radio" name="accesspicker" id="accesspicker2" value="2" class="k-js-access-button k-js-access-presets"
                           data-toggle data-pane="presets" />
                    <label for="accesspicker2"><?= translate('Presets'); ?></label>
                    <div class="k-faux-focus"></div>
                </div>
            </div>
        </div>

        <div class="k-form-group k-js-access-tab k-js-access-tab-groups" style="display: none">
            <label class="control-label">
                <?= translate('This {item_type} can be viewed by:', array('item_type' => $type)); ?>
            </label>
            <?= helper('listbox.groups', array(
                'name' => '',
                'selected' => array_keys($entity->getGroups()),
                'attribs'  => array(
                    'multiple' => 'true',
                    'class'    => 'select2-listbox group_selector'
                )
            )); ?>
        </div>

        <div class="k-form-group k-js-access-tab k-js-access-tab-presets" style="display: none">
            <?= helper('listbox.access', array(
                'name'     => '',
                'deselect' => false,
                'selected' => $entity->access_raw >= 0 ? $entity->access_raw : null,
                'attribs' => array(
                    'class' => 'select2-listbox access_selector input-block-level'
                )
            )); ?>
        </div>

        <div class="who-can-see-container">
            <label>
                <?= translate('This {item_type} can be viewed by:', array('item_type' => $type)); ?>
            </label>
            <ul class="who-can-see"></ul>
        </div>

    </div>

</div>
