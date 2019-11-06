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
defined('_JEXEC') or die('Restricted access');
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EASYSOCIAL_INTEGRATIONS'); ?>

			<div class="panel-body">
				<div>
					<img width="64" align="left" src="<?php echo JURI::root();?>administrator/components/com_easydiscuss/themes/default/images/integrations/easysocial.png" style="margin-left: 20px;margin-right:25px; float: left;">
					
					<div class="small" style="overflow:hidden;">
						<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_INFO');?><br /><br />
						<a target="_blank" class="btn btn-primary btn-sm t-lg-mb--lg" href="https://stackideas.com/easysocial"><?php echo JText::_('COM_EASYDISCUSS_LEARN_MORE_EASYSOCIAL'); ?> &rarr;</a>
					</div>
				</div>

				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'integration_easysocial_toolbar', 'COM_ED_SETTINGS_INTEGRATIONS_EASYSOCIAL_TOOLBAR'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_toolbar_profile', 'COM_EASYDISCUSS_LINK_TO_EASYSOCIAL_PROFILE'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_popbox', 'COM_EASYDISCUSS_EASYSOCIAL_POPBOX_AVATAR'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_mini_header', 'COM_EASYDISCUSS_EASYSOCIAL_MINI_HEADER'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_INTEGRATIONS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'integration_easysocial_notify_create', 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_NEW_DISCUSSION'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_notify_moderate', 'COM_ED_EASYSOCIAL_NOTIFY_NEW_MODERATION'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_notify_reply', 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_NEW_REPLY'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_notify_comment', 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_NEW_COMMENT'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_notify_accepted', 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_ACCEPTED_ANSWER'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_notify_likes', 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_LIKES'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_notify_vote', 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_VOTES'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_notify_mentions', 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_MENTIONS'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EASYSOCIAL_POINTS_INTEGRATIONS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'integration_easysocial_points', 'COM_EASYDISCUSS_EASYSOCIAL_USE_POINTS_INTEGRATIONS'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EASYSOCIAL_MEMBERS_INTEGRATIONS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'integration_easysocial_members', 'COM_EASYDISCUSS_LINK_TO_EASYSOCIAL_MEMBERS'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EASYSOCIAL_CONVERSATION_INTEGRATIONS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'integration_easysocial_messaging', 'COM_EASYDISCUSS_LINK_TO_EASYSOCIAL_MESSAGING'); ?>
				</div>
			</div>
		</div>


		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'integration_easysocial_activity_new_question', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM_NEW_DISCUSSION'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_activity_reply_question', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM_REPLY_DISCUSSION'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_activity_comment', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM_COMMENTS'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_activity_likes', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_LIKE_QUESTION'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_activity_ranks', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_UPGRADE_RANK'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_activity_favourite', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_FAVORITE_POST'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_activity_accepted', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_REPLY_ACCEPTED_ANSWER'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_activity_vote', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_VOTE_POST'); ?>

					<?php echo $this->html('settings.textbox', 'integration_easysocial_activity_content_length', 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_CONTENT_LENGTH', '', array('postfix' => 'COM_EASYDISCUSS_CHARACTERS', 'size' => 9), '', 'form-control-sm text-center'); ?>
					<?php echo $this->html('settings.textbox', 'integration_easysocial_activity_title_length', 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_TITLE_LENGTH', '', array('postfix' => 'COM_EASYDISCUSS_CHARACTERS', 'size' => 9), '', 'form-control-sm text-center'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
