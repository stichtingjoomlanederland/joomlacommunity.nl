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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MAIL_PARSER', '', '/docs/easydiscuss/administrators/configuration/setting-up-email-parser'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('panel.info', 'COM_ED_EMAIL_PARSER_INFO'); ?>

					<div class="o-form-group">
						<div class="col-md-5 o-form-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MAIN_TEST_EMAIL_PARSER'); ?>
						</div>
						<div class="col-md-7">
							<button type="button" class="o-btn o-btn--default-o" data-test>
								<i class="fa fa-bolt"></i>&nbsp; <?php echo JText::_('COM_EASYDISCUSS_TEST_CONNECTION_BUTTON');?>
							</button>
							
							<div class="t-mt--sm t-hidden" data-test-result></div>
						</div>
					</div>
					<?php echo $this->html('settings.toggle', 'main_email_parser', 'COM_EASYDISCUSS_MAIN_ALLOW_EMAIL_PARSER'); ?>
					<?php echo $this->html('settings.textbox', 'main_email_parser_server', 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_ADDRESS'); ?>
					<?php echo $this->html('settings.textbox', 'main_email_parser_port', 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_PORT', '', array(), '', 'o-form-control-sm text-center'); ?>

					<div class="o-form-group">
						<div class="col-md-5 o-form-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_PARSER_SERVICE_TYPE'); ?>
						</div>
						<div class="col-md-7">
							<?php
								$services = array();
								$services[] = JHTML::_('select.option', 'imap', JText::_('IMAP'));
								$services[] = JHTML::_('select.option', 'pop3', JText::_('POP3'));
								echo JHTML::_('select.genericlist', $services, 'main_email_parser_service', 'size="1" class="o-form-select"', 'value', 'text', $this->config->get('main_email_parser_service'));
							?>
						</div>
					</div>

					<?php echo $this->html('settings.toggle', 'main_email_parser_ssl', 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_SSL'); ?>
					<?php echo $this->html('settings.toggle', 'main_email_parser_validate', 'COM_EASYDISCUSS_EMAIL_PARSER_VALIDATE'); ?>
					<?php echo $this->html('settings.textbox', 'main_email_parser_username', 'COM_EASYDISCUSS_EMAIL_PARSER_USERNAME'); ?>
					<?php echo $this->html('settings.password', 'main_email_parser_password', 'COM_EASYDISCUSS_EMAIL_PARSER_PASSWORD', array('attributes' => 'autocomplete="new-password" ')); ?>
					<?php echo $this->html('settings.textbox', 'main_email_parser_limit', 'COM_EASYDISCUSS_EMAIL_PARSER_PROCESS_LIMIT', '', array('size' => 7, 'postfix' => 'COM_EASYDISCUSS_EMAILS'), '', 'o-form-control-sm', 'text-center'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MAIL_PARSER_PUBLISHING'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_email_parser_mapuser', 'COM_EASYDISCUSS_EMAIL_PARSER_DETECT_USER_ACCOUNT'); ?>
					<?php echo $this->html('settings.toggle', 'main_email_parser_appendemail', 'COM_EASYDISCUSS_EMAIL_PARSER_APPEND_EMAIL_ADDRESS_IN_CONTENT'); ?>
					<?php echo $this->html('settings.toggle', 'main_email_parser_receipt', 'COM_EASYDISCUSS_EMAIL_PARSER_SEND_RECEIPT'); ?>
					<?php echo $this->html('settings.toggle', 'main_email_parser_replies', 'COM_EASYDISCUSS_EMAIL_PARSER_ALLOW_REPLIES'); ?>

					<?php echo $this->html('settings.textbox', 'mail_reply_breaker', 'COM_EASYDISCUSS_SETTINGS_REPLYBREAK'); ?>
					<?php echo $this->html('settings.toggle', 'main_email_parser_moderation', 'COM_EASYDISCUSS_EMAIL_PARSER_MODERATE_POSTS'); ?>

					<div class="o-form-group">
						<div class="col-md-5 o-form-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_PARSER_CATEGORY'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.categories', 'main_email_parser_category', $this->config->get('main_email_parser_category')); ?>
						</div>
					</div>

					<?php echo $this->html('settings.textarea', 'main_email_parser_sender_whitelist', 'COM_ED_SETTINGS_MAILBOX_WHITE_LIST'); ?>
					<?php echo $this->html('settings.textarea', 'main_email_parser_sender_blacklist', 'COM_ED_SETTINGS_MAILBOX_BLACK_LIST'); ?>
				</div>
			</div>
		</div>
	</div>
</div>

