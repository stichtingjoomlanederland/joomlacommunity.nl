<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EMAIL_CONFIGURATIONS'); ?>

			<div id="option01" class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.textbox', 'notification_sender_email', 'COM_EASYDISCUSS_NOTIFICATIONS_SENDER_EMAIL', '', array('defaultValue' => $this->jconfig->get('mailfrom'))); ?>
					<?php echo $this->html('settings.textbox', 'notification_sender_name', 'COM_EASYDISCUSS_NOTIFICATIONS_SENDER_NAME', '', array('defaultValue' => $this->jconfig->get('fromname'))); ?>
					<?php echo $this->html('settings.toggle', 'notify_modify_from', 'COM_EASYDISCUSS_SETTINGS_USE_USER_AS_FROM'); ?>
					<?php echo $this->html('settings.textbox', 'notify_custom', 'COM_EASYDISCUSS_NOTIFY_CUSTOM_EMAIL_ADDRESS'); ?>

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

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFY_SPECIFIC_USER_GROUPS'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'notify_joomla_groups', $this->config->get('notify_joomla_groups'));?>

							<div class="t-lg-mt--xl">
								<?php echo $this->html('form.usergroups', 'notify_joomla_groups_ids', explode(',', $this->config->get('notify_joomla_groups_ids'))); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.textbox', 'notify_email_title', 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TITLE'); ?>	
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MAIL_SPOOL'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_mailqueueonpageload', 'COM_EASYDISCUSS_SEND_EMAIL_ON_PAGE_LOAD'); ?>
				<?php echo $this->html('settings.toggle', 'notify_html_format', 'COM_EASYDISCUSS_NOTIFICATIONS_HTML_FORMAT'); ?>
				<?php echo $this->html('settings.textbox', 'main_mailqueuenumber', 'COM_EASYDISCUSS_MAILNUMBER_PERLOAD', '', array('postfix' => 'E-mails', 'size' => 8), '', 'form-control-sm text-center'); ?>
				<?php echo $this->html('settings.textbox', 'main_notification_max_length', 'COM_EASYDISCUSS_TRUNCATE_EMAIL_LENGTH', '', array('postfix' => 'Characters', 'size' => 9), '', 'form-control-sm text-center'); ?>
			</div>
		</div>
	</div>
</div>
