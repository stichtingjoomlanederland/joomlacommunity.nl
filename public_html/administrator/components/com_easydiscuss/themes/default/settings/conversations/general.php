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
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_CONVERSATIONS'); ?>
			
			<div id="messaging" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_CONVERSATIONS'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_conversations', $this->config->get('main_conversations')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CONVERSATIONS_NOTIFICATIONS_ENABLE'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_conversations_notification', $this->config->get('main_conversations_notification')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CONVERSATIONS_NOTIFICATIONS_POLLING_INTERVAL'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" class="form-control form-control-sm text-center" name="main_conversations_notification_interval" value="<?php echo $this->config->get('main_conversations_notification_interval');?>" />
							<span class="form-help-inline"><?php echo JText::_('COM_EASYDISCUSS_SECONDS'); ?></span>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CONVERSATIONS_TOTAL_ITEMS'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" class="form-control form-control-sm text-center" name="main_conversations_notification_items" value="<?php echo $this->config->get('main_conversations_notification_items');?>" />
							<span><?php echo JText::_('COM_EASYDISCUSS_ITEMS'); ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
	</div>
</div>