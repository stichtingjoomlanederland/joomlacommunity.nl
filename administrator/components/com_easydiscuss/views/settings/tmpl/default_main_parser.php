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
?>
<script type="text/javascript">
function testParser()
{
	var server		= EasyDiscuss.$('input[name=main_email_parser_server]').val();
	var port		= EasyDiscuss.$('input[name=main_email_parser_port]').val();
	var service		= EasyDiscuss.$('#main_email_parser_service').val();
	var ssl			= EasyDiscuss.$('input[name=main_email_parser_ssl]').val();
	var user		= EasyDiscuss.$('input[name=main_email_parser_username]').val();
	var pass		= EasyDiscuss.$('input[name=main_email_parser_password]').val();
	var validate	= EasyDiscuss.$('input[name=main_email_parser_validate]').val();

	disjax.load( 'settings' , 'testParser' , server , port , service , ssl , user , pass , validate );
}
</script>
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_MAIL_PARSER_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_MAIL_PARSER_DESC' );?>
		</p>
		<!-- <a href="javascript:void(0)" class="btn btn-success">Documentation</a> -->
	</div>
</div>

<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_MAIL_PARSER' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="alert">
						<?php echo JText::_( 'COM_EASYDISCUSS_CRONJOB_INFO' ); ?> <a href="http://stackideas.com/docs/easydiscuss/cronjobs/" target="_blank">http://stackideas.com/docs/easydiscuss/cronjobs/</a>
					</div>
					<div class="alert">
						<?php echo JText::_( 'COM_EASYDISCUSS_YOUR_CRON_URL' ); ?>: <a href="<?php echo JURI::root() ; ?>index.php?option=com_easydiscuss&task=cron" target="_blank"><?php echo JURI::root(); ?>index.php?option=com_easydiscuss&task=cron</a>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_TEST_EMAIL_PARSER' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_TEST_EMAIL_PARSER' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_MAIN_TEST_EMAIL_PARSER_DESC'); ?>"
						>
							<button type="button" class="btn" onclick="testParser();"><?php echo JText::_( 'COM_EASYDISCUSS_TEST_CONNECTION_BUTTON');?></button>
							<span id="test-result"></span>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_ALLOW_EMAIL_PARSER' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_ALLOW_EMAIL_PARSER' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_MAIN_ALLOW_EMAIL_PARSER_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_email_parser' , $this->config->get( 'main_email_parser' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_ADDRESS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_ADDRESS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_SERVER_ADDRESS_DESC'); ?>"
						>
							<input type="text" name="main_email_parser_server" value="<?php echo $this->config->get( 'main_email_parser_server' );?>" class="full-width"/>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_PORT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_PORT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_SERVER_PORT_DESC'); ?>"
						>
							<input type="text" name="main_email_parser_port" value="<?php echo $this->config->get( 'main_email_parser_port' );?>" class="full-width"/>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SERVICE_TYPE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SERVICE_TYPE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_SERVICE_TYPE_DESC'); ?>"
						>
							<select name="main_email_parser_service" id="main_email_parser_service" class="full-width">
								<option value="imap"><?php echo JText::_( 'IMAP' );?></option>
								<option value="pop3"><?php echo JText::_( 'POP3' );?></option>
							</select>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_SSL' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_SSL' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_SERVER_SSL_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_email_parser_ssl' , $this->config->get( 'main_email_parser_ssl' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_VALIDATE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_VALIDATE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_VALIDATE_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_email_parser_validate' , $this->config->get( 'main_email_parser_validate' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_USERNAME' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_USERNAME' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_USERNAME_DESC'); ?>"
						>
							<input type="text" name="main_email_parser_username" value="<?php echo $this->config->get( 'main_email_parser_username' );?>" class="full-width"/>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_PASSWORD' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_PASSWORD' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_PASSWORD_DESC'); ?>"
						>
							<input name="main_email_parser_password" value="<?php echo $this->config->get( 'main_email_parser_password' );?>" type="password" autocomplete="off" class="full-width"/>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_PROCESS_LIMIT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_PROCESS_LIMIT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_PROCESS_LIMIT_DESC'); ?>"
						>
							<input type="text" name="main_email_parser_limit" value="<?php echo $this->config->get( 'main_email_parser_limit' );?>" style="text-align:center;width:50px;" />
							<?php echo JText::_( 'COM_EASYDISCUSS_EMAILS' );?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option02">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_MAIL_PARSER_PUBLISHING' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option02" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SEND_RECEIPT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SEND_RECEIPT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_SEND_RECEIPT_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_email_parser_receipt' , $this->config->get( 'main_email_parser_receipt' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_ALLOW_REPLIES' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_ALLOW_REPLIES' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_ALLOW_REPLIES_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_email_parser_replies' , $this->config->get( 'main_email_parser_replies' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_REPLYBREAK' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_REPLYBREAK' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_REPLYBREAK_DESC'); ?>"
						>
							<input type="text" name="mail_reply_breaker" value="<?php echo $this->config->get( 'mail_reply_breaker' );?>" class="full-width"/>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_MODERATE_POSTS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_MODERATE_POSTS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_MODERATE_POSTS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_email_parser_moderation' , $this->config->get( 'main_email_parser_moderation' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_CATEGORY' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_CATEGORY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_CATEGORY_DESC'); ?>"
						>
							<select name="main_email_parser_category" class="full-width">
								<?php foreach( $this->getCategories() as $category ){ ?>
								<option value="<?php echo $category->id; ?>"<?php echo $this->config->get( 'main_email_parser_category' ) == $category->id ? ' selected="selected"' : '';?>><?php echo $category->title; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

