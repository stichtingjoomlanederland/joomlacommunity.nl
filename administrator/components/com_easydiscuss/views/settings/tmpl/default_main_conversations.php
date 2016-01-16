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
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_CONVERSATIONS_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_CONVERSATIONS_DESC' );?>
		</p>
	</div>
</div>


<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#messaging"><h6><?php echo JText::_( 'COM_EASYDISCUSS_GENERAL' ); ?></h6><i class="icon-chevron-down"></i></a>
			</div>
			<div id="messaging" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_CONVERSATIONS' ); ?></label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_CONVERSATIONS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_CONVERSATIONS_DESC'); ?>">
							<?php echo $this->renderCheckbox( 'main_conversations' , $this->config->get( 'main_conversations' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_MESSAGES_LIMIT' ); ?></label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_MESSAGES_LIMIT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_MESSAGES_LIMIT_DESC'); ?>">
							<input type="text" class="input-mini center" name="main_messages_limit" value="<?php echo $this->config->get( 'main_messages_limit' );?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#messaging-notifications"><h6><?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_NOTIFICATIONS' ); ?></h6><i class="icon-chevron-down"></i></a>
			</div>
			<div id="messaging-notifications" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_NOTIFICATIONS_ENABLE' ); ?></label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_NOTIFICATIONS_ENABLE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CONVERSATIONS_NOTIFICATIONS_ENABLE_DESC'); ?>">
							<?php echo $this->renderCheckbox( 'main_conversations_notification' , $this->config->get( 'main_conversations_notification' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_NOTIFICATIONS_POLLING_INTERVAL' ); ?></label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_NOTIFICATIONS_POLLING_INTERVAL' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CONVERSATIONS_NOTIFICATIONS_POLLING_INTERVAL_DESC'); ?>">
							<input type="text" class="input-mini center" name="main_conversations_notification_interval" value="<?php echo $this->config->get( 'main_conversations_notification_interval' );?>" />
							<span><?php echo JText::_( 'COM_EASYDISCUSS_SECONDS' ); ?></span>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_TOTAL_ITEMS' ); ?></label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_TOTAL_ITEMS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CONVERSATIONS_TOTAL_ITEMS_DESC'); ?>">
							<input type="text" class="input-mini center" name="main_conversations_notification_items" value="<?php echo $this->config->get( 'main_conversations_notification_items' );?>" />
							<span><?php echo JText::_( 'COM_EASYDISCUSS_ITEMS' ); ?></span>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
