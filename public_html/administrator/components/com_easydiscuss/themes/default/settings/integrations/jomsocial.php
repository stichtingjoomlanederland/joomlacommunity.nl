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
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_JOMSOCIAL_INTEGRATIONS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_JOMSOCIAL_TOOLBAR'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_jomsocial_toolbar', $this->config->get('integration_jomsocial_toolbar')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_JOMSOCIAL_USERPOINTS'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_jomsocial_points', $this->config->get('integration_jomsocial_points')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_LINK_TO_JOMSOCIAL_PROFILE'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_toolbar_jomsocial_profile', $this->config->get('integration_toolbar_jomsocial_profile')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_LINK_TO_JOMSOCIAL_MESSAGING'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_jomsocial_messaging', $this->config->get('integration_jomsocial_messaging')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_STREAM'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_jomsocial_activity_new_question', $this->config->get('integration_jomsocial_activity_new_question')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION_CONTENT'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_jomsocial_activity_new_question_content', $this->config->get('integration_jomsocial_activity_new_question_content')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_jomsocial_activity_reply_question', $this->config->get('integration_jomsocial_activity_reply_question')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION_CONTENT'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_jomsocial_activity_reply_question_content', $this->config->get('integration_jomsocial_activity_reply_question_content')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_CONTENT_LENGTH'); ?>
						</div>
						<div class="col-md-6">
							<input type="text" class="form-control form-control-sm text-center" name="integration_jomsocial_activity_content_length" value="<?php echo $this->config->get('integration_jomsocial_activity_content_length');?>" />
							<?php echo JText::_( 'COM_EASYDISCUSS_CHARACTERS' ); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_TITLE_LENGTH'); ?>
						</div>
						<div class="col-md-6">
							<input type="text" class="form-control form-control-sm text-center" name="integration_jomsocial_activity_title_length" value="<?php echo $this->config->get('integration_jomsocial_activity_title_length');?>" />
							<?php echo JText::_('COM_EASYDISCUSS_CHARACTERS'); ?>
						</div>
					</div>


					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_COMMENT'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_jomsocial_activity_comment', $this->config->get('integration_jomsocial_activity_comment')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_LIKE_QUESTION'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_jomsocial_activity_likes', $this->config->get('integration_jomsocial_activity_likes')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_BADGES_EARNED'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_jomsocial_activity_badges', $this->config->get('integration_jomsocial_activity_badges')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_RANKED_UP'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_jomsocial_activity_ranks', $this->config->get('integration_jomsocial_activity_ranks')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
