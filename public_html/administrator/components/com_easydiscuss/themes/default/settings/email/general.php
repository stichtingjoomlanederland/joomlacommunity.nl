<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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
			<a href="https://stackideas.com/docs/easydiscuss/administrators/cronjobs" class="btn btn-success t-lg-ml--lg t-lg-mt--lg" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_DOCS_CRONJOB'); ?> &rarr;</a>

			<div id="option01" class="panel-body">
				<div class="form-horizontal">

					<div class="alert">
						<?php echo JText::_('COM_EASYDISCUSS_YOUR_CRON_URL'); ?>:<br/> <a href="<?php echo JURI::root() ; ?>index.php?option=com_easydiscuss&task=cron" target="_blank"><?php echo JURI::root(); ?>index.php?option=com_easydiscuss&task=cron</a>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MAIN_TEST_EMAIL_PARSER'); ?>
						</div>
						<div class="col-md-7">
							<button type="button" class="btn btn-default" onclick="return;" data-eparser-test><?php echo JText::_('COM_EASYDISCUSS_TEST_CONNECTION_BUTTON');?></button>
							<span id="test-result"></span>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MAIN_ALLOW_EMAIL_PARSER'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_email_parser', $this->config->get('main_email_parser')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_ADDRESS'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" name="main_email_parser_server" value="<?php echo $this->config->get('main_email_parser_server');?>" class="form-control"/>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_PORT'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" name="main_email_parser_port" value="<?php echo $this->config->get('main_email_parser_port');?>" class="form-control"/>
						</div>
					</div>
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
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_SSL'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_email_parser_ssl', $this->config->get('main_email_parser_ssl')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_PARSER_VALIDATE'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_email_parser_validate', $this->config->get('main_email_parser_validate')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_PARSER_USERNAME'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" name="main_email_parser_username" value="<?php echo $this->config->get('main_email_parser_username');?>" class="form-control"/>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_PARSER_PASSWORD'); ?>
						</div>
						<div class="col-md-7">
							<input name="main_email_parser_password" value="<?php echo $this->config->get('main_email_parser_password');?>" type="password" autocomplete="off" class="form-control"/>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_PARSER_PROCESS_LIMIT'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" name="main_email_parser_limit" class="form-control form-control-sm text-center" value="<?php echo $this->config->get('main_email_parser_limit');?>" />
							<?php echo JText::_( 'COM_EASYDISCUSS_EMAILS' );?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MAIL_PARSER_PUBLISHING'); ?>

			<div id="option02" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_PARSER_DETECT_USER_ACCOUNT'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_email_parser_mapuser', $this->config->get('main_email_parser_mapuser')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_PARSER_APPEND_EMAIL_ADDRESS_IN_CONTENT'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_email_parser_appendemail', $this->config->get('main_email_parser_appendemail')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_PARSER_SEND_RECEIPT'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_email_parser_receipt', $this->config->get('main_email_parser_receipt')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_PARSER_ALLOW_REPLIES'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_email_parser_replies', $this->config->get('main_email_parser_replies')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_REPLYBREAK'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" name="mail_reply_breaker" value="<?php echo $this->config->get('mail_reply_breaker');?>" class="form-control"/>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_PARSER_MODERATE_POSTS'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_email_parser_moderation', $this->config->get('main_email_parser_moderation')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_PARSER_CATEGORY'); ?>
						</div>
						<div class="col-md-7">
							<select name="main_email_parser_category" class="form-control">
								<?php foreach( $categories as $category ){ ?>
								<option value="<?php echo $category->id; ?>"<?php echo $this->config->get('main_email_parser_category') == $category->id ? ' selected="selected"' : '';?>><?php echo $category->title; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

