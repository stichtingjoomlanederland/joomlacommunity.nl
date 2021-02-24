<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_ED_EMAIL_SETTINGS'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.textbox', 'notification_sender_name', 'COM_EASYDISCUSS_NOTIFICATIONS_SENDER_NAME', '', array('defaultValue' => $this->jconfig->get('fromname'))); ?>
					<?php echo $this->html('settings.textbox', 'notification_sender_email', 'COM_EASYDISCUSS_NOTIFICATIONS_SENDER_EMAIL', '', array('defaultValue' => $this->jconfig->get('mailfrom')), 'COM_ED_SENDER_INSTRUCTIONS'); ?>
					<?php echo $this->html('settings.textbox', 'notify_custom', 'COM_EASYDISCUSS_NOTIFY_CUSTOM_EMAIL_ADDRESS'); ?>
					<?php echo $this->html('settings.toggle', 'main_mailqueueonpageload', 'COM_EASYDISCUSS_SEND_EMAIL_ON_PAGE_LOAD'); ?>

					<div class="o-form-group" data-email-logo-wrapper>
						<div class="col-md-5 o-form-label">
							<?php echo $this->html('form.label', 'COM_ED_EMAIL_LOGO_SETTINGS'); ?>
						</div>

						<div class="col-md-7" data-email-logo>
							<div>
								<div class="ed-img-holder">
									<div class="ed-img-holder__remove <?php echo !ED::hasOverrideLogo('emails') ? 't-hidden' : '';?>">
										<a href="javascript:void(0);" class="o-btn o-btn--default-o o-btn--sm t-text--danger t-mb--sm" data-email-logo-restore>
											<i class="fa fa-times"></i>&nbsp; <?php echo JText::_('COM_ED_EMAIL_LOGO_SETTINGS_REMOVE_BUTTON'); ?>
										</a>
									</div>
									<img src="<?php echo ED::getLogo('emails'); ?>?<?php echo time();?>=1" width="200" data-email-logo-image />
								</div>
							</div>
							<div class="t-mt--sm">
								<input type="file" name="email_logo" class="o-form-control" />
							</div>
						</div>
					</div>

					<?php echo $this->html('settings.textbox', 'main_mailqueuenumber', 'COM_EASYDISCUSS_MAILNUMBER_PERLOAD', '', array('size' => 5), '', '', 'text-center'); ?>
					<?php echo $this->html('settings.textbox', 'main_notification_max_length', 'COM_EASYDISCUSS_TRUNCATE_EMAIL_LENGTH', '', array('size' => 5), '', '', 'text-center'); ?>

					<?php echo $this->html('settings.toggle', 'notify_modify_from', 'COM_EASYDISCUSS_SETTINGS_USE_USER_AS_FROM', '', array(), 'COM_ED_SETTINGS_USE_USER_INSTRUCTIONS'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_ED_GENERAL_NOTIFICATIONS_SETTINGS'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'notify_admin', 'COM_EASYDISCUSS_NOTIFY_ADMINS_ON_NEW_POST'); ?>
					<?php echo $this->html('settings.toggle', 'notify_admin_onreply', 'COM_EASYDISCUSS_NOTIFY_ADMINS_ON_NEW_REPLY'); ?>
					<?php echo $this->html('settings.toggle', 'notify_moderator', 'COM_EASYDISCUSS_NOTIFY_MODERATORS_ON_NEW_POST'); ?>
					<?php echo $this->html('settings.toggle', 'notify_moderator_onreply', 'COM_EASYDISCUSS_NOTIFY_MODERATORS_ON_NEW_REPLY'); ?>

					<?php echo $this->html('settings.toggle', 'notify_all', 'COM_EASYDISCUSS_NOTIFY_ALL_USERS_ON_NEW_POST'); ?>
					<?php echo $this->html('settings.toggle', 'notify_reply_all_members', 'COM_ED_NOTIFY_ALL_USERS_ON_NEW_REPLY'); ?>
					<?php echo $this->html('settings.toggle', 'notify_all_respect_category', 'COM_EASYDISCUSS_NOTIFY_ALL_USERS_RESPECT_CATEGORY_PERMISSIONS'); ?>
					<?php echo $this->html('settings.toggle', 'notify_participants', 'COM_EASYDISCUSS_NOTIFY_PARTICIPANTS_ON_NEW_REPLY'); ?>
					<?php echo $this->html('settings.toggle', 'notify_owner', 'COM_EASYDISCUSS_NOTIFY_OWNER_ON_NEW_REPLY'); ?>
					<?php echo $this->html('settings.toggle', 'notify_subscriber', 'COM_EASYDISCUSS_NOTIFY_SUBSCRIBER_ON_NEW_REPLY'); ?>
					<?php echo $this->html('settings.toggle', 'notify_owner_answer', 'COM_EASYDISCUSS_NOTIFY_OWNER_WHEN_REPLY_ACCEPTED_OR_UNACCEPT'); ?>
					<?php echo $this->html('settings.toggle', 'notify_owner_like', 'COM_EASYDISCUSS_NOTIFY_OWNER_WHEN_LIKE_THEIR_POST'); ?>
					<?php echo $this->html('settings.toggle', 'notify_mention', 'COM_EASYDISCUSS_NOTIFY_USER_WHEN_MENTIONED'); ?>
					<?php echo $this->html('settings.toggle', 'notify_actor', 'COM_EASYDISCUSS_NOTIFY_ACTOR_ON_NEW_ACTION'); ?>
					<?php echo $this->html('settings.toggle', 'notify_comment_all_members', 'COM_ED_NOTIFY_COMMENT_FOR_ALL_USERS'); ?>
					<?php echo $this->html('settings.toggle', 'notify_comment_participants', 'COM_EASYDISCUSS_NOTIFY_COMMENT_PARTICIPANTS'); ?>

					<div class="o-form-group">
						<div class="col-md-5 o-form-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFY_SPECIFIC_USER_GROUPS'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'notify_joomla_groups', $this->config->get('notify_joomla_groups'));?>

							<div class="t-mt--lg">
								<?php echo $this->html('form.usergroups', 'notify_joomla_groups_ids', explode(',', $this->config->get('notify_joomla_groups_ids'))); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
