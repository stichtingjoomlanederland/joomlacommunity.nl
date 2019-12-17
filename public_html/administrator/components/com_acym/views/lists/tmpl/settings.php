<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.6.1
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA SAS - All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<div id="acym__list__settings" class="acym__content">
	<form id="acym_form" action="<?php echo acym_completeLink(acym_getVar('cmd', 'ctrl')); ?>" method="post" name="acyForm" data-abide novalidate>
		<div class="cell grid-x text-right">
			<h5 class="cell medium-auto margin-bottom-1 medium-text-left text-center font-bold"><?php echo acym_translation('ACYM_LIST'); ?></h5>
            <?php if (!empty($data['listInformation']->id)) { ?>
				<button type="button" id="acym__button--delete" class="cell shrink button acym__user__button alert acy_button_submit" data-task="deleteOne"><i class="acymicon-delete acym__list__display__delete__icon acym__color__white"></i></button>
                <?php
                if (!empty($data['subscribersEntitySelect'])) {
                    echo $data['subscribersEntitySelect'];
                }
            }
            ?>
			<button type="submit" data-task="apply" class="cell acy_button_submit button-secondary button medium-shrink acym__user__button margin-right-1"><?php echo acym_translation('ACYM_SAVE'); ?></button>
			<button type="submit" data-task="save" class="cell acy_button_submit button medium-shrink acym__user__button"><?php echo acym_translation('ACYM_SAVE_EXIT'); ?></button>
		</div>
		<div class="grid-x margin-bottom-1 grid-margin-x">
			<div class="cell grid-x margin-bottom-1 xlarge-5 small-12 acym__content">
				<div class="cell">
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
						<input type='text' name="list[color]" id="acym__list__settings__color-picker" value="<?php echo acym_escape($data["listInformation"]->color); ?>" />
					</p>
					<div class="cell grid-x acym__list__settings__visible small-6">
                        <?php echo acym_switch('list[visible]', acym_escape($data['listInformation']->visible), acym_translation('ACYM_VISIBLE'), [], 'shrink', 'shrink', 'tiny margin-0'); ?>
					</div>
                    <?php if (!empty($data['listInformation']->id)) { ?>
						<p class="cell margin-bottom-1 small-6 text-center" id="acym__list__settings__list-id"><?php echo acym_translation('ACYM_LIST_ID'); ?> : <b class="acym__color__blue"><?php echo acym_escape($data['listInformation']->id); ?></b></p>
                    <?php } ?>

				</div>
			</div>
			<div class="cell grid-x margin-bottom-1 xlarge-7 small-12 text-center">
				<div class="cell grid-x acym__list__settings__tmpls acym__content">
					<div class="cell grid-y medium-4 medium-margin-right-1 text-center acym__list__settings__subscriber__nb">
						<div class="cell small-2 acym__list__settings__tmpls__title grid-x align-center acym_vcenter"><label><?php echo acym_translation('ACYM_SUBSCRIBERS'); ?></label><i class="acymicon-group margin-left-1"></i></div>
						<div class="cell small-10 align-center acym_vcenter acym__list__settings__subscriber__nb__display grid-x">
                            <?php
                            if ($this->config->get('require_confirmation', 1) == 1 && $data['listInformation']->subscribers['nbSubscribers'] != $data['listInformation']->subscribers['sendable']) {
                                ?>
								<div class="cell grid-x">
									<div class="cell small-4 text-right"><a href="#subscribers" class="acym__color__blue"><?= $data['listInformation']->subscribers['sendable']; ?>&nbsp;</a></div>
									<div class="cell small-8 text-left"><a href="#subscribers"><?= acym_translation('ACYM_CONFIRMED'); ?></a></div>
									<div class="cell small-4 text-right"><a href="#subscribers" class="acym__color__blue"><?php echo($data['listInformation']->subscribers['nbSubscribers'] - $data['listInformation']->subscribers['sendable']); ?>&nbsp;</a></div>
									<div class="cell small-8 text-left"><a href="#subscribers"><?= acym_translation('ACYM_PENDING'); ?></a></div>
								</div>
                            <?php } else { ?>
								<div class="cell grid-x">
									<div class="cell small-4 text-right"><a href="#subscribers" class="acym__color__blue"><?= $data['listInformation']->subscribers['nbSubscribers']; ?>&nbsp;</a></div>
									<div class="cell small-8 text-left"><a href="#subscribers"><?= acym_translation('ACYM_USERS'); ?></a></div>
								</div>
                            <?php } ?>
						</div>
					</div>
					<div class="cell grid-y medium-4 medium-margin-right-1 acym__list__settings__tmpls__welcome">
						<div class="cell small-2 acym__list__settings__tmpls__title align-center acym_vcenter">
                            <?php echo acym_tooltip('<label>'.acym_translation('ACYM_WELCOME_MAIL').'</label>', '('.acym_translation('ACYM_OPTIONAL').') '.acym_translation('ACYM_WELCOME_MAIL_DESC')); ?>
							<i class="acymicon-email margin-left-1"></i>
						</div>
						<div class="cell grid-x acym__template__block align-center acym_vcenter small-10 acym__list__button__add__mail">
                            <?php
                            if (empty($data['listInformation']->id)) {
                                echo acym_tooltip('<i class="fa fa-ban acym__list__button__add__mail__disabled"></i>', acym_translation('ACYM_SAVE_LIST_FIRST'));
                            } elseif (empty($data['listInformation']->welcome_id)) { ?>
								<a class="acym_vcenter text-center align-center acym__color__white acym__list__button__add__mail__welcome__unsub" href="<?= $data['tmpls']['welcomeTmplUrl']; ?>">
									<i class="acymicon-add"></i>
								</a>
                            <?php } else { ?>
								<button type="button" template="<?php echo acym_escape($data['listInformation']->welcome_id); ?>" class="cell acym__templates__oneTpl acym__listing__block acym_template_option">
									<div class="text-center cell acym__listing__block__delete acym__background-color__red">
										<div>
											<i class='fa fa-trash-o acym__listing__block__delete__trash acym__color__white'></i>
											<p class="acym__listing__block__delete__cancel acym__background-color__very-dark-gray acym__color__white">
                                                <?php echo acym_translation('ACYM_CANCEL'); ?>
											</p>
											<p class="acym__listing__block__delete__submit acym__color__white acy_button_submit" data-task="unsetWelcome"><?php echo acym_translation('ACYM_DELETE'); ?></p>
										</div>
									</div>
									<a href="<?= $data['tmpls']['welcomeTmplUrl']; ?>">
										<div class="cell grid-x text-center">
											<div class="cell acym__templates__pic text-center">
												<img src="<?php echo acym_getMailThumbnail($data['tmpls']['welcome']->thumbnail); ?>" alt="<?php echo acym_escape($data['tmpls']['welcome']->name); ?>" />
											</div>
											<div class="cell grid-x text-center acym__templates__footer">
												<div class="cell acym__template__footer__title"><?php echo acym_escape($data['tmpls']['welcome']->name); ?></div>
											</div>
										</div>
									</a>
								</button>
                            <?php } ?>
						</div>
					</div>
					<div class="cell grid-y medium-4 medium-margin-right-1 acym__list__settings__tmpls__unsubscribe">
						<div class="cell small-2 acym__list__settings__tmpls__title align-center acym_vcenter">
                            <?php echo acym_tooltip('<label>'.acym_translation('ACYM_UNSUBSCRIBE_MAIL').'</label>', '('.acym_translation('ACYM_OPTIONAL').') '.acym_translation('ACYM_UNSUBSCRIBE_MAIL_DESC')); ?>
							<i class="acymicon-email margin-left-1"></i>
						</div>
						<div class="cell grid-x acym__template__block align-center acym_vcenter small-10 acym__list__button__add__mail">
                            <?php
                            if (empty($data['listInformation']->id)) {
                                echo acym_tooltip('<i class="fa fa-ban acym__list__button__add__mail__disabled"></i>', acym_translation('ACYM_SAVE_LIST_FIRST'));
                            } elseif (empty($data['listInformation']->unsubscribe_id)) { ?>
								<a class="acym_vcenter text-center align-center acym__color__white acym__list__button__add__mail__welcome__unsub" href="<?= $data['tmpls']['unsubTmplUrl']; ?>">
									<i class="acymicon-add"></i>
								</a>
                            <?php } else { ?>
								<button type="button" template="<?php echo acym_escape($data['listInformation']->unsubscribe_id); ?>" class="cell acym__templates__oneTpl acym__listing__block acym_template_option">
									<div class="text-center cell acym__listing__block__delete acym__background-color__red">
										<div>
											<i class='fa fa-trash-o acym__listing__block__delete__trash acym__color__white'></i>
											<p class="acym__listing__block__delete__cancel acym__background-color__very-dark-gray acym__color__white">
                                                <?php echo acym_translation('ACYM_CANCEL'); ?>
											</p>
											<p class="acym__listing__block__delete__submit acym__color__white acy_button_submit" data-task="unsetUnsubscribe"><?php echo acym_translation('ACYM_DELETE'); ?></p>
										</div>
									</div>
									<a href="<?= $data['tmpls']['unsubTmplUrl']; ?>">
										<div class="cell grid-x text-center">
											<div class="cell acym__templates__pic text-center">
												<img src="<?php echo acym_getMailThumbnail($data['tmpls']['unsubscribe']->thumbnail); ?>" alt="<?php echo acym_escape($data['tmpls']['unsubscribe']->name); ?>" />
											</div>
											<div class="cell grid-x text-center acym__templates__footer">
												<div class="cell acym__template__footer__title"><?php echo acym_escape($data['tmpls']['unsubscribe']->name); ?></div>
											</div>
										</div>
									</a>
								</button>
                            <?php } ?>
						</div>
					</div>
				</div>
			</div>
			<div class="cell grid-x align-middle text-center acym__list__settings__stats acym__content margin-0">
                <?php
                echo acym_round_chart('', $data['listStats']['deliveryRate'], 'delivery', 'cell large-auto medium-6 small-12', '<label>'.acym_translation('ACYM_DELIVERY_RATE').'</label>');
                echo acym_round_chart('', $data['listStats']['openRate'], 'open', 'cell large-auto medium-6 small-12', '<label>'.acym_translation('ACYM_OPEN_RATE').'</label>');
                echo acym_round_chart('', $data['listStats']['clickRate'], 'click', 'cell large-auto medium-6 small-12', '<label>'.acym_translation('ACYM_CLICK_RATE').'</label>');
                echo acym_round_chart('', $data['listStats']['failRate'], 'fail', 'cell large-auto medium-6 small-12', '<label>'.acym_translation('ACYM_FAIL_RATE').'</label>');
                echo acym_round_chart('', $data['listStats']['bounceRate'], '', 'cell large-auto medium-6 small-12', '<label>'.acym_translation('ACYM_BOUNCE_RATE').'</label>');
                ?>
			</div>
		</div>

		<div class="grid-x acym__list__settings__subscribers acym__content" id="acym__list__settings__subscribers">
			<a name="subscribers"></a>
			<input type="hidden" id="subscribers_subscribed" value="<?php echo acym_escape(json_encode($data['subscribers'])); ?>" />
			<input type="hidden" id="requireConfirmation" value="<?php echo acym_escape($data['requireConfirmation']); ?>" />
			<h5 class="cell font-bold"><?php echo acym_translation('ACYM_SUBSCRIBERS'); ?></h5>

			<div class="cell grid-x acym__list__settings__subscribers__search">
				<div class="medium-9"></div>
				<div class="cell medium-3"><input type="text" class="acym__light__input" v-model="searchSubscribers" placeholder="<?= acym_translation('ACYM_SEARCH'); ?>"></div>
			</div>
			<div v-show="displayedSubscribers.length > 0" style="display:none;" class="cell grid-x">
				<div class="grid-x cell acym__listing acym__listing__header hide-for-medium-only hide-for-small-only">
					<div class="cell" :class="requireConfirmation==1?'large-4':'large-5'"><?= acym_translation('ACYM_EMAIL'); ?></div>
					<div class="cell" :class="requireConfirmation==1?'large-3':'large-4'"><?= acym_translation('ACYM_NAME'); ?></div>
					<div class="cell large-2"><?= acym_translation('ACYM_SUBSCRIPTION_DATE'); ?></div>
					<div class="cell large-1" v-show="requireConfirmation==1"><?= acym_translation('ACYM_STATUS'); ?></div>
					<div class="cell large-2"></div>
				</div>
				<div class="grid-x cell acym__listing acym__list__settings__subscribers__listing" v-infinite-scroll="loadMoreSubscriber" :infinite-scroll-disabled="busy">
					<div class="grid-x cell acym__listing__row" v-for="(sub, index) in displayedSubscribers">
						<div class="cell small-12 large-4">
							<h6 :class="sub.confirmed==1 || requireConfirmation==0?'':'acym__color__dark-gray'">{{ sub.email }}</h6>
						</div>
						<div class="cell medium-7 small-10" :class="requireConfirmation==1?'large-3':'large-4'">
							<span :class="sub.confirmed==1 || requireConfirmation==0?'':'acym__color__dark-gray'">{{ sub.name }}</span>
						</div>
						<div class="large-2 hide-for-medium-only hide-for-small-only cell">
							<span :class="sub.confirmed==1 || requireConfirmation==0?'':'acym__color__dark-gray'">{{ sub.subscription_date }}</span>
						</div>
						<div class="cell large-1 hide-for-medium-only hide-for-small-only" v-show="requireConfirmation==1 && sub.confirmed==0">
							<span class="acym__color__dark-gray">
								<?= acym_translation('ACYM_PENDING'); ?>
							</span>
						</div>
						<div class="cell large-1 hide-for-medium-only hide-for-small-only" v-show="requireConfirmation==1 && sub.confirmed==1">
							<span><?= acym_translation('ACYM_CONFIRMED'); ?></span>
						</div>
						<div class="large-2 medium-5 small-2 cell acym__list__settings__subscribers__users--action acym__list__action--unsubscribe_one" v-on:click="unsubscribeUser(sub.id)">
							<i class="fa fa-times-circle"></i><span class="hide-for-small-only"><?php echo strtolower(acym_translation('ACYM_UNSUBSCRIBE')); ?></span>
						</div>
					</div>
				</div>
			</div>
			<div class="cell grid-x align-center acym__list__subscribers__loading margin-top-1" v-show="loading">
				<div class="cell text-center acym__list__subscribers__loading__title"><?= acym_translation('ACYM_WE_ARE_LOADING_YOUR_DATA'); ?></div>
				<div class="cell grid-x shrink margin-top-1"><?= $data['svg']; ?></div>
			</div>
			<div class="grid-x cell acym__listing v-align-top acym__list__settings__subscribers__listing" v-show="displayedSubscribers.length==0 && !loading" style="display:none;">
				<span><?= acym_translation('ACYM_NO_USERS_FOUND'); ?></span>
			</div>
		</div>

		<input type="hidden" name="id" value="<?php echo acym_escape($data['listInformation']->id); ?>">
		<input type="hidden" name="list[welcome_id]" value="<?php echo acym_escape($data['listInformation']->welcome_id); ?>">
		<input type="hidden" name="list[unsubscribe_id]" value="<?php echo acym_escape($data['listInformation']->unsubscribe_id); ?>">
        <?php acym_formOptions(true, 'edit', 'settings'); ?>
	</form>
</div>

