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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_NOTIFICATIONS'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_notifications', $this->config->get('main_notifications')); ?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFICATIONS_LIMIT'); ?>
                        </div>
                        <div class="col-md-7">
							<input type="text" name="main_notifications_limit" style="text-align:center;" class="form-control form-control-sm text-center" value="<?php echo $this->config->get('main_notifications_limit');?>" />
							<?php echo JText::_('COM_EASYDISCUSS_ITEMS'); ?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFICATIONS_INTERVAL'); ?>
                        </div>
                        <div class="col-md-7">
							<input type="text" style="text-align: center;" class="form-control form-control-sm text-center" name="main_notifications_interval" value="<?php echo $this->config->get('main_notifications_interval');?>" />
							<?php echo JText::_('COM_EASYDISCUSS_SECONDS'); ?>
						</div>
					</div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_NOTIFICATION_LIMIT_DISPLAY'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" class="form-control form-control-sm text-center" name="main_notification_listings_limit" value="<?php echo $this->config->get('main_notification_listings_limit', '20' );?>" />
                        </div>
                    </div>

				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_RULES'); ?>

			<div id="option02" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_REPLY'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_notifications_reply', $this->config->get('main_notifications_reply')); ?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_LOCK'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_notifications_locked', $this->config->get('main_notifications_locked')); ?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_RESOLVED'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_notifications_resolved', $this->config->get('main_notifications_resolved')); ?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_ACCEPTED_ANSWER'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_notifications_accepted', $this->config->get('main_notifications_accepted')); ?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_COMMENTS'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_notifications_comments', $this->config->get('main_notifications_comments')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>