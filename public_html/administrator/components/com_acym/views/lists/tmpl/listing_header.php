<?php
defined('_JEXEC') or die('Restricted access');
?><div class="grid-x grid-margin-x">
	<div class="large-auto medium-8 cell">
        <?php echo acym_filterSearch($data['search'], 'lists_search', 'ACYM_SEARCH'); ?>
	</div>
	<div class="large-auto medium-4 cell">
        <?php
        $allTags = new stdClass();
        $allTags->name = acym_translation('ACYM_ALL_TAGS');
        $allTags->value = '';
        array_unshift($data['tags'], $allTags);

        echo acym_select($data['tags'], 'lists_tag', $data['tag'], 'class="acym__lists__filter__tags"', 'value', 'name');
        ?>
	</div>
	<div class="xxlarge-4 xlarge-3 hide-for-large-only medium-auto hide-for-small-only cell"></div>
	<div class="medium-shrink cell">
		<button data-task="settings" class="button acy_button_submit"><?php echo acym_translation('ACYM_CREATE_NEW_LIST'); ?></button>
	</div>
</div>
