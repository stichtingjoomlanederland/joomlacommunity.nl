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
<div class="row-fluid">
	<div class="span12 panel-title">
		<img src="<?php echo JURI::root();?>media/com_easydiscuss/images/easysocial.png" class="pull-left mr-10" width="64" />
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_EASYSOCIAL_INTEGRATIONS_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_EASYSOCIAL_INTEGRATIONS_DESC' );?>
			<br /><br />
			<a href="http://stackideas.com/easysocial" class="btn btn-success" style="margin-left: 75px;"><?php echo JText::_( 'COM_EASYDISCUSS_LEARN_MORE_EASYSOCIAL' ); ?> &rarr;</a>
		</p>
	</div>
</div>

<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_GENERAL_INTEGRATIONS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_TOOLBAR' ); ?>
							</label>
						</div>
						<div class="span6">
							<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_TOOLBAR_EASYSOCIAL_USE_MODULE_INFO');?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LINK_TO_EASYSOCIAL_PROFILE' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LINK_TO_JOMSOCIAL_PROFILE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LINK_TO_JOMSOCIAL_PROFILE_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_easysocial_toolbar_profile' , $this->config->get( 'integration_easysocial_toolbar_profile' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_POPBOX_AVATAR' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_POPBOX_AVATAR' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_POPBOX_AVATAR_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_easysocial_popbox' , $this->config->get( 'integration_easysocial_popbox' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_POINTS_INTEGRATIONS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_USE_POINTS_INTEGRATIONS' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_USE_POINTS_INTEGRATIONS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_USE_POINTS_INTEGRATIONS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_easysocial_points' , $this->config->get( 'integration_easysocial_points' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_CONVERSATION_INTEGRATIONS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LINK_TO_EASYSOCIAL_MESSAGING' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LINK_TO_EASYSOCIAL_MESSAGING' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LINK_TO_EASYSOCIAL_MESSAGING_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_easysocial_messaging' , $this->config->get( 'integration_easysocial_messaging' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#notification">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_INTEGRATIONS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="notification" class="accordion-body collapse in">
				<div class="wbody">

					<p>
						<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_INTEGRATIONS_DESC' ); ?>
					</p>

					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_NEW_DISCUSSION' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_NEW_DISCUSSION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_NEW_DISCUSSION_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_easysocial_notify_create' , $this->config->get( 'integration_easysocial_notify_create' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_NEW_REPLY' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_NEW_REPLY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_NEW_REPLY_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_easysocial_notify_reply' , $this->config->get( 'integration_easysocial_notify_reply' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_NEW_COMMENT' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_NEW_COMMENT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_NEW_COMMENT_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_easysocial_notify_comment' , $this->config->get( 'integration_easysocial_notify_comment' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_ACCEPTED_ANSWER' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_ACCEPTED_ANSWER' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_ACCEPTED_ANSWER_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_easysocial_notify_accepted' , $this->config->get( 'integration_easysocial_notify_accepted' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_LIKES' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_LIKES' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_NOTIFY_LIKES_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_easysocial_notify_likes' , $this->config->get( 'integration_easysocial_notify_likes' ) );?>
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
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option02" class="accordion-body collapse in">
				<div class="wbody">
					<p>
						<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM_DESC' ); ?>
					</p>

					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM_NEW_DISCUSSION' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM_NEW_DISCUSSION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM_NEW_DISCUSSION_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_easysocial_activity_new_question' , $this->config->get( 'integration_easysocial_activity_new_question' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM_REPLY_DISCUSSION' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM_REPLY_DISCUSSION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM_REPLY_DISCUSSION_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_easysocial_activity_reply_question' , $this->config->get( 'integration_easysocial_activity_reply_question' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM_COMMENTS' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM_COMMENTS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_STREAM_COMMENTS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_easysocial_activity_comment' , $this->config->get( 'integration_easysocial_activity_comment' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_LIKE_QUESTION' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_LIKE_QUESTION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_LIKE_QUESTION_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_easysocial_activity_likes' , $this->config->get( 'integration_easysocial_activity_likes' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_UPGRADE_RANK' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_UPGRADE_RANK' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_UPGRADE_RANK_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_easysocial_activity_ranks' , $this->config->get( 'integration_easysocial_activity_ranks' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_FAVORITE_POST' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_FAVORITE_POST' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_FAVORITE_POST_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_easysocial_activity_favourite' , $this->config->get( 'integration_easysocial_activity_favourite' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_REPLY_ACCEPTED_ANSWER' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_REPLY_ACCEPTED_ANSWER' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_REPLY_ACCEPTED_ANSWER_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_easysocial_activity_accepted' , $this->config->get( 'integration_easysocial_activity_accepted' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_VOTE_POST' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_VOTE_POST' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_ACTIVITY_VOTE_POST_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_easysocial_activity_vote' , $this->config->get( 'integration_easysocial_activity_vote' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_CONTENT_LENGTH' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_CONTENT_LENGTH' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_CONTENT_LENGTH_DESC'); ?>"
						>
							<input type="text" class="input-mini center" name="integration_easysocial_activity_content_length" value="<?php echo $this->config->get( 'integration_easysocial_activity_content_length' );?>" />
							<?php echo JText::_( 'COM_EASYDISCUSS_CHARACTERS' ); ?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_TITLE_LENGTH' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_TITLE_LENGTH' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_TITLE_LENGTH_DESC'); ?>"
						>
							<input type="text" class="input-mini center" name="integration_easysocial_activity_title_length" value="<?php echo $this->config->get( 'integration_easysocial_activity_title_length' );?>" />
							<?php echo JText::_( 'COM_EASYDISCUSS_CHARACTERS' ); ?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
