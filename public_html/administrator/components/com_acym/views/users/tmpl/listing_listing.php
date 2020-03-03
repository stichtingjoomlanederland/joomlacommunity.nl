<?php
defined('_JEXEC') or die('Restricted access');
?><?php if (empty($data['allUsers'])) { ?>
	<h1 class="cell acym__listing__empty__search__title text-center"><?php echo acym_translation('ACYM_NO_RESULTS_FOUND'); ?></h1>
<?php } else { ?>
	<div class="cell grid-x margin-top-1">
		<div class="grid-x acym__listing__actions auto cell">
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
                    '' => ['ACYM_ALL', $data["userNumberPerStatus"]["all"]],
                    'active' => ['ACYM_ACTIVE', $data["userNumberPerStatus"]["active"]],
                    'inactive' => ['ACYM_INACTIVE', $data["userNumberPerStatus"]["inactive"]],
                ];
                echo acym_filterStatus($options, $data["status"], 'users_status');
                ?>
			</div>
		</div>
		<div class="grid-x grid-x cell auto">
			<div class="cell acym_listing_sort-by">
                <?php echo acym_sortBy(
                    [
                        'id' => strtolower(acym_translation('ACYM_ID')),
                        'email' => acym_translation('ACYM_EMAIL'),
                        'name' => acym_translation('ACYM_NAME'),
                        'creation_date' => acym_translation('ACYM_DATE_CREATED'),
                        'active' => acym_translation('ACYM_ACTIVE'),
                        'confirmed' => acym_translation('ACYM_CONFIRMED'),
                    ],
                    'users'
                ); ?>
			</div>
		</div>
	</div>
	<div class="grid-x acym__listing">
		<div class="grid-x cell acym__listing__header">
			<div class="medium-shrink small-1 cell">
				<input id="checkbox_all" type="checkbox" name="checkbox_all">
			</div>
			<div class="grid-x medium-auto small-11 cell acym__listing__header__title__container">
				<div class="medium-4 small-7 cell acym__listing__header__title">
                    <?php echo acym_translation('ACYM_EMAIL'); ?>
				</div>
				<div class="cell hide-for-small-only hide-for-medium-only large-2 acym__listing__header__title">
                    <?= acym_translation('ACYM_NAME'); ?>
				</div>
                <?php
                if (!empty($data['fields'])) {
                    foreach ($data['fields'] as $field) {
                        ?>
						<div class="medium-auto hide-for-small-only cell acym__listing__header__title text-center">
                            <?php echo acym_escape($field); ?>
						</div>
                        <?php
                    }
                }
                ?>
				<div class="medium-auto hide-for-small-only cell acym__listing__header__title">
                    <?php echo acym_translation('ACYM_LISTS'); ?>
				</div>
                <?php if (acym_isAdmin()) { ?>
					<div class="medium-1 hide-for-small-only text-center cell acym__listing__header__title">
                        <?php echo acym_translation_sprintf('ACYM_CMS_USER', ACYM_CMS_TITLE); ?>
					</div>
                <?php } ?>
				<div class="medium-1 small-5 text-right medium-text-center cell acym__listing__header__title">
                    <?php echo acym_translation('ACYM_ACTIVE'); ?>
				</div>
                <?php if ($this->config->get('require_confirmation', '0') == '1') { ?>
					<div class="large-1 medium-2 hide-for-small-only text-center cell acym__listing__header__title">
                        <?php echo acym_translation('ACYM_CONFIRMED'); ?>
					</div>
                <?php } ?>
				<div class="medium-1 hide-for-small-only text-center cell acym__listing__header__title">
                    <?php echo acym_translation('ACYM_ID'); ?>
				</div>
			</div>
		</div>
        <?php
        foreach ($data['allUsers'] as $user) {
            ?>
			<div class="grid-x cell acym__listing__row">
				<div class="medium-shrink small-1 cell">
					<input id="checkbox_<?php echo acym_escape($user->id); ?>" type="checkbox" name="elements_checked[]" value="<?php echo acym_escape($user->id); ?>">
				</div>
				<div class="grid-x medium-auto small-11 cell acym__listing__title__container">
					<div class="grid-x cell medium-4 small-9 acym__listing__title">
						<a class="cell auto" href="<?php echo acym_completeLink(acym_getVar('cmd', 'ctrl').'&task=edit&id='.$user->id); ?>">
							<div><?php echo acym_escape($user->email); ?></div>
						</a>
					</div>
					<div class="cell hide-for-small-only hide-for-medium-only large-2 acym__listing__header__title">
                        <?= acym_escape($user->name); ?>
					</div>
                    <?php
                    if (!empty($user->fields)) {
                        foreach ($user->fields as $field) {
                            ?>
							<div class="medium-auto hide-for-small-only cell text-center">
                                <?php echo acym_escape($field); ?>
							</div>
                            <?php
                        }
                    }
                    ?>
					<div class="acym__users__subscription medium-auto small-11 cell">
                        <?php if (!empty($data['usersSubscriptions'][$user->id])) {
                            $counter = 0;
                            foreach ($data['usersSubscriptions'][$user->id] as $oneSub) {
                                if ($counter < 5) {
                                    echo acym_tooltip('<i class="acym_subscription acymicon-circle" style="color:'.acym_escape($oneSub->color).'"></i>', acym_escape($oneSub->name));
                                } else {
                                    echo acym_tooltip('<i class="acym_subscription acym_subscription_more acymicon-circle" style="color:'.acym_escape($oneSub->color).'"></i>', acym_escape($oneSub->name));
                                }
                                $counter++;
                            }
                            if ($counter > 5) {
                                $counter = $counter - 5;
                                echo '<span class="acym__user__show-subscription acymicon-stack hide-for-medium" data-iscollapsed="0" value="'.$counter.'">
													<i class="acym__user__button__showsubscription acymicon-circle acymicon-stack-2x"></i>
													<h6 class="acym__listing__text acym__user__show-subscription-bt acymicon-stack-1x">+'.$counter.'</h6>
												</span>';
                            }
                        } ?>

					</div>
                    <?php if (acym_isAdmin()) { ?>
						<div class="cell hide-for-small-only medium-1 text-center">
                            <?php
                            if (empty($user->cms_id)) {
                                echo '-';
                            } else {
                                echo '<a href="'.acym_getCmsUserEdit($user->cms_id).'" target="_blank">'.$user->cms_id.'</a>';
                            }
                            ?>
						</div>
                    <?php } ?>
					<div class="acym__listing__controls acym__users__controls small-1 text-center cell">
                        <?php
                        $class = $user->active == 1 ? 'acymicon-check-circle acym__color__green" data-acy-newvalue="0' : 'acymicon-times-circle acym__color__red" data-acy-newvalue="1';
                        echo '<i data-acy-table="user" data-acy-field="active" data-acy-elementid="'.acym_escape($user->id).'" class="acym_toggleable '.$class.'"></i>';
                        ?>
					</div>
                    <?php if ($this->config->get('require_confirmation', '0') == '1') { ?>
						<div class="acym__listing__controls acym__users__controls hide-for-small-only large-1 medium-2 text-center cell">
                            <?php
                            $class = $user->confirmed == 1 ? 'acymicon-check-circle acym__color__green" data-acy-newvalue="0' : 'acymicon-times-circle acym__color__red" data-acy-newvalue="1';
                            echo '<i data-acy-table="user" data-acy-field="confirmed" data-acy-elementid="'.acym_escape($user->id).'" class="acym_toggleable '.$class.'"></i>';
                            ?>
						</div>
                    <?php } ?>
					<h6 class="text-center medium-1 hide-for-small-only acym__listing__text"><?php echo acym_escape($user->id); ?></h6>
				</div>
			</div>
            <?php
        }
        ?>
	</div>
    <?php
    echo $data['pagination']->display('users');
}

