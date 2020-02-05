<?php
defined('_JEXEC') or die('Restricted access');
?><div class="cell">
	<label>
        <?php echo acym_translation('ACYM_LIST_NAME'); ?>
		<input name="list[name]" type="text" class="acy_required_field" value="<?php echo acym_escape($data['listInformation']->name); ?>" required>
	</label>
</div>
<div class="cell">
	<label>
        <?php echo acym_translation('ACYM_TAGS'); ?>
        <?php echo acym_selectMultiple($data['allTags'], "list_tags", $data['listTagsName'], ['id' => 'acym__tags__field', 'placeholder' => acym_translation('ACYM_ADD_TAGS')], "name", "name"); ?>
	</label>
</div>
<div class="cell grid-x grid-margin-x margin-left-0 margin-right-0">
	<div class="cell grid-x acym__list__settings__active small-6">
        <?php echo acym_switch('list[active]', acym_escape($data['listInformation']->active), acym_translation('ACYM_ACTIVE'), [], 'shrink', 'shrink', 'tiny margin-0'); ?>
	</div>
	<p class="cell margin-bottom-1 small-6 text-center" id="acym__lists__settings__list-color">
        <?php echo acym_translation('ACYM_COLOR'); ?> :
		<input type="text" name="list[color]" id="acym__list__settings__color-picker" value="<?php echo acym_escape($data['listInformation']->color); ?>" />
	</p>
	<div class="cell grid-x acym__list__settings__visible small-6">
        <?php echo acym_switch('list[visible]', acym_escape($data['listInformation']->visible), acym_translation('ACYM_VISIBLE'), [], 'shrink', 'shrink', 'tiny margin-0'); ?>
	</div>
    <?php if (!empty($data['listInformation']->id)) { ?>
		<p class="cell margin-bottom-1 small-6 text-center" id="acym__list__settings__list-id"><?php echo acym_translation('ACYM_LIST_ID'); ?> : <b class="acym__color__blue"><?php echo acym_escape($data['listInformation']->id); ?></b></p>
    <?php } ?>
</div>

