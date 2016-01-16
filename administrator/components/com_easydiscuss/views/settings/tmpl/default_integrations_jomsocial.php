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
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_JOMSOCIAL_INTEGRATIONS_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_JOMSOCIAL_INTEGRATIONS_DESC' );?>
		</p>
	</div>
</div>

<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_INTEGRATIONS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_TOOLBAR' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_TOOLBAR' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_TOOLBAR_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_jomsocial_toolbar' , $this->config->get( 'integration_jomsocial_toolbar' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_USERPOINTS' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_USERPOINTS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_USERPOINTS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_jomsocial_points' , $this->config->get( 'integration_jomsocial_points' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LINK_TO_JOMSOCIAL_PROFILE' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LINK_TO_JOMSOCIAL_PROFILE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LINK_TO_JOMSOCIAL_PROFILE_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_toolbar_jomsocial_profile' , $this->config->get( 'integration_toolbar_jomsocial_profile' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LINK_TO_JOMSOCIAL_MESSAGING' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LINK_TO_JOMSOCIAL_MESSAGING' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LINK_TO_JOMSOCIAL_MESSAGING_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_jomsocial_messaging' , $this->config->get( 'integration_jomsocial_messaging' ) );?>
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
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_STREAM' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option02" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_jomsocial_activity_new_question' , $this->config->get( 'integration_jomsocial_activity_new_question' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION_CONTENT' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION_CONTENT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION_CONTENT_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_jomsocial_activity_new_question_content' , $this->config->get( 'integration_jomsocial_activity_new_question_content' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_jomsocial_activity_reply_question' , $this->config->get( 'integration_jomsocial_activity_reply_question' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION_CONTENT' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION_CONTENT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION_CONTENT_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_jomsocial_activity_reply_question_content' , $this->config->get( 'integration_jomsocial_activity_reply_question_content' ) );?>
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
							<input type="text" class="input-mini center" name="integration_jomsocial_activity_content_length" value="<?php echo $this->config->get( 'integration_jomsocial_activity_content_length' );?>" />
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
							<input type="text" class="input-mini center" name="integration_jomsocial_activity_title_length" value="<?php echo $this->config->get( 'integration_jomsocial_activity_title_length' );?>" />
							<?php echo JText::_( 'COM_EASYDISCUSS_CHARACTERS' ); ?>
						</div>
					</div>


					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_COMMENT' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_COMMENT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_COMMENT_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_jomsocial_activity_comment' , $this->config->get( 'integration_jomsocial_activity_comment' ) );?>
						</div>
					</div>



					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_LIKE_QUESTION' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_LIKE_QUESTION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_LIKE_QUESTION_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_jomsocial_activity_likes' , $this->config->get( 'integration_jomsocial_activity_likes' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_BADGES_EARNED' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_BADGES_EARNED' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_BADGES_EARNED_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_jomsocial_activity_badges' , $this->config->get( 'integration_jomsocial_activity_badges' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_RANKED_UP' ); ?>
							</label>
						</div>
						<div class="span6"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_RANKED_UP' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_RANKED_UP_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_jomsocial_activity_ranks' , $this->config->get( 'integration_jomsocial_activity_ranks' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
