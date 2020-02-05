<?php
defined('_JEXEC') or die('Restricted access');
?><div class="grid-x grid-margin-x cell">
	<div class="large-auto medium-12 cell">
        <?php echo acym_filterSearch($data['search'], 'users_search', 'ACYM_SEARCH'); ?>
	</div>
	<div class="large-auto show-for-xlarge cell"></div>
	<div class="large-shrink medium-6 small-12 cell">
		<button data-task="import" class="button button-secondary expanded acy_button_submit">
            <?php echo acym_translation('ACYM_IMPORT'); ?>
		</button>
	</div>
	<div class="large-shrink medium-6 small-12 cell">
		<button type="submit" data-task="export" class="button expanded button-secondary acy_button_submit">
            <?php echo acym_translation('ACYM_EXPORT'); ?> (<span id="acym__users__listing__number_to_export" data-default="<?php echo strtolower(acym_translation('ACYM_ALL')); ?>"><?php echo strtolower(acym_translation('ACYM_ALL')); ?></span>)
		</button>
	</div>
	<div class="large-shrink medium-6 small-12 cell">
        <?php
        $entityHelper = acym_get('helper.entitySelect');

        echo acym_modal(
            acym_translation('ACYM_ADD_TO_LIST').' (<span id="acym__users__listing__number_to_add_to_list">0</span>)',
            $entityHelper->entitySelect('list', ['join' => ''], ['name', 'id'], ['text' => acym_translation('ACYM_SUBSCRIBE_USERS_TO_THESE_LISTS'), 'action' => 'addToList']),
            null,
            '',
            'class="button button-secondary disabled expanded" id="acym__users__listing__button--add-to-list"'
        );
        ?>
	</div>
	<div class="large-shrink medium-6 small-12 cell">
		<button data-task="edit" class="button expanded acy_button_submit">
            <?php echo acym_translation('ACYM_CREATE'); ?>
		</button>
	</div>
</div>

