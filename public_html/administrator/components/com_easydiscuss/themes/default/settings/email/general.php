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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MAIL_PARSER'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="alert">
						<?php echo JText::_('COM_EASYDISCUSS_YOUR_CRON_URL'); ?>:<br /> 
						<a href="<?php echo JURI::root() ; ?>index.php?option=com_easydiscuss&task=cron" target="_blank"><?php echo JURI::root(); ?>index.php?option=com_easydiscuss&task=cron</a>

						<br /><br />
						<a href="https://stackideas.com/docs/easydiscuss/administrators/cronjobs" class="btn btn-success btn-sm" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_DOCS_CRONJOB'); ?> &rarr;</a>
					</div>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MAIN_TEST_EMAIL_PARSER'); ?>
						</div>
						<div class="col-md-7">
							<button type="button" class="btn btn-primary btn-sm" onclick="return;" data-eparser-test><?php echo JText::_('COM_EASYDISCUSS_TEST_CONNECTION_BUTTON');?></button>
							<span id="test-result"></span>
						</div>
					</div>
					<?php echo $this->html('settings.toggle', 'main_email_parser', 'COM_EASYDISCUSS_MAIN_ALLOW_EMAIL_PARSER'); ?>
					<?php echo $this->html('settings.textbox', 'main_email_parser_server', 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_ADDRESS'); ?>
					<?php echo $this->html('settings.textbox', 'main_email_parser_port', 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_PORT', '', array(), '', 'form-control-sm text-center'); ?>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_PARSER_SERVICE_TYPE'); ?>
						</div>
						<div class="col-md-7">
							<?php
								$services = array();
								$services[] = JHTML::_('select.option', 'imap', JText::_('IMAP'));
								$services[] = JHTML::_('select.option', 'pop3', JText::_('POP3'));
								echo JHTML::_('select.genericlist', $services, 'main_email_parser_service', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('main_email_parser_service'));
							?>
						</div>
					</div>

					<?php echo $this->html('settings.toggle', 'main_email_parser_ssl', 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_SSL'); ?>
					<?php echo $this->html('settings.toggle', 'main_email_parser_validate', 'COM_EASYDISCUSS_EMAIL_PARSER_VALIDATE'); ?>
					<?php echo $this->html('settings.textbox', 'main_email_parser_username', 'COM_EASYDISCUSS_EMAIL_PARSER_USERNAME'); ?>
					<?php echo $this->html('settings.textbox', 'main_email_parser_password', 'COM_EASYDISCUSS_EMAIL_PARSER_PASSWORD', '', array('type' => 'password', 'attributes' => 'autocomplete="off"')); ?>
					<?php echo $this->html('settings.textbox', 'main_email_parser_limit', 'COM_EASYDISCUSS_EMAIL_PARSER_PROCESS_LIMIT', '', array('size' => 7, 'postfix' => 'COM_EASYDISCUSS_EMAILS'), '', 'form-control-sm text-center'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MAIL_PARSER_PUBLISHING'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_email_parser_mapuser', 'COM_EASYDISCUSS_EMAIL_PARSER_DETECT_USER_ACCOUNT'); ?>
					<?php echo $this->html('settings.toggle', 'main_email_parser_appendemail', 'COM_EASYDISCUSS_EMAIL_PARSER_APPEND_EMAIL_ADDRESS_IN_CONTENT'); ?>
					<?php echo $this->html('settings.toggle', 'main_email_parser_receipt', 'COM_EASYDISCUSS_EMAIL_PARSER_SEND_RECEIPT'); ?>
					<?php echo $this->html('settings.toggle', 'main_email_parser_replies', 'COM_EASYDISCUSS_EMAIL_PARSER_ALLOW_REPLIES'); ?>
					<?php echo $this->html('settings.textbox', 'mail_reply_breaker', 'COM_EASYDISCUSS_SETTINGS_REPLYBREAK'); ?>
					<?php echo $this->html('settings.toggle', 'main_email_parser_moderation', 'COM_EASYDISCUSS_EMAIL_PARSER_MODERATE_POSTS'); ?>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_PARSER_CATEGORY'); ?>
						</div>
						<div class="col-md-7">
							<select name="main_email_parser_category" class="form-control">
								<?php foreach ($categories as $category) { ?>
								<option value="<?php echo $category->id; ?>"<?php echo $this->config->get('main_email_parser_category') == $category->id ? ' selected="selected"' : '';?>><?php echo $category->title; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_ED_SETTINGS_MAILBOX_WHITE_LIST'); ?>
						</div>

						<div class="col-md-7">
							<textarea class="form-control" id="main_email_parser_sender_whitelist" name="main_email_parser_sender_whitelist" data-mailbox-whitelist><?php echo $this->config->get('main_email_parser_sender_whitelist');?></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

