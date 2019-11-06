<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA SAS - All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<div id="acym__list__settings" class="acym__content">
	<form id="acym_form" action="<?php echo acym_completeLink(acym_getVar('cmd', 'ctrl')); ?>" method="post" name="acyForm" data-abide novalidate>
        <?php
        $workflow = acym_get('helper.workflow');
        if (empty($data['listInformation']->id)) $workflow->disabledAfter = $this->step;
        echo $workflow->display($this->steps, $this->step);
        ?>
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
					<div class="cell grid-x acym__list__settings__active medium-auto">
                        <?php echo acym_switch('list[active]', acym_escape($data['listInformation']->active), acym_translation('ACYM_ACTIVE'), [], 'shrink', 'shrink', 'tiny margin-0'); ?>
					</div>
                    <?php if (!empty($data['listInformation']->id)) { ?>
						<p class="cell margin-bottom-1 medium-auto text-center" id="acym__lists__settings__list-color">
                            <?php echo acym_translation('ACYM_COLOR'); ?> :
							<input type='text' name="list[color]" id="acym__list__settings__color-picker" value="<?php echo acym_escape($data["listInformation"]->color); ?>" />
						</p>
						<p class="cell margin-bottom-1 medium-auto text-right" id="acym__list__settings__list-id"><?php echo acym_translation('ACYM_LIST_ID'); ?> : <b class="acym__color__blue"><?php echo acym_escape($data['listInformation']->id); ?></b></p>
                    <?php } else { ?>
						<p class="cell margin-bottom-1 medium-auto text-center" id="acym__lists__settings__list-color">
                            <?php echo acym_translation('ACYM_COLOR'); ?> :
							<input type='text' name="list[color]" id="acym__list__settings__color-picker" value="<?php echo acym_escape($data["listInformation"]->color); ?>" />
						</p>
                    <?php } ?>
				</div>
			</div>
			<div class="cell grid-x margin-bottom-1 xlarge-7 small-12 text-center">
				<div class="cell grid-x acym__list__settings__tmpls acym__content">
					<div class="cell grid-y medium-4 medium-margin-right-1 text-center acym__list__settings__subscribers">
						<div class="cell large-2 small-4 acym__list__settings__tmpls__title grid-x align-center acym_vcenter"><label><?php echo acym_translation('ACYM_SUBSCRIBERS'); ?></label><i class="acymicon-group margin-left-1"></i></div>
						<div class="cell large-10 small-8 align-center acym_vcenter acym__list__settings__subscribers__nb grid-x">
                            <?php
                            if ($this->config->get('require_confirmation', 1) == 1 && $data['listInformation']->subscribers['nbSubscribers'] != $data['listInformation']->subscribers['sendable']) {
                                ?>
								<div class="cell grid-x">
									<div class="cell small-4 acym__color__blue text-right"><?= $data['listInformation']->subscribers['sendable']; ?>&nbsp;</div>
									<div class="cell small-8 text-left"><?= acym_translation('ACYM_CONFIRMED'); ?></div>
									<div class="cell small-4 acym__color__blue text-right"><?php echo($data['listInformation']->subscribers['nbSubscribers'] - $data['listInformation']->subscribers['sendable']); ?>&nbsp;</div>
									<div class="cell small-8 text-left"><?= acym_translation('ACYM_PENDING'); ?></div>
								</div>
                            <?php } else { ?>
								<div class="cell grid-x">
									<div class="cell small-4 acym__color__blue text-right"><?= $data['listInformation']->subscribers['nbSubscribers']; ?>&nbsp;</div>
									<div class="cell small-8 text-left"><?= acym_translation('ACYM_USERS'); ?></div>
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
		<div class="cell grid-x">
			<div class="cell medium-shrink medium-margin-bottom-0 margin-bottom-1 text-left">
                <?php echo acym_backToListing("lists"); ?>
			</div>
			<div class="cell medium-auto grid-x text-right">
				<div class="cell medium-auto"></div>
				<button data-task="save" data-step="listing" type="submit" class="cell medium-shrink button medium-margin-bottom-0 margin-right-1 acy_button_submit button-secondary">
                    <?php echo acym_translation('ACYM_SAVE_EXIT'); ?>
				</button>
				<button data-task="save" data-step="subscribers" type="submit" class="cell medium-shrink button margin-bottom-0 acy_button_submit">
                    <?php echo acym_translation('ACYM_SAVE_CONTINUE'); ?><i class="fa fa-chevron-right"></i>
				</button>
			</div>
		</div>
		<input type="hidden" name="id" value="<?php echo acym_escape($data['listInformation']->id); ?>">
		<input type="hidden" name="list[welcome_id]" value="<?php echo acym_escape($data['listInformation']->welcome_id); ?>">
		<input type="hidden" name="list[unsubscribe_id]" value="<?php echo acym_escape($data['listInformation']->unsubscribe_id); ?>">
        <?php acym_formOptions(true, 'edit', 'settings'); ?>
	</form>
</div>

