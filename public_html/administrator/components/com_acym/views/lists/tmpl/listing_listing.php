<?php
defined('_JEXEC') or die('Restricted access');
?><?php if (empty($data['lists'])) { ?>
	<h1 class="cell acym__listing__empty__search__title text-center"><?php echo acym_translation('ACYM_NO_RESULTS_FOUND'); ?></h1>
<?php } else { ?>
	<div class="cell grid-x margin-top-1">
		<div class="grid-x acym__listing__actions cell auto">
            <?php
            $actions = [
                'delete' => acym_translation('ACYM_DELETE'),
                'setActive' => acym_translation('ACYM_ENABLE'),
                'setInactive' => acym_translation('ACYM_DISABLE'),
            ];
            echo acym_listingActions($actions);
            ?>
			<div class="auto cell">
                <?php
                $options = [
                    '' => ['ACYM_ALL', $data['listNumberPerStatus']['all']],
                    'active' => ['ACYM_ACTIVE', $data['listNumberPerStatus']['active']],
                    'inactive' => ['ACYM_INACTIVE', $data['listNumberPerStatus']['inactive']],
                    'visible' => ['ACYM_VISIBLE', $data['listNumberPerStatus']['visible']],
                    'invisible' => ['ACYM_INVISIBLE', $data['listNumberPerStatus']['invisible']],
                ];
                echo acym_filterStatus($options, $data['status'], 'lists_status');
                ?>
			</div>
		</div>
		<div class="grid-x cell auto">
			<div class="cell acym_listing_sort-by">
                <?php echo acym_sortBy(
                    [
                        'id' => strtolower(acym_translation('ACYM_ID')),
                        'name' => acym_translation('ACYM_NAME'),
                        'creation_date' => acym_translation('ACYM_DATE_CREATED'),
                        'active' => acym_translation('ACYM_ACTIVE'),
                        'visible' => acym_translation('ACYM_VISIBLE'),
                    ],
                    'lists'
                ); ?>
			</div>
		</div>
	</div>
	<div class="grid-x acym__listing acym__listing__view__list">
		<div class="grid-x cell acym__listing__header">
			<div class="medium-shrink small-1 cell">
				<input id="checkbox_all" type="checkbox" name="checkbox_all">
			</div>
			<div class="grid-x medium-auto small-11 cell acym__listing__header__title__container">
				<div class="acym__listing__header__title cell small-8 medium-4">
                    <?php echo acym_translation('ACYM_LIST'); ?>
				</div>
				<div class="acym__listing__header__title cell small-3 medium-3 text-center">
                    <?php echo acym_translation('ACYM_USERS'); ?>
				</div>
				<div class="acym__listing__header__title cell hide-for-small-only medium-2 text-center">
                    <?php echo acym_translation('ACYM_ACTIVE'); ?>
				</div>
				<div class="acym__listing__header__title cell hide-for-small-only medium-2 text-center">
                    <?php echo acym_translation('ACYM_VISIBLE'); ?>
				</div>
				<div class="acym__listing__header__title cell hide-for-small-only medium-1 text-center">
                    <?php echo acym_translation('ACYM_ID'); ?>
				</div>
			</div>
		</div>
        <?php foreach ($data['lists'] as $list) { ?>
			<div data-acy-elementid="<?php echo acym_escape($list->id); ?>" class="grid-x cell acym__listing__row">
				<div class="medium-shrink small-1 cell">
					<input id="checkbox_<?php echo acym_escape($list->id); ?>" type="checkbox" name="elements_checked[]" value="<?php echo acym_escape($list->id); ?>">
				</div>
				<div class="grid-x medium-auto small-11 cell acym__listing__title__container">
					<div class="grid-x medium-4 small-8 cell acym__listing__title">
						<i class='cell shrink acymicon-circle' style="color:<?php echo acym_escape($list->color); ?>"></i>
						<a class="cell auto" href="<?php echo acym_completeLink(acym_getVar('cmd', 'ctrl').'&task=settings&id='.intval($list->id)); ?>">
                            <?php echo '<h6 class="acym__listing__title__primary">'.acym_escape($list->name).'</h6>'; ?>
                            <?php echo '<p class="acym__listing__title__secondary">'.acym_date($list->creation_date, 'M. j, Y').'</p>'; ?>
						</a>
					</div>
					<div class="medium-3 small-3 text-center small-up-1 cell grid-x">
						<h6 class="cell acym__listing__text">
                            <?php
                            if ($this->config->get('require_confirmation', 1) == 1 && $list->sendable != $list->subscribers) {
                                if ($list->sendable < $list->subscribers && $this->config->get('require_confirmation', 1) == 1) {
                                    echo $list->sendable.acym_tooltip('<span> (+ '.($list->subscribers - $list->sendable).')</span>', acym_translation('ACYM_INACTIVE_USERS'));
                                }
                            } else {
                                echo $list->subscribers;
                            }
                            ?>
						</h6>
					</div>
					<div class="medium-2 small-1 cell acym__listing__controls acym__lists__controls grid-x">
						<div class="text-center cell">
                            <?php
                            $class = $list->active == 1 ? 'acymicon-check-circle acym__color__green" data-acy-newvalue="0' : 'acymicon-times-circle acym__color__red" data-acy-newvalue="1';
                            echo '<i data-acy-table="list" data-acy-field="active" data-acy-elementid="'.acym_escape($list->id).'" class="acym_toggleable '.$class.'"></i>';
                            ?>
						</div>
					</div>
					<div class="medium-2 hide-for-small-only cell acym__listing__controls acym__lists__controls grid-x">
						<div class="text-center cell">
                            <?php
                            $class = $list->visible == 1 ? 'acymicon-eye" data-acy-newvalue="0' : 'acymicon-eye-slash acym__color__dark-gray" data-acy-newvalue="1';
                            echo '<i data-acy-table="list" data-acy-field="visible" data-acy-elementid="'.acym_escape($list->id).'" class="acym_toggleable '.$class.'"></i>';
                            ?>
						</div>
					</div>
					<div class="medium-1 hide-for-small-only grid-x">
						<h6 class="cell text-center acym__listing__text"><?php echo acym_escape($list->id); ?></h6>
					</div>
				</div>
			</div>
        <?php } ?>
	</div>
    <?php echo $data['pagination']->display('lists'); ?>
<?php } ?>

