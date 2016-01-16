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
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LIVE_NOTIFICATIONS_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LIVE_NOTIFICATIONS_DESC' );?>
		</p>
		<!-- <a href="javascript:void(0)" class="btn btn-success">Documentation</a> -->
	</div>
</div>

<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_NOTIFICATIONS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_NOTIFICATIONS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_NOTIFICATIONS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_notifications' , $this->config->get( 'main_notifications' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_LIMIT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_LIMIT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS_LIMIT_DESC'); ?>"
						>
							<input type="text" name="main_notifications_limit" style="text-align:center;" class="input-mini" value="<?php echo $this->config->get( 'main_notifications_limit' );?>" />
							<?php echo JText::_( 'COM_EASYDISCUSS_ITEMS' ); ?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_INTERVAL' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_INTERVAL' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS_INTERVAL_DESC'); ?>"
						>
							<input type="text" style="text-align: center;" class="input-mini" name="main_notifications_interval" value="<?php echo $this->config->get( 'main_notifications_interval' );?>" />
							<?php echo JText::_( 'COM_EASYDISCUSS_SECONDS' ); ?>
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
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_RULES' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option02" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_REPLY' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_REPLY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_REPLY_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_notifications_reply' , $this->config->get( 'main_notifications_reply' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_LOCK' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_LOCK' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_LOCK_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_notifications_locked' , $this->config->get( 'main_notifications_locked' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_RESOLVED' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_RESOLVED' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_RESOLVED_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_notifications_resolved' , $this->config->get( 'main_notifications_resolved' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_ACCEPTED_ANSWER' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_ACCEPTED_ANSWER' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_ACCEPTED_ANSWER_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_notifications_accepted' , $this->config->get( 'main_notifications_accepted' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_COMMENTS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_COMMENTS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_COMMENTS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_notifications_comments' , $this->config->get( 'main_notifications_comments' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
