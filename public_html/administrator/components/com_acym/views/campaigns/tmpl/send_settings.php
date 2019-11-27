<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.5.2
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA SAS - All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<div id="acym__campaign__sendsettings">
	<form id="acym_form" action="<?php echo acym_completeLink(acym_getVar('cmd', 'ctrl')); ?>" method="post" name="acyForm" class="cell grid-x acym__form__campaign__edit" data-abide>
		<input type="hidden" value="<?php echo acym_escape($data['currentCampaign']->id); ?>" name="id">
		<input type="hidden" value="<?php echo acym_escape($data['from']); ?>" name="from">
		<input type="hidden" name="sending_type" value="<?php echo $data['currentCampaign']->sending_type; ?>">
		<div class="large-auto"></div>
		<div id="acym__campaigns" class="cell xxlarge-9 grid-x grid-margin-x acym__content">

            <?php
            $workflow = acym_get('helper.workflow');
            echo $workflow->display($this->steps, $this->step);
            ?>

			<h5 class="cell acym__campaign__sendsettings__title-settings"><?php echo acym_translation('ACYM_SENDER_INFORMATION'); ?></h5>
			<div class="cell grid-x align-center margin-top-1">
				<div class="cell grid-x medium-11 grid-margin-x">
					<div class="cell medium-5">
						<label for="acym__campaign__sendsettings__from-name" class="cell acym__campaign__sendsettings__label-settings"><?php echo acym_translation('ACYM_FROM_NAME'); ?></label>
						<input type="text" id="acym__campaign__sendsettings__from-name" class="cell acym__light__input" value="<?php echo acym_escape($data['senderInformations']->from_name); ?>" name="senderInformation[from_name]" placeholder="<?php echo acym_escape($data['senderInformations']->from_name) == '' ? empty($data['config_values']->from_name) ? 'Default Value' : 'Default : '.acym_escape($data['config_values']->from_name) : ''; ?>">
					</div>
					<div class="cell medium-1"></div>
					<div class="cell medium-5">
						<label for="acym__campaign__sendsettings__from-email" class="cell acym__campaign__sendsettings__label-settings"><?php echo acym_translation('ACYM_FROM_EMAIL'); ?></label>
						<input type="email" id="acym__campaign__sendsettings__from-email" class="cell acym__light__input" value="<?php echo acym_escape($data['senderInformations']->from_email); ?>" name="senderInformation[from_email]" placeholder="<?php echo acym_escape($data['senderInformations']->from_email == '' ? empty($data['config_values']->from_email) ? 'Default Value' : 'Default : '.acym_escape($data['config_values']->from_email) : ''); ?>">
					</div>

					<div class="cell medium-5">
						<label for="acym__campaign__sendsettings__reply-name" class="cell acym__campaign__sendsettings__label-settings"><?php echo acym_translation('ACYM_REPLYTO_NAME'); ?></label>
						<input type="text" id="acym__campaign__sendsettings__reply-name" class="cell acym__light__input" value="<?php echo acym_escape($data['senderInformations']->reply_to_name); ?>" name="senderInformation[reply_to_name]" placeholder="<?php echo acym_escape($data['senderInformations']->reply_to_name == '' ? empty($data['config_values']->reply_to_name) ? 'Default Value' : 'Default : '.acym_escape($data['config_values']->reply_to_name) : ''); ?>">
					</div>
					<div class="cell medium-1"></div>
					<div class="cell medium-5">
						<label for="acym__campaign__sendsettings__reply-email" class="cell acym__campaign__sendsettings__label-settings"><?php echo acym_translation('ACYM_REPLYTO_EMAIL'); ?></label>
						<input type="email" id="acym__campaign__sendsettings__reply-email" class="cell acym__light__input" value="<?php echo acym_escape($data['senderInformations']->reply_to_email); ?>" name="senderInformation[reply_to_email]" placeholder="<?php echo acym_escape($data['senderInformations']->reply_to_email == '' ? empty($data['config_values']->reply_to_email) ? 'Default Value' : 'Default : '.acym_escape($data['config_values']->reply_to_email) : ''); ?>">
					</div>

					<div class="cell medium-5 grid-x acym__campaign__sendsettings__bcc">
						<label for="acym__campaign__sendsettings__bcc--input" class="cell acym__campaign__sendsettings__label-settings"><?php echo acym_translation('ACYM_BCC').' '.acym_info(acym_translation('ACYM_BCC_DESC')); ?></label>
						<input type="text" class="cell acym__light__input" id="acym__campaign__sendsettings__bcc--input" name="senderInformation[bcc]" placeholder="<?php echo acym_translation('ACYM_TEST_ADDRESS'); ?>" value="<?php echo acym_escape($data['currentCampaign']->bcc); ?>">
					</div>
				</div>
			</div>

			<h5 class="cell margin-top-1 acym__campaign__sendsettings__title-settings"><?php echo acym_translation('ACYM_WHEN_EMAIL_WILL_BE_SENT'); ?></h5>
			<div class="cell grid-x align-center margin-top-1">
				<div class="cell grid-x medium-11 acym__campaign__sendsettings__send-type grid-margin-x">
                    <?php if (!empty($data['currentCampaign']->sent && empty($data['currentCampaign']->active))) { ?>
						<div class="acym__hide__div"></div>
						<h3 class="acym__title__primary__color acym__middle_absolute__text text-center"><?php echo acym_translation('ACYM_CAMPAIGN_ALREADY_QUEUED'); ?></h3>
                    <?php } ?>
					<div class="cell grid-x grid-margin-x margin-bottom-2">
						<div class="cell auto grid-x align-left">
                            <?php
                            $class = $data['currentCampaign']->send_now ? '' : 'button-radio-unselected';
                            $class .= $data['currentCampaign']->draft ? '' : ' button-radio-disabled';
                            ?>
							<button type="button" class="cell medium-6 small-12 button-radio acym__campaign__sendsettings__buttons-type <?php echo $class; ?>" id="acym__campaign__sendsettings__now" data-sending-type="<?php echo $data['campaignClass']::SENDING_TYPE_NOW; ?>"><?php echo acym_translation('ACYM_NOW'); ?></button>
						</div>
                        <?php if (acym_level(1)) {
                            $tooltip = acym_level(1) ? '' : 'data-tooltip="'.acym_translation_sprintf('ACYM_USE_THIS_FEATURE', 'AcyMailing Essential').'"';
                            $class = $data['currentCampaign']->send_scheduled ? '' : 'button-radio-unselected';
                            $class .= $data['currentCampaign']->draft ? '' : ' button-radio-disabled';
                            ?>
							<div class="cell auto grid-x align-center">
								<button type="button" <?php echo $tooltip; ?> class="cell medium-6 small-12 button-radio acym__campaign__sendsettings__buttons-type <?php echo $class; ?>" id="acym__campaign__sendsettings__scheduled" data-sending-type="<?php echo $data['campaignClass']::SENDING_TYPE_SCHEDULED; ?>"><?php echo acym_translation('ACYM_SCHEDULED'); ?></button>
							</div>
                        <?php }
                        if (acym_level(2)) {
                            $tooltip = acym_level(2) ? '' : 'data-tooltip="'.acym_translation_sprintf('ACYM_USE_THIS_FEATURE', 'AcyMailing Enterprise').'"';
                            $class = $data['currentCampaign']->send_auto ? '' : 'button-radio-unselected';
                            $class .= $data['currentCampaign']->draft ? '' : ' button-radio-disabled';
                            ?>
							<div class="cell auto grid-x align-right">
								<button type="button" <?php echo $tooltip; ?> class="cell medium-6 small-12 button-radio acym__campaign__sendsettings__buttons-type <?php echo $class; ?>" id="acym__campaign__sendsettings__auto" data-sending-type="<?php echo $data['campaignClass']::SENDING_TYPE_AUTO; ?>"><?php echo acym_translation('ACYM_AUTO'); ?></button>
							</div>
                        <?php } ?>
					</div>
				</div>
				<h5 class="cell margin-top-1 margin-bottom-1 acym__campaign__sendsettings__title-settings"><?php echo acym_translation('ACYM_ADDITIONAL_SETTINGS'); ?></h5>
				<div class="cell medium-11 grid-margin-x grid-x acym__campaign__sendsettings__params" data-show="acym__campaign__sendsettings__now" <?php echo $data['currentCampaign']->send_now ? '' : 'style="display: none"'; ?>>
					<p class="cell"><?php echo acym_translation('ACYM_SENT_AS_SOON_CAMPAIGN_SAVE'); ?></p>
				</div>
				<div class="cell grid-x medium-11 grid-margin-x acym__campaign__sendsettings__params" data-show="acym__campaign__sendsettings__scheduled" <?php echo $data['currentCampaign']->send_scheduled ? '' : 'style="display: none"'; ?>>
					<div class="grid-x cell">
						<div class="cell grid-x acym__campaign__sendsettings__display-send-type-scheduled">
							<p id="acym__campaign__sendsettings__scheduled__send-date__label" class="cell shrink"><?php echo acym_translation('ACYM_CAMPAIGN_WILL_BE_SENT'); ?></p>
							<label class="cell shrink" for="acym__campaign__sendsettings__send">
                                <?php
                                $value = empty($data['currentCampaign']->sending_date) ? '' : acym_date($data['currentCampaign']->sending_date,  'd M Y H:i');
                                echo acym_tooltip(
                                    '<input class="text-center acy_date_picker" type="text" name="sendingDate" id="acym__campaign__sendsettings__send-type-scheduled__date" value="'.acym_escape($value).'" readonly>',
                                    acym_translation('ACYM_CLICK_TO_EDIT')
                                );
                                ?>
							</label>
						</div>
					</div>
				</div>
				<div class="cell grid-x align-center">
					<div class="cell medium-11 grid-margin-x grid-x align-center acym__campaign__sendsettings__params" data-show="acym__campaign__sendsettings__auto" <?php echo $data['currentCampaign']->send_auto ? '' : 'style="display: none"'; ?>>
						<div class="cell grid-x acym_vcenter">
							<p class="cell shrink"><?php echo acym_translation('ACYM_THIS_WILL_GENERATE_CAMPAIGN_AUTOMATICALLY'); ?></p>
							<div class="cell shrink grid-x margin-left-1">
								<div class="cell shrink margin-right-1">
                                    <?php
                                    echo acym_select(
                                        $data['triggers_select'],
                                        'acym_triggers',
                                        empty($data['currentCampaign']->sending_params) ? null : key($data['currentCampaign']->sending_params),
                                        'class="acym__select"'
                                    );
                                    ?>
								</div>
								<div class="cell shrink grid-x grid-margin-x">
                                    <?php
                                    foreach ($data['triggers_display'] as $key => $display) {
                                        echo '<div class="acym__campaign__sendsettings__params__one cell grid-x" data-trigger-show="'.$key.'" style="display: none">';
                                        echo str_replace('[triggers][classic]['.$key.']', $key, $display);
                                        echo '</div>';
                                    }
                                    ?>
								</div>
							</div>
						</div>
						<div class="cell grid-x margin-top-2">
                            <?php
                            echo acym_switch(
                                'need_confirm',
                                isset($data['currentCampaign']->sending_params['need_confirm_to_send']) ? $data['currentCampaign']->sending_params['need_confirm_to_send'] : 1,
                                acym_translation('ACYM_CONFIRM_AUTOCAMPAIGN'),
                                [],
                                'shrink',
                                'shrink'
                            );
                            ?>
						</div>
					</div>
				</div>
			</div>
            <?php
            ?>
			<div class="cell grid-x acym__campaign__sendsettings__save">
				<div class="cell medium-shrink medium-margin-bottom-0 margin-bottom-1">
                    <?php echo acym_backToListing("campaigns"); ?>
				</div>
				<div class="cell medium-auto grid-x text-right">
					<div class="cell medium-auto"></div>
                    <?php if ($data['from'] == 'create') { ?>
						<button data-task="save" data-step="tests" type="submit" class="cell medium-shrink button margin-bottom-0 acy_button_submit">
                            <?php echo strtoupper(acym_translation('ACYM_SAVE_CONTINUE')); ?><i class="fa fa-chevron-right"></i>
						</button>
                    <?php } else { ?>
						<button data-task="save" data-step="listing" type="submit" class="cell button-secondary medium-shrink button medium-margin-bottom-0 margin-right-1 acy_button_submit">
                            <?php echo acym_translation('ACYM_SAVE_EXIT'); ?>
						</button>
						<button data-task="save" data-step="tests" type="submit" class="cell medium-shrink button margin-bottom-0 acy_button_submit">
                            <?php echo acym_translation('ACYM_SAVE_CONTINUE'); ?><i class="fa fa-chevron-right"></i>
						</button>
                    <?php } ?>
				</div>
			</div>
		</div>
		<div class="large-auto"></div>
        <?php acym_formOptions(false, 'edit', 'sendSettings'); ?>
	</form>
</div>

