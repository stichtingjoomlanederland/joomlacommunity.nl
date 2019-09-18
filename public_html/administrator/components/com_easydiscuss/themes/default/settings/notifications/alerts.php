<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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
					<?php echo $this->html('settings.toggle', 'main_notifications', 'COM_EASYDISCUSS_ENABLE_NOTIFICATIONS'); ?>
					<?php echo $this->html('settings.textbox', 'main_notifications_limit', 'COM_EASYDISCUSS_NOTIFICATIONS_LIMIT', '', array('size' => 7, 'postfix' => 'COM_EASYDISCUSS_ITEMS'), '', 'form-control-sm text-center'); ?>
					<?php echo $this->html('settings.textbox', 'main_notifications_interval', 'COM_EASYDISCUSS_NOTIFICATIONS_INTERVAL', '', array('size' => 8, 'postfix' => 'COM_EASYDISCUSS_SECONDS'), '', 'form-control-sm text-center'); ?>
					<?php echo $this->html('settings.textbox', 'main_notification_listings_limit', 'COM_EASYDISCUSS_NOTIFICATION_LIMIT_DISPLAY', '', array('size' => 7, 
									'defaultValue' => 20, 'postfix' => 'Items'), '', 'form-control-sm text-center'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_RULES'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_notifications_reply', 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_REPLY'); ?>
					<?php echo $this->html('settings.toggle', 'main_notifications_locked', 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_LOCK'); ?>
					<?php echo $this->html('settings.toggle', 'main_notifications_resolved', 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_RESOLVED'); ?>
					<?php echo $this->html('settings.toggle', 'main_notifications_accepted', 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_ACCEPTED_ANSWER'); ?>
					<?php echo $this->html('settings.toggle', 'main_notifications_comments', 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_COMMENTS'); ?>
					<?php echo $this->html('settings.toggle', 'main_notifications_liked', 'COM_ED_LIVE_NOTIFICATIONS_FOR_LIKED'); ?>
				</div>
			</div>
		</div>
	</div>
</div>