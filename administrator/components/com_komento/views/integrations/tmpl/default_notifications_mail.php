<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
$cronlink = rtrim( JURI::root(), '/' ) . '/index.php?option=com_komento&task=cron'; ?>
<div class="row">
	<div class="col-md-6">
		<fieldset class="panel">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION_INFO' ); ?></div>
			<div class="panel-body">
				<p class="small"><a href="http://stackideas.com/docs/komento/cronjobs/339-setting-up-cronjobs-in-cpanel"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION_SETUP_CRONJOB' ) ;?></a></p>
				<p class="small"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION_CRON_LINK' ); ?>: <a href="<?php echo $cronlink; ?>" target="_blank"><?php echo $cronlink; ?></a></p>
			</div>
		</fieldset>

		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION' ); ?></div>
			<div class="panel-body">

					<!-- Toggle Notification -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_ENABLE', 'notification_enable' ); ?>

					<!-- Send mail on page load -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SEND_MAIL_ON_PAGE_LOAD', 'notification_sendmailonpageload' ); ?>

					<!-- Allow Email in HTML format -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SEND_MAIL_IN_HTML', 'notification_sendmailinhtml' ); ?>
			</div>
		</fieldset>

		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EVENTS' ); ?></div>
			<div class="panel-body">

					<!-- New comment -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EVENT_NEW_COMMENT', 'notification_event_new_comment' ); ?>

					<!-- New reply -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EVENT_NEW_REPLY', 'notification_event_new_reply' ); ?>

					<!-- New comment pending moderation -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EVENT_NEW_PENDING', 'notification_event_new_pending' ); ?>

					<!-- Reported comment -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EVENT_REPORTED_COMMENT', 'notification_event_reported_comment' ); ?>
			</div>
		</fieldset>
	</div>

	<div class="col-md-6">
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION_RECIPIENTS' ); ?></div>
			<div class="panel-body">
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
			</div>
		</fieldset>
	</div>
</div>