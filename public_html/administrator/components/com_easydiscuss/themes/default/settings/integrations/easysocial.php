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
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_TOOLBAR'); ?>
						</div>
						<div class="col-md-6">
							<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_TOOLBAR_DESC');?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_LINK_TO_EASYSOCIAL_PROFILE'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_toolbar_profile', $this->config->get('integration_easysocial_toolbar_profile')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_POPBOX_AVATAR'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_popbox', $this->config->get('integration_easysocial_popbox')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_MINI_HEADER'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_mini_header', $this->config->get('integration_easysocial_mini_header')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EASYSOCIAL_POINTS_INTEGRATIONS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_USE_POINTS_INTEGRATIONS'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_points', $this->config->get('integration_easysocial_points')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EASYSOCIAL_MEMBERS_INTEGRATIONS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_LINK_TO_EASYSOCIAL_MEMBERS'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_members', $this->config->get('integration_easysocial_members')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EASYSOCIAL_CONVERSATION_INTEGRATIONS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_LINK_TO_EASYSOCIAL_MESSAGING'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_messaging', $this->config->get('integration_easysocial_messaging')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_INTEGRATIONS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_NEW_DISCUSSION'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_notify_create', $this->config->get('integration_easysocial_notify_create')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_NEW_REPLY'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_notify_reply', $this->config->get('integration_easysocial_notify_reply')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_NEW_COMMENT'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_notify_comment', $this->config->get('integration_easysocial_notify_comment')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_ACCEPTED_ANSWER'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_notify_accepted', $this->config->get('integration_easysocial_notify_accepted')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_LIKES'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_notify_likes', $this->config->get('integration_easysocial_notify_likes')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_VOTES'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_notify_vote', $this->config->get('integration_easysocial_notify_vote')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_MENTIONS'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_notify_mentions', $this->config->get('integration_easysocial_notify_mentions')); ?>
						</div>
					</div>					

				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM_NEW_DISCUSSION'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_activity_new_question', $this->config->get('integration_easysocial_activity_new_question')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM_REPLY_DISCUSSION'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_activity_reply_question', $this->config->get('integration_easysocial_activity_reply_question')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM_COMMENTS'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_activity_comment', $this->config->get('integration_easysocial_activity_comment')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_LIKE_QUESTION'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_activity_likes', $this->config->get('integration_easysocial_activity_likes')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_UPGRADE_RANK'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_activity_ranks', $this->config->get('integration_easysocial_activity_ranks')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_FAVORITE_POST'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_activity_favourite', $this->config->get('integration_easysocial_activity_favourite')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_REPLY_ACCEPTED_ANSWER'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_activity_accepted', $this->config->get('integration_easysocial_activity_accepted')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_VOTE_POST'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_easysocial_activity_vote', $this->config->get('integration_easysocial_activity_vote')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_CONTENT_LENGTH'); ?>
						</div>
						<div class="col-md-6">
							<input type="text" class="form-control form-control-sm text-center" name="integration_easysocial_activity_content_length" value="<?php echo $this->config->get('integration_easysocial_activity_content_length');?>" />
							<?php echo JText::_('COM_EASYDISCUSS_CHARACTERS'); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_TITLE_LENGTH'); ?>
						</div>
						<div class="col-md-6">
							<input type="text" class="form-control form-control-sm text-center" name="integration_easysocial_activity_title_length" value="<?php echo $this->config->get('integration_easysocial_activity_title_length');?>" />
							<?php echo JText::_('COM_EASYDISCUSS_CHARACTERS'); ?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
