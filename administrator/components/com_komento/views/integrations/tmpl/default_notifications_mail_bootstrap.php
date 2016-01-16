<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
$cronlink = rtrim( JURI::root(), '/' ) . '/index.php?option=com_komento&task=cron'; ?>
<div class="row-fluid">
	<div class="span12">
		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION_INFO' ); ?></legend>
			<p class="small"><a href="http://stackideas.com/docs/komento/cronjobs/setting-up-cronjobs-in-cpanel.html"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION_SETUP_CRONJOB' ) ;?></a></p>
			<p class="small"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION_CRON_LINK' ); ?>: <a href="<?php echo $cronlink; ?>" target="_blank"><?php echo $cronlink; ?></a></p>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Toggle Notification -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_ENABLE', 'notification_enable' ); ?>

					<!-- Send mail on page load -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SEND_MAIL_ON_PAGE_LOAD', 'notification_sendmailonpageload' ); ?>

					<!-- Allow Email in HTML format -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SEND_MAIL_IN_HTML', 'notification_sendmailinhtml' ); ?>

				</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EVENTS' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- New comment -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EVENT_NEW_COMMENT', 'notification_event_new_comment' ); ?>

					<!-- New reply -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EVENT_NEW_REPLY', 'notification_event_new_reply' ); ?>

					<!-- New comment pending moderation -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EVENT_NEW_PENDING', 'notification_event_new_pending' ); ?>

					<!-- Reported comment -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EVENT_REPORTED_COMMENT', 'notification_event_reported_comment' ); ?>

				</tbody>
			</table>
			</fieldset>
		</div>

		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION_RECIPIENTS' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Notify Admins -->
					<?php // echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_TO_ADMINS', 'notification_to_admins' ); ?>

					<!-- Notify Author -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_TO_AUTHOR', 'notification_to_author' ); ?>

					<!-- Notify Subscribers -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_TO_SUBSCRIBERS', 'notification_to_subscribers' ); ?>

					<!-- Prepare Usergroups -->
					<?php $usergroups = $this->getUsergroupsMultilist(); ?>
					
					<!-- Notify Usergroups of new comments -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_TO_USERGROUP_COMMENT', 'notification_to_usergroup_comment', 'multilist', $usergroups ); ?>

					<!-- Notify Usergroups of new reply -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_TO_USERGROUP_REPLY', 'notification_to_usergroup_reply', 'multilist', $usergroups ); ?>

					<!-- Notify Usergroups of new pending -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_TO_USERGROUP_PENDING', 'notification_to_usergroup_pending', 'multilist', $usergroups ); ?>

					<!-- Notify Usergroups of new reported -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_TO_USERGROUP_REPORTED', 'notification_to_usergroup_reported', 'multilist', $usergroups ); ?>

				</tbody>
			</table>
			</fieldset>
		</div>
	</div>
</div>

