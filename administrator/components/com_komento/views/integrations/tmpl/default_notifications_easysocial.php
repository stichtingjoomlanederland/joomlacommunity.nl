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
?>
<div class="row">
	<div class="col-md-6">
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION' ); ?></div>
			<div class="panel-body">
			<?php if( Komento::getHelper( 'components' )->isInstalled( 'com_easysocial' ) ) { ?>
					<!-- Toggle Notification -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_ENABLE', 'notification_es_enable' ); ?>
			<?php } else { ?>
				<img src="<?php echo JURI::root(); ?>administrator/components/com_komento/assets/images/easysocial.png" />
				<?php echo JText::_( 'COM_KOMENTO_WHAT_IS_EASYSOCIAL' ); ?>
				<a target="_blank" href="http://www.stackideas.com/easysocial.html"><?php echo JText::_( 'COM_KOMENTO_GET_EASYSOCIAL' ); ?></a>
			<?php } ?>
			</div>
		</fieldset>

		<?php if( Komento::getHelper( 'components' )->isInstalled( 'com_easysocial' ) ) { ?>
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EVENTS' ); ?></div>
			<div class="panel-body">
					<!-- New comment -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_EVENT_NEW_COMMENT', 'notification_es_event_new_comment' ); ?>

					<!-- New reply -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_EVENT_NEW_REPLY', 'notification_es_event_new_reply' ); ?>

					<!-- New like -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_EVENT_NEW_LIKE', 'notification_es_event_new_like' ); ?>
			</div>
		</fieldset>
		<?php } ?>
	</div>

	<div class="col-md-6">
		<?php if( Komento::getHelper( 'components' )->isInstalled( 'com_easysocial' ) ) { ?>
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION_RECIPIENTS' ); ?></div>
			<div class="panel-body">
					<!-- Notify Author -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_TO_AUTHOR', 'notification_es_to_author' ); ?>

					<!-- Notify Participants -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_TO_PARTICIPANT', 'notification_es_to_participant' ); ?>

					<!-- Prepare Usergroups -->
					<?php $usergroups = $this->getUsergroupsMultilist(); ?>

					<!-- Notify Usergroups of new comments -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_TO_USERGROUP_COMMENT', 'notification_es_to_usergroup_comment', 'multilist', $usergroups ); ?>

					<!-- Notify Usergroups of new reply -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_TO_USERGROUP_REPLY', 'notification_es_to_usergroup_reply', 'multilist', $usergroups ); ?>

					<!-- Notify Usergroups of new like -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_TO_USERGROUP_LIKE', 'notification_es_to_usergroup_like', 'multilist', $usergroups ); ?>
			</div>
		</fieldset>
		<?php } ?>
	</div>
</div>

