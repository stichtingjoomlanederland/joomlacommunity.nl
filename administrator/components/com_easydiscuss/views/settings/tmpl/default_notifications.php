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
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_DESC' );?>
		</p>
		<!-- <a href="javascript:void(0)" class="btn btn-success">Documentation</a> -->
	</div>
</div>
<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_CONFIGURATIONS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_SENDER_EMAIL' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_SENDER_EMAIL' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS_SENDER_EMAIL_DESC'); ?>"
						>
							<input type="text" value="<?php echo $this->config->get( 'notification_sender_email' , $this->jconfig->get( 'mailfrom') );?>" name="notification_sender_email" class="input-full" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_SENDER_NAME' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_SENDER_NAME' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS_SENDER_NAME_DESC'); ?>"
						>
							<input type="text" value="<?php echo $this->config->get( 'notification_sender_name' , $this->jconfig->get( 'fromname') );?>" name="notification_sender_name" class="input-full"/>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_CUSTOM_EMAIL_ADDRESS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_CUSTOM_EMAIL_ADDRESS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_NOTIFY_CUSTOM_EMAIL_ADDRESS_DESC'); ?>"
						>
							<input type="text" value="<?php echo $this->config->get( 'notify_custom' );?>" name="notify_custom" class="input-full"/>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_ADMINS_ON_NEW_POST' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_ADMINS_ON_NEW_POST' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_NOTIFY_ADMINS_ON_NEW_POST_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'notify_admin' , $this->config->get( 'notify_admin' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_ADMINS_ON_NEW_REPLY' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_ADMINS_ON_NEW_REPLY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_NOTIFY_ADMINS_ON_NEW_REPLY_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'notify_admin_onreply' , $this->config->get( 'notify_admin_onreply' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_MODERATORS_ON_NEW_POST' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_MODERATORS_ON_NEW_POST' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_NOTIFY_MODERATORS_ON_NEW_POST_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'notify_moderator' , $this->config->get( 'notify_moderator' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_MODERATORS_ON_NEW_REPLY' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_MODERATORS_ON_NEW_REPLY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_NOTIFY_MODERATORS_ON_NEW_REPLY_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'notify_moderator_onreply' , $this->config->get( 'notify_moderator_onreply' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_ALL_USERS_ON_NEW_POST' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_ALL_USERS_ON_NEW_POST' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_NOTIFY_ALL_USERS_ON_NEW_POST_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'notify_all' , $this->config->get( 'notify_all' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_PARTICIPANTS_ON_NEW_REPLY' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_PARTICIPANTS_ON_NEW_REPLY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_NOTIFY_PARTICIPANTS_ON_NEW_REPLY_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'notify_participants' , $this->config->get( 'notify_participants' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_OWNER_ON_NEW_REPLY' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_OWNER_ON_NEW_REPLY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_NOTIFY_OWNER_ON_NEW_REPLY_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'notify_owner' , $this->config->get( 'notify_owner' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_SUBSCRIBER_ON_NEW_REPLY' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_SUBSCRIBER_ON_NEW_REPLY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_NOTIFY_SUBSCRIBER_ON_NEW_REPLY_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'notify_subscriber' , $this->config->get( 'notify_subscriber' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_OWNER_WHEN_REPLY_ACCEPTED_OR_UNACCEPT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_OWNER_WHEN_REPLY_ACCEPTED_OR_UNACCEPT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_NOTIFY_OWNER_WHEN_REPLY_ACCEPTED_OR_UNACCEPT_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'notify_owner_answer' , $this->config->get( 'notify_owner_answer' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_OWNER_WHEN_LIKE_THEIR_POST' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_OWNER_WHEN_LIKE_THEIR_POST' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_NOTIFY_OWNER_WHEN_LIKE_THEIR_POST_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'notify_owner_like' , $this->config->get( 'notify_owner_like' ) );?>
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
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option02" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span3 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TITLE' ); ?>
							</label>
						</div>
						<div class="span9"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TITLE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TITLE_DESC'); ?>"
						>
							<input type="text" class="input-full" name="notify_email_title" value="<?php echo $this->config->get( 'notify_email_title' );?>" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span3 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_FILENAME' ); ?>
							</label>
						</div>
						<div class="span9"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_FILENAME' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_FILENAME_DESC'); ?>"
						>
							<ul class="unstyled file-list">
							<?php echo $this->getEmailsTemplate(); ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
