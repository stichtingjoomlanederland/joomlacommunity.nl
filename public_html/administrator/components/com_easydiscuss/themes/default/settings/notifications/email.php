<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

JHTML::_( 'behavior.modal' , 'a.modal' );
?>

<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EMAIL_CONFIGURATIONS'); ?>

			<div id="option01" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFICATIONS_SENDER_EMAIL'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" value="<?php echo $this->config->get( 'notification_sender_email' , $this->jconfig->get( 'mailfrom') );?>" name="notification_sender_email" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFICATIONS_SENDER_NAME'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" value="<?php echo $this->config->get( 'notification_sender_name' , $this->jconfig->get( 'fromname') );?>" name="notification_sender_name" class="form-control"/>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFY_CUSTOM_EMAIL_ADDRESS'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" value="<?php echo $this->config->get( 'notify_custom' );?>" name="notify_custom" class="form-control"/>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFY_ADMINS_ON_NEW_POST'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'notify_admin', $this->config->get('notify_admin'));?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFY_ADMINS_ON_NEW_REPLY'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'notify_admin_onreply', $this->config->get('notify_admin_onreply'));?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFY_MODERATORS_ON_NEW_POST'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'notify_moderator', $this->config->get('notify_moderator'));?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFY_MODERATORS_ON_NEW_REPLY'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'notify_moderator_onreply', $this->config->get('notify_moderator_onreply'));?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFY_ALL_USERS_ON_NEW_POST'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'notify_all', $this->config->get('notify_all'));?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFY_ALL_USERS_RESPECT_CATEGORY_PERMISSIONS'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'notify_all_respect_category', $this->config->get('notify_all_respect_category'));?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFY_PARTICIPANTS_ON_NEW_REPLY'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'notify_participants', $this->config->get('notify_participants'));?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFY_OWNER_ON_NEW_REPLY'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'notify_owner', $this->config->get('notify_owner'));?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFY_SUBSCRIBER_ON_NEW_REPLY'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'notify_subscriber', $this->config->get('notify_subscriber'));?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFY_OWNER_WHEN_REPLY_ACCEPTED_OR_UNACCEPT'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'notify_owner_answer', $this->config->get('notify_owner_answer'));?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFY_OWNER_WHEN_LIKE_THEIR_POST'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'notify_owner_like', $this->config->get('notify_owner_like'));?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFY_USER_WHEN_MENTIONED'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'notify_mention', $this->config->get('notify_mention'));?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFY_ACTOR_ON_NEW_ACTION'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'notify_actor', $this->config->get('notify_actor'));?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MAIL_SENDER'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_USE_USER_AS_FROM'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'notify_modify_from', $this->config->get('notify_modify_from'));?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TITLE'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" class="form-control" name="notify_email_title" value="<?php echo $this->config->get( 'notify_email_title' );?>" />
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MAIL_SPOOL'); ?>

			<div class="panel-body">

				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SEND_EMAIL_ON_PAGE_LOAD'); ?>
					</div>

					<div class="col-md-7">
						<?php echo $this->html('form.boolean', 'main_mailqueueonpageload', $this->config->get('main_mailqueueonpageload')); ?>
					</div>
				</div>

				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFICATIONS_HTML_FORMAT'); ?>
					</div>

					<div class="col-md-7">
						<?php echo $this->html('form.boolean', 'notify_html_format', $this->config->get('notify_html_format')); ?>
					</div>
				</div>

				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MAILNUMBER_PERLOAD'); ?>
					</div>

					<div class="col-md-7">
						<input type="text" class="form-control form-control-sm text-center" name="main_mailqueuenumber" value="<?php echo $this->config->get('main_mailqueuenumber');?>" />
					</div>
				</div>

				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_EASYDISCUSS_TRUNCATE_EMAIL_LENGTH'); ?>
					</div>

					<div class="col-md-7">
						<input type="text" class="form-control form-control-sm text-center" name="main_notification_max_length" value="<?php echo $this->config->get('main_notification_max_length');?>" />
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
